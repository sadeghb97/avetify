<?php
namespace Avetify\Components\Containers;

use Avetify\Interface\WebModifier;

class VertDiv extends NiceDiv {
    protected function initBaseModifier() : void {
        $this->baseModifier = WebModifier::createInstance();
    }

    public function separate(?WebModifier $webModifier = null) : void {
        $this->separateWith("height");
    }
}
