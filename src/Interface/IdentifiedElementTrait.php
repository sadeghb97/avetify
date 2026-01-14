<?php
namespace Avetify\Interface;

trait IdentifiedElementTrait {
    public bool $useIDIdentifier = true;
    public bool $useNameIdentifier = false;

    abstract public function getElementIdentifier($item = null);
}
