<?php


namespace Traits;

use Exception\ArgumentException;

/**
 * Class Value
 * @package Trait/Traits
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
trait Thrower
{
    protected static function ensureArgument(bool $expr, string $message)
    {
        if (!$expr) {
            throw new ArgumentException($message);
        }
    }
}