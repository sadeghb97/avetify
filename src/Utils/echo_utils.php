<?php
namespace Avetify\Utils;

use Avetify\Interface\CSS;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;

function br($count = 1) : string {
    $str = "";
    for($i=0; $count>$i; $i++){
        $str .= (isCli() ? PHP_EOL : "<br>");
    }
    return $str;
}

function endline($count = 1){
    for($i=0; $count>$i; $i++){
        echo (isCli() ? PHP_EOL : "<br>");
    }
}

function bufferOut(){
    if(!isCli()) ob_flush();
    flush();
    usleep(10000);
}

function safeLog($l){
    echo '<textarea style="width: 90%; height: auto; min-width: 600px; 
        min-height: 320px; margin: 12px; padding-top: 8px; 
        padding-bottom: 20px;">' . $l . '</textarea>' . br();
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
