<?php


namespace Cli\Command;

use Cli\Basic\Environment;

/**
 * Class Value
 * @package Cli/Command
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
abstract class Command
{
    abstract public static function run(Environment $env);
}