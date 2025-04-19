<?php

class FlagField extends SBTableField {
    public function presentValue($item) {
        $countryCode = $this->getValue($item);
        $flag = World::getCountryFlag($countryCode);

        if($flag){
            HTMLInterface::placeImageWithHeight($flag, 50);
        }
    }
}
