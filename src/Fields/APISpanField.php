<?php
namespace Avetify\Fields;

use Avetify\Fields\JSTextFields\JSInputField;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\IdentifiedElementTrait;
use Avetify\Interface\Placeable;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class APISpanField extends JSInputField implements Placeable {
    use IdentifiedElementTrait;
    public function __construct(public string $recordKey, public string $fieldKey,
                                public string $initValue, public string $apiEndpoint){
    }

    public function getElementIdentifier($item = null) {
        return $this->fieldKey . "_" . $this->recordKey;
    }

    public function place(?WebModifier $webModifier = null){
        echo '<div ';
        $this->placeElementIdAttributes();
        HTMLInterface::addAttribute("onkeydown", "");
        Styler::startAttribute();
        $this->appendMoreStyles();
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        $this->printValue();
        HTMLInterface::closeDiv();

        ?>
        <script>
            addLongClickEvent(
                '<?php echo $this->getElementIdentifier() ?>',
                (fieldId) => {
                    applyField('<?php echo $this->recordKey; ?>',
                        '<?php echo $this->fieldKey; ?>', '<?php echo $this->initValue; ?>',
                        '<?php echo $this->apiEndpoint; ?>',
                        (data) => {<?php echo $this->toggleCallback(); ?>}
                    );
                },
                (fieldId) => {<?php echo $this->clickCallback(); ?>})
        </script>
        <?php
    }

    public function toggleCallback() : string {
        return 'console.log("ToggleData", data);';
    }

    public function clickCallback() : string {
        return 'console.log("NormalClick", fieldId);';
    }

    public function appendMoreStyles(){}

    public function printValue(){
        echo $this->initValue;
    }
}
