<?php
namespace Avetify\Components\Countries;

use Avetify\Fields\JSACTextField;
use Avetify\Repo\Countries\World;

class CountriesACTextField extends JSACTextField {
    /**
     * @param string $childKey id elemente asli ke dar bargirande country code hast va mamulan hidden ast.
     * yadavari: az tarkibe fieldKey va childKey id elemente inpute ac text sakhte mishavad.
     */
    public function __construct(string $fieldKey = "", string $childKey = "", string $initValue = "",
                                string $enterCallbackName = "onSelectCountry"){
        parent::__construct($fieldKey, $childKey, $initValue, World::getCountriesDatalist());
        $this->enterCallbackName = $enterCallbackName;
    }

    public function callbackMoreData(): array {
        return [
            "pre_link" => $this->getPreCountryLink(),
            "post_link" => $this->getPostCountryLink(),
            "disable_auto_submit" => $this->disableSubmitOnEnter,
        ];
    }

    public function getPreCountryLink(): string {
        return "";
    }

    public function getPostCountryLink(): string {
        return "";
    }
}
