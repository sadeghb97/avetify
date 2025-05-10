<?php

class World {
    private static array | null $flagsMap = null;
    private static array | null $flagFilesMap = null;
    private static array | null $detailsMap = null;
    private static CountriesDatalist | null $countriesDatalist = null;

    private static function init(){
        $worldCountries = new WorldCountries();
        self::$detailsMap = $worldCountries->countriesMapClone();
        $countriesArray = [];
        foreach (self::$detailsMap as $item){
            $countriesArray[] = $item;
        }

        self::$countriesDatalist = new CountriesDatalist($countriesArray);

        foreach (self::$detailsMap as $countryCode => $country){
            $flag = $worldCountries->getCountryFlag($countryCode);
            if($flag) self::$flagsMap[strtolower($countryCode)] = $flag;

            $flagFile = $worldCountries->getCountryFlagFile($countryCode);
            if($flagFile) self::$flagFilesMap[strtolower($countryCode)] = $flagFile;
        }
    }

    public static function getCountryFlag($countryCode) : string | null {
        if(self::$flagsMap == null) self::init();
        if(isset(self::$flagsMap[strtolower($countryCode)])) return self::$flagsMap[strtolower($countryCode)];
        return null;
    }

    public static function getCountryFlagFile($countryCode) : string | null {
        if(self::$flagFilesMap == null) self::init();
        if(isset(self::$flagFilesMap[strtolower($countryCode)])) return self::$flagFilesMap[strtolower($countryCode)];
        return null;
    }

    public static function getCountry($countryCode) : array | null {
        if(self::$detailsMap == null) self::init();
        if(isset(self::$detailsMap[strtolower($countryCode)])) return self::$detailsMap[strtolower($countryCode)];
        return null;
    }

    public static function getCountriesDatalist() : CountriesDatalist {
        if(self::$countriesDatalist == null) self::init();
        return self::$countriesDatalist;
    }
}
