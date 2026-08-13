<?php
namespace Avetify\Repo\Countries;

use Avetify\AvetifyManager;

class WorldCountries {
    private array $map = [];

    public function __construct(){
        $countriesRaw = file_get_contents(AvetifyManager::dataPath("countries/countries.json"));
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
        $finalCode = strtolower($countryCode);

        // ISO 3166-2 → Chess.com code
        if($finalCode == "gb-eng") $finalCode = "xe";
        else if($finalCode == "gb-nir") $finalCode = "xn";
        else if($finalCode == "gb-sct") $finalCode = "xs";
        else if($finalCode == "gb-wls") $finalCode = "xw";
        else if($finalCode == "es-ct") $finalCode = "xc";
        else if($finalCode == "es-pv") $finalCode = "xb";
        else if($finalCode == "es-ga") $finalCode = "xg";
        else if($finalCode == "es-cn") $finalCode = "xa";

        // Alternative country codes → Chess.com code
        else if(in_array($finalCode, ["pse", "275", "ple"])) $finalCode = "ps";
        else if(in_array($finalCode, ["xt", "twn", "158", "tpe"])) $finalCode = "tw";
        else if(in_array($finalCode, ["xk", "xkx", "ks", "kos"])) $finalCode = "xk";

        if(isset($this->map[$finalCode])) return $this->map[$finalCode];

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
                AvetifyManager::imageUrl("flags/cdc/") . $country['alpha2'] . ".png";
            $physicalFlag =
                AvetifyManager::assetPath("img/flags/cdc/") . $country['alpha2'] . ".png";
            return [$browserFlag, $physicalFlag];
        }

        if($country['dep'] === True){
            $browserFlag =
                AvetifyManager::imageUrl("flags/more/") . $country['alpha2'] . ".png";
            $physicalFlag =
                AvetifyManager::assetPath("img/flags/more/") . $country['alpha2'] . ".png";
            return [$browserFlag, $physicalFlag];
        }

        return $this->extractCountryFlag($country['dep']);
    }

    public function countriesMapClone() : array {
        return array_merge([], $this->map);
    }
}
