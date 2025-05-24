<?php
namespace Avetify\Components;

use Avetify\Fields\JSACTextField;
use Avetify\Fields\JSDatalist;
use Avetify\Forms\FormUtils;
use Avetify\Interface\Attrs;
use Avetify\Interface\CSS;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class SetSelector implements Placeable {
    public bool $useNameIdentifier = false;
    public bool $disableAutoSubmit = false;
    public bool $tinyAvatars = false;

    public function __construct(public string $label,
                                public string $key,
                                public string $initValue,
                                public JSDatalist $dlInfo
    ){
        $this->initValue = trim($this->initValue);
    }

    public function place(WebModifier $webModifier = null) {
        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("selbox");
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();
        Styler::startAttribute();
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        HTMLInterface::applyModifiers($webModifier);
        HTMLInterface::closeTag();

        HTMLInterface::placeVerticalDivider(12);
        $acText = new SetSelectorAC($this->key, $this->key, $this->initValue, $this->dlInfo, $this);
        $acText->disableSubmitOnEnter = $this->disableAutoSubmit;
        $acText->tinyAvatars = $this->tinyAvatars;
        $acText->place();

        $titleModifier = WebModifier::createInstance();
        $titleModifier->styler->pushStyle(CSS::fontSize, "11pt");
        $titleModifier->styler->pushStyle(CSS::fontWeight, "bold");
        $titleModifier->styler->pushStyle(CSS::marginTop, "8px");
        $titleModifier->styler->pushStyle(CSS::marginBottom, "6px");
        HTMLInterface::placeDiv($this->label, $titleModifier);
        FormUtils::placeHiddenField($this->getMainElementId(), $this->initValue, !$this->useNameIdentifier);

        $niceDiv = new NiceDiv(0);
        $niceDiv->addModifier(Attrs::id, $this->getImagesDivId());
        $niceDiv->open();
        $niceDiv->close();

        HTMLInterface::closeDiv();

        $currentIds = $this->initValue ? explode(",", $this->initValue) : [];
        ?>
        <script>
            var <?php echo $this->getJSSelectedListVarName(); ?> = new Set(<?php if(count($currentIds) > 0) echo json_encode($currentIds); ?>);
            <?php echo $this->jsUpdateSelector(); ?>
        </script>
        <?php
    }

    public function selectorMoreData() : array {
        return [
            "disable_auto_submit" => $this->disableAutoSubmit,
            "tiny_avatars" => $this->tinyAvatars,
        ];
    }

    public function jsUpdateSelector() : string {
        $cmdJson = json_encode($this->selectorMoreData());
        return "updateSelectorSet('" . $this->key . "', "
            . $this->dlInfo->getRecordsListJSVarName() . ", "
            . $this->dlInfo->getRecordsIdsMapJSVarName() . ', ' . $cmdJson . ');';
    }

    public function getMainElementId() : string {
        return $this->key;
    }

    public function getImagesDivId() : string {
        return $this->key . "_images";
    }

    public function getJSSelectedListVarName(){
        return $this->key . "_selected";
    }
}

class SetSelectorAC extends JSACTextField {
    public bool $disableSubmitOnEnter = true;
    public bool $tinyAvatars = false;

    public function __construct(string $fieldKey, string $childKey, string $initValue,
                                JSDatalist $dlInfo, public SetSelector $selector) {
        parent::__construct($fieldKey, $childKey, $initValue, $dlInfo);
        $this->enterCallbackName = "addRecordToSelector";
    }

    public function callbackMoreData(): array {
        return $this->selector->selectorMoreData();
    }
}