<?php

namespace Logket;

use Throwable;
use WebSocket\Client as WebSocketClient;

class Logket
{
    private $bucket_id;
    private $signals = [];
    private $bucket_websocket_client;
    private $debug_backtrace;
    private $called_file_name = null;
    private $called_file_line_number = null;

    public function __construct(string $bucket_id=null, array $debug_backtrace=[]){
        $bucket_id = self::getAutoBucketId($bucket_id);
        
        $this->bucket($bucket_id);
        $this->debug_backtrace = $debug_backtrace;

        if(isset($this->debug_backtrace[0]) && isset($this->debug_backtrace[0]['file'])) $this->called_file_name = basename($this->debug_backtrace[0]['file']);
        if(isset($this->debug_backtrace[0]) && isset($this->debug_backtrace[0]['line'])) $this->called_file_line_number = strval($this->debug_backtrace[0]['line']);
    }

    public static function init(string $bucket_id=null, array $debug_backtrace=[]){
        return new Logket($bucket_id, $debug_backtrace);
    }

    public function dump(...$variables){
        foreach($variables as $variable){
            $signal = new Signal(Command::$LOG_CAPTURE);
            $signal->setCalledFile($this->called_file_name, $this->called_file_line_number);
            $signal->setPayload($variable);
            $this->signals[] = $signal;
        }

        return $this;
    }

    public function pause(){
        if(count($this->signals)>0){
            $final_signal = array_pop($this->signals);
            $final_signal->setExectionState(Signal::$EXECUTION_PAUSED);

            foreach($this->signals as $signal){
                $this->bucket_websocket_client->text($signal->getJson());
            }

            $this->bucket_websocket_client->text($final_signal->getJson());
            $this->signals = [];

            $is_execution_played = false;

            while(!$is_execution_played){
                $signal = $this->bucket_websocket_client->receive();
                $signal = json_decode($signal->getContent(), true);

                if(isset($signal['command']) && $signal['command']==Command::$EXECUTION_PLAY) $is_execution_played = true;
            }
        }
    }

    public function print(...$variables){
        // if(count($this->signals)==0){
        //     $signal = new Signal(Command::$LOG_CAPTURE);
        //     $signal->setCalledFile($this->called_file_name, $this->called_file_line_number);
        //     $signal->setPayload('🐛');
        //     $this->signals[] = $signal;
        // }

        $this->dump(...$variables);

        if(php_sapi_name()!='cli'){
            foreach($this->signals as $signal){
                PrintView::render($signal, $this->called_file_name, $this->called_file_line_number);
            }
        }
    }

    public function bucket(string $bucket_id){
        if(!isset($GLOBALS['__logket_websocket_pool'][$bucket_id])){
            $GLOBALS['__logket_websocket_pool'][$bucket_id] = new WebSocketClient('ws://localhost:3500/up?bucket-id='.$bucket_id);
        }

        $this->bucket_id = $bucket_id;
        $this->bucket_websocket_client = $GLOBALS['__logket_websocket_pool'][$bucket_id];
        $this->bucket_websocket_client->setTimeout(PHP_INT_MAX);

        return $this;
    }

    public function __destruct(){
        if(count($this->signals)==0){
            $signal = new Signal(Command::$LOG_CAPTURE);
            $signal->setCalledFile($this->called_file_name, $this->called_file_line_number);
            $signal->setPayload('🐛');
            $this->signals[] = $signal;
        }

        try{
            foreach($this->signals as $signal){
                $this->bucket_websocket_client->text($signal->getJson());
            }
        }
        catch(Throwable $exception){
            if(!isset($GLOBALS['__logket_is_websocket_error_shown'])){
                $GLOBALS['__logket_is_websocket_error_shown'] = true;
                Error::throw('Unable to connect to the logket server', 'Please check if your host have access to internet connection.');
            }
        }
    }

    private static function getAutoBucketId(string $bucket_id=null){
        if($bucket_id) return $bucket_id;
        else if(isset($GLOBALS['__logket_global_bucket_id'])) return $GLOBALS['__logket_global_bucket_id'];
        else return false;
    }
}