<?php


use PHPUnit\Framework\TestCase;

use Cli\Basic\Environment;
use Cli\Domain\Command;
use Cli\Collection\CommandCollection;

class CommandCollectionTest extends TestCase
{
    /**
     * @expectedException Cli\Exception\ArgumentException
     */
    public function testSetException()
    {
        $collection = new CommandCollection();
        $collection->command = 1;
    }

    public function testSetValue()
    {
        $collection = new CommandCollection();
        $command = new Command(
            'command',
            function() {return true;},
            [],
            new Environment([]));

        $collection->command = $command;

        $this->assertEquals($command, $collection->command);
    }
}