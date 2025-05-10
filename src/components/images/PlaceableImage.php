<?php

class PlaceableImage implements Placeable {
    public function __construct(public string $src, public int $size, public  bool $widthWidth = true){
    }

    public function place(WebModifier $webModifier = null){
        if($this->widthWidth){
            HTMLInterface::placeImageWithWidth($this->src, $this->size, $webModifier);
        }
        else HTMLInterface::placeImageWithHeight($this->src, $this->size, $webModifier);
    }
}
