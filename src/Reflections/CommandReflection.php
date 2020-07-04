<?php


namespace Cli\Reflections;

use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;

use Cli\Domain\Command;

/**
 * Class CommandReflection
 * @package Cli/Reflections
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class CommandReflection
{
    /**
     * @var Command
     */
    private $command;

    private $reflection;

    private $parameters;

    public function __construct(Command $command)
    {
        $this->command = $command;
        $this->setReflections();
    }

    private function setReflections()
    {
        $this->setCommandReflection();
        $this->setCommandParametersReflection();
    }

    private function setCommandReflection()
    {
        $callable = $this->command->getCallable();
        if (is_array($callable) ) {
            $this->reflection = new ReflectionMethod($callable[0], $callable[1]);
        } else {
            $this->reflection = new ReflectionFunction($callable);
        }
    }

    private function setCommandParametersReflection()
    {
        $this->parameters = $this->reflection->getParameters();
    }

    public function invoke(array $params)
    {
        $callable = $this->command->getCallable();
        if (is_array($callable) ) {
            return $this->reflection->invokeArgs(null, $params);
        }
        return $this->reflection->invokeArgs($params);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
