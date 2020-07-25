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
     * @var array
     */
    private $aliases = [];

    /**
     * Environment constructor.
     * @param array $env
     */
    public function __construct(array $env)
    {
        $this->env = $env;
        $this->interpolateAliases();
    }

    private function interpolateAliases()
    {
        $this->setAliases();
        $aliases = array_keys($this->aliases);
        $replacements = array_values($this->aliases);
        foreach ($this->env as &$value) {
            if (!is_string($value)) {
                continue;
            }
            $value = str_replace($aliases, $replacements, $value);
        }
    }

    private function setAliases()
    {
        foreach($this->env as $key => $value) {
            if (!is_string($value)) {
                continue;
            }
            $this->aliases["@$key"] = $value;
        }
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
        $this->env = array_merge($newVariables, $oldVariables);
    }
}