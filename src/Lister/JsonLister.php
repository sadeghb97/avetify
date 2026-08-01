<?php
namespace Avetify\Lister;

use Exception;

abstract class JsonLister extends AvtLister {
    public array $listerData;

    public function __construct(string $key, array $items) {
        parent::__construct($key, $items);
        $this->ensureListerData();
    }

    abstract public function getJsonStorageFilePath() : string;

    public function ensureListerData() : void {
        $dataDilePath = $this->getJsonStorageFilePath();
        if(!file_exists($dataDilePath)){
            $newData = [
                "lists" => [],
                "items" => []
            ];

            $ndRes = file_put_contents($dataDilePath, json_encode($newData));
            if(!$ndRes) throw new Exception("Cant create data json file");
            $this->ensureListerData();
        }

        $dataRawContents = file_get_contents($dataDilePath);
        $this->listerData = json_decode($dataRawContents, true);
    }

    public function getItemCategoryOriginalPk($item) {
        $itemId = $item->getItemId();
        return $this->listerData['items'][$itemId]['list'] ?? 0;
    }

    public function catOrgPkToListIndex($item): int {
        return $this->getItemCategoryOriginalPk($item);
    }

    public function listIndexToNewOrgPk($listIndex): int {
        return $listIndex;
    }

    public function getSecondarySortFactor($item){
        $itemId = $item->getItemId();
        return $this->listerData['items'][$itemId]['priority'] ?? count($this->records) + 1000;
    }

    public function getListTitles(): array {
        $listsData = $this->listerData['lists'];
        $titles = ["unlisted"];
        foreach ($listsData as $ld){
            $titles[] = $ld['title'];
        }
        return $titles;
    }

    public function handleSubmittedList(array $lists, array $itemsParams, $allFields) {
        $listsCount = count($lists);

        $listsData = [];
    }
}