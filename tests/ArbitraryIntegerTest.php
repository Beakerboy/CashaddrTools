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
        $this->assertTrue($shifted->equals($expectedObject));
    }

    public function dataProviderForTestLeftShift()
    {
        return [
            [20, 2, 80],
            [7, 9, 3584],
        ];
    }
}
