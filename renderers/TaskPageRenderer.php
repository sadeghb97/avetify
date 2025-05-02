<?php

abstract class TaskPageRenderer implements PageRenderer {
    public string $formId = "form_task";
    public string $triggerIdentifier = "trigger_task";

    public function renderPage(string $title) {
        $theme = $this->getTheme();
        $theme->placeHeader($title);
        $theme->loadHeaderElements();

        if(!empty($_POST['task']) && $_POST['task'] == $this->triggerIdentifier){
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

    abstract public function getTheme() : ThemesManager;
    abstract public function getTriggerImage() : string;
    abstract public function doTask();
}
