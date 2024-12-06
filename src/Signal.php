<?php

namespace Logket;

use Logket\Serialize\Serializer;

class Signal
{
    public static $EXECUTION_PAUSED = 'execution-paused';
    public static $EXECUTION_PLAYING = 'execution-playing';

    private $attributes = [];

    public function __construct(string $command){
        $this->attributes['command'] = $command;
    }

    public function setCommand(string $command){
        $this->attributes['command'] = $command;

        return $this;
    }

    public function setPayload(mixed $payload){
        $this->attributes['payload'] = json_encode(Serializer::serialize($payload, [
            'calledFileName' => $this->attributes['calledFileName'] ?? null,
            'calledFileLineNumber' => $this->attributes['calledFileLineNumber'] ?? null,
        ]));

        return $this;
    }

    public function getPayload(){
        return $this->attributes['payload'] ?? null;
    }

    public function setCalledFile(string $called_file_name=null, string $called_file_line_number=null){
        if($called_file_name) $this->attributes['calledFileName'] = $called_file_name;
        if($called_file_line_number) $this->attributes['calledFileLineNumber'] = $called_file_line_number;

        return $this;
    }

    public function setExectionState(bool $state){
        if($state==self::$EXECUTION_PAUSED) $this->attributes['isExecutionPaused'] = true;
        else $this->attributes['isExecutionPaused'] = false;
    }

    public function getJson(){
        return json_encode($this->attributes);
    }
}