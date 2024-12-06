<?php

namespace Logket;

class PrintView
{
    public static function render(Signal $signal, string $called_file_name, string $called_file_line_number){
        $id = random_int(1000000000, 9999999999);

        self::injectHeader();

        echo Template::render('print', [
            'id' => $id,
            'called_file_name' => $called_file_name,
            'called_file_line_number' => $called_file_line_number,
            'payload' => $signal->getPayload()
        ]);
    }
    
    public static function injectHeader(){
        if(!isset($GLOBALS['__logket_print_view_is_header_added'])){
            echo Template::render('Headers/print');

            $GLOBALS['__logket_print_view_is_header_added'] = true;
        }
    }
}