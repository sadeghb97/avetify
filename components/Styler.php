<?php

class Styler {
    public array $styles = [];

    public function pushStyle($styleKey, $styleValue){
        $this->styles[$styleKey] = $styleValue;
    }

    public function applyStyles(){
        self::startAttribute();
        $this->appendStyles();
        self::closeAttribute();
    }

    public function appendStyles(){
        foreach ($this->styles as $styleKey => $styleValue){
            self::addStyle($styleKey, $styleValue);
        }
    }

    public static function startAttribute(){
        echo ' style="';
    }

    public static function closeAttribute(){
        echo '" ';
    }

    public static function addStyle(string $key, string $value){
        echo ' ' . $key . ': ' . $value . '; ';
    }

    public static function imageWithHeight($height){
        self::addStyle("height", $height);
        self::addStyle("width", "auto");
    }

    public static function imageWithWidth($width){
        self::addStyle("width", $width);
        self::addStyle("height", "auto");
    }
}
