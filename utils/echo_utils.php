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

function classicMenu(array $rows, $marginTop = 16){
    $fontSize = "14pt";
    $splitterContents = "&nbsp;&nbsp;|&nbsp;&nbsp;";
    $prBold=true;
    $prNoDecor=true;
    $prBlank=false;

    $vertDiv = new VertDiv(4);
    $vertDiv->addStyle("margin-top", $marginTop . "px");
    $vertDiv->addStyle("font-size", $fontSize);

    $vertDiv->open();

    foreach ($rows as $rowIndex => $row){
        $niceDiv = new NiceDiv(0);
        $niceDiv->open();
        $color = $row['color'];
        $splitter = '<span style="font-weight: bold; color: ' . $color . ';">' . $splitterContents . '</span>';

        foreach ($row['links'] as $linkIndex => $link){
            if($linkIndex > 0) echo $splitter;
            prLink($link[1], $link[0], $color, $prBold, $prBlank, $prNoDecor);
        }
        $niceDiv->close();
    }

    $vertDiv->close();
}
