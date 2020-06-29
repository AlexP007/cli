<?php


namespace Cli\Strategy;

use Cli\Basic\Flags;
use Cli\Basic\Environment;
use Cli\Basic\Params;
use Cli\Domain\Command;
use Cli\Domain\CliRequest;
use Cli\Reflections\CommandReflection;
use Cli\Traits\ArgumentThrower;

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
    use ArgumentThrower;

    /**
     * @var Command
     */
    private $command;

    /**
     * @var CliRequest
     */
    private $cliRequest;

    /**
     * CommandExecuteStrategy constructor.
     *
     * @param Command $command
     * @param CliRequest $cliRequest
     */
    public function __construct(Command $command, CliRequest $cliRequest)
    {
        $this->command = $command;
        $this->cliRequest = $cliRequest;
    }


    public function run()
    {
        return $this->command
            ->prepareForInvocation($this->cliRequest)
            ->validateRequest()
            ->invoke();
    }
}
