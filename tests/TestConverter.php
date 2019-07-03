<?php
namespace CashaddrTools;

/**
 * TestConverter
 *
 * This class exposes protected classes in Converter to allow unit testing
 */
class TestConverter extends Converter
{
    public static function getByteArray($x)
    {
        return parent::toByteArray($x);
    }

    public static function polymod($x)
    {
        return parent::polymod($x);
    }
}
