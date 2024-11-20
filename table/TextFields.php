<?php

class TitleCaseField extends SBTableSimpleField {
    public function presentValue($item) {
        $val = $this->getValue($item);
        $val = str_replace("_", " ", $val);
        echo ucwords($val);
    }
}