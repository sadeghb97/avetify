<?php

class AventadorRenderer implements PageRenderer {
    public function renderPage($title = "Aventador") {
        $theme = new GreenTheme();
        $theme->placeHeader($title);
        $avnImage = Routing::browserPathFromAventador("assets/img/aventador.webp");

        HTMLInterface::openContainer();
        $div = new VertDiv(8);
        $div->open();

        $imgDiv = new NiceDiv(8);
        $imgDiv->open();
        HTMLInterface::placeImageWithWidth($avnImage, 750);
        $imgDiv->close();
        $div->separate();

        $titleDiv = new NiceDiv(8);
        $titleDiv->open();
        $titleModifier = WebModifier::createInstance();
        $titleModifier->styler->pushFontFaceStyle("Queen");
        $titleModifier->styler->pushStyle("font-size", "2rem");
        HTMLInterface::placeText("Lamborghini Aventador", $titleModifier);
        $titleDiv->close();
        $div->separate();

        $versionDiv = new NiceDiv(8);
        $versionDiv->open();
        $versionModifier = WebModifier::createInstance();
        $versionModifier->styler->pushFontFaceStyle("Varsity");
        $versionModifier->styler->pushStyle("font-size", "1.25rem");
        HTMLInterface::placeText("Version: " . AVENTADOR_VERSION, $versionModifier);
        $versionDiv->close();

        $div->close();
        HTMLInterface::closeDiv();
    }
}
