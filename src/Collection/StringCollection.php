<?php


namespace Collection;

/**
 * Class Value
 * @package Cli/Collection
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class StringCollection extends Collection
{
    public function __set(string $name, $value)
    {
        self::ensureArgument(is_string($value),"attribute $name can only be string");
        parent::__set($name, $value);
    }
}