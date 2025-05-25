<?php
namespace Avetify\Forms\Buttons;

use Avetify\AvetifyManager;

class PrimaryFormButton extends AbsoluteFormButton {
    public function __construct(string $formIdentifier,
                                string $triggerIdentifier,
                                string $formTriggerElementId = "") {
        parent::__construct($formIdentifier, $triggerIdentifier,
            ["bottom" => "20px", "inset-inline-end" => "20px"],
            AvetifyManager::imageUrl("sync.svg"), $formTriggerElementId);
    }
}
