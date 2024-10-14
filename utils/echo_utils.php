<?php

function br($count = 1) : string {
    $str = "";
    for($i=0; $count>$i; $i++){
        $str .= (php_sapi_name() == "cli" ? PHP_EOL : "<br>");
    }
    return $str;
}

function endline($count = 1){
    for($i=0; $count>$i; $i++){
        echo (php_sapi_name() == "cli" ? PHP_EOL : "<br>");
    }
}

function bufferOut(){
    if(php_sapi_name() != "cli") ob_flush();
    flush();
    usleep(10000);
}
