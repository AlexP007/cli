<?php


namespace Request;

use Exception\ArgumentException;

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
    const SCRIPT_NAME = 'cli.php';

    private $params = [];

    public final function __construct(array $args)
    {
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
        if ($value != self::SCRIPT_NAME) {
            $ex = new ArgumentException("not valid arguments");
            throw new $ex;
        }
    }
}