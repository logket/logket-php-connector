<?php

namespace Logket\Serialize;

class NullType
{
    public static function serialize($null){
        $nodes = [];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted'],
            'value' => 'null'
        ];

        return $nodes;
    }
}