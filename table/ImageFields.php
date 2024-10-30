<?php

class SBTableAvatarField extends SBTableField {
    public function __construct(string $title, string $key, public null | string $imageWidth){
        parent::__construct($title, $key);
    }

    public function presentValue($item){
        $image = $this->getValue($item);
        echo '<img src="' . $image . '" style="';
        if($this->imageWidth){
            SBTable::addStyle("width", $this->imageWidth);
            SBTable::addStyle("height", "auto");
        }
        echo '" >';
    }
}
