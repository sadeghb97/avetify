<?php

use Avetify\Themes\Green\GreenTheme;

class IndexTheme extends GreenTheme {
    public function __construct()
    {
        parent::__construct();
        $this->includesMarkdownTools = true;
        $this->noNavigationMenu = true;
    }

    public function render(string $title, callable $content): void
    {
        $this->openPage($title);
        $content();
        self::closeBody();
    }
}
