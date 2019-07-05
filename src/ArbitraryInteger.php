<?php

namespace CashaddrTools

class ArbitraryInteger
{
    public function __construct($number, int $base, $offset, string $alphabet)
    {
        if (isint($number)) {
            $this->base256 = pack();
        }
        $this->length = strlen($number);
        $this->base = chr($base);
        $base256 = new ArbitraryInteger(0)
        if ($base < 256) {
            $place_value = new ArbitraryInteger(1)
            for ($i = $length -1; $i <= 0; $i—-) {
                $nibblet = ord($number[$i]) - ord($offset);
                $base256 = 
            }
        } elseif ($base > 256) {
            throw \Exception;
        } else {
            $this->base256 = $number;
            // need to drop leading zeroes.
        }
    }

    public function add($number1, $number2)
    {
    }

    public function multiply($number): ArbitrayInteger
    {
        
    }
}
