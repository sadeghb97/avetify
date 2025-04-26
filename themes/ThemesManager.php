<?php

class ThemesManager {
    public bool $includesListerTools = false;
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
        $this->generalHeaderTags();
        $this->moreHeaderTags();
    }

    public function moreHeaderTags(){

    }

    public function generalHeaderTags(){
        $this->loadFavicon();
        self::importMainStyles();
        $this->importCropperCSS();
        $this->importCropperJS();
        $this->importMainJSInterface();
        $this->importAvnJSFields();
        $this->importJoshButtons();
        $this->importGeneralFonts();
        $this->importContextMenuStyles();
        $this->importGalleryGrids();
        if($this->includesListerTools) $this->importListerTools();
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

    public static function importMainStyles(){
        self::importStyle(Routing::browserPathFromAventador("themes/assets/main.css"));
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

    public function importListerTools(){
        $this->importContextMenuStyles();
        $this->importListerStyles();
        $this->importSortableJS();
    }

    public function importContextMenuStyles(){
        self::importStyle($this->ROOT_PATH . "themes/assets/context_menu.css");
    }

    public function importListerStyles(){
        self::importStyle(Routing::getAventadorRoot() . "lister/lister.css");
    }

    public function importSortableJS(){
        ThemesManager::importJS(Routing::getAventadorRoot() . "lister/sortable.js");
    }

    public function importGeneralFonts(){
        self::importStyle($this->ROOT_PATH . "assets/fonts/fonts.css");
    }

    public function appendBodyStyles(){}

    public function openBody(){
        echo '<body ';
        Styler::startAttribute();
        $this->appendBodyStyles();
        Styler::closeAttribute();
        HTMLInterface::closeTag();
    }

    public function closeBody(){
        echo '</body>';
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
