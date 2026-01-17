<?php
namespace Avetify\Forms\Buttons;

use Avetify\Components\Buttons\JoshButton;
use Avetify\Routing\Routing;

class ClearButton extends JoshButton {
    public function __construct(string $title, string $buttonId, string $buttonStyle){
        parent::__construct($title, $buttonId, $buttonStyle);
        $this->clickAction = "redir('" . html_entity_decode(Routing::getCurrentLink()) . "', 0);";
    }
}
