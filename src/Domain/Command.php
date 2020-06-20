<?php


namespace Domain;

/**
 * Class Value
 * @package Cli/Domain
 * @license MIT
 *
 * @author AlexP007
 * @email alex.p.panteleev@gmail.com
 * @link https://github.com/AlexP007/cli
 */
class Command
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $callable;

    /**
     * @var array
     */
    private $flags;

    /**
     * @var array
     */
    private $params;


    public function __construct(string $name, callable $callable, array $flags)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->flags = $flags;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }

    public function getFlags(): array
    {
        return $this->flags;
    }
}
