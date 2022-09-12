<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Task;
use App\Entity\TaskAnnotation;
use App\Repository\TaskAnnotationRepository;
use App\Service\ProfileService;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TaskAnnotateCommand extends Command
{
    protected static $defaultName = 'task:annotate';
    protected static $defaultDescription = 'Annotates a given task';

    private TaskAnnotationRepository $taskAnnotationRepository;
    private ProfileService $profileService;

    public function __construct(ProfileService $profileService, TaskAnnotationRepository $taskAnnotationRepository) {
        parent::__construct();
        $this->taskAnnotationRepository = $taskAnnotationRepository;
        $this->profileService = $profileService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('taskId', InputArgument::REQUIRED, 'Identifier of the task to be started')
            ->addArgument('annotation', InputArgument::REQUIRED, 'Text to annotate the task with')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $taskId = (int) $input->getArgument('taskId');
        $annotation = $input->getArgument('annotation');

        $profile = $this->profileService->getCurrentProfile();
        $task = $profile->getTasks()->filter(fn (Task $t) => $t->getId() === $taskId)->first();

        if ($task === false) {
            $io->error("Task '{$taskId}' not found.");
            return self::FAILURE;
        }

        $taskAnnotation = (new TaskAnnotation())
            ->setTask($task)
            ->setText($annotation)
            ->setCreatedAt(new DateTimeImmutable());

        $this->taskAnnotationRepository->add($taskAnnotation, true);

        return self::SUCCESS;
    }
}
