<?php


namespace Cli\Domain;

use Cli\Collection\FlagCollection;
use Cli\Registry\Config;

/**
 * Class Value
 * @package Cli/Domain
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class CliRequest extends Domain
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $firstArg;

    /**
     * @var string
     */
    private $commandName;

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

        $this->firstArg = array_shift($args);

        $commandName = array_shift($args);
        $this->setCommandName($commandName);

        $flags = $this->collectFlags($args);
        $this->cleanArgsFromFlags($args);

        $this->setParams($args);
        $this->setFlags($flags);

        $this->validate();
    }

    /**
     * @param string $commandName
     */
    private function setCommandName(string $commandName)
    {
        $this->commandName = $commandName;
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

    protected function validate()
    {
        $this->validateFirstArgsKeyValue();
        $this->validateCommandName();
    }

    /**
     * Validating first value in args (GLOBALS)
     * Should be the name of this script (configured when initialize)
     *
     * @param string $value
     * @throws \Cli\Exception\ArgumentException
     */
    private function validateFirstArgsKeyValue()
    {
        self::ensureArgument(
            $this->firstArg === $this->config->getScriptName(),
            'invalid input arguments'
        );
    }

    private function validateCommandName()
    {
        self::ensureArgument(
            strlen($this->commandName) !== 0,
            "Command name should't be empty"
        );
    }

    /**
     * @return string
     */
    public function getCommandName(): string
    {
        return $this->commandName;
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
     * Recursive method
     * Collects flags with - or -- that are passed before arguments
     * Works before finds first non-flag value
     *
     * @param array $args
     * @param int $pointer
     *
     * @return array
     */
    private function collectFlags(array &$args, int $pointer = 0, array &$flags = []): array
    {
        if ($pointer < count($args) ) {
            if (Flag::isFlag($args[$pointer]) ) {
                $flags[] = $args[$pointer];
                $this->collectFlags($args, $pointer + 1, $flags);
            }
        }

        return $flags;
    }

    /**
     * Unset all flags values before first non-flag value
     *
     * @param array $args
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
