<?php


namespace Cli\Basic;

use Exception;

use Cli\Domain\Command;
use Cli\Domain\CliRequest;
use Cli\Exception\ArgumentException;
use Cli\Exception\CommandException;
use Cli\Exception\InterfaceException;
use Cli\Exception\RegistryException;
use Cli\Registry\Config;
use Cli\Registry\HandlerRegistry;
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
     * Initializing config and handler registry
     *
     * @param array $config
     */
    public final static function initialize(array $config)
    {
        try {
            // checking the correct SAPI interface
            if (PHP_SAPI != self::CLI_SAPI_NAME) {
                throw new InterfaceException("use this interface only in cli mode");
            }

            // disable Notice
            error_reporting(E_ERROR | E_WARNING | E_PARSE);

            $instance = self::getInstance();

            $instance->setConfig($config);
            $instance->setHandleRegistry();

            // setting basic list command
            if (
                $instance->config->isKeySet('list')
                && $instance->config->list === 'Y'
            ) {
                $instance->setListCommand();
            }
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

            $instance->printOut($commandExecutor->run() );
        } catch (Exception $e) {
            self::getInstance()->redOutput($e->getMessage() );
            die();
        }
    }

    /**
     * Handle command
     *
     * Saving command with all parameters
     * To handler registry
     *
     * @param string $command
     * @param callable $callback
     * @param array $flags
     * @param array $env
     */
    public final static function handle(string $command, callable $callback, array $flags = [], array $env = [])
    {
        try {
            $newCommand = new Command($command, $callback, $flags, $env);
            self::getInstance()->handlers->$command = $newCommand;
        } catch (ArgumentException $e) {
           self::getInstance()->redOutput($e->getMessage() . "in Cli::handle command $command");
           die();
        }
    }

    /**
     * Setting config
     *
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
     * Set basic list command
     */
    private function setListCommand()
    {
        self::handle(
            'list',
            ['Cli\Command\ListCommand', 'run'],
            [],
            ['handlers' => $this->handlers]
        );
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
        if (!$this->handlers->isKeySet($this->cliRequest->getCommandName() ) ) {
            throw new CommandException("not allowed command {$this->cliRequest->getCommandName()}");
        }
    }

    /**
     * Print output in red color
     *
     * @param string $string
     */
    private function redOutput(string $string)
    {
        $this->printOut((new Formatter($string) )->red() );
    }

    /**
     * Printing output with line break
     *
     * @param $string
     */
    private function printOut($string)
    {
        echo $string . "\n";
    }
}
