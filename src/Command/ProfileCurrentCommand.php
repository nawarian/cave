<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ProfileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProfileCurrentCommand extends Command
{
    protected static $defaultName = 'profile:current|profile';
    protected static $defaultDescription = 'Outputs the currently active profile name';

    private ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        parent::__construct();
        $this->profileService = $profileService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $profile = $this->profileService->getCurrentProfile();

        $io->writeln($profile->getName());

        return self::SUCCESS;
    }
}
