<?php


namespace Cli\Basic;

use Exception;

use Cli\Domain\{Command, CliRequest};
use Cli\Exception\{ArgumentException, CommandException, InterfaceException, RegistryException};
use Cli\Registry\{Config, HandlerRegistry};
use Cli\Strategy\CommandExecuteStrategy;

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

    /**
     * @var Cli
     */
    protected static $instance;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var HandlerRegistry
     */
    private $handlers;

    /**
     * @var CliRequest;
     */
    private $cliRequest;

    /**
     * @param array $config
     *
     * Initializing config and handler registry
     */
    public final static function initialize(array $config)
    {
        try {
            // checking the correct SAPI interface
            if (PHP_SAPI != self::CLI_SAPI_NAME) {
                throw new InterfaceException("use this interface only in cli mode");
            }

            $instance = self::getInstance();

            $instance->setConfig($config);
            $instance->setHandleRegistry();
        } catch (Exception $e) {
            self::getInstance()->redOutput($e->getMessage() );
            die();
        }
    }

    /**
     * Main method that run application
     * Setting request, validating allowed command
     * Execute command and print output
     */
    public final static function run()
    {
        try {
            $instance = self::getInstance();

            $instance->setRequest();

            $instance->validateAllowedCommands();
            $command = $instance->cliRequest->getCommandName();

            $commandExecutor = new CommandExecuteStrategy(
                $instance->handlers->$command,
                $instance->cliRequest
            );

            $instance->print($commandExecutor->run() );
        } catch (Exception $e) {
            self::getInstance()->redOutput($e->getMessage() );
            die();
        }
    }

    /**
     * @param string $command
     * @param callable $callback
     * @param array $flags
     *
     * Handle command
     */
    public final static function handle(string $command, callable $callback, array $flags = [])
    {
        try {
            $newCommand = new Command($command, $callback, $flags);
            self::getInstance()->handlers->$command = $newCommand;
        } catch (ArgumentException $e) {
           self::getInstance()->redOutput($e->getMessage() . "in Cli::handle command $command");
           die();
        }
    }

    /**
     * @param array $config
     * @throws Exception
     */
    private function setConfig(array $config)
    {
        $this->config = Config::getInstance();
        try {
            $this->config->load($config);
        } catch (RegistryException $e) {
            throw new Exception($e->getMessage() . ' in Cli::initialize configuration');
        }
    }

    /**
     * Setting Handle Registry
     */
    private function setHandleRegistry()
    {
        $this->handlers = HandlerRegistry::getInstance();
    }

    /**
     * Set request
     */
    private function setRequest()
    {
        $this->cliRequest = new CliRequest($GLOBALS['argv'], self::getInstance()->config);
    }

    /**
     * @throws CommandException
     */
    private function validateAllowedCommands()
    {
        if (!$this->handlers->isSet($this->cliRequest->getCommandName() ) ) {
            throw new CommandException("not allowed command {$this->cliRequest->getCommandName()}");
        }
    }

    /**
     * @param string $string
     *
     * Echo output in red color
     */
    private function redOutput(string $string)
    {
        $this->print((new Formatter($string) )->red() );
    }

    /**
     * @param $string
     *
     * Printing output
     */
    private function print($string)
    {
        echo $string;
    }
}
