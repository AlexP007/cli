<?php


namespace Cli\Command;

use Cli\Basic\Environment;
use Cli\Basic\Formatter;

/**
 * Class Value
 * @package Cli/Command
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class ListCommand
{
    public static function run(Environment $env)
    {
        $handlers = $env->getEnv('handlers');

        var_dump($handlers);
    }
}