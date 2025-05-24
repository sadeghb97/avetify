<?php
namespace Avetify\Entities\Fields;

use Avetify\Components\CountriesACTextFactory;
use Avetify\Entities\EntityField;

class EntityFlagField extends EntityField {
    public function __construct($key, $title, public CountriesACTextFactory $countriesACFactory) {
        parent::__construct($key, $title);
        $this->type = "country";
    }
}
