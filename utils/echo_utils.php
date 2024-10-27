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

function safeLog($l){
    echo '<textarea style="width: 90%; height: auto; min-width: 600px; 
        min-height: 320px; margin: 12px; padding-top: 8px; 
        padding-bottom: 20px;">' . $l . '</textarea>' . br();
}
