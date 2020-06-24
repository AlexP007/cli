<?php


namespace Cli\Strategy;

use ReflectionFunction;
use ReflectionMethod;

use Cli\Basic\Flags;
use Cli\Basic\Environment;
use Cli\Basic\Params;
use Cli\Domain\Command;
use Cli\Domain\CliRequest;
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
     * @var bool
     */
    private $invokeMethod = false;

    /**
     * @var CliRequest
     */
    private $cliRequest;

    /**
     * #var ReflectionFunction or ReflectionMethod
     */
    private $commandReflection;

    /**
     * @var
     */
    private $commandParametersReflection;

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

        $callable = $command->getCallable();
        if (is_array($callable) ) {
            $this->invokeMethod = true;
        }
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $this->setReflections();
        $this->validate();

        $params = $this->getParamsForInvocation();

        if ($this->invokeMethod) {
            return $this->commandReflection->invokeArgs(null, $params);
        }

        return $this->commandReflection->invokeArgs($params);
    }

    /**
     * Set reflections
     */
    private function setReflections()
    {
        $this->setCommandReflection();
        $this->setCommandParametersReflection();
    }

    /**
     * Validate flags and parameters
     */
    private function validate()
    {
        $this->validateAllowedFlags();
        $this->validateIncomingParameters();
    }

    /**
     * @throws \ReflectionException
     */
    private function setCommandReflection()
    {
        if ($this->invokeMethod) {
            $callable = $this->command->getCallable();
            $this->commandReflection = new ReflectionMethod($callable[0], $callable[1]);
        } else {
            $this->commandReflection = new ReflectionFunction($this->command->getCallable());
        }
    }

    /**
     * Set this commandParametersReflection
     */
    private function setCommandParametersReflection()
    {
        $this->commandParametersReflection = $this->commandReflection->getParameters();
    }

    /**
     * @throws \Cli\Exception\ArgumentException
     *
     * Validate that number of incoming parameters
     * is equal to number of command parameters
     */
    private function validateIncomingParameters()
    {
        $paramsWithoutDefaultValues = 0;

        foreach ($this->commandParametersReflection as $param) {
            $class = $param->getClass();

            // if use params, then no validation
            if ($class && $class->getName() === Params::class) {
                $this->command->setUseParams(true);
                return;
            }

            // if with flags, we are not count this argument
            if ($class && $class->getName() === Flags::class) {
                continue;
            }

            // if with env, we are not count last this argument
            if ($class && $class->getName() === Environment::class) {
                continue;
            }

            $param->isDefaultValueAvailable() or ++$paramsWithoutDefaultValues;
        }

        $paramsCount = count($this->cliRequest->getParams() );
        $commandName = $this->command->getName();

        self::ensureArgument(
            $paramsCount === $paramsWithoutDefaultValues,
            "{{$commandName}} expected $paramsWithoutDefaultValues params got: $paramsCount"
        );
    }

    /**
     * @throws \Cli\Exception\ArgumentException
     */
    private function validateAllowedFlags()
    {
        $flags = array_keys($this->cliRequest->getFlags()->asArray() );
        $diff = array_diff($flags, $this->command->getFlags() );

        $commandName = $this->command->getName();

        self::ensureArgument(
            count($diff) < 1,
            '[' .join(', ', $diff) . "] are not allowed flags for command {{$commandName}}"
        );
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
