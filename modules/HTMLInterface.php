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

     public static function placeText(string $content, WebModifier | null $modifier = null){
         echo '<span ';
         if($modifier && $modifier->htmlModifier) $modifier->htmlModifier->applyModifiers();
         Styler::startAttribute();
         if($modifier && $modifier->styler) $modifier->styler->appendStyles();
         Styler::closeAttribute();
         HTMLInterface::closeTag();
         echo $content;
         echo '</span>';
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

     public static function placeVerticalDivider(int $height){
         echo '<div ';
         Styler::startAttribute();
         Styler::addStyle("height", $height . "px");
         Styler::closeAttribute();
         HTMLInterface::closeTag();
         HTMLInterface::closeDiv();
     }

     public static function placeHorizontalDivider(int $width){
         echo '<span ';
         Styler::startAttribute();
         Styler::addStyle("display", "inline-block");
         Styler::addStyle("width", $width . "px");
         Styler::closeAttribute();
         HTMLInterface::closeTag();
         echo '</span>';
     }

     public static function placeFullACText(string $id, string $listId, array $list){
         self::placeACText($id, $listId);
         self::placeDatalist($listId, $list);
     }

     public static function placeACText(string $id, string $listId){
         echo '<input ';
         HTMLInterface::addAttribute("list", $listId);
         HTMLInterface::addAttribute("id", $id);
         HTMLInterface::closeSingleTag();
     }

     public static function placeHiddenField(string $id, string $value, WebModifier | null $modifier = null){
         echo '<input ';
         HTMLInterface::addAttribute("type", "hidden");
         self::applyInputIdentifiers(true, true, $id);
         HTMLInterface::addAttribute("value", $value);
         if($modifier && $modifier->htmlModifier) $modifier->htmlModifier->applyModifiers();
         HTMLInterface::closeSingleTag();
     }

     public static function placeSimpleInput(string $id, string $value, string $label = "",
                                             WebModifier | null $modifier = null,
                                             $idSet = true, $nameSet = true){
         echo '<input ';
         HTMLInterface::addAttribute("type", "text");
         self::applyInputIdentifiers($idSet, $nameSet, $id);
         HTMLInterface::addAttribute("value", $value);
         if($label) HTMLInterface::addAttribute("placeholder", $label);
         if($modifier && $modifier->htmlModifier) $modifier->htmlModifier->applyModifiers();

         Styler::startAttribute();
         if($modifier && $modifier->styler) $modifier->styler->appendStyles();
         Styler::closeAttribute();
         HTMLInterface::closeSingleTag();
     }

     public static function placeSimpleCheckBox(string $id, string $value, string $label = "",
                                                WebModifier | null $modifier = null,
                                                $idSet = true, $nameSet = true){
         if($label) echo $label . "&nbsp;";
         echo '<input ';
         HTMLInterface::addAttribute("type", "checkbox");
         self::applyInputIdentifiers($idSet, $nameSet, $id);
         if(!empty($value)) HTMLInterface::addAttribute("checked", "true");
         if($modifier && $modifier->htmlModifier) $modifier->htmlModifier->applyModifiers();

         Styler::startAttribute();
         if($modifier && $modifier->styler) $modifier->styler->appendStyles();
         Styler::closeAttribute();
         HTMLInterface::closeSingleTag();
     }

     public static function placePostInput(string $id, string $value, string $label = "",
                                           WebModifier | null $modifier = null){
         self::placeSimpleInput($id, $value, $label, $modifier, false, true);
     }

     public static function placeFormInput(string $id, string $value, string $label = "",
                                           WebModifier | null $modifier = null){
         self::placeSimpleInput($id, $value, $label, $modifier, true, false);
     }

     public static function placePostCheckbox(string $id, string $value, string $label = "",
                                              WebModifier | null $modifier = null){
         self::placeSimpleCheckBox($id, $value, $label, $modifier, false, true);
     }

     public static function placeFormCheckbox(string $id, string $value, string $label = "",
                                              WebModifier | null $modifier = null){
         self::placeSimpleCheckBox($id, $value, $label, $modifier, true, false);
     }

     public static function placeDatalist(string $listId, array $list){
         echo '<datalist ';
         HTMLInterface::addAttribute("id", $listId);
         HTMLInterface::closeTag();
         foreach ($list as $item){
             echo '<option ';
             HTMLInterface::addAttribute("value", $item);
             HTMLInterface::closeTag();
         }
         echo '</datalist>';
     }

     public static function applyInputIdentifiers($idSet, $nameSet, $identifier){
         if($idSet) HTMLInterface::addAttribute("name", $identifier);
         if($nameSet) HTMLInterface::addAttribute("id", $identifier);
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
         $absButton = new AbsoluteButton($imageSrc, $positionStyles, $rawOnclick);
         $absButton->place();
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