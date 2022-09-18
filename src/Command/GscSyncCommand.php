<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\GoogleStat;
use App\Repository\GoogleStatRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Google\Service\SearchConsole;
use Google_Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GscSyncCommand extends Command
{
    protected static $defaultName = 'gsc:sync';

    private Google_Client $client;
    private GoogleStatRepository $googleStatRepository;

    public function __construct(GoogleStatRepository $googleStatRepository, Google_Client $client)
    {
        parent::__construct();
        $this->client = $client;
        $this->googleStatRepository = $googleStatRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('site', InputArgument::REQUIRED, 'Which website to sync from')
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'From which date to sync', 'today -3 months')
            ->addOption('until', null, InputOption::VALUE_OPTIONAL, 'Until which date to sync', 'today')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->syncGoogleStats(
            $input->getArgument('site'),
            new DateTimeImmutable($input->getOption('from')),
            new DateTimeImmutable($input->getOption('until')),
        );

        return self::SUCCESS;
    }

    private function syncGoogleStats(string $site, DateTimeInterface $begin, DateTimeInterface $end): void
    {
        $searchAnalyticsQuery = new SearchConsole\SearchAnalyticsQueryRequest();
        $searchAnalyticsQuery->setStartDate($begin->format('Y-m-d'));
        $searchAnalyticsQuery->setEndDate($end->format('Y-m-d'));
        $searchAnalyticsQuery->setDimensions('date');

        $gsc = new SearchConsole($this->client);
        $response = $gsc->searchanalytics->query($site, $searchAnalyticsQuery);

        /** @var SearchConsole\ApiDataRow $row */
        foreach ($response as $row) {
            [$date] = $row->keys;
            $date = new DateTimeImmutable($date);

            $stat = $this->googleStatRepository->findOneBy([
                'site' => $site,
                'date' => $date,
            ]) ?? new GoogleStat();

            $stat
                ->setPosition($row->position)
                ->setClicks($row->clicks)
                ->setCtr($row->ctr)
                ->setImpressions($row->impressions)
                ->setDate($date)
                ->setSite($site);

            $this->googleStatRepository->add($stat, true);
        }
    }
}
