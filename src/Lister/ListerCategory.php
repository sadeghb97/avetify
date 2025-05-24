<?php
namespace Avetify\Lister;

class ListerCategory {
    public int $index = 0;
    public string $title = "";

    public function __construct($index, $title){
        $this->index = $index;
        $this->title = $title;
    }
}