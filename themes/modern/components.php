<?php

function selectBabe($names, $ids, $imgs, $title, $key, $selectedPs = null, $separator = null){
    $len = count($names);
    $selKey = $key . '_' . "sel";
    $hidKey = $key . '_' . "hid";
    $txtKey = $key . '_' . "txt";
    $targetKey = $key . '_' . "hdps";
    $boobKey = $key . '_' . 'boob';
    $babesListKey = $key . '_' . 'babeslist';
    $selArrayJSName = $key . '_' . 'selarray';
    $firstIndexJsName = $key . '_' . 'k';

    $enterBabeCall = "enterBabe(event, $selArrayJSName, '$selArrayJSName', '$txtKey', '$selKey', '$hidKey', '$boobKey', '$babesListKey');";
    $addBabeCall = "addBabe($selArrayJSName, '$selArrayJSName', '$txtKey', '$selKey', '$hidKey', '$boobKey', '$babesListKey');";
    $delBabeCall = "delBabe(event, $selArrayJSName, \\\"$hidKey\\\", \\\"$boobKey\\\", \\\"$babesListKey\\\");";

    echo '<script type="text/javascript" src="oldselect.js"></script>';

    echo '<script type="text/javascript">';
    echo 'var ind=0;';
    echo 'var ' . $selArrayJSName . '=Array();';
    echo 'var len='.$len.';';
    echo 'var babes = '.json_encode($names).';';
    echo 'var imgs = '.  json_encode($imgs).';';
    echo 'var ids = '.json_encode($ids).';';

    echo '</script>';

    echo'<div id="selbox" style="margin: auto;">';
    echo '<center>';
    echo '<div style="font-size: 11pt; font-weight: bold; margin-top: 6px; margin-bottom: 6px;">' . $title . '</div>';
    echo '<div id="' . $selKey . '">';
    echo '</div>';

    echo '<input id="' . $txtKey . '" type="text" list="' . $babesListKey . '" autocomplete="off" onkeypress="' . $enterBabeCall . '" />';
    echo '<input type="button" id="btnadd" onclick="' . $addBabeCall . '" value="Add">';
    echo '<datalist id="' . $babesListKey . '">';
    for($i=0; array_key_exists($i, $names)!==false; $i++){
        echo '<option id="' . $boobKey .$ids[$i].'">',$names[$i],'</option>';
    }
    echo '</datalist>';
    echo '</center>';
    echo '</div>';
    echo '<input id="' . $hidKey . '" type="hidden" name="' . $targetKey . '" value="">';
    if($selectedPs){
        $p = !is_array($selectedPs) ? explode($separator, $selectedPs) : $selectedPs;

        echo '<script type="text/javascript">';
        echo 'let ' . $firstIndexJsName . ' = null;';
        for($i=0; array_key_exists($i, $p)!==false; $i++){
            for($j=0; count($ids) > $j; $j++){
                if($ids[$j]==$p[$i]) echo $firstIndexJsName . '='.$j.";";
            }

            $addProcess = '("<img src=\'"+imgs[' . $firstIndexJsName . ']+"\' id=\'"+ids[' . $firstIndexJsName . ']+"\' class=\'selimg\' title=\'"+babes[' . $firstIndexJsName . ']+"\' onclick=\'' . $delBabeCall . '\'>");';

            echo 'var selected=document.getElementById("' . $selKey . '");';
            echo 'selected.innerHTML+=' . $addProcess;
            echo 'document.getElementById("' . $boobKey . '"+ids[' . $firstIndexJsName . ']).remove();';
            echo 'document.getElementById("' . $txtKey . '").value="";';
            echo $selArrayJSName . '.push(ids[' . $firstIndexJsName . ']);';
            echo 'document.getElementById("' . $hidKey . '").value=' . $selArrayJSName . '.toString();';
        }
        echo '</script>';
    }

    heightMargin(20);
}