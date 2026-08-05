<?php
namespace Avetify\Interface;

use Avetify\Themes\Main\AvtTheme;

interface PageRenderer {
    public function getTheme() : AvtTheme;
    public function renderBody();
    public function renderPage(?string $title = null);
}
