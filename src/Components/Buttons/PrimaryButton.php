<?php
namespace Avetify\Components\Buttons;

use Avetify\AvetifyManager;

class PrimaryButton extends AbsoluteButton {
    public function __construct(string $rawOnclick = "") {
        parent::__construct(AvetifyManager::imageUrl("sync.svg"),
            ["bottom" => "20px", "inset-inline-end" => "20px"], $rawOnclick);
    }
}
