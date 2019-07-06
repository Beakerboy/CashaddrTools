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
        $this->expectEquals($expected, $number->getBinary());
    }

    public function dataProviderForConstructor()
    {
        return [
            ["52”, 10, "0", "4"],
            ["4", 256, null, "4"],
            [52, 10, "0", "4"],
        ];
    }
}
