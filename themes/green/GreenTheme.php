<?php

class GreenTheme extends ThemesManager {
    public function headerTags(){
        self::importStyle($this->ROOT_PATH . "themes/green/styles.css");
        self::importJS($this->ROOT_PATH . "themes/green/scripts.js");
        $this->importCropperCSS();
        $this->importCropperJS();
        $this->importMainJSInterface();
        $this->importJoshButtons();
        $this->importGalleryGrids();
    }
}
