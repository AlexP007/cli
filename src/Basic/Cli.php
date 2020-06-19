<?php


namespace Basic;

use Collection\CallbackCollection;
use Exception;
use Exception\{ArgumentException, CommandException, InterfaceException, RegistryException};
use Registry\Config;
use Request\ParamsRequest;
use Strategy\CommandExecuteStrategy;

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
     * @var Config
     */
    private $config;

    /**
     * @var ParamsRequest
     */
    private $params;

    /**
     * @var CallbackCollection
     */
    private $handlers;

    public final static function initialize(array $config)
    {
        try {
            if (PHP_SAPI != self::CLI_SAPI_NAME) {
                throw new InterfaceException("use this interface only in cli mode");
            }

            $instance = self::getInstance();

            $instance->setConfig($config);
            $instance->setParams();
            $instance->initHandlerCollection();
        } catch (Exception $e) {
            die($instance->redOut($e->getMessage() ) );
        }
    }

    public final static function run()
    {
        try {
            $instance = self::getInstance();

            $instance->checkCommand();
            $commandExecutor = new CommandExecuteStrategy(
                $instance->handlers,
                $instance->params
            );

            return $commandExecutor->run();
        } catch (Exception $e) {
            die($instance->redOut($e->getMessage() ) );
        }
    }

    public final static function handle(string $command, callable $callback)
    {
        try {
            self::getInstance()->handlers->$command = $callback;
        } catch (ArgumentException $e) {
            die(self::getInstance()->redOut(
                $e->getMessage() . "in Cli::handle command $command")
            );
        }
    }

    private function setConfig(array $config)
    {
        $this->config = Config::getInstance();
        try {
            $this->config->load($config);
        } catch (RegistryException $e) {
            throw new Exception($e->getMessage() . ' in Cli::initialize configuration');
        }
    }

    private function setParams()
    {
        $this->params = new ParamsRequest($GLOBALS['argv'], self::getInstance()->config);
    }

    private function initHandlerCollection()
    {
        $this->handlers = new CallbackCollection();
    }

    private function checkCommand()
    {
        $commands = [];

        foreach ($this->handlers->getIterator() as $commandName => $callable) {
            $commands[$commandName] = $callable;
        }

        if (!in_array($this->params->getCommand(), array_keys($commands) ) ) {
            throw new CommandException("not allowed command {$this->params->getCommand()}");
        }
    }

    private function redOut(string $string): string
    {
        return "\033[31m$string\033[0m";
    }
}
