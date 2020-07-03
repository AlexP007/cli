<?php


use PHPUnit\Framework\TestCase;

use Cli\Basic\Flags;
use Cli\Domain\Flag;
use Cli\Collection\FlagCollection;

class FlagCollectionTest extends TestCase
{
    /**
     * @expectedException Cli\Exception\ArgumentException
     */
    public function testSetException()
    {
        $collection = new FlagCollection();
        $collection->param = 1;
    }

    public function testSetValue()
    {
        $collection = new FlagCollection();
        $flag = new Flag('-f');
        $collection->flag = $flag;

        $this->assertEquals($flag, $collection->flag);
    }

    public function testLoadArray(): array
    {
        $collection = new FlagCollection();
        $array = [
            '-f=2',
            '-c',
        ];

        $resultArray = [
            '-f' => 2,
            '-c' =>true,
        ];

        $collection->loadArray($array);
        $this->assertEquals($resultArray, $collection->asArray());

        return [
            'array' => $array,
            'resultArray' => $resultArray,
        ];
    }

    /**
     * @depends testLoadArray
     */
    public function testGetFlagsObject(array $data)
    {
        $collection = new FlagCollection();
        $collection->loadArray($data['array']);
        $resultFlagsArray = (new Flags($data['resultArray']) )->getArray();

        $this->assertEquals($resultFlagsArray, $collection->getFlagsObject()->getArray());
    }
}
