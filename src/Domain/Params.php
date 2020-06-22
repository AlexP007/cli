<?php


namespace Domain;

use Exception\ArgumentException;
use Registry\Config;

/**
 * Class Value
 * @package Cli/Domain
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Params
{
    /**
     * @var array
     */
    private $params = [];

    public final function __construct(array $args)
    {
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
}
