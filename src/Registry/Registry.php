<?php


namespace Cli\Registry;

use Cli\Basic\Singleton;
use Cli\Collection\{Collection, StringCollection};
use Cli\Exception\RegistryException;

/**
 * Class Value
 * @package Cli/Registry
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
abstract class Registry extends Singleton
{
    /**
     * @var Collection;
     */
    private $collection;

    /**
     * @var array;
     */
    private $allowedKeys = [];

    /**
     * @var array
     */
    private $usedKeys = [];

    protected static $instance;

    protected function init()
    {
        $this->setCollection(new StringCollection() );
        $this->setAllowedKeys();
    }

    protected function setCollection(Collection $collection)
    {
        $this->collection = $collection;
    }

    private function setAllowedKeys()
    {
        $this->allowedKeys = $this->getAllowedKeys();
    }

    protected function getAllowedKeys(): array
    {
        return [];
    }

    protected abstract function validateAllowedKeys(): bool;

    final protected function setValue(string $key, $value)
    {
        self::ensure(!in_array($key, $this->usedKeys), "\"$key\" was already set");
        $this->usedKeys[] = $key;

        if ($this->validateAllowedKeys() ) {
            self::ensure(in_array($key, $this->allowedKeys), "\"$key\" key is not allowed");
        }

        $this->collection->$key = $value;
    }

    final protected function getValue(string $key)
    {
        return $this->collection->$key;
    }

    public function __set(string $key, $value)
    {
        self::getInstance()->setValue($key, $value);
    }

    public function __get(string $key)
    {
        return self::getInstance()->getValue($key);
    }

    public function load(array $array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    public function isSet(string $key): bool
    {
        if ($this->$key) {
            return true;
        }
        return false;
    }

    protected static function ensure(bool $expr, string $message)
    {
        if (!$expr) {
            throw new RegistryException($message);
        }
    }
}
