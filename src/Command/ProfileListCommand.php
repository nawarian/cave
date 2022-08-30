<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProfileListCommand extends Command
{
    protected static $defaultName = 'profile:list';
    protected static $defaultDescription = 'Shows a list with all existent profiles';

    private ProfileRepository $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        parent::__construct();

        $this->profileRepository = $profileRepository;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $profiles = $this->profileRepository->findAll();
        $rows = array_map(
            fn (Profile $p) => [$p->getId(), $p->isCurrent() ? "<bg=blue>{$p->getName()} (active)</>" : $p->getName()],
            $profiles
        );

        $io->table(['#', 'Profile'], $rows);

        return Command::SUCCESS;
    }
}
