<?php
namespace Avetify\Themes\Green;

use Avetify\AvetifyManager;
use Avetify\Themes\Main\AvtTheme;

class GreenTheme extends AvtTheme {
    public function moreHeaderTags(){
        self::importStyle(AvetifyManager::assetUrl("themes/green/styles.css"));
        self::importJS(AvetifyManager::assetUrl("themes/green/scripts.js"));
    }
}
