<?php

class CountriesTable extends JSONTable {
    public function __construct(){
        parent::__construct([],
            Routing::serverPathFromAventador("repo/countries/countries.json"), "countries");

        $this->isSortable = false;

        $this->setFields([
            (new FlagField("Flag", "alpha2")),
            (new SBTableSimpleField("Code", "alpha2")),
            (new SBTableSimpleField("Name", "short_name")),
            (new SBTableSimpleField("PerName", "per_name")),
            (new SBTableSimpleField("Continent", "continent")),
            (new SBTableSimpleField("Region", "subregion"))
        ]);
    }
    
    public function getItemId($item): string {
        return EntityUtils::getSimpleValue($item, "alpha2");
    }
}
