<?php

class WorldCountries {
    private array $map = [];

    public function __construct(){
        $countriesRaw = file_get_contents(ReposManager::getRepo("countries/countries.json"));
        $countries = json_decode($countriesRaw, true);

        foreach ($countries as $country){
            $countryCode = strtolower($country['alpha2']);
            $this->map[$countryCode] = $country;
        }

        foreach ($countries as $country){
            $countryCode = strtolower($country['alpha2']);
            $flags = $this->extractCountryFlag($countryCode);
            $this->map[$countryCode]['flag'] = $flags[0];
            $this->map[$countryCode]['flag_file'] = $flags[1];
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

    public function getCountryFlagFile($countryCode) : string | null {
        $country = $this->getCountryDetails($countryCode);
        if($country) return $country['flag_file'];
        return null;
    }

    public function extractCountryFlag($countryCode) : array | null {
        $country = $this->getCountryDetails($countryCode);
        if($country == null) return null;

        if(!isset($country['dep']) || !$country['dep']) {
            $browserFlag =
                AssetsManager::getImage("flags/cdc/") . $country['alpha2'] . ".png";
            $physicalFlag =
                ReposManager::getFile("assets/img/flags/cdc/") . $country['alpha2'] . ".png";
            return [$browserFlag, $physicalFlag];
        }

        if($country['dep'] === True){
            $browserFlag =
                AssetsManager::getImage("flags/more/") . $country['alpha2'] . ".png";
            $physicalFlag =
                ReposManager::getRepo("assets/img/flags/more/") . $country['alpha2'] . ".png";
            return [$browserFlag, $physicalFlag];
        }

        return $this->extractCountryFlag($country['dep']);
    }

    public function countriesMapClone() : array {
        return array_merge([], $this->map);
    }
}
