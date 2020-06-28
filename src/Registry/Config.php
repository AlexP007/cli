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
    /**
     * @return array
     */
    protected function getAllowedKeys(): array
    {
        return [
            'script_file_name',
            'list',
        ];
    }

    /**
     * @return bool
     */
    protected function validateAllowedKeys(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getScriptName(): string
    {
        return $this->script_file_name;
    }
}
