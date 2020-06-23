<?php


namespace Cli\Domain;

use Cli\Collection\FlagCollection;
use Cli\Registry\Config;
use Cli\Traits\Thrower;

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
     * @var array
     */
    private $params;

    /**
     * @var FlagCollection;
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
        $this->params = array_values($args);
    }

    private function setFlags(array $flags)
    {
        $this->flags = new FlagCollection();
        $this->flags->loadArray($flags);
    }

    public function getCommandName(): string
    {
        return $this->command;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getFlags(): FlagCollection
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
