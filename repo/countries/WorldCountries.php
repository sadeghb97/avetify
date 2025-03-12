<?php

class WorldCountries {
    private array $map = [];

    public function __construct(){
        $countriesRaw = file_get_contents(__DIR__ . "/countries.json");
        $countries = json_decode($countriesRaw, true);

        foreach ($countries as $country){
            $this->map[$country['alpha2']] = $country;
        }
    }

    public function getCountryDetails($countryCode) : array | null {
        if(isset($this->map[$countryCode])) return $this->map[$countryCode];
        return null;
    }

    public function getCountryFlag($countryCode) : string | null {
        $details = $this->getCountryDetails($countryCode);
        if(!$details) return null;

        if(!isset($details['dep']) || !$details['dep']) {
            return Routing::browserPathFromAventador("assets/img/flags/cdc/") . $details['alpha2'] . ".png";
        }

        if($details['dep'] === True){
            return Routing::browserPathFromAventador("assets/img/flags/more/") . $details['alpha2'] . ".png";
        }

        return $this->getCountryFlag($details['dep']);
    }
}
