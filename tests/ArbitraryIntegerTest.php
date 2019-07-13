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
            ["52", 10, "0", "4"],
            [3000, 10, "0", chr(11) . chr(184)],
        ];
    }

    /**
     * @dataProvider dataProviderForMultiply
     */
    public function testMultiply($number1, $number2, $expected)
    {
        $num1 = new ArbitraryInteger($number1);
        $exp = new ArbitraryInteger($expected);
        $this->assertEquals($exp->getBinary(), $num1->multiply($number2)->getBinary());
    }

    public function dataProviderForMultiply()
    {
        return [
            [80, 2, 160],
            [300, 10, 3000],
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
            //[20, 10, 10240],
        ];
    }
}
