<?php

class GreenTheme extends ThemesManager {
    public function getNavigationRenderer(NavigationBar $navigationBar): ?NavigationRenderer {
        return new GreenNavigationRenderer($navigationBar);
    }

    public function moreHeaderTags(){
        self::importStyle(AvetifyManager::assetUrl("themes/green/styles.css"));
        self::importJS(AvetifyManager::assetUrl("themes/green/scripts.js"));
    }
}
