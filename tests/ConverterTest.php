<?php
namespace CashaddrTools\Tests;

use CashaddrTools\Converter;

class ConverterTest extends \PHPUnit\Framework\TestCase
{
    public function testIsCashaddr($address, $legacy)
    {
        $this->assertTrue(Converter::isCashaddr($address));
    }

    public function dataproviderForTestIsCashaddr()
    {
        return[
            [
                'bitcoincash:qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a',
                '1BpEi6DfDAUFd7GtittLSdBeYJvcoaVggu',
            ],
            [
                'bitcoincash:qr95sy3j9xwd2ap32xkykttr4cvcu7as4y0qverfuy',
                '1KXrWXciRDZUpQwQmuM1DbwsKDLYAYsVLR',
            ],
        ];
    }
}
