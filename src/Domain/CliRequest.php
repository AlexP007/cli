<?php


namespace Cli\Domain;

use Cli\Collection\FlagCollection;
use Cli\Registry\Config;
use Cli\Traits\ArgumentThrower;

/**
 * Class Value
 * @package Cli/Domain
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class CliRequest
{
    use ArgumentThrower;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $command;

    /**
     * @var array
     */
    private $params;

    /**
     * @var FlagCollection;
     */
    private $flags;

    /**
     * CliRequest constructor.
     * @param array $args
     * @param Config $config
     */
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

    /**
     * @param string $value
     * @throws \Cli\Exception\ArgumentException
     *
     * Validating first value in args (GLOBALS)
     * Should be the name of this script (configured when initialize)
     */
    private function validateFirstArgsKeyValue(string $value)
    {
        self::ensureArgument($value === $this->config->getScriptName(), 'invalid input arguments');
    }

    /**
     * @param string $commandName
     */
    private function setCommandName(string $commandName)
    {
        $this->command = $commandName;
    }

    /**
     * @param array $args
     */
    private function setParams(array $args)
    {
        $this->params = array_values($args);
    }

    /**
     * @param array $flags
     * @throws \Cli\Exception\ArgumentException
     */
    private function setFlags(array $flags)
    {
        $this->flags = new FlagCollection();
        $this->flags->loadArray($flags);
    }

    /**
     * @return string
     */
    public function getCommandName(): string
    {
        return $this->command;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return FlagCollection
     */
    public function getFlags(): FlagCollection
    {
        return $this->flags;
    }

    /**
     * @param array $args
     * @param int $pointer
     *
     * @return array
     *
     * Recursive method
     * Collects flags with - or -- that are passed before arguments
     * Works before first non-flag value
     */
    private function collectFlags(array &$args, int $pointer = 0, array &$flags = []): array
    {
        if ($pointer + 1 < count($args) ) {
            if (Flag::isFlag($args[$pointer]) ) {
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
            if (Flag::isFlag($arg) ) {
                unset($args[$key]);
            } else {
                break;
            }
        }
    }
}
