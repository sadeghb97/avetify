<?php

abstract class ModernSetRenderer extends SetRenderer {
    public function __construct(SetModifier $setModifier){
        parent::__construct($setModifier, new ModernTheme());
    }

    public function openContainer() {
        echo '<div class="container">';
    }

    public function closeContainer() {
        echo '</div>';
    }
}
