<?php

class World {
    public static array | null $flagsMap = null;
    public static array | null $detailsMap = null;

    private static function init(){
        $worldCountries = new WorldCountries();
        self::$detailsMap = $worldCountries->countriesClone();

        foreach (self::$detailsMap as $countryCode => $country){
            $flag = $worldCountries->getCountryFlag($countryCode);
            if($flag) self::$flagsMap[strtolower($countryCode)] = $flag;
        }
    }

    public static function getCountryFlag($countryCode) : string | null {
        if(self::$flagsMap == null) self::init();
        if(isset(self::$flagsMap[strtolower($countryCode)])) return self::$flagsMap[strtolower($countryCode)];
        return null;
    }

    public static function getCountry($countryCode) : array | null {
        if(self::$detailsMap == null) self::init();
        if(isset(self::$detailsMap[strtolower($countryCode)])) return self::$detailsMap[strtolower($countryCode)];
        return null;
    }
}
