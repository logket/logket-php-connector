<?php

namespace Logket;

class DebugView
{
    public static function render(string $title='Something went wrong', string $description=null){
        self::injectHeader();

        echo Template::render('debug', [
            'title' => $title,
            'description' => $description
        ]);
    }

    public static function injectHeader(){
        if(!isset($GLOBALS['__logket_debug_view_is_header_added'])){
            echo Template::render('Headers/debug');

            $GLOBALS['__logket_debug_view_is_header_added'] = true;
        }
    }
}