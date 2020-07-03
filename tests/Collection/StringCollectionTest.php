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

    public function testSetValue()
    {
        $collection = new StringCollection();
        $collection->param = 'value';

        $this->assertEquals('value', $collection->param);
    }

    public function testLoadArray()
    {
        $collection = new StringCollection();
        $array = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];

        $collection->loadArray($array);
        $this->assertEquals($array, $collection->asArray());
    }
}
