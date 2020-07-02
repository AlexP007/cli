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
    const VALUE_ON = "on";

    /**
     * @return array
     */
    protected function getAllowedKeys(): array
    {
        return [
            'script_file_name',
            'enable_list',
            'enable_exceptions',
        ];
    }

    /**
     * @return bool
     */
    protected function validateAllowedKeys(): bool
    {
        return true;
    }

    public function getScriptName(): string
    {
        return $this->script_file_name;
    }

    public function isEnableList(): string
    {
        return $this->enable_list === self::VALUE_ON;
    }

    public function isEnableExceptions(): string
    {
        return $this->enable_exceptions === self::VALUE_ON;
    }
}
