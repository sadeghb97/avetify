<?php
namespace Avetify\Table\Fields\FlagFields;

use Avetify\Repo\Countries\World;
use Avetify\Table\Fields\EditableFields\SelectFields\SetSelectField;

class CountrySetField extends SetSelectField {
    public function __construct(string $title, string $key) {
        parent::__construct($title, $key, World::getCountriesDatalist());
    }
}
