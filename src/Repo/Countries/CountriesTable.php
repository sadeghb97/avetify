<?php
namespace Avetify\Repo\Countries;

use Avetify\AvetifyManager;
use Avetify\Entities\EntityUtils;
use Avetify\Table\AvtTable;
use Avetify\Table\Fields\FlagFields\FlagField;
use Avetify\Table\Fields\TableSimpleField;

class CountriesTable extends AvtTable {
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
        if($this->includesCode) $fieldsList[] = (new TableSimpleField("Code", "alpha2"));
        if($this->includesName) $fieldsList[] = (new TableSimpleField("Name", "short_name"))->setMaxWidth("200px");
        if($this->includesPerName) $fieldsList[] = (new TableSimpleField("PerName", "per_name"))->setMaxWidth("200px");
        if($this->includesContinent) $fieldsList[] = (new TableSimpleField("Continent", "continent"));
        if($this->includesSubRegion) $fieldsList[] = (new TableSimpleField("Region", "subregion"))->setMaxWidth("200px");
        $this->setFields($fieldsList);
    }

    public function fetchCountries(){
        $jsFilename = AvetifyManager::dataPath("countries/countries.json");
        return json_decode(file_get_contents($jsFilename), true);
    }

    public function getFlagField($countryCodeKey) : FlagField {
        return new FlagField("Flag", $countryCodeKey);
    }

    public function getItemId($record): string {
        return EntityUtils::getSimpleValue($record, "alpha2");
    }
}
