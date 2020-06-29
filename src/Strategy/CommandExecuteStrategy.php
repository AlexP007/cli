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
        $this->command->prepareForInvocation($this->cliRequest);
        $this->command->validateRequest();
        $this->command->invoke($this->getParamsForInvocation());
    }

    /**
     * Preparing parameters and flags for command invocation
     *
     * @return array
     */
    private function getParamsForInvocation(): array
    {
        $params = $this->cliRequest->getParams();

        if ($this->command->useParams() ) {
            $params = array(new Params($params));
        }

        if ($this->command->useFlags() ) {
            $params[] = $this->cliRequest->getFlags()->getFlagsObject();
        }

        if ($this->command->useEnv() ) {
            $params[] = $this->command->getEnv();
        }

        return $params;
    }
}
