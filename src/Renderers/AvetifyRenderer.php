<?php
namespace Avetify\Renderers;

use Avetify\AvetifyManager;
use Avetify\Components\Containers\NiceDiv;
use Avetify\Components\Containers\VertDiv;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\PageRenderer;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Themes\Green\GreenTheme;
use Avetify\Themes\Main\ThemesManager;

class AvetifyRenderer implements PageRenderer {
    public function renderPage($title = "Avetify") {
        $theme = $this->getTheme();
        $theme->placeHeader($title);
        $avtImage = AvetifyManager::imageUrl("avetify.webp");

        $theme->openBody();
        HTMLInterface::openContainer();
        $div = new VertDiv(8);
        $div->open();

        $imgDiv = new NiceDiv(8);
        $imgDiv->open();
        HTMLInterface::placeImageWithWidth($avtImage, 750);
        $imgDiv->close();
        $div->separate();

        $titleDiv = new NiceDiv(8);
        $titleDiv->open();
        $titleModifier = WebModifier::createInstance();
        $titleModifier->styler->pushFontFaceStyle("Queen");
        $titleModifier->styler->pushStyle("font-size", "2rem");
        HTMLInterface::placeText("Avetify", $titleModifier);
        $titleDiv->close();
        $div->separate();

        $versionDiv = new NiceDiv(8);
        $versionDiv->open();
        $versionModifier = WebModifier::createInstance();
        $versionModifier->styler->pushFontFaceStyle("Varsity");
        $versionModifier->styler->pushStyle("font-size", "1.25rem");
        HTMLInterface::placeText("Version: " . AvetifyManager::AVETIFY_VERSION, $versionModifier);
        $versionDiv->close();

        $div->close();
        HTMLInterface::closeDiv();
        $theme->closeBody();
    }

    public static function getTheme() : ThemesManager {
        return new class extends GreenTheme {
            public function appendBodyStyles() {
                Styler::addStyle("display", "flex");
                Styler::addStyle("justify-content", "center");
                Styler::addStyle("align-items", "center");
                Styler::addStyle("height", "100%");
            }
        };
    }
}