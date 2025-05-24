<?php

class ThemesManager {
    public bool $noNavigationMenu = false;
    public bool $includesListerTools = false;
    public bool $includesCropperTools = false;
    public bool $includesCodingFieldTools = false;
    public bool $includesHighlightCodesTools = false;
    public ?NavigationRenderer $navigationRenderer = null;

    public function __construct(){
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

    public function moreHeaderTags(){}

    public function generalHeaderTags(){
        $this->loadFavicon();
        self::importMainStyles();
        $this->importMainJSInterface();
        $this->importAvtJSFields();
        self::importAvtJSDialogs();
        $this->importJoshButtons();
        $this->importGeneralFonts();
        $this->importContextMenuStyles();
        $this->importGalleryGrids();
        if($this->includesListerTools) self::importListerTools();
        if($this->includesCropperTools) self::importCropperTools();
        if($this->includesCodingFieldTools) self::importCodingFieldTools();
        if($this->includesHighlightCodesTools) self::importHighlightCodeTools();

        if($this->navigationRenderer){
            $this->navigationRenderer->headImports();
        }
    }

    public function lateImports(){
        if($this->navigationRenderer){
            $this->navigationRenderer->lateImports();
        }
    }

    public function loadHeaderElements(){
        if(!$this->noNavigationMenu && $this->navigationRenderer != null){
            $this->navigationRenderer->place();
        }
        AvtDialog::place();
    }

    public function loadFavicon(){}

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

    public static function importListerTools(){
        self::importContextMenuStyles();
        self::importListerStyles();
        self::importSortableJS();
        self::importJS(AvetifyManager::assetUrl("components/lister/lister.js"));
    }

    public static function importCropperTools(){
        self::importStyle(AvetifyManager::assetUrl("components/cropper/cropper.min.css"));
        self::importJS(AvetifyManager::assetUrl("components/cropper/cropper.min.js"));
        self::importJS(AvetifyManager::assetUrl("components/cropper/avt_cropper.js"));
    }

    public static function importQuillEditor(){
        self::importStyle(AvetifyManager::assetUrl("components/quill/quill.snow.css"));
        self::importJS(AvetifyManager::assetUrl("components/quill/quill.min.js"));
    }

    public static function importCodingFieldTools(){
        self::importQuillEditor();
        self::importStyle(AvetifyManager::assetUrl("components/coding/quill_codes.css"));
        self::importJS(AvetifyManager::assetUrl("components/coding/quill_codes.js"));
    }

    public static function importHighlightCodeTools(){
        self::importStyle(AvetifyManager::assetUrl("components/highlight/agate.min.css"));
        self::importStyle(AvetifyManager::assetUrl("components/highlight/highlightjs-copy.min.css"));
        self::importJS(AvetifyManager::assetUrl("components/highlight/highlight.min.js"));
        self::importJS(AvetifyManager::assetUrl("components/highlight/highlightjs-copy.min.js"));
    }

    public static function importStandingsTools(){
        self::importStyle(AvetifyManager::assetUrl("components/standings/standings.css"));
    }

    public static function importMainJSInterface(){
        self::importJS(AvetifyManager::assetUrl("themes/main/js/interface.js"));
    }

    public static function importMainStyles(){
        self::importStyle(AvetifyManager::assetUrl("themes/main/css/main.css"));
    }

    public static function importAvtJSFields(){
        self::importJS(AvetifyManager::assetUrl("themes/main/js/fields.js"));
    }

    public static function importAvtJSDialogs(){
        self::importJS(AvetifyManager::assetUrl("themes/main/js/dialogs.js"));
    }

    public static function importBootstrap(){
        self::importStyle(AvetifyManager::assetUrl("components/bootstrap/bootstrap.min.css"));
    }

    public static function importJoshButtons(){
        self::importStyle(AvetifyManager::assetUrl("themes/main/css/josh_buttons.css"));
    }

    public static function importGalleryGrids(){
        self::importStyle(AvetifyManager::assetUrl("themes/main/css/gallery_grid.css"));
    }

    public static function importContextMenuStyles(){
        self::importStyle(AvetifyManager::assetUrl("themes/main/css/context_menu.css"));
    }

    public static function importListerStyles(){
        self::importStyle(AvetifyManager::assetUrl("components/lister/lister.css"));
    }

    public static function importSortableJS(){
        ThemesManager::importJS(AvetifyManager::assetUrl("components/lister/sortable.js"));
    }

    public static function importGeneralFonts(){
        self::importStyle(AvetifyManager::assetUrl("fonts/fonts.css"));
    }

    public function appendBodyStyles(){}

    public function openBody(){
        echo '<body ';
        Styler::startAttribute();
        $this->appendBodyStyles();
        Styler::closeAttribute();
        HTMLInterface::closeTag();
    }

    public static function closeBody(){
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
