<?php
namespace Avetify\Components\Selectors;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Fields\JSDatalist;
use Avetify\Forms\FormUtils;
use Avetify\Interface\Attrs;
use Avetify\Interface\CSS;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\IdentifiedElement;
use Avetify\Interface\IdentifiedElementTrait;
use Avetify\Interface\Placeable;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class SetSelector implements Placeable, IdentifiedElement {
    use IdentifiedElementTrait;
    public bool $disableAutoSubmit = false;
    public bool $tinyAvatars = false;
    public bool $isReadonly = false;

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

        HTMLInterface::placeVerticalDivider(8);

        if(!$this->isReadonly) {
            $acText = new SetSelectorAC($this->key, $this->key, $this->initValue, $this->dlInfo, $this);
            $acText->disableSubmitOnEnter = $this->disableAutoSubmit;
            $acText->tinyAvatars = $this->tinyAvatars;
            $acText->place();
        }

        $titleModifier = WebModifier::createInstance();
        $titleModifier->styler->pushStyle(CSS::fontSize, "11pt");
        $titleModifier->styler->pushStyle(CSS::fontWeight, "bold");
        $titleModifier->styler->pushStyle(CSS::marginTop, "8px");
        $titleModifier->styler->pushStyle(CSS::marginBottom, "6px");
        HTMLInterface::placeDiv($this->label, $titleModifier);
        FormUtils::placeHiddenField($this->getElementIdentifier(), $this->initValue, !$this->useNameIdentifier);

        $niceDiv = new NiceDiv(0);
        $niceDiv->addModifier(Attrs::id, $this->getImagesDivId());
        $niceDiv->open();
        $niceDiv->close();

        HTMLInterface::closeDiv();
        $initVarJS = "setSelectorFieldValue_" . $this->key;

        echo '<script>';
        echo 'var ' . $this->getJSSelectedListVarName() . ' = null;';
        echo 'console.log(' . $this->getJSSelectedListVarName() . ');';
        echo '{';
        echo 'const ' . $initVarJS . ' = "' . $this->initValue . '";';
        echo 'console.log(' . $initVarJS . ');';
        echo $this->loadValueUsingJS($initVarJS);
        echo '}';
        echo '</script>';

    }

    public function loadValueUsingJS(string $valueVarName): string {
        $out = ($this->getJSSelectedListVarName() . " = new Set($valueVarName ? $valueVarName.split(',') : []);");
        $out .= 'console.log(' . $this->getJSSelectedListVarName() . ');';
        $out .= $this->jsUpdateSelector();
        return $out;
    }

    public function selectorMoreData() : array {
        return [
            "disable_auto_submit" => $this->disableAutoSubmit,
            "tiny_avatars" => $this->tinyAvatars,
            "is_readonly" => $this->isReadonly,
        ];
    }

    public function jsUpdateSelector() : string {
        $cmdJson = json_encode($this->selectorMoreData());
        return "updateSelectorSet('" . $this->key . "', "
            . $this->dlInfo->getRecordsListJSVarName() . ", "
            . $this->dlInfo->getRecordsIdsMapJSVarName() . ', ' . $cmdJson . ');';
    }

    public function getElementIdentifier($item = null) : string {
        return $this->key;
    }

    public function getImagesDivId() : string {
        return $this->key . "_images";
    }

    public function getJSSelectedListVarName(){
        return $this->key . "_selected";
    }
}