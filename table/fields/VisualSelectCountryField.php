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

        $acTextField = new CountriesACTextField("countries-actext",
            $this->getEditableFieldIdentifier($item), "");
        $acTextField->presentListField();

        $div->separate();
        $flag = World::getCountryFlag($this->getValue($item));
        $flagModifier = new WebModifier(new HTMLModifier(), null);
        $flagModifier->htmlModifier->pushModifier("id",
            $this->getEditableFieldIdentifier($item) . "_flag");
        HTMLInterface::placeImageWithHeight($flag ? $flag : "", 50, $flagModifier);

        $div->close();
    }
}

class CountriesACTextField extends JSACTextField {
    /**
     * @param string $childKey id elemente asli ke dar bargirande country code hast va mamulan hidden ast.
     * yadavari: az tarkibe fieldKey va childKey id elemente inpute ac text sakhte mishavad.
     */
    public function __construct(string $fieldKey, string $childKey, string $initValue,
                                string $enterCallbackName = "onSelectCountry"){
        parent::__construct($fieldKey, $childKey, $initValue, World::getCountriesDatalistInfo());
        $this->enterCallbackName = $enterCallbackName;
    }
}

class CountriesDatalist extends JSDatalist {
    public function __construct(array $countries){
        parent::__construct("avn_countries_datalist", $countries,
            "alpha2", "short_name");
    }
}


