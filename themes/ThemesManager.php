<?php

class ThemesManager {
    public string $ROOT_PATH = "";

    public function __construct(){
        global $AVENTADOR_ROOT_PATH;
        $this->ROOT_PATH = $AVENTADOR_ROOT_PATH;
    }

    public function placeHeader($title){
        self::openHead();
        self::setHeaderTitle($title);
        $this->headerTags();
        self::closeHead();
    }

    public function headerTags(){
        $this->importBootstrap();
    }

    public function loadFavicon(){}

    public function loadHeaderElements(){}

    public static function importFavicon($path, $type = "image/png", $sizes = "32x32"){
        echo '<link rel="icon" href="' . $path . '" type="' . $type . '" sizes="' . $sizes . '">';
    }

    public static function importStyle($path){
        echo '<link rel="stylesheet" type="text/css" href="' . $path . '">';
    }

    public static function importJS($path){
        echo '<script type="text/javascript" src="' . $path . '"></script>';
    }

    public static function importCdnStyle($path, $integrity = null){
        echo '<link rel="stylesheet" href="' . $path . '" ';
        if($integrity != null) {
            echo ' integrity="' . $integrity . '" crossorigin="anonymous" referrerpolicy="no-referrer" ';
        }
        echo ' />';
    }

    public static function importCdnJS($path, $integrity = null){
        echo '<script src="' . $path . '" ';
        if($integrity != null) {
            echo ' integrity="' . $integrity . '" crossorigin="anonymous" referrerpolicy="no-referrer" ';
        }
        echo '></script>';
    }

    public function importCropperCSS(){
        self::importStyle($this->ROOT_PATH . "externals/cropper/cropper.min.css");
    }

    public function importCropperJS(){
        self::importJS($this->ROOT_PATH . "externals/cropper/cropper.min.js");
    }

    public function importAvnJSFields(){
        self::importJS($this->ROOT_PATH . "fields/fields.js");
    }

    public function importMainJSInterface(){
        self::importJS($this->ROOT_PATH . "themes/assets/interface.js");
    }

    public function importBootstrap(){
        self::importStyle($this->ROOT_PATH . "externals/bootstrap.min.css");
    }

    public function importJoshButtons(){
        self::importStyle($this->ROOT_PATH . "themes/assets/josh_buttons.css");
    }

    public function importGalleryGrids(){
        self::importStyle($this->ROOT_PATH . "themes/assets/gallery_grid.css");
    }

    public function importContextMenuStyles(){
        self::importStyle($this->ROOT_PATH . "themes/assets/context_menu.css");
    }

    public function importGeneralFonts(){
        self::importStyle($this->ROOT_PATH . "assets/fonts/fonts.css");
    }

    public static function openHead(){
        echo '<head>';
    }

    public static function closeHead(){
        echo '</head>';
    }

    public static function setHeaderTitle($title){
        echo '<title>';
        echo $title;
        echo '</title>';
    }
}
