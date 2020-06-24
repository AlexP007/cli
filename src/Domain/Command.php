<?php


namespace Cli\Domain;

use Cli\Basic\Environment;

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
     * @var bool
     */
    private $useParams = false;

    /**
     * @var bool
     */
    private $useFlags = false;

    /**
     * @var bool
     */
    private $useEnv = false;

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
    }

    /**
     * @param bool $val
     */
    public function setUseParams(bool $val)
    {
        $this->useParams = $val;
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
