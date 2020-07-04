<?php


namespace Cli\Domain;


use Cli\Traits\ArgumentThrower;

/**
 * Class Domain
 * @package Cli/Domain
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
abstract class Domain
{
    use ArgumentThrower;

    abstract protected function validate();
}