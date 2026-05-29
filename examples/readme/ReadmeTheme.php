<?php
use Avetify\Themes\Modern\ModernTheme;

class ReadmeTheme extends ModernTheme {
    public function __construct() {
        parent::__construct();
        $this->includesMarkdownTools = true;
        $this->noNavigationMenu = true;
    }
}
