<?php
 class HTMLInterface {
     public static function addAttribute($attr, $value){
         echo ' ' . $attr . '="' . $value . '" ';
     }

     public static function closeTag(){
         echo ' >';
     }

     public static function closeSingleTag(){
         echo ' />';
     }

     public static function closeDiv(){
         echo '</div>';
     }

     public static function closeLink(){
         echo '</a>';
     }

     public static function openContainer($width = "90%", $maxWidth = "90%", WebModifier | null $modifier = null){
         echo '<div ';
         HTMLInterface::addAttribute("class", "container");
         if($modifier && $modifier->htmlModifier != null) $modifier->htmlModifier->applyModifiers();
         Styler::startAttribute();
         Styler::addStyle("width", $width);
         Styler::addStyle("max-width", $maxWidth);
         if($modifier && $modifier->styler != null) $modifier->styler->appendStyles();
         Styler::closeAttribute();
         self::closeTag();
     }

     public static function openLink(string $href, WebModifier | null $modifier = null){
         echo '<a ';
         self::addAttribute("href", $href);
         if($modifier && $modifier->htmlModifier != null) $modifier->htmlModifier->applyModifiers();
         if($modifier && $modifier->styler != null) $modifier->styler->applyStyles();
         echo ' >';
     }

     public static function placeLink(string $href, string $title, WebModifier | null $modifier = null){
         self::openLink($href, $modifier);
         echo $title;
         echo '</a>';
     }

     public static function placeImageWithWidth(string $src, int $width, WebModifier | null $modifier = null){
         echo '<img ';
         self::addAttribute("src", $src);
         if($modifier && $modifier->htmlModifier != null) $modifier->htmlModifier->applyModifiers();
         Styler::startAttribute();
         Styler::imageWithWidth($width);
         if($modifier && $modifier->styler != null) $modifier->styler->appendStyles();
         Styler::closeAttribute();
         self::closeTag();
     }

     public static function placeImageWithHeight(string $src, int $height, WebModifier | null $modifier = null){
         echo '<img ';
         self::addAttribute("src", $src);
         if($modifier && $modifier->htmlModifier != null) $modifier->htmlModifier->applyModifiers();
         Styler::startAttribute();
         Styler::imageWithHeight($height);
         if($modifier && $modifier->styler != null) $modifier->styler->appendStyles();
         Styler::closeAttribute();
         self::closeTag();
     }
 }