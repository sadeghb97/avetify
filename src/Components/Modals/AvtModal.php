<?php
namespace Avetify\Components\Modals;

use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Utils\StringUtils;

abstract class AvtModal {
    public string $templateId = "";

    public function __construct() {
        $this->templateId = "template_" . StringUtils::generateUUID();
    }

    abstract public function placeTemplateBody() : void;
    abstract public function setupJs() : void;

    public function openTemplate() : void {
        echo '<template ';
        HTMLInterface::addAttribute("id", $this->templateId);
        HTMLInterface::closeTag();
    }

    public function closeTemplate() : void {
        echo '</template>';
    }

    public function attachTemplate() : void {
        $this->openTemplate();
        $this->placeTemplateBody();
        $this->closeTemplate();
    }

    public function openScript() : string {
        ob_start();
        echo 'ModalManager.show("' . $this->templateId . '", function () {';
        $this->setupJs();
        echo '});';
        return ob_get_clean();
    }
}