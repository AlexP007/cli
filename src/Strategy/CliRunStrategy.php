<?php


namespace Cli\Strategy;

use Cli\Basic\Cli;
use Cli\Domain\CliRequest;
use Cli\Registry\HandlerRegistry;
use Cli\Exception\CommandException;

/**
 * Class Value
 * @package Cli/Strategy
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class CliRunStrategy extends Strategy
{
    /**
     * @var CliRequest;
     */
    private $cliRequest;

    /**
     * @var HandlerRegistry
     */
    private $handlers;

    /**
     * @var string
     */
    private $commandName;

    public function __construct(Cli $cli, CliRequest $request, HandlerRegistry $handlers)
    {
        $this->cli = $cli;
        $this->cliRequest = $request;
        $this->handlers = $handlers;
        $this->commandName = $this->cliRequest->getCommandName();
    }

    public function run()
    {
        $this->validateAllowedCommands();

        $commandName = $this->commandName;
        $commandExecutor = new CommandExecuteStrategy(
            $this->handlers->$commandName,
            $this->cliRequest
        );

        return $commandExecutor->run();
    }

    /**
     * @throws CommandException
     */
    private function validateAllowedCommands()
    {
        $commandName = $this->commandName;
        if (!$this->handlers->isSet($commandName) ) {
            throw new CommandException("not allowed command {$this->cliRequest->getCommandName()}");
        }
    }
}