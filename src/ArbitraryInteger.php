<?php

namespace CashaddrTools;

class ArbitraryInteger
{
    protected $base256;

    public function __construct($number, int $base = 10, $offset = "0", string $alphabet = null)
    {
        if (is_int($number)) {
            $int_part = intdiv($number, 256);
            $string = chr($number % 256);

            while ($int_part > 0) {
                $string = chr($int_part % 256) . $string;
                $int_part = intdiv($int_part, 256);
            }
            // Need to get this to work on $number > 255
            $this->base256 = $string;
        } else {
            // Check that all elements are greater than the offset, and elements of the alphabet.
            $length = strlen($number);
            $this->base = $base;

            // Set to zero offset and ascii alphabet
            if ($offset !== 0) {
            }
            $base256 = new ArbitraryInteger(0);
            if ($base < 256) {
                $base_obj = new ArbitraryInteger(chr($base), 256);
                $place_value = new ArbitraryInteger(1);
                for ($i = $length - 1; $i <= 0; $i--) {
                    $nibblet = new ArbitraryInteger(ord($number[$i]) - ord($offset));
                    $base256 = $base256->add($nibblet->multiply($place_value));
                    $place_value = $place_value->multiply($base);
                }
                $this->base256 = $base256->getBinary();
            } elseif ($base > 256) {
                throw \Exception;
            } else {
                $this->base256 = $number;
                $this->original = $number;
                $this->length = strlen($number);
                $this->base = 256;
                // need to drop leading zeroes.
            }
        }
    }

    public function getBinary()
    {
        return $this->base256;
    }

    public function add($number1, $number2)
    {
    }

    public function multiply($number): ArbitrayInteger
    {
    }

    public function leftShift(int $bits)
    {
        $bytes = 0;
        $shifted_string = "";
        $length = strlen($this->base256);
        if ($bits > 7) {
            $bytes = intdiv($bits, 8);
            $bits = $bits % 8;
        }
        $carry = 0;
        for ($i = 0; $i < $length; $i++) {
            // If $shifted string is empty, don’t add 0x00.
            $new_value = chr($carry | (ord($this->base256[$i]) >> (8 - $bits)));
            if ($shifted_string !== "" || $new_value !== chr(0)) {
                $shifted_string .= $new_value;
                $carry = (ord($this->base256[$i]) << $bits) % 256;
            }
        }
        $shifted_string .= chr($carry);

        // Pad $bytes of 0x00 on the right.
        $shifted_string = str_pad($shifted_string, strlen($shifted_string) + $bytes, chr(0));

        return new ArbitraryInteger($shifted_string, 256, 0);
    }

    public function equals(ArbitraryInteger $int): bool
    {
        return $this->base256 == $int->getBinary();
    }
}
