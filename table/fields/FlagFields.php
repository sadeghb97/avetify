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
        $countrySelector = new CountrySelector(
            $this->getEditableFieldIdentifier($item),
            $this->getCountriesACFactory("countries-actext", $this->getEditableFieldIdentifier($item)),
            "",
            true,
            $this->getValue($item)
        );
        $countrySelector->place();
    }

    public function getCountriesACFactory(
        string $fieldKey,
        string $childKey,
        string $initValue = "",
        string $callbackName = "onSelectCountry"
    ) : CountriesACTextFactory {
        return new CountriesACTextFactory($fieldKey, $childKey, $initValue, $callbackName);
    }
}
