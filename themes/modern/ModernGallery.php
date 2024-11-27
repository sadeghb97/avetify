<?php

abstract class ModernGallery extends ModernSetRenderer {
    public function __construct(SetModifier $setModifier, public string $title){
        parent::__construct($setModifier);
    }

    public function renderRecord($item, $index) {
        printCard($this->getItemImage($item), "", "", $this->getItemLink($item), []);
    }

    public function getTitle(): string {
        return $this->title;
    }

    abstract public function getItemImage($record) : string;

    public function getItemLink($record) : string | null {
        return null;
    }
}