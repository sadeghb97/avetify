<?php
namespace Avetify\Fields;

use Avetify\Entities\AvtEntityItem;
use Avetify\Entities\BasicProperties\EntityID;
use Avetify\Entities\BasicProperties\EntityImage;
use Avetify\Entities\BasicProperties\EntityTitle;
use Avetify\Entities\EntityUtils;
use Avetify\Interface\Placeable;

abstract class JSDataElement implements Placeable, EntityID, EntityTitle, EntityImage {
    public array $namesMap = [];
    public array $idsMap = [];

    public function __construct(public string $dataSetKey, public array $records,
                                public string $primaryKey = "",
                                public string $labelKey = "", public string $imageKey = ""){
        $this->namesMap = [];
        $this->idsMap = [];
        foreach ($this->records as $recordIndex => $record){
            $title = strtolower($this->getItemTitle($record));
            $this->namesMap[$title] = $recordIndex;
            $id = $this->getItemId($record);
            $this->idsMap[$id] = $recordIndex;
        }
    }

    public function getRecordByName($name) : AvtEntityItem|array|null {
        $lowerName = strtolower($name);
        if(isset($this->namesMap[$lowerName])) return $this->records[$this->namesMap[$lowerName]];
        return null;
    }

    public function getRecordById($id) : AvtEntityItem|array|null {
        if(isset($this->idsMap[$id])) return $this->records[$this->idsMap[$id]];
        return null;
    }

    public function getItemId($record) : string {
        if($record instanceof AvtEntityItem) return $record->getItemId();
        return EntityUtils::getSimpleValue($record, $this->primaryKey);
    }

    public function getItemTitle($record) : string {
        if($record instanceof AvtEntityItem) return $record->getItemTitle();
        return EntityUtils::getSimpleValue($record, $this->labelKey);
    }

    public function getItemImage($record) : string {
        if($record instanceof AvtEntityItem) return $record->getItemImage();
        return EntityUtils::getSimpleValue($record, $this->imageKey);
    }
}
