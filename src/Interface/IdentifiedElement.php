<?php
namespace Avetify\Interface;

interface IdentifiedElement {
    public function placeElementIdAttributes($item = null): void;
    public function loadValueUsingJS(string $valueVarName) : string;
    public function loadValueUsingJSStorage(string $key) : void;
    public function getElementIdentifier($item = null);
}
