<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Task;
use App\Entity\TaskLog;
use App\Repository\TaskLogRepository;
use App\Repository\TaskRepository;
use App\Service\ProfileService;
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
    private ProfileService $profileService;

    public function __construct(ProfileService $profileService, TaskRepository $taskRepository, TaskLogRepository $taskLogRepository)
    {
        parent::__construct();
        $this->taskRepository = $taskRepository;
        $this->taskLogRepository = $taskLogRepository;
        $this->profileService = $profileService;
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
        $profile = $this->profileService->getCurrentProfile();
        $taskId = $input->getArgument('taskId') ?? null;
        if ($taskId !== null) {
            $taskId = (int) $taskId;
        }

        $due = new DateTimeImmutable($input->getOption('due'));
        $project = $input->getOption('project');

        /** @var Task[] $tasks */
        $tasks = array_filter(
            $this->taskRepository->findTasksByDueDate($profile, $due),
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

            $task->markAsPending();
            $this->taskRepository->add($task, true);
        }

        return self::SUCCESS;
    }
}
