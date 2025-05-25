<?php
namespace Avetify\Components\Containers;

use Avetify\Interface\WebModifier;

class VertDiv extends NiceDiv {
    public function __construct(string $sepSize){
        parent::__construct($sepSize);
        $this->styles = [];
    }

    public function separate(WebModifier $webModifier = null){
        $this->separateWith("height");
    }
}
