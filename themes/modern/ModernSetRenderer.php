<?php

abstract class ModernSetRenderer extends SetRenderer {
    public function __construct(SetModifier $setModifier){
        parent::__construct($setModifier, new ModernTheme());
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
