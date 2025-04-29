<?php

class CountriesTable extends JSONTable {
    public bool $includesCode = true;
    public bool $includesName = true;
    public bool $includesPerName = true;
    public bool $includesContinent = true;
    public bool $includesSubRegion = true;

    public function __construct(){
        parent::__construct([],
            Routing::serverPathFromAventador("repo/countries/countries.json"), "countries");

        $this->isSortable = false;

        $fieldsList = [];
        $fieldsList[] = ($this->getFlagField("alpha2"));
        if($this->includesCode) $fieldsList[] = (new SBTableSimpleField("Code", "alpha2"));
        if($this->includesName) $fieldsList[] = (new SBTableSimpleField("Name", "short_name"));
        if($this->includesPerName) $fieldsList[] = (new SBTableSimpleField("PerName", "per_name"));
        if($this->includesContinent) $fieldsList[] = (new SBTableSimpleField("Continent", "continent"));
        if($this->includesSubRegion) $fieldsList[] = (new SBTableSimpleField("Region", "subregion"));
        $this->setFields($fieldsList);
    }

    public function getFlagField($countryCodeKey) : FlagField {
        return new FlagField("Flag", $countryCodeKey);
    }

    public function getItemId($record): string {
        return EntityUtils::getSimpleValue($record, "alpha2");
    }
}
