<?php
namespace Avetify\Components\Countries;

use Avetify\Fields\JSDatalist;

class CountriesDatalist extends JSDatalist {
    public function __construct(array $countries){
        parent::__construct("avt_countries_datalist", $countries,
            "alpha2", "short_name");
    }
}
