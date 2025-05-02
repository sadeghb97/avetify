<?php

class ThemesManager {
    public bool $includesListerTools = false;
    public bool $includesCropperTools = false;
    public string $ROOT_PATH = "";
    public ?NavigationRenderer $navigationRenderer = null;

    public function __construct(){
        global $AVETIFY_ROOT_PATH;
        $this->ROOT_PATH = $AVETIFY_ROOT_PATH;

        $navigationBar = $this->getNavigationBar();
        if($navigationBar){
            $this->navigationRenderer = $this->getNavigationRenderer($navigationBar);
        }

        $this->postConstruct();
    }

    public function postConstruct(){}

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
        $this->importMainJSInterface();
        $this->importAvtJSFields();
        $this->importJoshButtons();
        $this->importGeneralFonts();
        $this->importContextMenuStyles();
        $this->importGalleryGrids();
        if($this->includesListerTools) self::importListerTools();
        if($this->includesCropperTools) self::importCropperTools();
    }

    public function loadFavicon(){}

    public function loadHeaderElements(){
        if($this->navigationRenderer != null){
            $this->navigationRenderer->place();
        }
    }

    public function getNavigationBar() : ?NavigationBar {
        return null;
    }

    public function getNavigationRenderer(NavigationBar $navigationBar) : ?NavigationRenderer {
        return null;
    }

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

    public static function importCropperTools(){
        self::importStyle(Routing::browserPathFromAvetify("externals/cropper/cropper.min.css"));
        self::importJS(Routing::browserPathFromAvetify("externals/cropper/cropper.min.js"));
        self::importJS(Routing::browserPathFromAvetify("themes/assets/avt_cropper.js"));
    }

    public function importAvtJSFields(){
        self::importJS($this->ROOT_PATH . "fields/fields.js");
    }

    public function importMainJSInterface(){
        self::importJS($this->ROOT_PATH . "themes/assets/interface.js");
    }

    public static function importMainStyles(){
        self::importStyle(Routing::browserPathFromAvetify("themes/assets/main.css"));
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

    public static function importListerTools(){
        self::importContextMenuStyles();
        self::importListerStyles();
        self::importSortableJS();
    }

    public static function importContextMenuStyles(){
        self::importStyle(Routing::browserPathFromAvetify("themes/assets/context_menu.css"));
    }

    public static function importListerStyles(){
        self::importStyle(Routing::browserPathFromAvetify("lister/lister.css"));
    }

    public static function importSortableJS(){
        ThemesManager::importJS(Routing::browserPathFromAvetify("lister/sortable.js"));
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
