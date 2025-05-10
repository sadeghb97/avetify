<?php

class CountriesTable extends SBTable {
    public bool $isEditable = false;
    public bool $includesCode = true;
    public bool $includesName = true;
    public bool $includesPerName = true;
    public bool $includesContinent = true;
    public bool $includesSubRegion = true;

    public function __construct(){
        parent::__construct([], $this->fetchCountries(), "countries");

        $fieldsList = [];
        $fieldsList[] = ($this->getFlagField("alpha2"));
        if($this->includesCode) $fieldsList[] = (new SBTableSimpleField("Code", "alpha2"));
        if($this->includesName) $fieldsList[] = (new SBTableSimpleField("Name", "short_name"))->setMaxWidth("200px");
        if($this->includesPerName) $fieldsList[] = (new SBTableSimpleField("PerName", "per_name"))->setMaxWidth("200px");
        if($this->includesContinent) $fieldsList[] = (new SBTableSimpleField("Continent", "continent"));
        if($this->includesSubRegion) $fieldsList[] = (new SBTableSimpleField("Region", "subregion"))->setMaxWidth("200px");
        $this->setFields($fieldsList);
    }

    public function fetchCountries(){
        $jsFilename = ReposManager::getRepo("repo/countries/countries.json");
        return json_decode(file_get_contents($jsFilename), true);
    }

    public function getFlagField($countryCodeKey) : FlagField {
        return new FlagField("Flag", $countryCodeKey);
    }

    public function getItemId($record): string {
        return EntityUtils::getSimpleValue($record, "alpha2");
    }
}
