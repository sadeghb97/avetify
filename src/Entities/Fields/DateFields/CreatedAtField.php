<?php
namespace Avetify\Entities\Fields\DateFields;

class CreatedAtField extends UnixTimeField {
    public function __construct(string $key = "created_at", string $title = "Created At"){
        parent::__construct($key, $title);
        $this->setAutoTimeCreate();
    }
}
