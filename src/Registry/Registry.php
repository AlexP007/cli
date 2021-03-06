<?php


namespace Cli\Registry;

use Cli\Basic\Singleton;
use Cli\Collection\Collection;
use Cli\Collection\StringCollection;
use Cli\Exception\RegistryException;

/**
 * Class Registry
 * @package Cli/Registry
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
abstract class Registry
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

    public final function __construct()
    {
        $this->init();
    }

    /**
     * Could be overwritten for initializing reason
     */
    protected function init()
    {
        $this->setCollection(new StringCollection() );
        $this->setAllowedKeys();
    }

    /**
     * @param Collection $collection
     */
    protected function setCollection(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Setting allowed keys
     */
    private function setAllowedKeys()
    {
        $this->allowedKeys = $this->getAllowedKeys();
    }

    /**
     * @return array
     */
    protected function getAllowedKeys(): array
    {
        return [];
    }

    /**
     * Need or not to validate uniq keys for this Registry
     *
     * @return bool
     */
    protected abstract function validateAllowedKeys(): bool;

    /**
     * @param string $key
     * @param $value
     * @throws RegistryException
     */
    final protected function setValue(string $key, $value)
    {
        self::ensure(!in_array($key, $this->usedKeys), "\"$key\" was already set");
        $this->usedKeys[] = $key;

        if ($this->validateAllowedKeys() ) {
            self::ensure(in_array($key, $this->allowedKeys), "\"$key\" key is not allowed");
        }

        $this->collection->$key = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    final protected function getValue(string $key)
    {
        return $this->collection->$key;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function __set(string $key, $value)
    {
        $this->setValue($key, $value);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getValue($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isSet(string $key)
    {
        return !is_null($this->$key);
    }

    /**
     * @param array $array
     */
    public function load(array $array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @param bool $expr
     * @param string $message
     * @throws RegistryException
     */
    protected static function ensure(bool $expr, string $message)
    {
        if (!$expr) {
            throw new RegistryException($message);
        }
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return $this->collection->asArray();
    }
}
