<?php


namespace Cli\Basic;

use Exception;

/**
 * Class Value
 * @package Cli/Basic
 * @license MIT
 *
 * @author AlexP007kK
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
abstract class Singleton
{
    /**
     * Singleton constructor.
     */
    private final function __construct()
    {
        $this->init();
    }

    /**
     * @return Singleton
     */
    protected static function getInstance(): Singleton
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @throws Exception
     */
    final public function __clone() {
        throw new Exception('not allowed to clone singleton');
    }

    /**
     * @throws Exception
     */
    final public function __wakeup() {
        throw new Exception('not allowed to wakeup singleton');
    }

    /**
     * This method invoked in constructor
     * And could be overwritten
     */
    protected function init()
    {
    }
}
