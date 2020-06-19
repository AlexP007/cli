<?php


namespace Request;

use Exception\ArgumentException;
use Registry\Config;

/**
 * Class Value
 * @package Cli/Request
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class ParamsRequest extends Request
{
    /**
     * @var array
     */
    private $params = [];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $flags = [];

    public final function __construct(array $args, Config $config)
    {
        $this->config = $config;

        $firstArg = array_shift($args);
        $this->checkFirstArgsKeyValue($firstArg);

        $this->collectFlags($args);
        $this->cleanArgsFromFlags($args);
        $this->setParams($args);
    }

    private function setParams(array $args)
    {
        $this->params = $args;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getFlags()
    {
        return $this->flags;
    }

    private function checkFirstArgsKeyValue(string $value)
    {
        if ($value != $this->config->getScriptName() ) {
            throw new ArgumentException("invalid input arguments");
        }
    }

    /**
     * @param array $args
     * @param int $pointer
     *
     * Recursive method
     * Collects flags with - or -- that are passed before arguments
     * Works before first non-flag value
     */
    private function collectFlags(array &$args, int $pointer = 0)
    {
        if ($pointer + 1 < count($args) ) {
            if ($this->isFlag($args[$pointer]) ) {
                $this->flags[] = $args[$pointer];
                $this->collectFlags($args, $pointer + 1);
            }
        }
    }

    /**
     * @param array $args
     *
     * Cleans all flags values before first non-flag value
     */
    private function cleanArgsFromFlags(array &$args)
    {
        foreach ($args as $key => $arg) {
            if ($this->isFlag($arg) ) {
                unset($args[$key]);
            } else {
                break;
            }
        }
    }

    private function isFlag(string $value)
    {
        return  preg_match('/-{1,2}\w/', $value);
    }
}
