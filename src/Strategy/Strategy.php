<?php


namespace Cli\Strategy;

/**
 * Class Strategy
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
     * Interface method of strategy
     *
     * @return mixed
     */
    abstract public function run();
}