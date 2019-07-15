<?php

namespace CashaddrTools\Tests;

use CashaddrTools\ArbitraryInteger;
/**
 * https://github.com/php/php-src/tree/master/ext/gmp/tests
 */
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
     * @dataProvider dataProviderForAdd
     */
    public function testAdd($number1, $number2, $expected)
    {
        $num1 = new ArbitraryInteger($number1);
        $exp = new ArbitraryInteger($expected);
        $this->assertEquals($exp->getBinary(), $num1->add($number2)->getBinary());
    }

    public function dataProviderForAdd()
    {
        return [
            [80, 2, 82],
            [300, 10, 310],
            ['9223372036854775807', 1, '9223372036854775808'],
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
            [848, 10, 8480],
            [10, 848, 8480],
            ['9223372036854775807', 2, '18446744073709551614'],
            [2, '9223372036854775807', '18446744073709551614'],
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
            [20, 10, 20480],
        ];
    }
}
