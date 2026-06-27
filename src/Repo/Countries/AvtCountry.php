<?php
namespace Avetify\Repo\Countries;

use Avetify\Entities\AvtEntityItem;

class AvtCountry extends AvtEntityItem {
    public string $alpha2 = "";
    public string $alpha3 = "";
    public string $continent = "";
    public string $currency_code = "";
    public string $long_name = "";
    public string $short_name = "";
    public string $per_name = "";
    public array $languages = [];
    public string $nationality = "";
    public string $region = "";
    public string $subregion = "";
    public string $flag = "";
    public string $flag_file = "";

    public function getItemTitle(): string {
        return $this->short_name;
    }

    public function getItemId(): string {
        return $this->alpha2;
    }

    public function deleteAllResources() {}

    public function getItemImage(): string {
        return $this->flag;
    }

    public function getItemLink(): string {
        return "";
    }
}
