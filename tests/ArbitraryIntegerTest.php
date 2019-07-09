<?php

namespace CashaddrTools\Tests;

use CashaddrTools\ArbitraryInteger;

class ArbitraryIntegerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForConstructor
     */
    public function testConstructor($number, $base, $offset, $expected)
    {
        $number = new ArbitraryInteger($number, $base, $offset);
        $this->assertEquals($expected, $number->getBinary());
    }

    public function dataProviderForConstructor()
    {
        return [
            ["4", 256, null, "4"],
            [52, 10, "0", "4"],
            [13364, 10, "0", "44"],
         //   ["52", 10, "0", "4"],
        ];
    }

    /**
     * @dataProvider dataProviderForTestLeftShift
     */
    public function testLeftShift($value, $shift, $expected)
    {
        $start = new ArbitraryInteger($value);
        $shifted = $start->leftShift($shift);
        $expectedObject = new ArbitraryInteger($expected);
        $this->assertEquals($expectedObject->getBinary(), $shifted->getBinary());
    }

    public function dataProviderForTestLeftShift()
    {
        return [
            [20, 2, 80],
            [7, 9, 3584],
        ];
    }
}
