<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ScheduledCommand;
use App\Repository\ScheduledCommandRepository;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScheduleCommand extends Command
{
    protected static $defaultName = 'schedule:command';
    protected static $defaultDescription = 'Schedules a command to be executed';

    private ScheduledCommandRepository $scheduledCommandRepository;

    public function __construct(ScheduledCommandRepository $scheduledCommandRepository)
    {
        parent::__construct();
        $this->scheduledCommandRepository = $scheduledCommandRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('cmd', InputArgument::REQUIRED, 'Command line to be executed')
            ->addArgument(
                'date',
                InputArgument::REQUIRED,
                'When this command should be executed – e.g. tomorrow 9am'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $input->getArgument('cmd');
        $date = $input->getArgument('date');

        $scheduledCommand = new ScheduledCommand();
        $scheduledCommand->setCommandLine($command);
        $scheduledCommand->setDue(new DateTimeImmutable($date));
        $scheduledCommand->setAttempts(0);

        $this->scheduledCommandRepository->add($scheduledCommand, true);

        return self::SUCCESS;
    }
}
