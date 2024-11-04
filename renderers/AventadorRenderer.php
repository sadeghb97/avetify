<?php

class AventadorRenderer implements PageRenderer {
    public function renderPage($title = "Aventador") {
        $theme = new ClassicTheme();
        $theme->placeHeader($title);
        $avnImage = Routing::getAventadorRoot() . "assets/img/aventador.jpg";

        HTMLInterface::openContainer();
        $div = new NiceDiv(8);
        $div->open();
        HTMLInterface::placeImageWithWidth($avnImage, 400);
        $div->close();
        HTMLInterface::closeDiv();
    }
}
