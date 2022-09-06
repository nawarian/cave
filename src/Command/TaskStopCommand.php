<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Task;
use App\Entity\TaskLog;
use App\Repository\TaskLogRepository;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TaskStopCommand extends Command
{
    protected static $defaultName = 'task:stop';
    protected static $defaultDescription = 'Stops progress on a specific task';

    private TaskRepository $taskRepository;
    private TaskLogRepository $taskLogRepository;

    public function __construct(TaskRepository $taskRepository, TaskLogRepository $taskLogRepository)
    {
        parent::__construct();
        $this->taskRepository = $taskRepository;
        $this->taskLogRepository = $taskLogRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('taskId', InputArgument::OPTIONAL, 'Identifier of the task to stop progress')
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'A project to filter by')
            ->addOption('due', 'd', InputOption::VALUE_OPTIONAL, 'A due date to filter by', '2099-01-01')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $taskId = $input->getArgument('taskId');
        $due = new DateTimeImmutable($input->getOption('due'));
        $project = $input->getOption('project');

        /** @var Task[] $tasks */
        $tasks = array_filter(
            $this->taskRepository->findTasksByDueDate($due),
            function (Task $t) use ($taskId, $project) {
                if ($taskId !== null && $t->getId() !== $taskId) {
                    return false;
                }

                if ($project !== null && $t->getProject() !== $project) {
                    return false;
                }

                return true;
            }
        );

        foreach ($tasks as $task) {
            /** @var TaskLog|false $pendingLog */
            $pendingLog = $task
                ->getLogs()
                ->filter(fn (TaskLog $tl) => $tl->getFinish() === null)
                ->first();

            if ($pendingLog instanceof TaskLog) {
                $pendingLog->setFinish(new DateTimeImmutable());
                $this->taskLogRepository->add($pendingLog, true);
            }
        }

        return self::SUCCESS;
    }
}
