<?php


namespace Request;

use Domain\{Params, Flags};
use Registry\Config;
use Traits\Thrower;

/**
 * Class Value
 * @package Cli/Request
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class CliRequest
{
    use Thrower;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $command;

    /**
     * @var Params
     */
    private $params;

    /**
     * @var $flags;
     */
    private $flags;

    public final function __construct(array $args, Config $config)
    {
        $this->config = $config;

        $firstArg = array_shift($args);
        $this->validateFirstArgsKeyValue($firstArg);

        $commandName = array_shift($args);
        $this->setCommandName($commandName);

        $flags = $this->collectFlags($args);
        $this->cleanArgsFromFlags($args);

        $this->setParams($args);
        $this->setFlags($flags);
    }

    private function validateFirstArgsKeyValue(string $value)
    {
        self::ensureArgument($value === $this->config->getScriptName(), 'invalid input arguments');
    }

    private function setCommandName(string $commandName)
    {
        $this->command = $commandName;
    }

    private function setParams(array $args)
    {
        $this->params = new Params($args);
    }

    private function setFlags(array $flags)
    {
        $this->flags = new Flags($flags);
    }

    public function getCommandName(): string
    {
        return $this->command;
    }

    public function getParams(): Params
    {
        return $this->params;
    }

    public function getFlags(): Flags
    {
        return $this->flags;
    }

    /**
     * @param array $args
     * @param int $pointer
     *
     * Recursive method
     * Collects flags with - or -- that are passed before arguments
     * Works before first non-flag value
     */
    private function collectFlags(array &$args, int $pointer = 0, array &$flags = []): array
    {
        if ($pointer + 1 < count($args) ) {
            if ($this->isFlag($args[$pointer]) ) {
                $flags[] = $args[$pointer];
                $this->collectFlags($args, $pointer + 1, $flags);
            }
        }

        return $flags;
    }

    /**
     * @param array $args
     *
     * Cleans all flags values before first non-flag value
     */
    private function cleanArgsFromFlags(array &$args)
    {
        foreach ($args as $key => $arg) {
            if ($this->isFlag($arg) ) {
                unset($args[$key]);
            } else {
                break;
            }
        }
    }

    private function isFlag(string $value)
    {
        return  preg_match('/^-{1,2}\w/', $value);
    }

}