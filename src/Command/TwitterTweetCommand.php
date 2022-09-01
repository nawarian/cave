<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\TwitterService;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TwitterTweetCommand extends Command
{
    protected static $defaultName = 'twitter:tweet';
    protected static $defaultDescription = 'Publishes a new twitter status.';

    private TwitterService $twitterService;

    public function __construct(TwitterService $twitterService)
    {
        parent::__construct();
        $this->twitterService = $twitterService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('text', InputArgument::REQUIRED, 'Twitter status text')
            ->addOption(
                'attachments',
                'a',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'A list of images to be attached',
                []
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NEGATABLE,
                'Suppress confirmation messages',
                false
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $force = $input->getOption('force');
        $text = $input->getArgument('text');
        $medias = array_filter($input->getOption('attachments'), 'file_exists');

        ['name' => $twitterUserName, 'handle' => $twitterHandle] = $this->twitterService->whoAmI();

        !$io->isQuiet() && !$force && $io->horizontalTable(
            ['Text', 'Images', 'Account', 'Profile name'],
            [[$text, implode(', ', $medias), $twitterHandle, $twitterUserName]]
        );

        if (!$force && !$io->confirm('Proceed with this tweet?')) {
            !$io->isQuiet() && $io->writeln("Aborting.");
            return self::SUCCESS;
        }

        $mediaIds = [];
        foreach ($medias as $media) {
            $mediaIds[] = $this->twitterService->uploadMedia(new SplFileInfo($media));
        }

        $tweetLink = $this->twitterService->tweet($text, $mediaIds);

        !$io->isQuiet() && $io->writeln($tweetLink);

        return self::SUCCESS;
    }
}
