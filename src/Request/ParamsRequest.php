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

    public final function __construct(array $args, Config $config)
    {
        $this->config = $config;

        $firstArg = array_shift($args);
        $this->checkFirstArgsKeyValue($firstArg);

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

    private function checkFirstArgsKeyValue(string $value)
    {
        if ($value != $this->config->getScriptName() ) {
            throw new ArgumentException("invalid input arguments");
        }
    }
}