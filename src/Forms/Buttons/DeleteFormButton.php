<?php
namespace Avetify\Forms\Buttons;

use Avetify\AvetifyManager;

class DeleteFormButton extends AbsoluteFormButton {
    public function __construct(string $formIdentifier,
                                string $triggerIdentifier,
                                string $formTriggerElementId = "") {
        parent::__construct($formIdentifier, $triggerIdentifier,
            ["bottom" => "20px", "inset-inline-start" => "20px"],
            AvetifyManager::imageUrl("remove.svg"), $formTriggerElementId);
    }
}
