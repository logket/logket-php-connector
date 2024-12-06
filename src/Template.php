<?php

namespace Logket;

use Mustache_Engine;

class Template
{
    public static function render(string $template, array $parameters=[]){
        $engine = new Mustache_Engine(['entity_flags' => ENT_QUOTES]);
        $contents = self::getContents($template);

        return $engine->render($contents, $parameters);
    }

    private static function getContents(string $name){
        $path = __DIR__.'/Templates/'.$name.'.mustache';

        return file_get_contents($path);
    }
}