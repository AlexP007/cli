<?php


namespace Cli\Basic;

use Exception;

use Cli\Collection\CommandCollection;
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

    public final static function initialize(array $config)
    {
        try {
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

    private function setConfig(array $config)
    {
        $this->config = Config::getInstance();
        try {
            $this->config->load($config);
        } catch (RegistryException $e) {
            throw new Exception($e->getMessage() . ' in Cli::initialize configuration');
        }
    }

    private function setHandleRegistry()
    {
        $this->handlers = HandlerRegistry::getInstance();
    }

    private function setRequest()
    {
        $this->cliRequest = new CliRequest($GLOBALS['argv'], self::getInstance()->config);
    }

    private function validateAllowedCommands()
    {
        if (!$this->handlers->isSet($this->cliRequest->getCommandName() ) ) {
            throw new CommandException("not allowed command {$this->cliRequest->getCommandName()}");
        }
    }

    private function redOutput(string $string)
    {
        $this->print((new Formatter($string) )->red() );
    }

    private function print($string)
    {
        echo $string;
    }
}
