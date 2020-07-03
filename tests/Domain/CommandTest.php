<?php


use PHPUnit\Framework\TestCase;

use Cli\Domain\Command;
use Cli\Basic\Environment;

class CommandTest extends TestCase
{
    const SIMPLE_COMMAND_NAME = 'command';

    /**
     * @var callable
     */
    private $simpleFunc;

    /**
     * @var Environment
     */
    private $simplyEnvironment;

    /**
     * @expectedException Cli\Exception\ArgumentException
     */
    public function testValidate()
    {
        $this->newSimpleCommand('');
    }

    public function testConstruction()
    {
        $command = $this->newSimpleCommand(self::SIMPLE_COMMAND_NAME);
        $this->assertInstanceOf(Command::class, $command);

        $this->assertEquals(self::SIMPLE_COMMAND_NAME, $command->getName());
        $this->assertEquals($this->simpleFunc, $command->getCallable());
        $this->assertEquals($this->simpleFunc, $command->getCallable());
        $this->assertEquals([], $command->getFlags());
        $this->assertEquals($this->simplyEnvironment, $command->getEnv());
    }

    private function newSimpleCommand($name)
    {
        $this->simpleFunc = function($param1, $param2) {
            return $param1 . $param2;
        };
        $this->simplyEnvironment = new Environment([]);
        return new Command($name, $this->simpleFunc, [], $this->simplyEnvironment);
    }
}
