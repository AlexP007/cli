<?php


namespace Cli\Basic;

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

    /**
     * Flags constructor.
     * @param array $flags
     */
    public function __construct(array $flags)
    {
        $this->flags = $flags;
    }

    /**
     * Return json string
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->getArray(), JSON_PRETTY_PRINT);
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->flags;
    }

    /**
     * @param string $flag
     * @return mixed
     */
    public function getFlag(string $flag)
    {
        return $this->flags[$flag];
    }
}
