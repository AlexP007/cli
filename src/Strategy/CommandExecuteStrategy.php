<?php


namespace Cli\Strategy;

use Cli\Basic\Flags;
use Cli\Basic\Params;
use ReflectionFunction;

use Cli\Domain\{Command, CliRequest};
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
     * @var ReflectionFunction
     */
    private $commandReflection;

    /**
     * @var
     */
    private $commandParametersReflection;

    public function __construct(Command $command, CliRequest $cliRequest)
    {
        $this->command = $command;
        $this->cliRequest = $cliRequest;
    }

    public function run()
    {
        $this->initReflections();
        $this->validate();

        $params = $this->getParamsForInvocation();

        return $this->commandReflection->invokeArgs($params);
    }

    private function initReflections()
    {
        $this->setCommandReflection();
        $this->setCommandParametersReflection();
    }

    private function validate()
    {
        $this->validateAllowedFlags();
        $this->validateIncomingParameters();
    }

    private function getParamsForInvocation(): array
    {
        $params = $this->cliRequest->getParams();

        if ($this->command->useParams() ) {
            $params = array(new Params($params));
        }

        if ($this->command->useFlags() ) {
            $params[] = $this->cliRequest->getFlags()->getFlagsObject();
        }

        return $params;
    }

    private function setCommandReflection()
    {
        $this->commandReflection = new ReflectionFunction($this->command->getCallable() );
    }

    private function setCommandParametersReflection()
    {
        $this->commandParametersReflection = $this->commandReflection->getParameters();
    }

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
            // if with flags, we are not count last argument
            if ($class && $class->getName() === Flags::class) {
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
}
