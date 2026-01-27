<?php
namespace Avetify\Interface;

trait IdentifiedElementTrait {
    public bool $useIDIdentifier = true;
    public bool $useNameIdentifier = false;

    public function placeElementIdAttributes($item = null): void {
        $elementIdentifier = $this->getElementIdentifier($item);
        if($elementIdentifier) {
            if ($this->useIDIdentifier) HTMLInterface::addAttribute("id", $elementIdentifier);
            if ($this->useNameIdentifier) HTMLInterface::addAttribute("name", $elementIdentifier);
        }
    }

    public function loadValueUsingJS(string $valueVarName) : string {
        $out = 'if(' . $valueVarName . ') {';
        $out .= ('const el = document.getElementById("' . $this->getElementIdentifier() . '");');
        $out .= 'el.value = ' . $valueVarName . ';';
        $out .= '}';
        return $out;
    }

    public function loadValueUsingJSStorage(string $key) : void {
        echo '<script>';
        echo "{";
        echo 'const field_initial_value = localStorage.getItem("' . $key . '");';
        echo 'console.log(field_initial_value);';
        echo $this->loadValueUsingJS("field_initial_value");
        echo "}";
        echo '</script>';
    }

    abstract public function getElementIdentifier($item = null);
}
