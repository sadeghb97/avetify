<?php
namespace Avetify\Interface;

use Avetify\Fields\JSDataElement;

trait RecordFormTrait {
    /** @var JSDataElement[] */
    public array $requiredDataLists = [];

    public function placeFormDataLists(){
        foreach ($this->requiredDataLists as $dl){
            $dl->place();
        }
    }
}
