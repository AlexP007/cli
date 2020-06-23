<?php


namespace Cli\Registry;

/**
 * Class Value
 * @package Cli/Registry
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Config extends Registry
{
    protected static $instance;

    protected function getAllowedKeys(): array
    {
        return [
            'script_file_name'
        ];
    }

    protected function validateAllowedKeys(): bool
    {
        return true;
    }

    public function getScriptName()
    {
        return $this->script_file_name;
    }
}
