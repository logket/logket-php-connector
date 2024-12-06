<?php

namespace Logket\Serialize;

class UnknownType
{
    public static function serialize(mixed $string){
        $nodes = [];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted'],
            'value' => 'unknown type'
        ];

        return $nodes;
    }
}