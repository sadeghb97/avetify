<?php

class VertDiv extends NiceDiv {
    public function __construct(string $sepSize){
        parent::__construct($sepSize);
        $this->styles = [];
    }

    public function separate(){
        $this->separateWith("height");
    }
}
