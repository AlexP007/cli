<?php


namespace Cli\Basic;

/**
 * Class Value
 * @package Cli/Basic
 * @license MIT
 *
 * @author AlexP007kK
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Params
{
    /**
     * @var array
     */
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function __toString(): string
    {
        return json_encode($this->getArray());
    }

    public function getArray(): array
    {
        return $this->params;
    }

    public function getParam(int $n): string
    {
        return $this->params[$n];
    }
}