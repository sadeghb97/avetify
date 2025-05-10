<?php

class Detailed {
    public array $details = [];

    public function addDetail(string $key, string $value){
        $this->details[$key] = $value;
    }

    public function getDetail(string $key) : string | null {
        if(isset($this->details[$key])) return $this->details[$key];
        return null;
    }
}
