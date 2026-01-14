<?php
namespace Avetify\Components;

use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;

abstract class JSField implements Placeable
{
    public function __construct(public string $fieldId){
    }

    function basicJSRules(){}
    function moreJSRules(){}

    function onClickRule(){}

    function place(?WebModifier $webModifier = null): void {
        $this->basicJSRules();
        $this->place();
        $this->moreJSRules();
        $this->onClickRule();
    }

    function getChildID($newID) : string {
        return $this->fieldId . '__' . $newID;
    }
}
