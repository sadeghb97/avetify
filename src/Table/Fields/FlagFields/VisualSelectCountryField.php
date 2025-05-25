<?php
namespace Avetify\Table\Fields\FlagFields;

use Avetify\Components\Countries\CountriesACTextFactory;
use Avetify\Components\Countries\CountrySelector;
use Avetify\Table\Fields\EditableFields\EditableField;

class VisualSelectCountryField extends EditableField {
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
