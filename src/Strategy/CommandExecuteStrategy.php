<?php


namespace Strategy;

use Domain\Command;
use ReflectionFunction;
use Request\{ParamsRequest, Flags};
use Traits\Thrower;

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

    use Thrower;

    /**
     * @var Command
     */
    private $command;

    /**
     * @var ParamsRequest
     */
    private $params;

    /**
     * @var Flags
     */
    private $flags;

        /**
     * @var string
     */
    private $commandName;

    /**
     * @var ReflectionFunction
     */
    private $commandReflection;

    /**
     * @var
     */
    private $commandParameters;

    public function __construct(Command $command, ParamsRequest $params)
    {
        $this->command = $command;
        $this->params = $params;
        $this->commandName = $command->getName();
        $this->flags = new Flags($this->params->getFlags() );
    }

    public function run()
    {
        $this->setCommandReflection($this->command->getCallable() );
        $this->setCommandParameters();
        $this->checkIncomingParameters();
        $this->checkFlags();

        $params = $this->params->getParams();

        if ($this->command->useFlags() ) {
            $params[] = $this->flags->getFlagsAsArray();
        }

        return $this->commandReflection->invokeArgs($params);
    }

    private function setCommandReflection(callable $command)
    {
        $this->commandReflection = new ReflectionFunction($command);
    }

    private function setCommandParameters()
    {
        $this->commandParameters = $this->commandReflection->getParameters();
    }

    private function checkIncomingParameters()
    {
        $paramsWithoutDefaultValues = 0;

        foreach ($this->commandParameters as $param) {
            $param->isDefaultValueAvailable() or ++$paramsWithoutDefaultValues;
        }

        $paramsCount = count($this->params->getParams() );

        // if with flags, we are not count last argument
        $this->command->useFlags() and  --$paramsWithoutDefaultValues;

        self::ensureArgument(
            $paramsCount === $paramsWithoutDefaultValues,
            "{{$this->commandName}} expected $paramsWithoutDefaultValues params got: $paramsCount"
        );
    }

    private function checkFlags()
    {
        $flags = array_keys($this->flags->getFlagsAsArray() );
        $diff = array_diff($flags, $this->command->getFlags() );

        self::ensureArgument(
            count($diff) < 1,
            '[' .join(', ', $diff) . "] are not allowed flags for command {{$this->commandName}}"
        );
    }
}