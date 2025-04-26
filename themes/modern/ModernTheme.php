<?php

class ModernTheme extends ThemesManager {
    public function moreHeaderTags(){
        self::importStyle(Routing::browserPathFromAventador("themes/modern/styles.css"));
        self::importStyle(Routing::browserPathFromAventador("themes/modern/card_styles.css"));
        self::importJS(Routing::browserPathFromAventador("themes/modern/scripts.js"));
        self::importJS(Routing::browserPathFromAventador("themes/modern/oldselect.js"));
    }
}
