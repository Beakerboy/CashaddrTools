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
        $this->assertTrue(Converter::isValidCashaddr($address));
        $this->assertEquals('bitcoincash', Converter::getPrefix($address));
        // P2KH with 160 bit hash is version 0
        $this->assertEquals(0, Converter::getVersion($address));
        // $this->assertEquals($legacy, Converter::toLegacy($address, false));
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

    //public function testToCashaddr()
    //{
    //    $expected = 'bitcoincash:qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a';
    //    $legacy = '1BpEi6DfDAUFd7GtittLSdBeYJvcoaVggu';
    //    $this->assertEquals($expected, Converter::toCashaddr($legacy));
    //}

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
            ['bitcoincas^:qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a'],
            // Wrong separator
            ['bitcoincash;qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a'],
            // Disallowed character
            ['qbm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a'],
            // Too Short
            ['bitcoincash:qpm2qsznhks23z7'],
            // Too long
            ['bitcoincash:qpm2qsznhks23z7qpm2qsznhks23z7qpm2qsznhks23z7qpm2qsznhks23z7qpm2qsznhks23z7qpm2qsznhks23z7qpm2qsznhks23z7qpm2qsznhks23z7'],
            // no payload
            ['bitcoincash:'],
            // prefix only
            ['bitcoincash'],
        ];
    }

    /**
     * @testCase testIsNotValidCashaddr()
     * @dataProvider dataProviderForTestIsNotValidCashaddr
     */
    public function testIsNotValidCashaddr($address)
    {
        $this->assertFalse(Converter::isValidCashaddr($address));
    }

    public function dataProviderForTestIsNotValidCashaddr()
    {
        return[
            // First bit is not zero
            ['bitcoincash:6pm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6a'],
            // Too many chars for given hash size
            ['bitcoincash:qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6aq'],
            // Padding is not zeros
            ['bitcoincash:qpm2qsznhks23z7629mms6s4cwef74vcwdy22gdx6a'],
            // Checksum does not match
            ['bitcoincash:qpm2qsznhks23z7629mms6s4cwef74vcwvy22gdx6q'],
        ];
    }

    /**
     * @testCase Test Large Format
     * @dataProvider dataProviderForLarge
     */
    public function testLarge($hash_bytes, $type, $address, $hash)
    {
        $hash_version = $hash_bytes < 40 ? intdiv($hash_bytes - 20, 4) : intdiv($hash_bytes, 8) - 1;
        $this->assertEquals($hash_version, Converter::getHashVersion($address));
        $this->assertEquals($hash_bytes * 8, Converter::getNumberHashBits($address));
        $this->assertEquals($type, Converter::getTypeVersion($address));
        $this->assertEquals(strtolower($hash), Converter::getHash($address));
    }

    public function dataProviderForLarge()
    {
        return [
            [20, 0, 'qr6m7j9njldwwzlg9v7v53unlr4jkmx6eylep8ekg2', 'F5BF48B397DAE70BE82B3CCA4793F8EB2B6CDAC9'],
            [20, 1, 'bchtest:pr6m7j9njldwwzlg9v7v53unlr4jkmx6eyvwc0uz5t', 'F5BF48B397DAE70BE82B3CCA4793F8EB2B6CDAC9'],
            [20, 1, 'pref:pr6m7j9njldwwzlg9v7v53unlr4jkmx6ey65nvtks5', 'F5BF48B397DAE70BE82B3CCA4793F8EB2B6CDAC9'],
            [20, 15, 'prefix:0r6m7j9njldwwzlg9v7v53unlr4jkmx6ey3qnjwsrf', 'F5BF48B397DAE70BE82B3CCA4793F8EB2B6CDAC9'],
            [24, 0, 'bitcoincash:q9adhakpwzztepkpwp5z0dq62m6u5v5xtyj7j3h2ws4mr9g0', '7ADBF6C17084BC86C1706827B41A56F5CA32865925E946EA'],
            [24, 1, 'bchtest:p9adhakpwzztepkpwp5z0dq62m6u5v5xtyj7j3h2u94tsynr', '7ADBF6C17084BC86C1706827B41A56F5CA32865925E946EA'],
            [24, 1, 'pref:p9adhakpwzztepkpwp5z0dq62m6u5v5xtyj7j3h2khlwwk5v', '7ADBF6C17084BC86C1706827B41A56F5CA32865925E946EA'],
            [24, 15, 'prefix:09adhakpwzztepkpwp5z0dq62m6u5v5xtyj7j3h2p29kc2lp', '7ADBF6C17084BC86C1706827B41A56F5CA32865925E946EA'],
            [28, 0, 'bitcoincash:qgagf7w02x4wnz3mkwnchut2vxphjzccwxgjvvjmlsxqwkcw59jxxuz', '3A84F9CF51AAE98A3BB3A78BF16A6183790B18719126325BFC0C075B'],
            [28, 1, 'bchtest:pgagf7w02x4wnz3mkwnchut2vxphjzccwxgjvvjmlsxqwkcvs7md7wt', '3A84F9CF51AAE98A3BB3A78BF16A6183790B18719126325BFC0C075B'],
            [28, 1, 'pref:pgagf7w02x4wnz3mkwnchut2vxphjzccwxgjvvjmlsxqwkcrsr6gzkn', '3A84F9CF51AAE98A3BB3A78BF16A6183790B18719126325BFC0C075B'],
            [28, 15, 'prefix:0gagf7w02x4wnz3mkwnchut2vxphjzccwxgjvvjmlsxqwkc5djw8s9g', '3A84F9CF51AAE98A3BB3A78BF16A6183790B18719126325BFC0C075B'],
            [32, 0, 'bitcoincash:qvch8mmxy0rtfrlarg7ucrxxfzds5pamg73h7370aa87d80gyhqxq5nlegake', '3173EF6623C6B48FFD1A3DCC0CC6489B0A07BB47A37F47CFEF4FE69DE825C060'],
            [32, 1, 'bchtest:pvch8mmxy0rtfrlarg7ucrxxfzds5pamg73h7370aa87d80gyhqxq7fqng6m6', '3173EF6623C6B48FFD1A3DCC0CC6489B0A07BB47A37F47CFEF4FE69DE825C060'],
            [32, 1, 'pref:pvch8mmxy0rtfrlarg7ucrxxfzds5pamg73h7370aa87d80gyhqxq4k9m7qf9', '3173EF6623C6B48FFD1A3DCC0CC6489B0A07BB47A37F47CFEF4FE69DE825C060'],
            [32, 15, 'prefix:0vch8mmxy0rtfrlarg7ucrxxfzds5pamg73h7370aa87d80gyhqxqsh6jgp6w', '3173EF6623C6B48FFD1A3DCC0CC6489B0A07BB47A37F47CFEF4FE69DE825C060'],
            [40, 0, 'bitcoincash:qnq8zwpj8cq05n7pytfmskuk9r4gzzel8qtsvwz79zdskftrzxtar994cgutavfklv39gr3uvz', 'C07138323E00FA4FC122D3B85B9628EA810B3F381706385E289B0B25631197D194B5C238BEB136FB'],
            [40, 1, 'bchtest:pnq8zwpj8cq05n7pytfmskuk9r4gzzel8qtsvwz79zdskftrzxtar994cgutavfklvmgm6ynej', 'C07138323E00FA4FC122D3B85B9628EA810B3F381706385E289B0B25631197D194B5C238BEB136FB'],
            [40, 1, 'pref:pnq8zwpj8cq05n7pytfmskuk9r4gzzel8qtsvwz79zdskftrzxtar994cgutavfklv0vx5z0w3', 'C07138323E00FA4FC122D3B85B9628EA810B3F381706385E289B0B25631197D194B5C238BEB136FB'],
            [40, 15, 'prefix:0nq8zwpj8cq05n7pytfmskuk9r4gzzel8qtsvwz79zdskftrzxtar994cgutavfklvwsvctzqy', 'C07138323E00FA4FC122D3B85B9628EA810B3F381706385E289B0B25631197D194B5C238BEB136FB'],
            [48, 0, 'bitcoincash:qh3krj5607v3qlqh5c3wq3lrw3wnuxw0sp8dv0zugrrt5a3kj6ucysfz8kxwv2k53krr7n933jfsunqex2w82sl', 'E361CA9A7F99107C17A622E047E3745D3E19CF804ED63C5C40C6BA763696B98241223D8CE62AD48D863F4CB18C930E4C'],
            [48, 1, 'bchtest:ph3krj5607v3qlqh5c3wq3lrw3wnuxw0sp8dv0zugrrt5a3kj6ucysfz8kxwv2k53krr7n933jfsunqnzf7mt6x', 'E361CA9A7F99107C17A622E047E3745D3E19CF804ED63C5C40C6BA763696B98241223D8CE62AD48D863F4CB18C930E4C'],
            [48, 1, 'pref:ph3krj5607v3qlqh5c3wq3lrw3wnuxw0sp8dv0zugrrt5a3kj6ucysfz8kxwv2k53krr7n933jfsunqjntdfcwg', 'E361CA9A7F99107C17A622E047E3745D3E19CF804ED63C5C40C6BA763696B98241223D8CE62AD48D863F4CB18C930E4C'],
            [48, 15, 'prefix:0h3krj5607v3qlqh5c3wq3lrw3wnuxw0sp8dv0zugrrt5a3kj6ucysfz8kxwv2k53krr7n933jfsunqakcssnmn', 'E361CA9A7F99107C17A622E047E3745D3E19CF804ED63C5C40C6BA763696B98241223D8CE62AD48D863F4CB18C930E4C'],
            [56, 0, 'bitcoincash:qmvl5lzvdm6km38lgga64ek5jhdl7e3aqd9895wu04fvhlnare5937w4ywkq57juxsrhvw8ym5d8qx7sz7zz0zvcypqscw8jd03f', 'D9FA7C4C6EF56DC4FF423BAAE6D495DBFF663D034A72D1DC7D52CBFE7D1E6858F9D523AC0A7A5C34077638E4DD1A701BD017842789982041'],
            [56, 1, 'bchtest:pmvl5lzvdm6km38lgga64ek5jhdl7e3aqd9895wu04fvhlnare5937w4ywkq57juxsrhvw8ym5d8qx7sz7zz0zvcypqs6kgdsg2g', 'D9FA7C4C6EF56DC4FF423BAAE6D495DBFF663D034A72D1DC7D52CBFE7D1E6858F9D523AC0A7A5C34077638E4DD1A701BD017842789982041'],
            [56, 1, 'pref:pmvl5lzvdm6km38lgga64ek5jhdl7e3aqd9895wu04fvhlnare5937w4ywkq57juxsrhvw8ym5d8qx7sz7zz0zvcypqsammyqffl', 'D9FA7C4C6EF56DC4FF423BAAE6D495DBFF663D034A72D1DC7D52CBFE7D1E6858F9D523AC0A7A5C34077638E4DD1A701BD017842789982041'],
            [56, 15, 'prefix:0mvl5lzvdm6km38lgga64ek5jhdl7e3aqd9895wu04fvhlnare5937w4ywkq57juxsrhvw8ym5d8qx7sz7zz0zvcypqsgjrqpnw8', 'D9FA7C4C6EF56DC4FF423BAAE6D495DBFF663D034A72D1DC7D52CBFE7D1E6858F9D523AC0A7A5C34077638E4DD1A701BD017842789982041'],
            [64, 0, 'bitcoincash:qlg0x333p4238k0qrc5ej7rzfw5g8e4a4r6vvzyrcy8j3s5k0en7calvclhw46hudk5flttj6ydvjc0pv3nchp52amk97tqa5zygg96mtky5sv5w', 'D0F346310D5513D9E01E299978624BA883E6BDA8F4C60883C10F28C2967E67EC77ECC7EEEAEAFC6DA89FAD72D11AC961E164678B868AEEEC5F2C1DA08884175B'],
            [64, 1, 'bchtest:plg0x333p4238k0qrc5ej7rzfw5g8e4a4r6vvzyrcy8j3s5k0en7calvclhw46hudk5flttj6ydvjc0pv3nchp52amk97tqa5zygg96mc773cwez', 'D0F346310D5513D9E01E299978624BA883E6BDA8F4C60883C10F28C2967E67EC77ECC7EEEAEAFC6DA89FAD72D11AC961E164678B868AEEEC5F2C1DA08884175B'],
            [64, 1, 'pref:plg0x333p4238k0qrc5ej7rzfw5g8e4a4r6vvzyrcy8j3s5k0en7calvclhw46hudk5flttj6ydvjc0pv3nchp52amk97tqa5zygg96mg7pj3lh8', 'D0F346310D5513D9E01E299978624BA883E6BDA8F4C60883C10F28C2967E67EC77ECC7EEEAEAFC6DA89FAD72D11AC961E164678B868AEEEC5F2C1DA08884175B'],
            [64, 15, 'prefix:0lg0x333p4238k0qrc5ej7rzfw5g8e4a4r6vvzyrcy8j3s5k0en7calvclhw46hudk5flttj6ydvjc0pv3nchp52amk97tqa5zygg96ms92w6845', 'D0F346310D5513D9E01E299978624BA883E6BDA8F4C60883C10F28C2967E67EC77ECC7EEEAEAFC6DA89FAD72D11AC961E164678B868AEEEC5F2C1DA08884175B'],
        ];
    }

    /**
     * Test checksum generation
     *
     * These test addresses do not contaon a valid payload,
     * but do have valid checksums.
     *
     * @dataProvider dataProviderForChecksum
     */
    public function testChecksum($address)
    {
        $vars = \CashaddrTools\TestConverter::toByteArray($address);
        $checksum = \CashaddrTools\TestConverter::polymod($vars);
        $this->assertEquals(0, $checksum);
    }

    public function dataProviderForChecksum()
    {
        return [
            ['prefix:x64nx6hz'],
            ['p:gpf8m4h7'],
            ['bitcoincash:qpzry9x8gf2tvdw0s3jn54khce6mua7lcw20ayyn'],
            ['bchtest:testnetaddress4d6njnut'],
            ['bchreg:555555555555555555555555555555555555555555555udxmlmrz'],
        ];
    }
}
