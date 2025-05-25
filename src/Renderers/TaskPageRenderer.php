<?php
namespace Avetify\Renderers;

use Avetify\AvetifyManager;
use Avetify\Forms\Buttons\AbsoluteFormButton;
use Avetify\Forms\FormUtils;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\PageRenderer;
use Avetify\Themes\Main\ThemesManager;
use Avetify\Utils\CliUtils;
use function Avetify\Utils\isCli;

abstract class TaskPageRenderer implements PageRenderer {
    public string $formId = "form_task";
    public string $triggerIdentifier = "trigger_task";

    public function renderPage(?string $title = "Heavy Task") {
        if(!CliUtils::isCli()) {
            $theme = $this->getTheme();
            $theme->placeHeader($title);
            $theme->loadHeaderElements();

            if (!empty($_POST['task']) && $_POST['task'] == $this->triggerIdentifier) {
                HTMLInterface::placeVerticalDivider(16);
                $this->doTask();
            }

            FormUtils::openPostForm($this->formId);
            FormUtils::placeHiddenField("task", $this->triggerIdentifier);
            $primaryButton = new AbsoluteFormButton($this->formId, $this->triggerIdentifier,
                ["bottom" => "20px", "inset-inline-end" => "20px"], $this->getTriggerImage());
            $primaryButton->place();
            FormUtils::closeForm();
        }
        else $this->doTask();
    }

    public function getTriggerImage(): string {
        return AvetifyManager::imageUrl("send.svg");
    }

    abstract public function getTheme() : ThemesManager;
    abstract public function doTask();
}
