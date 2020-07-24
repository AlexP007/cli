<?php


namespace Cli\Basic;

/**
 * Class Environment
 * @package Cli/Basic
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Environment
{
    /**
     * @var array
     */
    private $env;

    /**
     * Environment constructor.
     * @param array $env
     */
    public function __construct(array $env)
    {
        $this->env = $env;
    }

    /**
     * Return json string
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->getArray(), JSON_PRETTY_PRINT);
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->env;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getEnv(string $key)
    {
        return $this->env[$key];
    }

    public function merge(Environment $environment)
    {
        $oldVariables = $this->getArray();
        $newVariables = $environment->getArray();
        $this->env = array_merge($oldVariables, $newVariables);
    }
}