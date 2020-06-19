<?php


namespace Collection;

use Traits\Thrower;

/**
 * Class Value
 * @package Cli/Collection
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
abstract class Collection
{
    use Thrower;

    protected $collection = [];

    private $keys = [];

    public $length = 0;

    public function __set(string $name, $value)
    {
        $this->keys[] = $name;
        $this->collection[$name] = $value;

        ++$this->length;
    }

    public function __get(string $name)
    {
        return $this->collection[$name];
    }

    public function setArrayToCollection(array $values)
    {
        foreach ($values as $key => $value)
        {
            self::ensureArgument(is_string($key), 'collection keys can only be strings');
            $this->$key = $value;
        }
    }

    public function getIterator(): iterable
    {
        for ($i = 0; $i < $this->length; $i++){
            $key = $this->keys[$i];
            $value = $this->$key;

            yield $key => $value;
        }
    }
}