<?php

class WorldCountries {
    private array $map = [];

    public function __construct(){
        $countriesRaw = file_get_contents(__DIR__ . "/countries.json");
        $countries = json_decode($countriesRaw, true);

        foreach ($countries as $country){
            $countryCode = strtolower($country['alpha2']);
            $this->map[$countryCode] = $country;
            $flag = $this->extractCountryFlag($countryCode);
            $this->map[$countryCode]['flag'] = $flag;
        }
    }

    public function getCountryDetails($countryCode) : array | null {
        if(isset($this->map[strtolower($countryCode)])) return $this->map[strtolower($countryCode)];
        return null;
    }

    public function getCountryFlag($countryCode) : string | null {
        $country = $this->getCountryDetails($countryCode);
        if($country) return $country['flag'];
        return null;
    }

    public function extractCountryFlag($countryCode) : string | null {
        $country = $this->getCountryDetails($countryCode);
        if($country == null) return null;

        if(!isset($country['dep']) || !$country['dep']) {
            return Routing::browserPathFromAventador("assets/img/flags/cdc/") . $country['alpha2'] . ".png";
        }

        if($country['dep'] === True){
            return Routing::browserPathFromAventador("assets/img/flags/more/") . $country['alpha2'] . ".png";
        }

        return $this->extractCountryFlag($country['dep']);
    }

    public function countriesMapClone() : array {
        return array_merge([], $this->map);
    }
}
