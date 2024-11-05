<?php

class FormButton implements Placeable {
    public string | null $confirmMessage = null;

    public function __construct(public string $formIdentifier,
                                public string $triggerIdentifier,
                                public string $buttonText,
                                public string $formTriggerElementId = ""
    ){
    }

    public function enableConfirmMessage($message){
        $this->confirmMessage = $message;
    }

    public function place() {
        echo '<button ';
        HTMLInterface::addAttribute("type", "button");
        HTMLInterface::addAttribute("id", $this->triggerIdentifier);
        HTMLInterface::closeTag();
        echo $this->buttonText;
        echo '</button>';

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
}
