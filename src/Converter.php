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
        if (isCashaddr($address) && isValid($address))
        {
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
        $regex = '/^(([a-z].*):)?[02-9ac-hj-np-z].*$/';
        return preg_match($regex, $address) === 1;
    }

    public static function isValidCashAddr()
    {
    }
}
