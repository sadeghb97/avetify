<?php
namespace Avetify\Fields\DateFields;

use Avetify\DB\DBConnection;
use Avetify\Fields\BaseRecordField;

abstract class DatePicker extends BaseRecordField {
    public bool $timeEnabled = false;
    public bool $printUnix = false;
    public DateValueType $valueType = DateValueType::MYSQL_DATE;

    public function __construct(string $key, string $title) {
        parent::__construct($key, $title);
        $this->setNullOnEmpty();
    }

    public function getValue($item): string {
        $val = parent::getValue($item);
        if ($val === '') return "0";


        return match ($this->valueType) {
            DateValueType::UNIX =>
            (string) (int) $val,

            DateValueType::MYSQL_DATE,
            DateValueType::MYSQL_DATETIME =>
            (($time = strtotime($val)) === false)
                ? "0"
                : (string) $time,
        };
    }

    public function getTimeValue($item) : ?int {
        if(is_object($item) && property_exists($item, $this->key)){
            $rawVal = $item->{$this->key};
        }
        else if(is_array($item) && isset($item[$this->key])){
            $rawVal = $item[$this->key];
        }
        else $rawVal = $this->getValue($item);

        if($rawVal === null) return null;
        return intval($this->getValue($item));
    }

    public function adjustDBValue(DBConnection $conn, string $value): string|null {
        if ($value === '' || !is_numeric($value)) return null;

        $time = (int)$value;

        if ($time === 0) {
            return match ($this->valueType) {
                DateValueType::UNIX => "0",
                DateValueType::MYSQL_DATE => "0000-00-00",
                DateValueType::MYSQL_DATETIME => "0000-00-00 00:00:00",
            };
        }

        return match ($this->valueType) {
            DateValueType::UNIX =>
            (string)$time,

            DateValueType::MYSQL_DATE =>
            gmdate('Y-m-d', $time),

            DateValueType::MYSQL_DATETIME =>
            gmdate('Y-m-d H:i:s', $time),
        };
    }

    public function initJs($item) : void {
        echo '<script>';
        echo $this->getInitJsString($item);
        echo '</script>';
    }

    abstract public function getInitJsString($item) : string;

    public function getFieldInputIdentifier($item) : string {
        return $this->getElementIdentifier($item) . "_display";
    }

    public function getUnixSpanIdentifier($item) : string {
        return $this->getElementIdentifier($item) . "_unix";
    }

    public function enableTime() : static {
        $this->timeEnabled = true;
        $this->setValueType(DateValueType::UNIX);
        return $this;
    }

    public function setValueType(DateValueType $valueType) : static {
        $this->valueType = $valueType;
        return $this;
    }

    public function enableUnix() : static {
        $this->printUnix = true;
        return $this;
    }
}