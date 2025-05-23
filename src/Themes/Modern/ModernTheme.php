<?php
namespace Avetify\Themes\Modern;

use Avetify\AvetifyManager;
use Avetify\Themes\Main\ThemesManager;

class ModernTheme extends ThemesManager {
    public function moreHeaderTags(){
        self::importStyle(AvetifyManager::assetUrl("themes/modern/styles.css"));
        self::importStyle(AvetifyManager::assetUrl("themes/modern/card_styles.css"));
        self::importJS(AvetifyManager::assetUrl("themes/modern/scripts.js"));
    }
}
