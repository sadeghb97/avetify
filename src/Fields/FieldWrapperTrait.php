<?php
namespace Avetify\Fields;

use Avetify\Entities\AvtEntityItem;
use Avetify\Entities\BasicProperties\EntityID;
use Avetify\Entities\BasicProperties\EntityImage;
use Avetify\Entities\BasicProperties\EntityTitle;
use Avetify\Entities\EntityUtils;
use Avetify\Interface\Placeable;

/**
 * @method self modifyRecordRemoveBaseMargins()
 */
trait FieldWrapperTrait {
    public BaseRecordField $recordField;

    public function __call($name, $arguments) {
        if(str_starts_with($name, "modifyRecord")) {
            $remainedName = substr($name, 12);
            $standardRemainedName = lcfirst($remainedName);
            if (method_exists($this->recordField, $remainedName)) {
                $this->recordField->$remainedName(...$arguments);
                return $this;
            }
            else if (method_exists($this->recordField, $standardRemainedName)) {
                $this->recordField->$standardRemainedName(...$arguments);
                return $this;
            }
        }
        throw new BadMethodCallException();
    }
}
