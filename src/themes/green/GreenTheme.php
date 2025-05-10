<?php

class GreenTheme extends ThemesManager {
    public function getNavigationRenderer(NavigationBar $navigationBar): ?NavigationRenderer {
        return new GreenNavigationRenderer($navigationBar);
    }

    public function moreHeaderTags(){
        self::importStyle(AssetsManager::getAsset("themes/green/styles.css"));
        self::importJS(AssetsManager::getAsset("themes/green/scripts.js"));
    }
}
