<?php
namespace Avetify\Interface;

use Avetify\Themes\Main\ThemesManager;

interface PageRenderer {
    public function getTheme() : ThemesManager;
    public function renderBody();
    public function renderPage(?string $title = null);
}
