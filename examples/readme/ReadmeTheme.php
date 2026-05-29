<?php

use Avetify\Themes\Green\GreenTheme;

class ReadmeTheme extends GreenTheme {
    public function __construct() {
        parent::__construct();
        $this->includesMarkdownTools = true;
        $this->noNavigationMenu = true;
    }
}
