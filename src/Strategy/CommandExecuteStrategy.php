<?php


namespace Strategy;

use Exception\ArgumentException;
use ReflectionFunction;
use Collection\CallbackCollection;
use Request\ParamsRequest;
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
     * @var CallbackCollection
     */
    private $handlers;

    /**
     * @var ParamsRequest
     */
    private $params;

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

    public function __construct(CallbackCollection $handlers, ParamsRequest $params)
    {
        $this->handlers = $handlers;
        $this->params = $params;
    }

    public function run()
    {
        $commandName = $this->params->getCommand();
        $this->commandName = $commandName;

        $command = $this->handlers->$commandName;

        $this->setCommandReflection($command);
        $this->setCommandParameters();
        $this->checkIncomingParameters();

        return $this->commandReflection->invokeArgs($this->params->getParams() );
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

        self::ensureArgument(
            $paramsCount === $paramsWithoutDefaultValues,
            "{$this->commandName} expected $paramsWithoutDefaultValues params got: $paramsCount"
        );
    }
}