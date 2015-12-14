<?php

namespace App\Services\Gremlin;

class ParserProperties
{
    public static function parsePropertiesToInsert($label = null, $properties, $sufix = '')
    {
        $string = '';
        $count = 1;
        foreach ($properties as $key => $value) {
            if ($count == 1 && !$label) {
                $string .= "PROPERTY{$count}{$sufix},VALUE{$count}{$sufix}";
            } else {
                $string .= ",PROPERTY{$count}{$sufix},VALUE{$count}{$sufix}";
            }

            $count++;
        }

        return $string;
    }

    public static function parsePropertiesToUpdate($properties, $sufix = '')
    {
        $string = '';
        $count = 1;
        foreach ($properties as $property => $value) {
            $string .= ".property(PROPERTY{$count}{$sufix},VALUE{$count}{$sufix})";
            $count++;
        }

        return $string;
    }

    public static function parsePropertiesToFindBy($properties, $sufix = '')
    {
        $string = '';
        $count = 1;
        foreach ($properties as $property => $value) {
            $string .= ".has(PROPERTY{$count}{$sufix},VALUE{$count}{$sufix})";
            $count++;
        }

        return $string;
    }

    public static function parsePropertiesToRemove($properties, $sufix = '')
    {
        $string = '';
        $countProperties = count($properties);
        $count = 1;
        foreach ($properties as $property) {
            $string .= "it.get().property(VALUE{$count}{$sufix}).remove()";
            if ($count != $countProperties) {
                $string .= ';';
            }
            $count++;
        }

        return $string;
    }

    public static function parseBindValues($label = false, $properties, $sufix = '')
    {
        $arrayBinds = [];
        if ($label) {
            $arrayBinds["BIND_LABEL{$sufix}"] = trim($label);
        }

        $count = 1;
        foreach ($properties as $property => $value) {
            $arrayBinds["PROPERTY{$count}{$sufix}"] = trim($property);
            $arrayBinds["VALUE{$count}{$sufix}"] = trim($value);

            $count++;
        }

        return $arrayBinds;
    }
}
