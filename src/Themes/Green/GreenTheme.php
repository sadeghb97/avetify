<?php
namespace Avetify\Themes\Green;

use Avetify\AvetifyManager;
use Avetify\Themes\Main\Navigations\NavigationBar;
use Avetify\Themes\Main\Navigations\NavigationRenderer;
use Avetify\Themes\Main\ThemesManager;

class GreenTheme extends ThemesManager {
    public function getNavigationRenderer(NavigationBar $navigationBar): ?NavigationRenderer {
        return new GreenNavigationRenderer($navigationBar);
    }

    public function moreHeaderTags(){
        self::importStyle(AvetifyManager::assetUrl("themes/green/styles.css"));
        self::importJS(AvetifyManager::assetUrl("themes/green/scripts.js"));
    }
}
