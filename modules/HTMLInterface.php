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

     public static function placeImage(string $src, WebModifier | null $modifier = null){
         echo '<img ';
         self::addAttribute("src", $src);
         self::applyModifiers($modifier);
         self::applyStyles($modifier);
         self::closeTag();
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

     public static function placeSquareImage(string $src, int $size, WebModifier | null $modifier = null){
         echo '<img ';
         self::addAttribute("src", $src);
         self::applyModifiers($modifier);
         Styler::startAttribute();
         Styler::imageSquare($size);
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

     public static function addAbsoluteIconButton(string $imageSrc, array $positionStyles, string $rawOnclick = ""){
         echo '<div style=" ';
         Styler::addStyle("position", "fixed");
         Styler::addStyle("cursor", "pointer");
         foreach ($positionStyles as $psKey => $psValue){
             Styler::addStyle($psKey, $psValue);
         }
         Styler::closeAttribute();
         HTMLInterface::addAttribute("class", "img-button");
         if($rawOnclick) HTMLInterface::addAttribute("onclick", $rawOnclick);
         echo ' >';
         echo '<img src="' . $imageSrc . '" alt="Icon" style="width: 50px; height: 50px; border-radius: 50%;">';
         echo '</div>';
     }

     public static function absPlace(Placeable $placeable,
                 string $right = "", string $left = "", string $top = "", string $bottom = ""){

         $styler = new Styler();
         $styler->pushStyle("position", "fixed");
         if($left) $styler->pushStyle("left", $left);
         if($right) $styler->pushStyle("right", $right);
         if($top) $styler->pushStyle("top", $top);
         if($bottom) $styler->pushStyle("bottom", $bottom);

         $webModifier = new WebModifier(null, $styler);
         $placeable->place($webModifier);
     }
 }