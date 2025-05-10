<?php

class CountrySelector implements Placeable {
    public bool $setNameIdentifier = false;

    public function __construct(public string $mainElementId,
                                public CountriesACTextFactory $countriesACFactory,
                                public string $label,
                                public bool $disableAutoSubmit,
                                public string $initCountryCode){
    }


    public function place(WebModifier $webModifier = null) {
        $countryDetails = World::getCountry($this->initCountryCode);
        $countryFlag = World::getCountryFlag($this->initCountryCode);
        $countryName = $countryDetails ? $countryDetails['short_name'] : "";

        $div = new NiceDiv(6);
        $div->open($webModifier);

        echo '<input ';
        HTMLInterface::addAttribute("type", "hidden");
        if($countryDetails != null) {
            HTMLInterface::addAttribute("value", $this->initCountryCode);
        }
        HTMLInterface::addAttribute("id", $this->mainElementId);
        if($this->setNameIdentifier) HTMLInterface::addAttribute("name", $this->mainElementId);
        HTMLInterface::closeSingleTag();

        $acTextField = $this->countriesACFactory->create();
        $acTextField->label = $this->label;
        $acTextField->place();
        $div->separate();

        $countryLink = "";
        $preLink = $acTextField->getPreCountryLink();
        $postLink = $acTextField->getPostCountryLink();
        if($preLink || $postLink){
            $countryLink = $preLink . $this->initCountryCode . $postLink;
        }

        if($countryLink){
            echo '<a ';
            HTMLInterface::addAttribute("href", $countryLink);
            HTMLInterface::addAttribute("id", $this->mainElementId . "_link");
            HTMLInterface::addAttribute("target", "_blank");
            HTMLInterface::closeTag();
        }

        $flagModifier = new WebModifier(new HTMLModifier(), null);
        $flagModifier->htmlModifier->pushModifier("id", $this->mainElementId . "_flag");
        $flagModifier->htmlModifier->pushModifier("title", $countryName);
        HTMLInterface::placeImageWithHeight($countryFlag ? $countryFlag : "", 50, $flagModifier);

        if($countryLink) HTMLInterface::closeLink();

        $div->close();
    }
}

class CountriesACTextField extends JSACTextField {
    /**
     * @param string $childKey id elemente asli ke dar bargirande country code hast va mamulan hidden ast.
     * yadavari: az tarkibe fieldKey va childKey id elemente inpute ac text sakhte mishavad.
     */
    public function __construct(string $fieldKey = "", string $childKey = "", string $initValue = "",
                                string $enterCallbackName = "onSelectCountry"){
        parent::__construct($fieldKey, $childKey, $initValue, World::getCountriesDatalist());
        $this->enterCallbackName = $enterCallbackName;
    }

    public function callbackMoreData(): array {
        return [
            "pre_link" => $this->getPreCountryLink(),
            "post_link" => $this->getPostCountryLink(),
            "disable_auto_submit" => $this->disableSubmitOnEnter,
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
        parent::__construct("avt_countries_datalist", $countries,
            "alpha2", "short_name");
    }
}

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
