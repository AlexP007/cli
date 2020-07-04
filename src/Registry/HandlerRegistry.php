<?php


namespace Cli\Registry;

use Cli\Collection\CommandCollection;

/**
 * Class HandlerRegistry
 * @package Cli/Registry
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class HandlerRegistry extends Registry
{
    /**
     * Initialize this Registry with CommandCollection
     */
    protected function init()
    {
        $this->setCollection(new CommandCollection() );
    }

    /**
     * @return bool
     */
    protected function validateAllowedKeys(): bool
    {
        return false;
    }
}
