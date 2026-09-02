<?php
namespace Avetify\Fields;

use Avetify\Entities\AvtEntityItem;
use Avetify\Entities\BasicProperties\EntityID;
use Avetify\Entities\BasicProperties\EntityImage;
use Avetify\Entities\BasicProperties\EntityManager;
use Avetify\Entities\BasicProperties\EntityTitle;
use Avetify\Entities\BasicProperties\Traits\EntityManagerTrait;
use Avetify\Entities\EntityUtils;
use Avetify\Interface\Placeable;

abstract class JSDataElement implements Placeable, EntityManager {
    use EntityManagerTrait;

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
}
