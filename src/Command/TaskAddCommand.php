<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Service\ProfileService;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TaskAddCommand extends Command
{
    protected static $defaultName = 'task:add';
    protected static $defaultDescription = 'Creates a new task';

    private TaskRepository $taskRepository;
    private ProfileService $profileService;

    public function __construct(ProfileService $profileService, TaskRepository $taskRepository)
    {
        parent::__construct();
        $this->profileService = $profileService;
        $this->taskRepository = $taskRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('summary', InputArgument::REQUIRED, 'The summary of your task')
            ->addOption('due', 'd', InputOption::VALUE_OPTIONAL, 'Tasks due date in PHP date format', '2099-01-01')
            ->addOption('project', null, InputOption::VALUE_OPTIONAL, 'Project name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $profile = $this->profileService->getCurrentProfile();

        $summary = $input->getArgument('summary');
        $due = new DateTimeImmutable($input->getOption('due'));
        $project = $input->getOption('project') ?? null;

        $task = new Task();
        $task->setSummary($summary);
        $task->setDue($due);
        $task->setProfile($profile);

        if ($project !== null) {
            $task->setProject($project);
        }

        $this->taskRepository->add($task, true);

        $io->success("Task {$task->getId()} {$task->getSummary()} created.");

        return self::SUCCESS;
    }
}
