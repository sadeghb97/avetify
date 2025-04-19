<?php

class VisualSelectCountryField extends SBEditableField {
    public function presentValue($item){
        $div = new NiceDiv(6);
        $div->open();

        echo '<input ';
        HTMLInterface::addAttribute("type", "hidden");
        if($item != null) {
            HTMLInterface::addAttribute("value", $this->getValue($item));
        }
        $this->setFieldIdentifiers($item);
        HTMLInterface::closeSingleTag();

        $acTextField = new CountriesACTextField("actext",
            $this->getEditableFieldIdentifier($item), "");
        $acTextField->presentListField();

        if($item != null && $this->getValue($item)) {
            $div->separate();
            $flag = World::getCountryFlag($this->getValue($item));
            $flagModifier = new WebModifier(new HTMLModifier(), null);
            $flagModifier->htmlModifier->pushModifier("id",
                $this->getEditableFieldIdentifier($item) . "_flag");
            if($flag) HTMLInterface::placeImageWithHeight($flag, 50, $flagModifier);
        }

        $div->close();
    }
}

class CountriesACTextField extends JSACTextField {
    public function __construct(string $fieldKey, string $childKey, string $initValue){
        parent::__construct($fieldKey, $childKey, $initValue, World::getCountriesDatalistInfo());
        $this->enterCallbackName = "onSelectCountry";
    }
}

class CountriesDatalist extends JSDatalist {
    public function __construct(array $countries){
        parent::__construct("avn_countries_datalist", $countries,
            "alpha2", "short_name");
    }
}


