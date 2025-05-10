<?php

class Flusher {
    public string $key = "";
    private int $limit = 1;
    public int $counter = 0;
    public $delayMs = 0;

    public function __construct($key, $lim, $delayMs = 50){
        $this->key = $key;
        $this->limit = $lim;
        $this->counter = 0;
        $this->delayMs = $delayMs;
    }

    public function out(){
        $this->counter++;
        if(($this->counter % $this->limit) == 0) $this->_bufferOut();
    }

    private function _bufferOut(){
        echo $this->key . " Flusher: " . $this->counter;
        endline();
        if(!isCli()) ob_flush();
        flush();
        usleep($this->delayMs * 1000);
    }
}