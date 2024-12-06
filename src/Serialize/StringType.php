<?php

namespace Logket\Serialize;

class StringType
{
    public static function serialize(string $string){
        $nodes = [];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted'],
            'value' => '"'
        ];
        
        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-yellow'],
            'value' => $string
        ];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted'],
            'value' => '"'
        ];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted', 'font-style-italic'],
            'value' => ' #'.gettype($string)
        ];

        return $nodes;
    }
}