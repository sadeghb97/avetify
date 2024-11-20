<?php

class FormButton implements Placeable {
    public string | null $confirmMessage = null;
    public int $iconSize = 48;

    public function __construct(public string $formIdentifier,
                                public string $triggerIdentifier,
                                public string $buttonText,
                                public string $style = "primary",
                                public string $formTriggerElementId = ""
    ){
    }

    public function enableConfirmMessage($message){
        $this->confirmMessage = $message;
    }

    public function initButtonJsActions(){
        ?>
        <script>
            document.getElementById("<?php echo $this->triggerIdentifier; ?>").onclick = function() {
                let isOk = true;
                <?php if($this->confirmMessage != null) { ?>
                isOk = confirm('<?php echo $this->confirmMessage; ?>');
                <?php } ?>

                if (isOk) {
                    <?php if($this->formTriggerElementId) { ?>
                    const formTriggerElement = document.getElementById('<?php echo $this->formTriggerElementId; ?>');
                    formTriggerElement.value = '<?php echo $this->triggerIdentifier ?>';
                    <?php } ?>

                    const event = new Event("submit", { cancelable: true });
                    document.getElementById("<?php echo $this->formIdentifier; ?>").dispatchEvent(event);
                }
            };
        </script>
        <?php
    }

    public function renderButton(){
        $buttonStyle = "pushable";
        if($this->style == "warning") $buttonStyle .= (' warning');

        echo '<button ';
        HTMLInterface::addAttribute("type", "button");
        HTMLInterface::addAttribute("id", $this->triggerIdentifier);
        HTMLInterface::addAttribute("class", $buttonStyle);
        HTMLInterface::closeTag();

        echo '<span ';
        HTMLInterface::addAttribute("class", "front");
        HTMLInterface::closeTag();
        echo $this->buttonText;
        echo '</span>';

        echo '</button>';
    }

    public function place(WebModifier $webModifier = null) {
        $this->renderButton();
        $this->initButtonJsActions();
    }
}

class AbsoluteFormButtons extends FormButton {
    public function __construct(string $formIdentifier,
                                string $triggerIdentifier,
                                public array $position,
                                public string $img,
                                string $formTriggerElementId = ""){
        parent::__construct($formIdentifier, $triggerIdentifier, "", "", $formTriggerElementId);
    }

    public function renderButton(){
        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle("position", "fixed");
        Styler::addStyle("cursor", "pointer");
        foreach ($this->position as $posKey => $pos){
            Styler::addStyle($posKey, $pos);
        }
        Styler::closeAttribute();
        HTMLInterface::addAttribute("type", "button");
        HTMLInterface::addAttribute("id", $this->triggerIdentifier);
        HTMLInterface::closeTag();

        echo '<img ';
        HTMLInterface::addAttribute("src", $this->img);
        HTMLInterface::addAttribute("alt", "Icon");
        HTMLInterface::addAttribute("width", $this->iconSize . "px");
        HTMLInterface::addAttribute("height", $this->iconSize . "px");
        HTMLInterface::closeSingleTag();

        HTMLInterface::closeDiv();
    }
}

class PrimaryFormButton extends AbsoluteFormButtons {
    public function __construct(string $formIdentifier,
                                string $triggerIdentifier,
                                string $formTriggerElementId = "") {
        parent::__construct($formIdentifier, $triggerIdentifier,
            ["bottom" => "20px", "left" => "20px"],
            Routing::getAventadorRoot() . "assets/img/sync.svg", $formTriggerElementId);
    }
}

class DeleteFormButton extends AbsoluteFormButtons {
    public function __construct(string $formIdentifier,
                                string $triggerIdentifier,
                                string $formTriggerElementId = "") {
        parent::__construct($formIdentifier, $triggerIdentifier,
            ["bottom" => "20px", "right" => "20px"],
            Routing::getAventadorRoot() . "assets/img/remove.svg", $formTriggerElementId);
    }
}
