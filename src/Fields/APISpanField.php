<?php
namespace Avetify\Fields;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class APISpanField extends JSInputField {
    public function __construct(public string $recordKey, public string $fieldKey,
                                public string $initValue, public string $apiEndpoint){
    }

    public function getFieldIdentifier() : string {
        return $this->fieldKey . "_" . $this->recordKey;
    }

    public function place(?WebModifier $webModifier = null){
        echo '<div ';
        HTMLInterface::addAttribute("id", $this->getFieldIdentifier());
        HTMLInterface::addAttribute("onkeydown", "");
        Styler::startAttribute();
        $this->appendMoreStyles();
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        $this->printValue();
        HTMLInterface::closeDiv();

        ?>
        <script>
            addLongClickEvent('<?php echo $this->getFieldIdentifier() ?>', (fieldId) => {
                applyField('<?php echo $this->recordKey; ?>',
                    '<?php echo $this->fieldKey; ?>', '<?php echo $this->initValue; ?>',
                    '<?php echo $this->apiEndpoint; ?>', (data) => {<?php echo $this->toggleCallback(); ?>});
            })
        </script>
        <?php
    }

    public function toggleCallback() : string {
        return 'console.log("ToggleData", data);';
    }

    public function appendMoreStyles(){}

    public function printValue(){
        echo $this->initValue;
    }
}
