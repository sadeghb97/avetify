<?php

class GreenTheme extends ThemesManager {
    public function getNavigationRenderer(NavigationBar $navigationBar): ?NavigationRenderer {
        return new GreenNavigationRenderer($navigationBar);
    }

    public function moreHeaderTags(){
        self::importStyle(Routing::browserPathFromAvetify("themes/green/styles.css"));
        self::importJS(Routing::browserPathFromAvetify("themes/green/scripts.js"));
    }
}
