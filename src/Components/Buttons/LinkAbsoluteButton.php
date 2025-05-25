<?php
namespace Avetify\Components\Buttons;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\WebModifier;

class LinkAbsoluteButton extends AbsoluteButton {
    public bool $isBlank = true;

    public function __construct(string $imageSrc, array $positionStyles, public string $link) {
        parent::__construct($imageSrc, $positionStyles, "");
    }

    public function place(WebModifier $webModifier = null) {
        echo '<a ';
        HTMLInterface::addAttribute("href", $this->link);
        if($this->isBlank) HTMLInterface::addAttribute("target", "_blank");
        HTMLInterface::closeTag();
        parent::place($webModifier);
        echo '</a>';
    }
}
