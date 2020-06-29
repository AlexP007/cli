<?php


namespace Cli\Domain;

use Cli\Basic\Flags;
use Cli\Basic\Params;
use Cli\Basic\Environment;
use Cli\Reflections\CommandReflection;

/**
 * Class Value
 * @package Cli/Domain
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Command extends Domain
{
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
     * @var Environment
     */
    private $environment;

    /**
     * @var CommandReflection
     */
    private $commandReflection;

    /**
     * @var CliRequest
     */
    private $cliRequest;

    /**
     * @var int
     */
    private $simpleParams = 0;

    /**
     * @var bool
     */
    private $useParams = false;

    /**
     * @var int
     */
    private $paramsPosition;

    /**
     * @var bool
     */
    private $useFlags = false;

    /**
     * @var int
     */
    private $flagsPosition;

    /**
     * @var bool
     */
    private $useEnv = false;

    /**
     * @var int
     */
    private $envPosition;

    /**
     * Command constructor.
     *
     * @param string $name
     * @param callable $callable
     * @param array $flags
     */
    public function __construct(string $name, callable $callable, array $flags, array $env)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->flags = $flags;
        $this->environment = new Environment($env);

        if (count($flags) > 0) {
            $this->useFlags = true;
        }

        if (count($env) > 0) {
            $this->useEnv = true;
        }

        $this->validate();
    }

    protected function validate()
    {
        self::ensureArgument(
            strlen($this->name) !== 0,
            "Command name should't be empty"
        );
    }

    public function prepareForInvocation(CliRequest $cliRequest)
    {
        $this->commandReflection = new CommandReflection($this);
        $this->cliRequest = $cliRequest;
        $this->scanParameters();
    }

    private function scanParameters()
    {
        $params = $this->commandReflection->getParameters();

        foreach ($params as $param) {
            $class = $param->getClass();

            // if use params, then no validation
            if ($class && $class->getName() === Params::class) {
                $this->useParams = true;
                $this->paramsPosition = $param->getPosition();
                continue;
            }

            // if with flags, we are not count this argument
            if ($class && $class->getName() === Flags::class) {
                $this->flagsPosition = $param->getPosition();
                continue;
            }

            // if with env, we are not count last this argument
            if ($class && $class->getName() === Environment::class) {
                $this->envPosition = $param->getPosition();
                continue;
            }

            $param->isDefaultValueAvailable() or ++$this->simpleParams;
        }
    }

    /**
     * Validate flags and parameters in CliRequest
     *
     * @throws \Cli\Exception\ArgumentException
     */
    public function validateRequest()
    {
        $this->validateAllowedFlags();
        $this->validateIncomingParameters();
    }

    /**
     * @throws \Cli\Exception\ArgumentException
     */
    private function validateAllowedFlags()
    {
        $flags = array_keys($this->cliRequest->getFlags()->asArray() );
        $diff = array_diff($flags, $this->getFlags() );

        $commandName = $this->getName();

        self::ensureArgument(
            count($diff) < 1,
            '[' .join(', ', $diff) . "] are not allowed flags for command {{$commandName}}"
        );
    }

    /**
     * Validate that number of incoming parameters
     * is equal to number of command parameters
     *
     * @throws \Cli\Exception\ArgumentException
     */
    private function validateIncomingParameters()
    {

        if ($this->useParams()) {
            return;
        }

        $paramsCount = count($this->cliRequest->getParams() );
        $commandName = $this->getName();

        self::ensureArgument(
            $paramsCount === $this->simpleParams,
            "{{$commandName}} expected {$this->simpleParams} params got: $paramsCount"
        );
    }

    public function invoke(array $params)
    {
        return $this->commandReflection->invoke($params);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }

    /**
     * @return array
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    /**
     * @return array
     */
    public function getEnv(): Environment
    {
        return $this->environment;
    }

    /**
     * @return bool
     */
    public function useFlags(): bool
    {
        return $this->useFlags;
    }

    /**
     * @return bool
     */
    public function useParams(): bool
    {
        return $this->useParams;
    }

    /**
     * @return bool
     */
    public function useEnv(): bool
    {
        return $this->useEnv;
    }
}
