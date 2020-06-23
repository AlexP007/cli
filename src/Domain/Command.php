<?php


namespace Domain;

use Basic\Flags;
use ReflectionFunction;
use Request\CliRequest;
use Traits\Thrower;

/**
 * Class Value
 * @package Cli/Domain
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Command
{
    use Thrower;

    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $callable;

    /**
     * @var array
     */
    private $flags;

    /**
     * @var bool
     */
    private $useFlags = false;

    /**
     * @var ReflectionFunction
     */
    private $commandReflection;

    /**
     * @var
     */
    private $commandParametersReflection;


    public function __construct(string $name, callable $callable, array $flags)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->flags = $flags;

        if (count($flags) > 0) {
            $this->useFlags = true;
        }
    }

    public function invoke(CliRequest $cliRequest)
    {
        $this->initReflections();
        $this->validate($cliRequest);

        $params = $this->getParamsForInvocation($cliRequest);

        return $this->commandReflection->invokeArgs($params);
    }

    private function initReflections()
    {
        $this->setCommandReflection();
        $this->setCommandParametersReflection();
    }

    private function validate(CliRequest $cliRequest)
    {
        $this->validateAllowedFlags($cliRequest);
        $this->validateIncomingParameters($cliRequest);
    }

    private function getParamsForInvocation(CliRequest $cliRequest): array
    {
        $params = $cliRequest->getParams()->getParamsAsArray();

        if ($this->useFlags() ) {
            $params[] = $cliRequest->getFlags()->getFlagsObject();
        }

        return $params;
    }

    private function setCommandReflection()
    {
        $this->commandReflection = new ReflectionFunction($this->callable);
    }

    private function setCommandParametersReflection()
    {
        $this->commandParametersReflection = $this->commandReflection->getParameters();
    }

    private function validateIncomingParameters(CliRequest $cliRequest)
    {
        $paramsWithoutDefaultValues = 0;

        foreach ($this->commandParametersReflection as $param) {
            $class = $param->getClass();
            // if with flags, we are not count last argument
            if ($class and $class->getName() === Flags::class) {
                continue;
            }
            $param->isDefaultValueAvailable() or ++$paramsWithoutDefaultValues;
        }

        $paramsCount = count($cliRequest->getParams()->getParamsAsArray() );

        self::ensureArgument(
            $paramsCount === $paramsWithoutDefaultValues,
            "{{$this->name}} expected $paramsWithoutDefaultValues params got: $paramsCount"
        );
    }

    private function validateAllowedFlags(CliRequest $cliRequest)
    {
        $flags = array_keys($cliRequest->getFlags()->asArray() );
        $diff = array_diff($flags, $this->getFlags() );

        self::ensureArgument(
            count($diff) < 1,
            '[' .join(', ', $diff) . "] are not allowed flags for command {{$this->name}}"
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }

    public function getFlags(): array
    {
        return $this->flags;
    }

    public function useFlags(): bool
    {
        return $this->useFlags;
    }
}
