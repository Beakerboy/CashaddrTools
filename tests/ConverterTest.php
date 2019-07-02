<?php
namespace CashaddrTools\Tests;

use CashaddrTools\Converter;

class ConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testCase testIsCashaddr()
     * @dataProvider dataProviderForTestIsCashaddr
     */
    public function testIsCashaddr($address, $legacy)
    {
        $this->assertTrue(Converter::isCashaddr($address));
        // $this->assertEquals($legacy, Converter::toLegacy($address));
    }

    public function dataProviderForTestIsCashaddr()
    {
        return[
            [
                'bitcoincash:qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a',
                '1BpEi6DfDAUFd7GtittLSdBeYJvcoaVggu',
            ],
            [
                'qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a',
                '1BpEi6DfDAUFd7GtittLSdBeYJvcoaVggu',
            ],
            [
                'QPM2QSZNHKS23Z7629MMS6S4CWEF74VCWVY22GDX6A',
                '1BpEi6DfDAUFd7GtittLSdBeYJvcoaVggu',
            ],
            [
                'bitcoincash:qr95sy3j9xwd2ap32xkykttr4cvcu7as4y0qverfuy',
                '1KXrWXciRDZUpQwQmuM1DbwsKDLYAYsVLR',
            ],
        ];
    }

    /**
     * @testCase testIsNotCashaddr()
     * @dataProvider dataProviderForTestIsNotCashaddr
     */
    public function testIsNotCashaddr($address)
    {
        $this->assertFalse(Converter::isCashaddr($address));
    }

    public function dataProviderForTestIsNotCashaddr()
    {
        return[
            // Mix of cases
            ['bitcoincash:Qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6A'],
            // Wrong Prefix
            ['bitcoincas:qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a'],
            // Wrong separator
            ['bitcoincash;qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a'],
            // Wrong first character
            ['bitcoincash:spm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a'],
            // Disallowed character
            ['qbm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a'],
            // Payload too long
            ['bitcoincash:qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6aa'],
        ];
    }
}
