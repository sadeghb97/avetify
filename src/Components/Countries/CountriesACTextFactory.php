<?php
namespace Avetify\Components\Countries;

class CountriesACTextFactory {
    public bool $disableAutoSubmit = true;

    public function __construct(
        public string $fieldKey = "",
        public string $childKey = "",
        public string $initValue = "",
        public string $callbackName = "onSelectCountry"
    ){
    }

    public function modifyField(CountriesACTextField $baseField) : CountriesACTextField {
        $baseField->fieldKey = $this->fieldKey;
        $baseField->childKey = $this->childKey;
        $baseField->initValue = $this->initValue;
        $baseField->enterCallbackName = $this->callbackName;
        $baseField->disableSubmitOnEnter = $this->disableAutoSubmit;
        return $baseField;
    }

    public function createBase() : CountriesACTextField {
        return new CountriesACTextField();
    }

    public function create() : CountriesACTextField {
        $baseField = $this->createBase();
        return $this->modifyField($baseField);
    }
}
