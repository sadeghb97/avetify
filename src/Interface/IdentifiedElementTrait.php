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

    abstract public function getElementIdentifier($item = null);
}
