<?php
namespace Avetify\Entities\Fields;

use Avetify\Components\NiceDiv;
use Avetify\Entities\EntityField;
use Avetify\Entities\EntityUtils;
use Avetify\Forms\FormUtils;
use Avetify\Interface\EntityView;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\WebModifier;

class EntityDisabledField extends EntityField implements EntityView {
    public function __construct($key, $title, public string $defaultValue = "") {
        parent::__construct($key, $title);
    }

    public function place($record, ?WebModifier $modifier = null){
        $value = $record ? EntityUtils::getSimpleValue($record, $this->key) : $this->defaultValue;

        $niceDiv = new NiceDiv();
        $niceDiv->open();

        $labelModifier = WebModifier::createInstance();
        HTMLInterface::placeSpan($this->title . ": ", $labelModifier);
        $niceDiv->separate();

        $valueModifier = WebModifier::createInstance();
        HTMLInterface::placeSpan($value, $valueModifier);

        $niceDiv->close();

        FormUtils::placeHiddenField($this->key, $value);
    }
}
