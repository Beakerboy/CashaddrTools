<?php

namespace CashaddrTools;

use CashaddrTools\ConverterException;

/**
 * Cashaddr Converter
 *
 * Converts legacy Bitcoin Cash addresses to the new CashAddr format and vice versa.
 *
 * https://www.bitcoincash.org/spec/cashaddr.html
 */
class Converter
{
    /**
     *  The 58 characters allowed in a legacy formatted address
     */
    const ALPHABET = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    /**
     *  Th 32 characters allowed in a Cashaddr formatted address
     */
    const CHARSET = 'qpzry9x8gf2tvdw0s3jn54khce6mua7l';

    /**
     * toLegacy
     *
     * Convert a Cashaddr address to a legacy address
     *
     * @param string $address
     *   Legacy bitcoin address
     *
     * @returns string
     */
    public static function toLegacy($address)
    {
        if (isCashaddr($address) && isValid($address)) {
            // Remove Prefix and separator
            //
        }
    }

    public static function getVersion($address)
    {
    }

    public static function getAddressType($address)
    {
    }

    public static function getHashSize($address)
    {
    }

    /**
     * toCashaddr
     *
     * Convert a legacy address to cashaddr format
     *
     * @param string $address
     * @return string
     */
    public static function toCashaddr(string $address): string
    {
    }

    /**
     * isCashaddr
     *
     * Check if an address conforms to the Cashaddr standard
     *
     * @param string $address
     *   bitcoin address
     *
     * @returns boolean
     */
    public static function isCashaddr($address)
    {
        // Must be all upper or all lower case.
        if (strtolower($address) !== $address && strtoupper($address) !== $address) {
            return false;
        }
        $address = strtolower($address);

        // Address has an optional prefix, which must have a colon after it followed by the payload.
        // The payload must be the set of base32 alphanumerics.
        $regex = '/^(([a-z]*):)?[02-9ac-hj-np-z]*$/';
        return preg_match($regex, $address) === 1;
    }

    public static function isValidCashAddr()
    {
    }

    /**
     * Polymod Function
     *
     * @param array $v an array of 5 bit numbers
     * @return a 40 bit number
     */ 
    protected static function polymod($v)
    {
        $c = 1;
        foreach ($v as $d) {
            $c0 = $c >> 35;
            $c = (($c & 0x07ffffffff) << 5) ^ $d;
            if ($c0 & 0x01) $c ^= 0x98f2bc8e61;
            if ($c0 & 0x02) $c ^= 0x79b76d99e2;
            if ($c0 & 0x04) $c ^= 0xf33e5fb3c4;
            if ($c0 & 0x08) $c ^= 0xae2eabe2a8;
            if ($c0 & 0x10) $c ^= 0x1e4f43e470;
        }
        return $c ^ 1;
    }
}
