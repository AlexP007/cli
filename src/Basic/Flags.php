<?php


namespace Basic;

/**
 * Class Value
 * @package Cli/Basic
 * @license MIT
 *
 * @author AlexP007kK
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Flags
{
    /**
     * @var array
     */
    private $flags;

    public function __construct(array $flags)
    {
        $this->flags = $flags;
    }

    public function getArray(): array
    {
        return $this->flags;
    }

    public function getFlag(string $flag)
    {
        return $this->flags[$flag];
    }
}
