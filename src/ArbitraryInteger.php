<?php

namespace CashaddrTools;

/**
 * Arbitrary Length Integer
 *
 * http://www.faqs.org/rfcs/rfc3548.html
 */
class ArbitraryInteger
{
    protected $base256;

    /**
     * Constructor
     *
     * @param mixed $number
     *   A string or integer
     * @param int $base
     *   The number base. The default is base 10.
     * @param mixed $offset
     *   The alphabet or offset of a string number.
     *   $offset can either be a number or an array.
     *   If $offset is null, the following default values are used:
     *     Base 2, 8, 10: $offset = '0';
     *     Base 16      : $offset = '0123456789abcdef’
     *     All others   : $offset = chr(0)
     */
    public function __construct($number, int $base = 10, $offset = null)
    {
        if (is_int($number)) {
            $int_part = intdiv($number, 256);
            $string = chr($number % 256);

            while ($int_part > 0) {
                $string = chr($int_part % 256) . $string;
                $int_part = intdiv($int_part, 256);
            }
            $this->base256 = $string;
        } elseif (is_string($number)) {
            // Check that all elements are greater than the offset, and elements of the alphabet.
            $length = strlen($number);

            // Set to default offset and ascii alphabet
            if ($offset === null) {
                switch ($base) {
                    case 2:
                    case 8:
                    case 10:
                        $offset = '0';
                        break;
                    case 16:
                        $offset = '0123456789abcdef';
                        break;
                    default:
                        $offset = chr(0);
                        break;
                }
            }
            // Remove the offset.
            if ($offset !== chr(0)) {
                $offset_num = 0;
                for ($i = 0; $i < $length; $i++) {
                    $chr = $number[$i];
                    if (strlen($offset) ==  1) {
                        $offset_num = ord($offset);
                        $number[$i] = chr(ord($chr) - $offset_num);
                    } else {
                        $number[$i] = strpos($offset, $chr);
                    }
                }
            }
            $base256 = new ArbitraryInteger(0);
            if ($base < 256) {
                $base_obj = new ArbitraryInteger($base);
                $place_value = new ArbitraryInteger(1);
                $length = strlen($number);
                for ($i = 0; $i < $length; $i++) {
                    $chr = ord($number[$i]);
                    $base256 = $base256->multiply($base)->add($chr);
                }
                $this->base256 = $base256->getBinary();
            } elseif ($base > 256) {
                throw \Exception;
            } else {
                $this->base256 = $number;
                // need to drop leading zeroes.
            }
        } else {
            throw \Exception;
        }
    }

    public function getBinary()
    {
        return $this->base256;
    }

    public function add($number)
    {
        // check if string, object, or int
        // throw exception if appropriate
        if (!is_object($number)) {
            $number = new ArbitraryInteger($number);
        }
        $number = $number->getBinary();
        $carry = 0;
        $len = strlen($this->base256);
        $num_len = str_len($number);
        $max_len = max($len, $num_len);
        $base_256 = str_pad($base_256, $max_len - $len, chr(0));
        $number = str_pad($number, $max_len - $len, chr(0));
        $result = '';
        for ($i = 0; $i < $max_len; $i++) {
            $base_chr = ord($base_256[$max_len - $i]);
            $num_chr = ord($number[$max_len - $i]);
            $sum = $base_chr + $num_chr + $carry;
            $carry = intdiv($sum, 256);
            
            $result = chr($sum % 256). $result;
        }
        return new ArbitraryInteger($result, 256);
    }

    public function multiply($number): ArbitrayInteger
    {
        // check if string, object, or int
        // throw exception if appropriate
        if (!is_object($number)) {
            $number = new ArbitraryInteger($number);
        }
        $number = $number->getBinary();
        $length = strlen($number);
        $product = new ArbitraryInteger(0);;
        for ($i = 0; $i < $length; $i++) {
            $this_len = strlen($this->base256);
            $base_digit = ord(substr($number, -1 * $i, 1));
            $carry = 0;
            $inner_product = '';
            for ($j = 0; $j < $this_len; $j++) {
                $digit = ord(substr($this->base256, -1 * $i, 1));
                $step_product = $digit * $base_digit;
                $mod = $step_product % 256;
                $carry = intdiv($step_product, 256);
                $inner_product = chr($mod) . $inner_product;
            }
            if ($carry > 0) {
                $inner_product = chr($carry) . $inner_product;
            }
            $inner_product = str_pad($inner_product, $i, chr(0));
            $product = $product->add($inner_product);
        }
        return $product;
    }

    public function leftShift(int $bits)
    {
        $shifted_string = "";
        $length = strlen($this->base256);
        $bytes = intdiv($bits, 8);
        $bits = $bits % 8;
        $carry = 0;
        for ($i = 0; $i < $length; $i++) {
            $chr = ord($this->base256[$i]);
            // If $shifted string is empty, don’t add 0x00.
            $new_value = chr($carry + intdiv($chr << $bits, 256));
            if ($shifted_string !== "" || $new_value !== chr(0)) {
                $shifted_string .= $new_value;
            }
            $carry = ($chr << $bits) % 256;
        }
        $shifted_string .= chr($carry);

        // Pad $bytes of 0x00 on the right.
        $shifted_string = str_pad($shifted_string, $bytes, chr(0));

        return new ArbitraryInteger($shifted_string, 256);
    }

    public function equals(ArbitraryInteger $int): bool
    {
        return $this->base256 == $int->getBinary();
    }
}
