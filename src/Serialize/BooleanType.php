<?php

namespace Logket\Serialize;

class BooleanType
{
    public static function serialize(bool $boolean){
        $nodes = [];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-yellow'],
            'value' => $boolean?'true':'false'
        ];

        $nodes[] = [
            'type' => 'text',
            'style' => ['font-color-muted', 'font-style-italic'],
            'value' => ' #'.gettype($boolean)
        ];

        return $nodes;
    }
}