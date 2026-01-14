<?php
namespace Avetify\Table\Fields\FlagFields;

use Avetify\Components\Countries\CountriesACTextFactory;
use Avetify\Components\Countries\CountrySelector;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\EditableFields\EditableField;

class VisualSelectCountryField extends EditableField {
    public function presentValue($item, ?WebModifier $webModifier = null){
        $countrySelector = new CountrySelector(
            $this->getElementIdentifier($item),
            $this->getCountriesACFactory("countries-actext", $this->getElementIdentifier($item)),
            "",
            true,
            $this->getValue($item)
        );
        $countrySelector->place($webModifier);
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
