<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\TwitterProfile;
use App\Entity\TwitterProfileStat;
use App\Repository\TwitterProfileRepository;
use App\Repository\TwitterProfileStatRepository;
use App\Service\TwitterService;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TwitterStatsCommand extends Command
{
    protected static $defaultName = 'twitter:stats';
    protected static $defaultDescription = 'Fetch latest twitter stats and, optionally, stores it';

    private TwitterService $twitterService;
    private TwitterProfileRepository $twitterProfileRepository;
    private TwitterProfileStatRepository $twitterProfileStatRepository;

    public function __construct(
        TwitterService $twitterService,
        TwitterProfileRepository $twitterProfileRepository,
        TwitterProfileStatRepository $twitterProfileStatRepository
    ) {
        parent::__construct();

        $this->twitterService = $twitterService;
        $this->twitterProfileRepository = $twitterProfileRepository;
        $this->twitterProfileStatRepository = $twitterProfileStatRepository;
    }

    protected function configure()
    {
        $this
            ->addOption('store', 's', InputOption::VALUE_NEGATABLE, 'Persist history', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $shouldStore = $input->getOption('store');
        ['handle' => $handle, 'followers' => $followers, 'friends' => $friends] = $this->twitterService->whoAmI();

        $profile = $this->twitterProfileRepository->findOneBy(['handle' => $handle]);
        if ($profile === null) {
            $profile = (new TwitterProfile())
                ->setHandle($handle)
                ->setFollowers($followers)
                ->setFriends($friends);

        }
        $profile
            ->setFollowers($followers)
            ->setFriends($friends);
        $shouldStore && $this->twitterProfileRepository->add($profile, true);

        $latestStat = $this->twitterProfileStatRepository->findOneBy(['profile' => $profile], ['createdAt' => 'DESC']);
        if ($latestStat === null || $latestStat->getCreatedAt() < (new DateTimeImmutable('today'))) {
            $latestStat = (new TwitterProfileStat())->setProfile($profile);
        }

        $latestStat
            ->setFollowers($followers)
            ->setFriends($friends)
            ->setCreatedAt(new DateTimeImmutable());
        $shouldStore && $this->twitterProfileStatRepository->add($latestStat, true);

        if (!$io->isQuiet()) {
            $io->horizontalTable(
                ['Twitter handle', 'Followers', 'Friends'],
                [[$handle, $followers, $friends]],
            );
        }

        return self::SUCCESS;
    }
}
