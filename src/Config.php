<?php

namespace Logket;

class Config
{
    public static function get(string $name){
        $options = $GLOBALS['__logket_options'] ?? [];

        return $options[$name] ?? false;
    }
}