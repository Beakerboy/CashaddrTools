<?php

namespace CashaddrTools;

use CashaddrTools\ConverterException;

/**
 * Cashaddr Converter
 *
 * Functions to describe and manipulate cashaddr formatted bitcoin addresses.
 *
 * The cashaddr format is:
 * ((prefix):)?(payload)
 *
 * The bitwise payload format is:
 * |0|4 type bits|3 hash-size bits|hash|optional padding|40 checksum bits|
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
     *  The 32 characters allowed in a Cashaddr formatted address
     */
    const CHARSET = 'qpzry9x8gf2tvdw0s3jn54khce6mua7l';

    /**
     * The expected size of the public key hash.
     *
     * "hash version value" => "hash bits"
     */
    const HASH_SIZE = [160, 192, 224, 256, 320, 384, 448, 512];

    /**
     * The type of address encoded.
     *
     * "type version value" => "address type"
     */
    const ADDRESS_TYPES = ['P2KH', 'P2SH'];

    /**
     * To Byte Array
     */
    public static function toByteArray($address)
    {
        $prefix = self::getPrefix($address);
        $payload = self::getPayload($address);
        $var = [];
        for ($i = 0; $i < strlen($prefix); $i++) {
            $var[] = ord($prefix[$i]) & 31;
        }
        $var[] = 0;
        for ($i = 0; $i < strlen($payload); $i++) {
            $var[] = strpos(self::CHARSET, $payload[$i]);
        }
        return $var;
    }

    /**
     * toLegacy
     *
     * Convert a Cashaddr address to a legacy address
     *
     * @param string $address
     *   Cashaddr bitcoin address
     *
     * @return string
     */
    public static function toLegacy($address)
    {
        if (isCashaddr($address) && isValid($address)) {
            $vars = [];
            $hash = self::getHash($address);

            // Add version byte
            // pubkey hash = 0x00, script hash = 0x05
            $version_array = ['00', '05'];
            $type = self::getType($address);
            $hash = $version_array[self::getTypeVersion($address)] . $hash;

            //Double hash the extended hash
            // $sha = SHA(SHA($hash)); 

            // Append first 4 bytes of the double hash to the extended hash.
            $checksum = substr($sha, 0, 8);
            $hash .= $checksum;

            // Perform Base58 Encoding
            while ($hash > 0) {
                // ($hash, $remainder) = $hash / 58;
                $vars[] = self::ALPHABET[$remainder];
            }
            // reverse the order of elements in $vars
            // join elements of $vars into a string
        }
    }

    /**
     * Get the address prefix
     */
    public static function getPrefix($address)
    {
        $seperator = strpos($address, ':');
        if ($seperator === false) {
            return 'bitcoincash';
        }
        return substr($address, 0, $seperator);
    }

    /**
     * Get the payload
     *
     * The payload is all the content after the prefix and the seperator
     *
     * @param string $address
     * @return string in base32 encoding
     */
    public static function getPayload($address)
    {
        $seperator = strpos($address, ':');
        if ($seperator === false) {
            return $address;
        }
        return substr($address, $seperator + 1);
    }

    /**
     * Get the hash in binary
     *
     * The public key hash is all the bits between the version and the checksum
     * This is a string represetation of the binary digits.
     * Would things be better if it were actually in binary?
     *
     * @param string @address
     * @return string in base2 encoding
     */
    public static function getBinaryHash(string $address): string
    {
        $payload = self::getPayload($address);
        $binary_hash = decbin(strpos(self::CHARSET, $payload[1]) & 3);
        $binary_hash = str_pad($binary_hash, 2, "0", STR_PAD_LEFT);
        for ($i = 2; $i < strlen($payload) - 8; $i++) {
            // 5 bit binary 'nibble'.
            $nibblet = decbin(strpos(self::CHARSET, $payload[$i]));
            $nibblet = str_pad($nibblet, 5, "0", STR_PAD_LEFT);
            $binary_hash .= $nibblet;
        }
        $padding_array = [2, 0, 3, 1, 2, 3, 4, 0];
        $padding = $padding_array[self::getHashVersion($address)];
        if ($padding > 0) {
            $binary_hash = substr($binary_hash, 0, -1 * $padding);
        }
        return $binary_hash;
    }

    /**
     * Get the hash
     *
     * The public key hash is all the bits between the version and the checksum
     *
     * @param string @address
     * @return string in base16 encoding
     */
    public static function getHash($address): string
    {
        $binary_hash = self::getBinaryHash($address);
        if (strlen($binary_hash) !== self::getNumberHashBits($address)) {
            throw new ConverterException("Incorrect number of binary digits");
        }
        $hash = "";
        while (strlen($binary_hash) > 4) {
            $nibble = substr($binary_hash, -4);
            $binary_hash = substr($binary_hash, 0, -4);
            $hash = dechex(bindec($nibble)) . $hash;
        }
        $hash = dechex(bindec($binary_hash)) . $hash;
        return $hash;
    }

    /**
     * Get the address version
     *
     * The version is stored in the first 8 bits of the address.
     * The fist bit should be a 0. The next four indicate the
     * address type, and the final three, the hash length.
     *
     * @param string $address
     * @return int - decimal value of the first 8 bits
     */
    public static function getVersion(string $address)
    {
        return self::getTypeVersion($address) * 8 + self::getHashVersion($address);
    }

    /**
     * Get the address type bits
     *
     * The type bits are the 4 least significant bits of the first character of the payload
     *
     * @param string $address
     * @return int - decimal value of the first 5 bits
     */
    public static function getTypeVersion($address)
    {
        $payload = self::getPayload($address);
        $type_bit = $payload[0];
        return strpos(self::CHARSET, $type_bit);
    }

    /**
     * Get the hash size
     *
     * The hash version is specified in the address version byte
     * as the first three bites of the second Base32 character in the payload.
     *
     * @param string $address
     * @return int - decimal value of the 3 hash size bits
     */
    public static function getHashVersion(string $address): int
    {
        $payload = self::getPayload($address);
        return intdiv((strpos(self::CHARSET, $payload[1]) & 28), 4);
    }

    /**
     * Get the number of hash bits
     *
     * The hash size is specified in the address version byte
     * as the first three bits of the second Base32 character in the payload.
     *
     * @param string $address
     * @return int
     */
    public static function getNumberHashBits(string $address): int
    {
        $hash_version = self::getHashVersion($address);
        return self::HASH_SIZE[$hash_version];
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
        // check if legacy format
        $len = strlen($address);
        $hash = 0;
        for ($i = 0; $i < $len; $i++) {
            $hash = $hash * 58 + strpos(self::ALPHABET, $address[0]);
        }
        // Prepend prefix, separator, version
        // Append 8 checksum zero bits
        // Generate checksum
    }

    /**
     * isCashaddr
     *
     * Check if an address conforms to the Cashaddr standard
     *
     * @param string $address
     *   bitcoin address
     *
     * @return boolean
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
        $regex = '/^(([0-9a-z]*):)?[02-9ac-hj-np-z]{42,112}$/';
        return preg_match($regex, $address) === 1;
    }

    /**
     * Is the CashAddr address valid
     *
     * Check the internal structure of the suppled address to ensure it matches the specification
     */
    public static function isValidCashAddr($address)
    {
        $address = strtolower($address);
        
        // MSB of version byte must be 0
        if (self::getTypeVersion($address) > 15) {
            return false;
        }

        // Do the number of bits in the address match expectations
        $payload = self::getPayload($address);
        $version = 8;
        $hash = self::getNumberHashBits($address);
        $padding_array = [2, 0, 3, 1, 2, 3, 4, 0];
        $padding = $padding_array[self::getHashVersion($address)];
        $checksum = 40;
        $expected_bits = $version + $hash + $padding + $checksum;
        if (strlen($payload) * 5 !== $expected_bits) {
            return false;
        }

        // Verify any hash padding bits are zero
        if ($padding > 0) {
            $padding_byte_value = strpos(self::CHARSET, $payload[-9]);
            $padding_mask = (2 ** $padding - 1);
            $padding_value = $padding_byte_value & $padding_mask;
            if ($padding_value !== 0) {
                return false;
            }
        }
        
        // Does the checksum match
        if (self::polymod(self::toByteArray($address)) !== 0) {
            return false;
        }
        return true;
    }

    /**
     * Fix a broken Cashaddr address
     *
     * The checksum allows up to 5 bitwise errors in an address.
     * If errors are detected, return the correct address
     */
    public static function fixAddress(string $address): string
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
            if ($c0 & 0x01) {
                $c ^= 0x98f2bc8e61;
            }
            if ($c0 & 0x02) {
                $c ^= 0x79b76d99e2;
            }
            if ($c0 & 0x04) {
                $c ^= 0xf33e5fb3c4;
            }
            if ($c0 & 0x08) {
                $c ^= 0xae2eabe2a8;
            }
            if ($c0 & 0x10) {
                $c ^= 0x1e4f43e470;
            }
        }
        return $c ^ 1;
    }
}
