<?php

class Styler {
    public array $styles = [];
    public array $classes = [];

    public function pushStyle($styleKey, $styleValue){
        $this->styles[$styleKey] = $styleValue;
    }

    public function pushClass($className){
        $this->classes[] = $className;
    }

    public function pushFontFaceStyle(string $fontFace){
        $this->styles["font-family"] = "'$fontFace', sans-serif";
    }

    public static function addStyle(string $key, string $value){
        echo ' ' . $key . ': ' . $value . '; ';
    }

    public static function addFontFaceStyle(string $fontFace){
        self::addStyle("font-family", "'$fontFace', sans-serif");
    }

    public static function addClass(string $className){
        echo ' ' . $className;
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

    public function applyClasses(){
        self::classStartAttribute();
        $this->appendClasses();
        self::closeAttribute();
    }

    public function appendClasses(){
        foreach ($this->classes as $className){
            self::addClass($className);
        }
    }

    public static function classStartAttribute(){
        echo ' class="';
    }

    public static function imageWithHeight($height){
        self::addStyle("height", $height);
        self::addStyle("width", "auto");
    }

    public static function imageWithWidth($width){
        self::addStyle("width", $width);
        self::addStyle("height", "auto");
    }

    public static function imageSquare($size){
        self::addStyle("height", $size);
        self::addStyle("width", $size);
    }
}
