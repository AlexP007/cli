<?php


use PHPUnit\Framework\TestCase;

use Cli\Domain\Flag;

class FlagTest extends TestCase
{
    /**
     * @expectedException Cli\Exception\ArgumentException
     */
    public function testValidate()
    {
        new Flag("");
    }

    public function testFlag()
    {
        $flag = new Flag("-f");
        $this->assertEquals('-f', $flag->getFlag());
    }

    public function testFlagValue()
    {
        $flag = new Flag("-f=Y");
        $this->assertEquals('Y', $flag->getValue());
    }
}
