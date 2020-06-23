<?php


namespace Cli\Strategy;

/**
 * Class Value
 * @package Cli/Strategy
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
abstract class Strategy
{
    /**
     * @return mixed
     *
     * Interface method of strategy
     */
    abstract public function run();
}