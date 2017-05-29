<?php

namespace App\Services\Gremlin;

class ParserProperties
{

    /**
     * @param null $label
     * @param $properties
     * @param string $suffix
     * @return string
     */
    public static function parsePropertiesToInsert($label = null, $properties, $suffix = '')
    {
        $string = '';
        $count = 1;
        foreach ($properties as $key => $value) {
            if ($count == 1 && !$label) {
                $string .= "PROPERTY{$count}{$suffix},VALUE{$count}{$suffix}";
            } else {
                $string .= ",PROPERTY{$count}{$suffix},VALUE{$count}{$suffix}";
            }

            $count++;
        }

        return $string;
    }

    /**
     * @param $properties
     * @param string $suffix
     * @return string
     */
    public static function parsePropertiesToUpdate($properties, $suffix = '')
    {
        $string = '';
        $count = 1;
        foreach ($properties as $property => $value) {
            $string .= ".property(PROPERTY{$count}{$suffix},VALUE{$count}{$suffix})";
            $count++;
        }

        return $string;
    }

    /**
     * @param $properties
     * @param string $suffix
     * @return string
     */
    public static function parsePropertiesToFindBy($properties, $suffix = '')
    {
        $string = '';
        $count = 1;
        foreach ($properties as $property => $value) {
            $string .= ".has(PROPERTY{$count}{$suffix},VALUE{$count}{$suffix})";
            $count++;
        }

        return $string;
    }

    /**
     * @param $properties
     * @param string $suffix
     * @return string
     */
    public static function parsePropertiesToRemove($properties, $suffix = '')
    {
        $string = '';
        $countProperties = count($properties);
        $count = 1;
        foreach ($properties as $property) {
            $string .= "it.get().property(VALUE{$count}{$suffix}).remove()";
            if ($count != $countProperties) {
                $string .= ';';
            }
            $count++;
        }

        return $string;
    }

    /**
     * @param bool $label
     * @param $properties
     * @param string $suffix
     * @return array
     */
    public static function parseBindValues($label = false, $properties, $suffix = '')
    {
        $arrayBinds = [];
        if ($label) {
            $arrayBinds["BIND_LABEL{$suffix}"] = trim($label);
        }

        $count = 1;
        foreach ($properties as $property => $value) {
            $arrayBinds["PROPERTY{$count}{$suffix}"] = trim($property);
            $arrayBinds["VALUE{$count}{$suffix}"] = (int) trim($value);

            $count++;
        }

        return $arrayBinds;
    }
}
