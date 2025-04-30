<?php

class ModernTheme extends ThemesManager {
    public function moreHeaderTags(){
        self::importStyle(Routing::browserPathFromAvetify("themes/modern/styles.css"));
        self::importStyle(Routing::browserPathFromAvetify("themes/modern/card_styles.css"));
        self::importJS(Routing::browserPathFromAvetify("themes/modern/scripts.js"));
        self::importJS(Routing::browserPathFromAvetify("themes/modern/oldselect.js"));
    }
}
