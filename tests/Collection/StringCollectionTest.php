<?php


use PHPUnit\Framework\TestCase;

use Cli\Collection\StringCollection;

class StringCollectionTest extends TestCase
{
    /**
     * @expectedException Cli\Exception\ArgumentException
     */
    public function testSetException()
    {
        $collection = new StringCollection();
        $collection->param = 1;
    }
}