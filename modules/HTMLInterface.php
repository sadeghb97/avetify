<?php
 class HTMLInterface {
     public static function addAttribute($attr, $value){
         echo ' ' . $attr . '="' . $value . '" ';
     }
 }