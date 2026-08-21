<?php
namespace Avetify\Table\Meta;

use Avetify\AvetifyManager;
use Avetify\Components\Buttons\AbsoluteButton;

class IntegratedPrimaryTrigger {
    public static bool $isPlaced = false;

    public static function placeTrigger() : void {
        if(self::$isPlaced) return;

        $intPrimaryButton = new AbsoluteButton(AvetifyManager::imageUrl("sync.svg"),
            ["bottom" => "20px", "inset-inline-end" => "20px"],
            "avtTablesIntegratedPrimaryTrigger()");

        $intPrimaryButton->place();

        self::$isPlaced = true;
    }
}
