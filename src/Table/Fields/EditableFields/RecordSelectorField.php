<?php
namespace Avetify\Table\Fields\EditableFields;

use Avetify\Entities\BasicProperties\EntityID;

class RecordSelectorField extends CheckboxField {
    public function __construct(string $title, EntityID $idGetter){
        parent::__construct($title, "select_Record", $idGetter);
    }

    public function getValue($item): string {
        return false;
    }
}
