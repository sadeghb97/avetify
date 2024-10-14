<?php
class SBListCategory {
    public int $index = 0;
    public string $title = "";

    public function __construct($index, $title){
        $this->index = $index;
        $this->title = $title;
    }
}