<?php

class ModernTheme extends ThemesManager {
    public function headerTags(){
        self::importStyle($this->ROOT_PATH . "themes/modern/styles.css");
        self::importStyle($this->ROOT_PATH . "themes/modern/card_styles.css");
        self::importJS($this->ROOT_PATH . "themes/green/scripts.js");
        self::importJS($this->ROOT_PATH . "themes/green/oldselect.js");
    }
}
