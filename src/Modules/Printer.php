<?php
namespace Avetify\Modules;

use Avetify\Interface\CSS;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Utils\CliUtils;

class Printer {
    public function __construct(public string $fontSize = "1rem", public string $fontWeight = "normal",
                                public string $color = "black", public string $bgColor = "",
                                public bool $inline = true){
    }

    public function print($message){
        if(!CliUtils::isCli()) {
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

        if(!CliUtils::isCli()) {
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

    function br($count = 1) : string {
        $str = "";
        for($i=0; $count>$i; $i++){
            $str .= (CliUtils::isCli() ? PHP_EOL : "<br>");
        }
        return $str;
    }

    function endline($count = 1){
        for($i=0; $count>$i; $i++){
            echo (CliUtils::isCli() ? PHP_EOL : "<br>");
        }
    }

    function bufferOut(){
        if(!CliUtils::isCli()) ob_flush();
        flush();
        usleep(10000);
    }

    function safeLog($l){
        echo '<textarea style="width: 90%; height: auto; min-width: 600px; 
        min-height: 320px; margin: 12px; padding-top: 8px; 
        padding-bottom: 20px;">' . $l . '</textarea>' . self::br();
    }

    function printPreArray($array, $name = "Array") {
        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle(CSS::marginTop, "20px");
        Styler::addStyle(CSS::marginBottom, "20px");
        Styler::addStyle(CSS::textAlign, "left");
        Styler::addStyle(CSS::backgroundColor, "#dee9e7");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo '##' . $name . '<br><pre>';
        print_r($array);
        HTMLInterface::closeDiv();
    }
}
