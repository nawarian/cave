<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\ScheduledCommandRepository;
use App\Service\ProfileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Throwable;

class ScheduleRunCommand extends Command
{
    protected static $defaultName = 'schedule:run';
    protected static $defaultDescription = 'Runs pending commands';

    private ScheduledCommandRepository $scheduledCommandRepository;
    private ProfileService $profileService;

    public function __construct(
        ScheduledCommandRepository $scheduledCommandRepository,
        ProfileService $profileService
    ) {
        parent::__construct();
        $this->scheduledCommandRepository = $scheduledCommandRepository;
        $this->profileService = $profileService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'count',
                InputArgument::OPTIONAL,
                'How many commands should run before exiting',
                1,
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $remaining = (int) $input->getArgument('count');

        $originalProfile = $this->profileService->getCurrentProfile();
        $allPendingCommands = $this->scheduledCommandRepository->getAllPendingScheduledCommands($remaining);

        try {
            foreach ($allPendingCommands as $command) {
                // Update current profile based on command
                $this->profileService->setCurrentProfile($command->getProfile());

                $process = Process::fromShellCommandline($command->getCommandLine());
                $exitCode = $process->run();

                if ($exitCode !== self::SUCCESS) {
                    $command->setAttempts($command->getAttempts() + 1);
                    $this->scheduledCommandRepository->add($command, true);
                    continue;
                }

                $this->scheduledCommandRepository->remove($command, true);
            }
        } catch (Throwable $e) {
            $this->profileService->setCurrentProfile($originalProfile);
            return self::FAILURE;
        }

        // Restore initial profile
        $this->profileService->setCurrentProfile($originalProfile);

        return self::SUCCESS;
    }
}
