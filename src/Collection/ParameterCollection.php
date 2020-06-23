<?php


namespace Collection;

use Domain\Command;

/**
 * Class Value
 * @package Cli/Collection
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class ParameterCollection extends Collection
{
    public function __set(string $name, $command)
    {
        self::ensureArgument(is_a($command, Command::class),"attribute $name can only be command");
        parent::__set($name, $command);
    }
}
