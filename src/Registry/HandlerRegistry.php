<?php


namespace Cli\Registry;

use Cli\Collection\CommandCollection;

/**
 * Class Value
 * @package Cli/Registry
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class HandlerRegistry extends Registry
{
    protected function init()
    {
        $this->setCollection(new CommandCollection() );
    }

    protected function validateAllowedKeys(): bool
    {
        return false;
    }
}