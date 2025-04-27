<?php

class EntityFlagField extends EntityField {
    public function __construct($key, $title, public CountriesACTextFactory $countriesACFactory) {
        parent::__construct($key, $title);
        $this->type = "country";
    }
}
