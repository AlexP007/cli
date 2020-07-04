<?php


namespace Cli\Collection;

use Cli\Traits\ArgumentThrower;

/**
 * Class Collection
 * @package Cli/Collection
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
abstract class Collection
{
    use ArgumentThrower;

    /**
     * @var array
     */
    protected $collection = [];

    /**
     * @var array
     */
    private $keys = [];

    /**
     * @var int
     */
    public $length = 0;

    /**
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value)
    {
        $this->keys[] = $name;
        $this->collection[$name] = $value;

        ++$this->length;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->collection[$name];
    }

    /**
     * @param array $values
     * @throws \Cli\Exception\ArgumentException
     */
    public function loadArray(array $values)
    {
        foreach ($values as $key => $value) {
            self::ensureArgument(is_string($key), 'collection keys can only be strings');
            $this->$key = $value;
        }
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        $result = [];

        foreach ($this->getIterator() as $key => $value)     {
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @return iterable
     */
    protected function getIterator(): iterable
    {
        for ($i = 0; $i < $this->length; $i++){
            $key = $this->keys[$i];
            $value = $this->$key;

            yield $key => $value;
        }
    }
}
