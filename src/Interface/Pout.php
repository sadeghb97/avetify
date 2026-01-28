<?php
namespace Avetify\Interface;

class Pout {
    public static function br($count = 1) : string {
        $str = "";
        for($i=0; $count>$i; $i++){
            $str .= (Platform::isCli() ? PHP_EOL : "<br>");
        }
        return $str;
    }

    public static function endline($count = 1) : void {
        for($i=0; $count>$i; $i++){
            echo (Platform::isCli() ? PHP_EOL : "<br>");
        }
    }

    public static function bufferOut() : void {
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
        usleep(10000);
    }

    public static function logLine($m) : void {
        echo '[' . date('Y-m-d H:i:s') . '] ' . $m . Pout::br();
    }

    public static function safeLog($l) : void {
        echo '<textarea style="width: 90%; height: auto; min-width: 600px; 
        min-height: 320px; margin: 12px; padding-top: 8px; 
        padding-bottom: 20px;">' . $l . '</textarea>' . self::br();
    }

    public static function printPreArray($array, $name = "Array") : void {
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
