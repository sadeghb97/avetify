<?php
namespace Avetify\Entities\Fields\FlagFields;

use Avetify\Components\Countries\CountriesACTextFactory;
use Avetify\Components\Countries\CountrySelector;
use Avetify\Entities\EntityField;
use Avetify\Interface\WebModifier;

class EntityFlagField extends EntityField {
    public function __construct($key, $title, public CountriesACTextFactory $countriesACFactory) {
        parent::__construct($key, $title);
    }

    public function presentWritableField($item, ?WebModifier $webModifier = null) {
        $key = $this->key;
        $value = $this->getValue($item);

        $catFactory = $this->countriesACFactory;
        $catFactory->fieldKey = "countries-actext";
        $catFactory->childKey = $key ? $key : "";

        $csModifier = WebModifier::createInstance();
        $csModifier->styler->pushStyle("margin-top", "12px");
        $csModifier->styler->pushStyle("margin-bottom", "12px");

        $countrySelector = new CountrySelector(
            $key,
            $catFactory,
            "Select Nation",
            true,
            $value ? $value : ""
        );
        $countrySelector->setNameIdentifier = true;
        $countrySelector->place($csModifier);
    }
}
