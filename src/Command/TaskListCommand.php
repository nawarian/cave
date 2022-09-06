<?php

declare(strict_types=1);

namespace App\Command;

use DateTimeImmutable;
use DateTimeInterface;
use DateInterval;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TaskListCommand extends Command
{
    protected static $defaultName = 'task:list';
    protected static $defaultDescription = 'Lists all tasks respecting provided filters';

    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        parent::__construct();
        $this->taskRepository = $taskRepository;
    }

    protected function configure(): void
    {
        $this
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Filter by project name')
            ->addOption('due', 'd', InputOption::VALUE_OPTIONAL, 'Due date range in PHP format', '2099-01-01')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $project = $input->getOption('project');
        $due = new DateTimeImmutable($input->getOption('due'));

        $tasks = $this->taskRepository->findTasksByDueDate($due);
        $tasks = array_filter(
            $this->taskRepository->findTasksByDueDate($due),
            fn (Task $t) => $project === null || $t->getProject() === $project,
        );

        $io->table(
            ['ID', 'Summary', 'Project', 'Due'],
            array_map(
                fn (Task $t) => [
                    $t->getId(),
                    $t->getSummary(),
                    $t->getProject(),
                    $this->formatDueDate($t->getDue()),
                ],
                $tasks
            )
        );

        return self::SUCCESS;
    }

    private function formatDueDate(DateTimeInterface $due): string
    {
        $today = new DateTimeImmutable();
        $diff = $today->diff($due);
        $days = $diff->days;

        if (
            (
                $due->format('Y-m-d') !== $today->format('Y-m-d')
                && $days === 0
                && $today < $due
            ) || $days === 1
        ) {
            return 'tomorrow';
        }

        if ($days === 0) {
            return 'today';
        }

        return "{$days} days";
    }
}

