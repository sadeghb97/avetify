<?php
namespace Avetify\Entities\Fields\DateFields;

class UpdatedAtField extends UnixTimeField {
    public function __construct(string $key = "updated_at", string $title = "Updated At"){
        parent::__construct($key, $title);
        $this->setAutoTimeUpdate();
    }
}
