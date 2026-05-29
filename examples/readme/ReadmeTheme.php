<?php
use Avetify\Themes\Main\ThemesManager;

class ReadmeTheme extends ThemesManager {
    public function __construct() {
        parent::__construct();
        $this->includesMarkdownTools = true;
        $this->noNavigationMenu = true;
    }
}
