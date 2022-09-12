<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\TaskAnnotation;
use App\Entity\TaskLog;
use App\Service\ProfileService;
use DateTimeImmutable;
use DateTimeInterface;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TaskListCommand extends Command
{
    private const TS_DAY = 60 * 60 * 24;
    private const TS_HOUR = 60 * 60;
    private const TS_MINUTE = 60;

    protected static $defaultName = 'task:list';
    protected static $defaultDescription = 'Lists all tasks respecting provided filters';

    private TaskRepository $taskRepository;
    private ProfileService $profileService;

    public function __construct(ProfileService $profileService, TaskRepository $taskRepository)
    {
        parent::__construct();
        $this->taskRepository = $taskRepository;
        $this->profileService = $profileService;
    }

    protected function configure(): void
    {
        $this
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Filter by project name')
            ->addOption('due', 'd', InputOption::VALUE_OPTIONAL, 'Due date range in PHP format', '2099-01-01')
            ->addOption('status', 's', InputOption::VALUE_OPTIONAL, 'Filter by status')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $profile = $this->profileService->getCurrentProfile();
        $project = $input->getOption('project');
        $due = new DateTimeImmutable($input->getOption('due'));
        $status = $input->getOption('status') ?? null;

        $tasks = array_filter(
            $this->taskRepository->findTasksByDueDate($profile, $due),
            fn (Task $t) => ($project === null || $t->getProject() === $project)
                && (($status === null && $t->isPending() || $t->isInProgress()) || $t->getStatus() === $status)
                && $t->getProfile() === $profile,
        );

        $io->table(
            ['ID', 'Summary', 'Project', 'Due', 'Time spent'],
            array_map(
                fn (Task $t) => [
                    new TableCell((string) $t->getId(), [
                        'style' => new TableCellStyle([
                            'bg' => ['progress' => 'green'][$t->getStatus()] ?? 'default',
                        ])
                    ]),
                    $this->formatSummary($t),
                    $t->getProject(),
                    $this->formatDueDate($t->getDue()),
                    $this->calculateTimeSpent($t),
                ],
                $tasks
            )
        );

        return self::SUCCESS;
    }

    private function formatSummary(Task $task): string
    {
        $summary = [
            $task->getSummary(),
            ...$task->getAnnotations()
                ->map(fn (TaskAnnotation $a) => "\t{$a->getText()}")
                ->toArray(),
        ];
        return implode(PHP_EOL, $summary);
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

    private function calculateTimeSpent(Task $t): string
    {
        $logsInSeconds = $t
            ->getLogs()
            ->filter(fn (TaskLog $tl) => $tl->getStart() !== null && $tl->getFinish() !== null)
            ->map(fn (TaskLog $tl) => $tl->getFinish()->getTimestamp() - $tl->getStart()->getTimestamp())
            ->toArray();

        $totalTimeSpentInSeconds = array_sum($logsInSeconds);

        if ($totalTimeSpentInSeconds === 0) {
            return '';
        }

        if ($totalTimeSpentInSeconds >= self::TS_DAY) {
            return floor($totalTimeSpentInSeconds / self::TS_DAY) . ' days';
        }

        if ($totalTimeSpentInSeconds >= self::TS_HOUR) {
            return floor($totalTimeSpentInSeconds / self::TS_HOUR) . ' hours';
        }

        return ceil($totalTimeSpentInSeconds / self::TS_MINUTE) . ' minutes';
    }
}
