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
    
    }

    public static function toCashaddr($address)
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

        // Addres has an optional prefix, which must have a colon after it followed by the payload.
        // The payload must start with a p or q and be followed by the set of base32 alphanumerics.
        $regex = '/^((bitcoincash|bchtest|bchreg):?)[qp]{1}[02-9ac-hj-np-z]{41}$/';
        return preg_match($regex, $address);
    }

    public static function isValidCashAddr()
    {
    }
}
    
