<?php

class ModernTheme extends ThemesManager {
    public function moreHeaderTags(){
        self::importStyle(AssetsManager::getAsset("themes/modern/styles.css"));
        self::importStyle(AssetsManager::getAsset("themes/modern/card_styles.css"));
        self::importJS(AssetsManager::getAsset("themes/modern/scripts.js"));
    }
}
