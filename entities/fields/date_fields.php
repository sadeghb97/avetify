<?php

class CreatedAtField extends EntityField {
    public function __construct(){
        parent::__construct("created_at", "Created At");
        $this->setAutoTimeCreate();
    }
}

class UpdatedAtField extends EntityField {
    public function __construct(){
        parent::__construct("updated_at", "Updated");
        $this->setAutoTimeUpdate();
    }
}
