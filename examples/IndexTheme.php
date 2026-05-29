<?php
use Avetify\Themes\Main\ThemesManager;

class IndexTheme extends ThemesManager {
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
