<?php


use PHPUnit\Framework\TestCase;

use Cli\Domain\Command;

class CommandTest extends TestCase
{
    const SIMPLE_COMMAND_NAME = 'command';

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
    }

    private function newSimpleCommand($name)
    {
        $func = function($param1, $param2) {
            return $param1 . $param2;
        };

        return new Command($name, $func, [], []);
    }
}
