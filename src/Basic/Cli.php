<?php


namespace Basic;

use Exception;
use Exception\{InterfaceException, RegistryException};
use Registry\Config;
use Request\ParamsRequest;

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

            $instance->setConfig($config);

            $paramsRequest = new ParamsRequest($GLOBALS['argv'], $instance->config);

            if (PHP_SAPI != self::CLI_SAPI_NAME) {
                throw new InterfaceException("use this interface only in cli mode");
            }

        } catch (Exception $e) {
            die($e->getMessage() );
        }
    }

    private function setConfig(array $config)
    {
        self::getInstance()->config = Config::getInstance();
        try {
            self::getInstance()->config->load($config);
        } catch (RegistryException $e) {
            throw new Exception($e->getMessage() . ' in Cli::initialize configuration');
        }
    }
}