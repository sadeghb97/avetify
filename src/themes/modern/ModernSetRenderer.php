<?php

abstract class ModernSetRenderer extends SetRenderer {
    public function __construct(SetModifier $setModifier, ThemesManager $theme,
                                string $title = "Set", bool | int $limit = 5000){
        parent::__construct($setModifier, $theme, $title, $limit);
    }

    public function openContainer() {
        $niceDiv = new VertDiv(0);
        $niceDiv->addStyle("margin-top", "16px");
        $niceDiv->open();
        Printer::boldPrint($this->getTitle());
        $niceDiv->close();
        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("container");
        Styler::closeAttribute();
        Styler::startAttribute();
        Styler::addStyle(CSS::marginTop, "12px");
        Styler::addStyle(CSS::paddingBottom, "12px");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
    }

    public function closeContainer() {
        echo '</div>';
    }
}
