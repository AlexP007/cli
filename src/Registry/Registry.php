<?php


namespace Registry;

use Basic\Singleton;
use Collection\StringCollection;
use Exception\RegistryException;

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
     * @var StringCollection;
     */
    private $registry;

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
        $this->registry = new StringCollection();

        $this->setAllowedKeys();
    }

    abstract protected function getAllowedKeys(): array;

    private function setAllowedKeys()
    {
        $this->allowedKeys = $this->getAllowedKeys();
    }

    final function setValue(string $key, $value)
    {
        self::ensure(!in_array($key, $this->usedKeys), "\"$key\" was already set");
        $this->usedKeys[] = $key;

        self::ensure(in_array($key, $this->allowedKeys), "\"$key\" key is not allowed");
        $this->registry->$key = $value;
    }

    final function getValue(string $key)
    {
        return $this->registry->$key;
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

    protected static function ensure(bool $expr, string $message)
    {
        if (!$expr) {
            throw new RegistryException($message);
        }
    }
}
