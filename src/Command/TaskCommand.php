<?php

declare(strict_types=1);

namespace App\Command;

use ParseError;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaskCommand extends Command
{
    protected static $defaultName = 'task';
    protected static $defaultDescription = 'A task manager';

    private const PARAM_COMMAND_MAP = [
        'add' => 'task:add',
        'annotate' => 'task:annotate',
        'done' => 'task:done',
        'list' => 'task:list',
        'start' => 'task:start',
        'stop' => 'task:stop',
    ];

    protected function configure(): void
    {
        $this
            ->addArgument('params', InputArgument::IS_ARRAY, '')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $params = $input->getArgument('params');

        $cmd = $this->getApplication()->find($this->guessCommandName($params));
        $input = new ArrayInput($this->guessCommandParams($cmd->getCommand(), $params));

        return $cmd->run($input, $output);
    }

    /** @param string[] $params */
    private function guessCommandParams(Command $command, array $params): array
    {
        $commandString = array_search($command->getName(), self::PARAM_COMMAND_MAP);

        $unset = [];
        // Remove first occurrence of the command name
        foreach ($params as $key => $param) {
            if ($param === $commandString) {
                $unset[] = $key;
                break;
            }
        }

        $commandParams = [];
        // Collect common params
        foreach ($params as $key => $param) {
            if (is_numeric($param) && $key === 1) {
                $commandParams['taskId'] = $param;
                $unset[] = $key;
            }

            if (str_starts_with($param, 'project:')) {
                $commandParams['--project'] = explode('project:', $param)[1];
                $unset[] = $key;
            }

            if (str_starts_with($param, 'due:')) {
                $commandParams['--due'] = explode('due:', $param)[1];
                $unset[] = $key;
            }

            if (str_starts_with($param, 'status:')) {
                $commandParams['--status'] = explode('status:', $param)[1];
                $unset[] = $key;
            }
        }

        // Remove consumed params from the array
        foreach ($unset as $key) {
            unset($params[$key]);
        }

        switch (get_class($command)) {
            case TaskAddCommand::class:
                $commandParams['summary'] = implode(' ', $params);
                break;
            case TaskAnnotateCommand::class:
                $commandParams['annotation'] = implode(' ', $params);
                break;
        }

        return $commandParams;
    }

    /** @param string[] $params */
    private function guessCommandName(array $params): string
    {
        foreach ($params as $param) {
            if (array_key_exists($param, self::PARAM_COMMAND_MAP)) {
                return self::PARAM_COMMAND_MAP[$param];
            }
        }

        throw new ParseError("Could not parse task command.");
    }
}
