<?php

namespace Logket\Serialize;

class NumberType
{
    public static function serialize(mixed $number){
        $nodes = [];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-blue'],
            'value' => $number
        ];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted', 'font-style-italic'],
            'value' => ' #'.gettype($number)
        ];

        return $nodes;
    }
}