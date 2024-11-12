<?php
 class HTMLInterface {
     public static function addAttribute($attr, $value = null){
         echo ' ' . $attr;
         if($value) echo '="' . $value . '"';
         echo ' ';
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
         self::applyModifiers($modifier);
         Styler::startAttribute();
         Styler::addStyle("width", $width);
         Styler::addStyle("max-width", $maxWidth);
         self::appendStyles($modifier);
         Styler::closeAttribute();
         self::closeTag();
     }

     public static function openLink(string $href, WebModifier | null $modifier = null){
         echo '<a ';
         self::addAttribute("href", $href);
         self::applyModifiers($modifier);
         if($modifier && $modifier->styler != null) $modifier->styler->applyStyles();
         echo ' >';
     }

     public static function placeLink(string $href, string $title, WebModifier | null $modifier = null){
         self::openLink($href, $modifier);
         echo $title;
         echo '</a>';
     }

     public static function placeSimpleLink(string $href, string $title){
         $htmlModifier = new HTMLModifier();
         $stylesModifier = new Styler();
         $webModifiers = new WebModifier($htmlModifier, $stylesModifier);
         $stylesModifier->pushStyle("color", "black");

         self::openLink($href, $webModifiers);
         echo $title;
         echo '</a>';
     }

     public static function placeImageWithWidth(string $src, int $width, WebModifier | null $modifier = null){
         echo '<img ';
         self::addAttribute("src", $src);
         self::applyModifiers($modifier);
         Styler::startAttribute();
         Styler::imageWithWidth($width);
         self::appendStyles($modifier);
         Styler::closeAttribute();
         self::closeTag();
     }

     public static function placeImageWithHeight(string $src, int $height, WebModifier | null $modifier = null){
         echo '<img ';
         self::addAttribute("src", $src);
         self::applyModifiers($modifier);
         Styler::startAttribute();
         Styler::imageWithHeight($height);
         self::appendStyles($modifier);
         Styler::closeAttribute();
         self::closeTag();
     }

     public static function placeElement(string $element, string $text, WebModifier | null $modifier = null){
         echo '<';
         echo $element . ' ';
         self::applyModifiers($modifier);
         self::applyStyles($modifier);
         self::closeTag();
         echo $text;
         echo '</';
         echo $element;
         echo '>';
     }

     public static function placeSpan(string $text, WebModifier | null $modifier = null){
         self::placeElement("span", $text, $modifier);
     }

     public static function placeDiv(string $text, WebModifier | null $modifier = null){
         self::placeElement("div", $text, $modifier);
     }

     public static function appendStyles(WebModifier | null $modifier = null){
         if($modifier && $modifier->styler != null) $modifier->styler->appendStyles();
     }

     public static function applyStyles(WebModifier | null $modifier = null){
         if($modifier && $modifier->styler != null) $modifier->styler->applyStyles();
     }

     public static function applyModifiers(WebModifier | null $modifier = null){
         if($modifier && $modifier->htmlModifier != null) $modifier->htmlModifier->applyModifiers();
     }
 }