<?php


use PHPUnit\Framework\TestCase;

use Cli\Domain\Command;
use Cli\Basic\Environment;

class CommandTest extends TestCase
{
    const SIMPLE_COMMAND_NAME = 'command';

    public function testConstruction(): array
    {
        $data = $this->newSimpleCommand(self::SIMPLE_COMMAND_NAME);
        $this->assertInstanceOf(Command::class, $data['command']);
        return $data;
    }

    /**
     * @depends testConstruction
     */
    public function testCommandName(array $data)
    {
        $this->assertEquals(self::SIMPLE_COMMAND_NAME, $data['command']->getName());
    }

    /**
     * @depends testConstruction
     */
    public function testCallable(array $data)
    {
        $this->assertEquals($data['function'], $data['command']->getCallable());
    }

    /**
     * @depends testConstruction
     */
    public function testFlags(array $data)
    {
        $this->assertEquals([], $data['command']->getFlags());
    }

    /**
     * @depends testConstruction
     */
    public function testEnvironment(array $data)
    {
        $this->assertEquals($data['environment'], $data['command']->getEnv());
    }

    /**
     * @expectedException Cli\Exception\ArgumentException
     */
    public function testValidate()
    {
        $this->newSimpleCommand('');
    }

    private function newSimpleCommand($name)
    {
        $func = function($param1, $param2) {
            return $param1 . $param2;
        };
        $env = new Environment([]);
        return [
            'command'     => new Command($name, $func, [], $env),
            'function'    => $func,
            'environment' => $env,
        ];
    }
}
