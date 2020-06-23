<?php


namespace Cli\Traits;

use Cli\Exception\ArgumentException;

/**
 * Class Value
 * @package Trait/Traits
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
trait ArgumentThrower
{
    /**
     * @param bool $expr
     * @param string $message
     * @throws ArgumentException
     */
    protected static function ensureArgument(bool $expr, string $message)
    {
        if (!$expr) {
            throw new ArgumentException($message);
        }
    }
}
