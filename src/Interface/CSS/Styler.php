<?php
namespace Avetify\Interface\CSS;

class Styler {
    public array $styles = [];
    public array $classes = [];

    /**
     * @param ('color'|'background-color'|string) $styleKey
     */
    public function pushStyle($styleKey, $styleValue) : void {
        $this->styles[$styleKey] = $styleValue;
    }

    public function pushClass($className) : void {
        $this->classes[$className] = true;
    }

    public function popStyle($styleKey) : void {
        unset($this->styles[$styleKey]);
    }

    public function popClass($className) : void {
        unset($this->classes[$className]);
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
        foreach ($this->classes as $className => $classValue){
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

    public function merge(Styler | null $secondStyler) : Styler {
        $outModifier = clone $this;
        if($secondStyler) {
            foreach ($secondStyler->styles as $secPropertyKey => $secPropertyValue) {
                $outModifier->styles[$secPropertyKey] = $secPropertyValue;
            }
            foreach ($secondStyler->classes as $secClassKey => $secClassValue) {
                $outModifier->classes[$secClassKey] = $secClassValue;
            }
        }
        return $outModifier;
    }
}
