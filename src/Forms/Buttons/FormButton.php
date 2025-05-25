<?php
namespace Avetify\Forms\Buttons;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;

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
                triggerForm(
                    '<?php echo $this->formIdentifier ? $this->formIdentifier : ''; ?>',
                    '<?php echo $this->confirmMessage ? $this->confirmMessage : ''; ?>',
                    '<?php echo $this->formTriggerElementId ? $this->formTriggerElementId : ''; ?>',
                    '<?php echo $this->triggerIdentifier ? $this->triggerIdentifier : ''; ?>'
                )
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
