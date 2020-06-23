<?php


namespace Strategy;

use Domain\{Command, CliRequest};

/**
 * Class Value
 * @package Cli/Strategy
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class CommandExecuteStrategy extends Strategy
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @var CliRequest
     */
    private $cliRequest;

    public function __construct(Command $command, CliRequest $cliRequest)
    {
        $this->command = $command;
        $this->cliRequest = $cliRequest;
    }

    public function run()
    {
        return $this->command->invoke($this->cliRequest);
    }
}
