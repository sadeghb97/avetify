<?php

class FlagField extends SBTableField {
    public function presentValue($item) {
        $countryCode = $this->getValue($item);
        $country = World::getCountry($countryCode);
        $flag = World::getCountryFlag($countryCode);

        if($flag){
            $countryLink = $this->getCountryLink($country);

            if($countryLink){
                echo '<a ';
                HTMLInterface::addAttribute("href", $countryLink);
                HTMLInterface::addAttribute("target", "_blank");
                HTMLInterface::closeTag();
            }

            $flagModifier = WebModifier::createInstance();
            $flagModifier->htmlModifier->pushModifier("title", $country['short_name']);
            HTMLInterface::placeImageWithHeight($flag, 50, $flagModifier);

            if($countryLink) HTMLInterface::closeLink();
        }
    }

    public function getCountryLink($country): string {
        return "";
    }
}

class VisualSelectCountryField extends SBEditableField {
    public function presentValue($item){
        $countryCode = $this->getValue($item);
        $countryDetails = World::getCountry($countryCode);
        $countryFlag = World::getCountryFlag($countryCode);
        $countryName = $countryDetails ? $countryDetails['short_name'] : "";

        $div = new NiceDiv(6);
        $div->open();

        echo '<input ';
        HTMLInterface::addAttribute("type", "hidden");
        if($item != null) {
            HTMLInterface::addAttribute("value", $countryCode);
        }
        $this->setFieldIdentifiers($item);
        HTMLInterface::closeSingleTag();

        $acTextField = $this->getCountriesACTextField("countries-actext",
            $this->getEditableFieldIdentifier($item), "");
        $acTextField->presentListField();
        $div->separate();

        $countryLink = "";
        $preLink = $acTextField->getPreCountryLink();
        $postLink = $acTextField->getPostCountryLink();
        if($preLink || $postLink){
            $countryLink = $preLink . $countryCode . $postLink;
        }

        if($countryLink){
            echo '<a ';
            HTMLInterface::addAttribute("href", $countryLink);
            HTMLInterface::addAttribute("id", $this->getEditableFieldIdentifier($item) . "_link");
            HTMLInterface::addAttribute("target", "_blank");
            HTMLInterface::closeTag();
        }

        $flagModifier = new WebModifier(new HTMLModifier(), null);
        $flagModifier->htmlModifier->pushModifier("id",
            $this->getEditableFieldIdentifier($item) . "_flag");
        $flagModifier->htmlModifier->pushModifier("title", $countryName);
        HTMLInterface::placeImageWithHeight($countryFlag ? $countryFlag : "", 50, $flagModifier);

        if($countryLink) HTMLInterface::closeLink();

        $div->close();
    }

    public function getCountriesACTextField(string $fieldKey, string $childKey,
                                            string $initValue, string $callbackName = "onSelectCountry"
    ) : CountriesACTextField {
        return new CountriesACTextField($fieldKey, $childKey, $initValue, $callbackName);
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

    public function callbackMoreData(): array {
        return [
            "pre_link" => $this->getPreCountryLink(),
            "post_link" => $this->getPostCountryLink()
        ];
    }

    public function getPreCountryLink(): string {
        return "";
    }

    public function getPostCountryLink(): string {
        return "";
    }
}

class CountriesDatalist extends JSDatalist {
    public function __construct(array $countries){
        parent::__construct("avn_countries_datalist", $countries,
            "alpha2", "short_name");
    }
}
