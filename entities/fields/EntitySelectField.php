<?php

class EntitySelectField extends EntityField {
    public function __construct($key, $title, public string $dataSetKey) {
        parent::__construct($key, $title);
        $this->type = "select";
    }
}
