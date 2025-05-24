<?php
namespace Avetify\Themes\Modernix;

use Avetify\AvetifyManager;
use Avetify\Themes\Green\GreenNavigationRenderer;
use Avetify\Themes\Main\Navigations\NavigationBar;
use Avetify\Themes\Main\Navigations\NavigationRenderer;
use Avetify\Themes\Main\ThemesManager;

class ModernixTheme extends ThemesManager {
    public function moreHeaderTags(){
        self::importStyle(AvetifyManager::assetUrl("themes/modern/styles.css"));
        self::importStyle(AvetifyManager::assetUrl("themes/modern/card_styles.css"));
        self::importJS(AvetifyManager::assetUrl("themes/modern/scripts.js"));
    }

    public function getNavigationRenderer(NavigationBar $navigationBar): ?NavigationRenderer {
        return new GreenNavigationRenderer($navigationBar);
    }
}
