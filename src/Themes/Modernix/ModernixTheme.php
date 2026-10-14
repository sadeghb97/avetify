<?php
namespace Avetify\Themes\Modernix;

use Avetify\AvetifyManager;
use Avetify\Themes\Main\AvtTheme;

class ModernixTheme extends AvtTheme {
    public function moreHeaderTags(){
        self::importStyle(AvetifyManager::assetUrl("themes/modern/styles.css"));
        self::importStyle(AvetifyManager::assetUrl("themes/modern/card_styles.css"));
        self::importJS(AvetifyManager::assetUrl("themes/modern/scripts.js"));
    }
}
