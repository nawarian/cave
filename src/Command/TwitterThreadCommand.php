<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Tweet;
use App\Entity\TweetMedia;
use App\Entity\TwitterThread;
use App\Repository\TweetMediaRepository;
use App\Repository\TweetRepository;
use App\Repository\TwitterThreadRepository;
use App\Service\TwitterService;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TwitterThreadCommand extends Command
{
    protected static $defaultName = 'twitter:thread';
    protected static $defaultDescription = 'Creates a twitter thread based on a markdown file';

    private TweetRepository $tweetRepository;
    private TweetMediaRepository $tweetMediaRepository;
    private TwitterThreadRepository $twitterThreadRepository;
    private TwitterService $twitterService;

    public function __construct(
        TweetRepository $tweetRepository,
        TweetMediaRepository $tweetMediaRepository,
        TwitterThreadRepository $twitterThreadRepository,
        TwitterService $twitterService
    ) {
        parent::__construct();
        $this->tweetRepository = $tweetRepository;
        $this->tweetMediaRepository = $tweetMediaRepository;
        $this->twitterThreadRepository = $twitterThreadRepository;
        $this->twitterService = $twitterService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Markdown to fetch tweets from')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $file = new SplFileInfo($input->getArgument('file'));
        $content = $file->openFile()->fread($file->getSize());

        $tweets = $this->parseContent($content);
        $thread = $this->persistThread($tweets);

        $twitterHandle = $this->twitterService->whoAmI()['handle'];
        $previousTweetId = null;

        $pendingTweets = $thread->getTweets()->filter(fn(Tweet $t) => !$t->isSent());
        foreach ($pendingTweets as $tweet) {
            $mediaIds = [];
            foreach ($tweet->getMedias() as $media) {
                $mediaId = $media->getTwitterMediaId() ?? $this->twitterService->uploadMedia(
                    new SplFileInfo($media->getFilepath())
                );
                $media->setTwitterMediaId($mediaId);
                $media->setUploaded(true);
                $this->tweetMediaRepository->add($media, true);
                $mediaIds[] = $mediaId;
            }

            if ($previousTweetId !== null) {
                $tweet->setReplyToTweetId($previousTweetId);
            }

            $tweetId = $this->twitterService->tweet(
                $tweet->getText(),
                $mediaIds,
                $tweet->getReplyToTweetId(),
                $twitterHandle
            );

            $tweet->setTwitterTweetId($tweetId);
            $tweet->setSent(true);
            $this->tweetRepository->add($tweet, true);

            $previousTweetId = $tweetId;
        }
        $thread->setProcessed(true);
        $this->twitterThreadRepository->add($thread, true);

        $firstTweetId = $thread->getTweets()->first()->getTwitterTweetId();
        !$io->isQuiet() && $io->writeln("https://twitter.com/{$twitterHandle}/status/{$firstTweetId}");

        return self::SUCCESS;
    }

    /** @return Tweet[] */
    private function parseContent(string $content): array
    {
        $tweets = [];

        $currentTweet = null;

        $i = 0;
        while ($char = $content[$i++] ?? false) {
            switch ($char) {
                case '#': // Heading detected
                    if ($currentTweet !== null) { // Starting a new tweet, record previous one
                        $tweets[] = $currentTweet;
                    }

                    while ($content[$i++] === '#'); // advances until end of heading marking

                    $heading = '';
                    while ($char = $content[$i++]) {
                        if ($char === PHP_EOL) {
                            break;
                        }
                        $heading .= $char;
                    }

                    $currentTweet = new Tweet();
                    break;
                case '!': // Image detected
                    $rawImg = '';
                    while($content[$i] !== PHP_EOL) {
                        $rawImg .= $content[$i++];
                    }

                    if (!preg_match('#^\[(.*)\]\((.+)\)$#', $rawImg, $matches)) {
                        throw new RuntimeException("Detected image is malformed: '{$rawImg}'");
                    }

                    [, $alt, $path] = $matches;
                    $tweetMedia = new TweetMedia();
                    $tweetMedia->setTweet($currentTweet);
                    $tweetMedia->setAltText($alt);
                    $tweetMedia->setFilepath($path);
                    $currentTweet->addMedia($tweetMedia);
                    break;
                case PHP_EOL:
                    break;
                default:
                    $text = '';
                    while (($content[$i] ?? PHP_EOL) !== PHP_EOL) {
                        $text .= $content[$i++ - 1];
                    }
                    $currentTweet->setText(trim($text));
                    break;
            }
        }

        if ($currentTweet !== null && $currentTweet !== []) {
            $tweets[] = $currentTweet;
        }

        return $tweets;
    }

    private function persistThread(array $tweets): TwitterThread
    {
        $thread = new TwitterThread();
        $this->twitterThreadRepository->add($thread, true);
        foreach ($tweets as $tweet) {
            $this->tweetRepository->add($tweet, true);
            foreach ($tweet->getMedias() as $media) {
                $this->tweetMediaRepository->add($media, true);
            }

            $thread->addTweet($tweet);
        }
        $this->twitterThreadRepository->add($thread, true);

        return $thread;
    }
}
