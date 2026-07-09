<?php
namespace Avetify\Fields;

use Avetify\DB\DBConnection;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;

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

    public function presentValue($item, ?WebModifier $webModifier = null) {
        echo '<div ';
        $webModifier?->apply();
        HTMLInterface::closeTag();
        $this->recordField->placeField($item);
        HTMLInterface::closeDiv();
    }

    public function adjustDBValue(DBConnection $conn, string $value): string|null {
        $value = parent::adjustDBValue($conn, $value);
        return $this->recordField->adjustDBValue($conn, $value);
    }
}
