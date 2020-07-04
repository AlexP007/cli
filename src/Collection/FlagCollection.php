<?php


namespace Cli\Collection;

use Cli\Basic\Flags;
use Cli\Domain\Flag;

/**
 * Class FlagCollection
 * @package Cli/Collection
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class FlagCollection extends Collection
{
    /**
     * @param string $name
     * @param $flag
     * @throws \Cli\Exception\ArgumentException
     */
    public function __set(string $name, $flag)
    {
        self::ensureArgument(is_a($flag, Flag::class),"attribute $name can only be flag");
        parent::__set($name, $flag);
    }

    /**
     * @param array $flags
     * @throws \Cli\Exception\ArgumentException
     */
    public function loadArray(array $flags)
    {
        foreach ($flags as $value) {
            $flag = new Flag($value);
            $key = $flag->getFlag();

            self::ensureArgument(is_string($key), 'FlagCollection keys can only be strings');

            $this->$key = $flag;
        }
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        $result = [];

        foreach ($this->getIterator() as $key => $flag)     {
            $result[$key] = $flag->getValue();
        }

        return $result;
    }

    /**
     * @return Flags
     */
    public function getFlagsObject(): Flags
    {
        return new Flags($this->asArray() );
    }
}
