<?php

class GreenTheme extends ThemesManager {
    public function postConstruct() {
        $navigationBar = $this->getNavigationBar();
        if($navigationBar){
            $this->navigationRenderer = new GreenNavigationRenderer($navigationBar);
        }
    }

    public function moreHeaderTags(){
        self::importStyle(Routing::browserPathFromAvetify("themes/green/styles.css"));
        self::importJS(Routing::browserPathFromAvetify("themes/green/scripts.js"));
    }
}
