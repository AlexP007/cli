<?php


namespace Cli\Collection;

use Cli\Basic\Flags;
use Cli\Domain\Flag;

/**
 * Class Value
 * @package Cli/Collection
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class FlagCollection extends Collection
{
    public function __set(string $name, $flag)
    {
        self::ensureArgument(is_a($flag, Flag::class),"attribute $name can only be flag");
        parent::__set($name, $flag);
    }

    public function loadArray(array $flags)
    {
        foreach ($flags as $value) {
            $flag = new Flag($value);
            $key = $flag->getFlag();

            self::ensureArgument(is_string($key), 'FlagCollection keys can only be strings');

            $this->$key = $flag;
        }
    }

    public function asArray(): array
    {
        $result = [];

        foreach ($this->getIterator() as $key => $flag)     {
            $result[$key] = $flag->getValue();
        }

        return $result;
    }

    public function getFlagsObject(): Flags
    {
        return new Flags($this->asArray() );
    }
}
