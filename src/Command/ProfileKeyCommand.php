<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ProfileKV;
use App\Repository\ProfileKVRepository;
use App\Repository\ProfileRepository;
use App\Service\ProfileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProfileKeyCommand extends Command
{
    protected static $defaultName = 'profile:kv';
    protected static $defaultDescription = 'Manages a profile\'s Key-value store';

    private ProfileService $profileService;
    private ProfileKVRepository $profileKVRepository;
    private ProfileRepository $profileRepository;

    public function __construct(
        ProfileService $profileService,
        ProfileRepository $profileRepository,
        ProfileKVRepository $profileKVRepository
    ) {
        parent::__construct();

        $this->profileService = $profileService;
        $this->profileRepository = $profileRepository;
        $this->profileKVRepository = $profileKVRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('key', InputArgument::REQUIRED, 'Key to search for in the KV store')
            ->addArgument('value', InputArgument::OPTIONAL, 'New value to be set')
            ->addOption(
                'delete',
                'd',
                InputOption::VALUE_NEGATABLE,
                'Deletes the found key',
                false,
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $key = $input->getArgument('key');
        $value = $input->getArgument('value') ?? null;
        $isDelete = $input->getOption('delete') ?? false;

        $profile = $this->profileService->getCurrentProfile();

        $kv = $profile->getKv()->filter(fn (ProfileKV $kv) => $kv->getKey() === $key)->first();
        if ($kv === false && $value === null) {
            $io->error("Key '{$key}' not found in profile '{$profile->getName()}'");
            return self::FAILURE;
        }

        if ($kv === false) {
            $kv = new ProfileKV();
            $kv->setProfile($profile);
            $kv->setKey($key);
        }

        if ($value !== null) {
            $kv->setValue($value);
            $this->profileKVRepository->add($kv, true);
        }

        if ($isDelete) {
            $profile->removeKv($kv);
            $this->profileRepository->add($profile, true);
        }

        if ($value === null && !$isDelete) {
            $io->writeln($kv->getValue());
        }

        return self::SUCCESS;
    }
}
