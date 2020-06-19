<?php


namespace Basic;

use Exception;
use Exception\InterfaceException;
use Registry\Config;

/**
 * Class Value
 * @package Cli/Basic
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Cli extends Singleton
{
    const CLI_SAPI_NAME = "cli";

    protected static $instance;

    /**
     * @var config
     */
    private $config;

    public static function initialize(array $config)
    {
        try {

            $instance = self::getInstance();
            $instance->config = Config::getInstance();

            if (PHP_SAPI != self::CLI_SAPI_NAME) {
                $ex = new InterfaceException("use this interface only in cli mode");
                throw new $ex;
            }

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}