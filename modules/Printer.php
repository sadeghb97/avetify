<?php

class Printer {
    public function __construct(public string $fontSize = "1rem", public string $fontWeight = "normal",
                                public string $color = "black", public string $bgColor = ""){
    }

    public function print($message){
        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle("display", "block");
        Styler::addStyle("font-size", $this->fontSize);
        Styler::addStyle("font-weight", $this->fontWeight);
        Styler::addStyle("color", $this->color);
        if($this->bgColor) Styler::addStyle("background-color", $this->bgColor);
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo $message;
        echo '</div>';
    }

    public static function basePrint($message){
        (new Printer())->print($message);
    }

    public static function warningPrint($message){
        (new Printer(color: "#af601a"))->print($message);
    }

    public static function errorPrint($message){
        (new Printer(color: "red"))->print($message);
    }

    public static function boldPrint($message){
        (new Printer(fontWeight: "bold"))->print($message);
    }
}
