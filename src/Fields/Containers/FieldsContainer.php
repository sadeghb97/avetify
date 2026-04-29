<?php
namespace Avetify\Fields\Containers;

use Avetify\Fields\BaseRecordField;
use Avetify\Interface\WebModifier;

class FieldsContainer extends BaseRecordField {
    /** @var BaseRecordField[] $childs */
    public array $childs = [];
    public int $sepSize = 4;

    public function __construct(string $key, string $title, array $childs){
        $this->baseModifier = WebModifier::createInstance();
        $this->baseModifier->pushStyle("margin-bottom", "12px");
        $this->baseModifier->pushStyle("margin-top", "12px");
        $this->baseModifier->pushStyle("margin-right", "8px");
        $this->baseModifier->pushStyle("margin-left", "8px");
        parent::__construct($key, $title);
        $this->childs = $childs;
    }

    public function setSeparatorSize(int $sepSize): FieldsContainer {
        $this->sepSize = $sepSize;
        return $this;
    }
}
