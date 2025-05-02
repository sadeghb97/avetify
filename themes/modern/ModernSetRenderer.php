<?php

abstract class ModernSetRenderer extends SetRenderer {
    public function __construct(SetModifier $setModifier, ModernTheme $theme,
                                string $title = "Set", bool | int $limit = 5000){
        parent::__construct($setModifier, $theme, $title, $limit);
    }

    public function openContainer() {
        $niceDiv = new VertDiv(0);
        $niceDiv->addStyle("margin-top", "16px");
        $niceDiv->open();
        Printer::boldPrint($this->getTitle());
        $niceDiv->close();
        echo '<div class="container" style="margin-top: 12px;">';
    }

    public function closeContainer() {
        echo '</div>';
    }
}
