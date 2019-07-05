<?php

namespace CashaddrTools

class ArbitraryInteger
{
    public function __construct($number, int $base = 10, $offset = "0", string $alphabet = null)
    {
        if (isint($number)) {
            // Need to get this to work on $number > 255
            $this->base256 = chr($number);
            $this->length = 1;
            $this->base = 10;
            $this->original = $number;
        } else {
            // Check that all elements are greater than the offset, and elements of the alphabet.
            $this->length = strlen($number);
            $this->base = $base;

            // Set to zero offset and ascii alphabet
            if ($offset !== 0) {
            }
            $base256 = new ArbitraryInteger(0)
            if ($base < 256) {
                $place_value = new ArbitraryInteger(1)
                for ($i = $length -1; $i <= 0; $i—-) {
                    $nibblet = ord($number[$i]) - ord($offset);
                    $base256 = 
                    $place_value = $place_value->multiply($base)
                }
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

    public function add($number1, $number2)
    {
    }

    public function multiply($number): ArbitrayInteger
    {
        
    }
}
