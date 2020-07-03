<?php


namespace Cli\Basic;

use Cli\Strategy\CliInitializeStrategy;
use Cli\Strategy\CliRunStrategy;
use Exception;

use Cli\Domain\Command;
use Cli\Domain\CliRequest;
use Cli\Exception\ArgumentException;
use Cli\Exception\CommandException;
use Cli\Exception\InterfaceException;
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
     * Initializing config and handler registry
     *
     * @param array $config
     */
    public final static function initialize(array $config)
    {
        $instance = self::getInstance();
        try {
            // checking the correct SAPI interface
            if (PHP_SAPI != self::CLI_SAPI_NAME) {
                die("use this interface only in cli mode");
            }

            // disable Notice
            error_reporting(E_ERROR | E_WARNING | E_PARSE);

            // initialize strategy
            $initializeStrategy = new CliInitializeStrategy($instance, $config);
            $initializeStrategy->run();

        } catch (Exception $e) {
            $instance->config->isEnableExceptions() and $instance->redOutput($e->getMessage());
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
        $instance = self::getInstance();
        try {
            $cliRequest = new CliRequest($GLOBALS['argv'], $instance->config);
            $handlers = $instance->handlers;
            // run strategy
            $runStrategy = new CliRunStrategy($instance, $cliRequest, $handlers);
            $instance->printOut($runStrategy->run());
        } catch (Exception $e) {
            $instance->config->isEnableExceptions() and $instance->redOutput($e->getMessage() );
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
        $instance = self::getInstance();
        try {
            $newCommand = new Command($command, $callback, $flags, new Environment($env));
            $instance->handlers->$command = $newCommand;
        } catch (ArgumentException $e) {
            $msg = $e->getMessage() . " in Cli::handle command: {{$command}}";
            $instance->config->isEnableExceptions() and $instance->redOutput($msg);
           die();
        }
    }

    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Setting Handle Registry
     */
    public function setHandlers(HandlerRegistry $handlers)
    {
        $this->handlers = $handlers;
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
