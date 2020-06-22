<?php


namespace Basic;

use Collection\CommandCollection;
use Domain\Command;
use Exception;
use Exception\{ArgumentException, CommandException, InterfaceException, RegistryException};
use Registry\Config;
use Domain\Params;
use Request\CliRequest;
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
     * @var CliRequest;
     */
    private $request;

    /**
     * @var CommandCollection
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
            $instance->initHandlerCollection();
        } catch (Exception $e) {
            die($instance->redOut($e->getMessage() ) );
        }
    }

    public final static function run()
    {
        try {
            $instance = self::getInstance();

            $instance->setRequest();

            $instance->checkCommand();
            $command = $instance->request->getCommandName();
            $commandExecutor = new CommandExecuteStrategy(
                $instance->handlers->$command,
                $instance->request
            );

            return $commandExecutor->run();
        } catch (Exception $e) {
            die($instance->redOut($e->getMessage() ) );
        }
    }

    public final static function handle(string $command, callable $callback, array $flags = [])
    {
        try {
            $newCommand = new Command($command, $callback, $flags);
            self::getInstance()->handlers->$command = $newCommand;
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

    private function setRequest()
    {
        $this->request = new CliRequest($GLOBALS['argv'], self::getInstance()->config);
    }

    private function initHandlerCollection()
    {
        $this->handlers = new CommandCollection();
    }

    private function checkCommand()
    {
        $commands = [];

        foreach ($this->handlers->getIterator() as $commandName => $callable) {
            $commands[$commandName] = $callable;
        }

        if (!in_array($this->request->getCommandName(), array_keys($commands) ) ) {
            throw new CommandException("not allowed command {$this->params->getCommandName()}");
        }
    }

    private function redOut(string $string): string
    {
        return "\033[31m$string\033[0m";
    }
}
