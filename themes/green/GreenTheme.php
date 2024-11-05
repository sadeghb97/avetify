<?php

class GreenTheme extends ThemesManager {
    public function headerTags(){
        self::importStyle($this->ROOT_PATH . "themes/green/styles.css");
        self::importJS($this->ROOT_PATH . "themes/green/scripts.js");
        $this->importJoshButtons();
    }
}
