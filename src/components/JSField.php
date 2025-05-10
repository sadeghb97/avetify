<?php

abstract class JSField implements Placeable
{
    public function __construct(public string $fieldId){
    }

    function basicJSRules(){}
    function moreJSRules(){}

    function onClickRule(){}

    function place(WebModifier $webModifier = null){
        $this->basicJSRules();
        $this->place();
        $this->moreJSRules();
        $this->onClickRule();
    }

    function getChildID($newID) : string {
        return $this->fieldId . '__' . $newID;
    }
}
