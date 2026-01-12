<?php
namespace Avetify\Entities\Fields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Entities\EntityField;
use Avetify\Forms\FormUtils;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\WebModifier;

class EntityDisabledField extends EntityField {
    public function __construct($key, $title, public string $defaultValue = "") {
        parent::__construct($key, $title);
    }

    public function presentWritableField($item, ?WebModifier $webModifier = null) {
        $title = $this->title;
        $key = $this->key;
        $value = $this->getValue($item);

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
