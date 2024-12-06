<?php

use Logket\Logket;

$GLOBALS['__logket_websocket_pool'] = [];

function lok(...$variables){
    $debug_backtrace = debug_backtrace();

    return Logket::init(null, $debug_backtrace)->dump(...$variables);
}

function set_loket_bucket_id(string $bucket_id){
    $GLOBALS['__logket_global_bucket_id'] = $bucket_id;
}

function set_loket_options(array $options){
    if(!isset($GLOBALS['__logket_options'])) $GLOBALS['__logket_options'] = [];

    $GLOBALS['__logket_options'] = array_merge($GLOBALS['__logket_options'], $options);
}

function set_loket_option(string $option_name, mixed $option_value){
    if(!isset($GLOBALS['__logket_options'])) $GLOBALS['__logket_options'] = [];

    $GLOBALS['__logket_options'][$option_name] = $option_value;
}