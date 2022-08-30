<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProfileSetCommand extends Command
{
    protected static $defaultName = 'profile:set';
    protected static $defaultDescription = 'Sets the current profile';

    private ProfileRepository $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        parent::__construct();

        $this->profileRepository = $profileRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the profile. If not existent, will be created.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $profileName = $input->getArgument('name');

        if (!is_string($profileName) || $profileName === '') {
            $io->error("Please provide a non-empty profile name: \"{$profileName}\" given.");
            return self::FAILURE;
        }

        foreach ($this->profileRepository->findAll() as $profile) {
            $profile->setCurrent(false);
            $this->profileRepository->add($profile);
        }

        $profile = $this->profileRepository->findOneBy(['name' => $profileName]);

        if ($profile === null) {
            $profile = new Profile();
            $profile->setName($profileName);
        }

        $profile->setCurrent(true);
        $this->profileRepository->add($profile, true);

        return self::SUCCESS;
    }
}
