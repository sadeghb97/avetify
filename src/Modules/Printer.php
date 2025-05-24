<?php
namespace Avetify\Modules;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use function Avetify\Utils\isCli;

class Printer {
    public function __construct(public string $fontSize = "1rem", public string $fontWeight = "normal",
                                public string $color = "black", public string $bgColor = "",
                                public bool $inline = true){
    }

    public function print($message){
        if(!isCli()) {
            echo '<div ';
            Styler::startAttribute();
            Styler::addStyle("display", $this->inline ? "inline" : "block");
            Styler::addStyle("font-size", $this->fontSize);
            Styler::addStyle("font-weight", $this->fontWeight);
            Styler::addStyle("color", $this->color);
            if ($this->bgColor) Styler::addStyle("background-color", $this->bgColor);
            Styler::closeAttribute();
            HTMLInterface::closeTag();
        }

        echo $message;

        if(!isCli()) {
            echo '</div>';
        }
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
