<?php
namespace Avetify\Entities\ContextMenus;

use Avetify\Interface\WebModifier;

class ContextMenuItem {
    public function __construct(public string $key, public string $title){}
    public ?WebModifier $modifier;
}
