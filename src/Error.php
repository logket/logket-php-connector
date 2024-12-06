<?php

namespace Logket;

class Error
{
    public static function throw(string $error, string $description=null){
        if(Config::get('debug')===true){
            DebugView::render($error, $description);
        }
    }
}