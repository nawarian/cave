<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Tweet;
use App\Entity\TweetMedia;
use App\Repository\TweetRepository;
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

    private TweetRepository $tweetRepository;
    private TwitterService $twitterService;

    public function __construct(TweetRepository $tweetRepository, TwitterService $twitterService)
    {
        parent::__construct();
        $this->tweetRepository = $tweetRepository;
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

        $tweet = new Tweet();
        $tweet->setText($text);

        foreach ($medias as $media) {
            $tweetMedia = new TweetMedia();
            $tweetMedia->setFilepath($media);
            $tweetMedia->setUploaded(false);
            $tweet->addMedia($tweetMedia);
        }

        !$io->isQuiet() && !$force && $io->horizontalTable(
            ['Text', 'Images', 'Account', 'Profile name'],
            [[$text, implode(', ', $medias), $twitterHandle, $twitterUserName]]
        );

        if (!$force && !$io->confirm('Proceed with this tweet?')) {
            !$io->isQuiet() && $io->writeln("Aborting.");
            return self::SUCCESS;
        }

        foreach ($tweet->getMedias() as $media) {
            $mediaId = $this->twitterService->uploadMedia(new SplFileInfo($media->getFilepath()));
            $media->setTwitterMediaId($mediaId);
            $media->setUploaded(true);
        }

        $tweetId = $this->twitterService->tweet(
            $text,
            $tweet
                ->getMedias()
                ->map(fn (TweetMedia $m) => $m->getTwitterMediaId())
                ->toArray()
        );
        $tweet->setTwitterTweetId($tweetId);
        $tweet->setSent(true);
        $this->tweetRepository->add($tweet, true);

        !$io->isQuiet() && $io->writeln("https://twitter.com/{$twitterHandle}/status/{$tweetId}");

        return self::SUCCESS;
    }
}
