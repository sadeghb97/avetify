<?php
namespace Avetify\Lister;

use Exception;

abstract class JsonLister extends AvtLister {
    public bool $renderListsInReverseOrder = true;
    public array $listerData;

    public function __construct(string $key, array $items) {
        parent::__construct($key, $items);
        $this->ensureListerData();
    }

    abstract public function getJsonStorageFilePath() : string;

    public function ensureListerData() : void {
        $dataDilePath = $this->getJsonStorageFilePath();
        if(!file_exists($dataDilePath)){
            $this->storeData([], []);
            $this->ensureListerData();
        }

        $dataRawContents = file_get_contents($dataDilePath);
        $this->listerData = json_decode($dataRawContents, true);
    }

    public function storeData(array $lists, array $items) : void {
        $dataDilePath = $this->getJsonStorageFilePath();
        $newData = [
            "lists" => $lists,
            "items" => $items
        ];

        $ndRes = file_put_contents($dataDilePath, json_encode($newData));
        if(!$ndRes) throw new Exception("Cant create data json file");
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
        $titles = ["Unlisted"];
        foreach ($listsData as $ld){
            $titles[] = $ld['title'];
        }
        return $titles;
    }

    public function handleSubmittedList(array $lists, array $itemsParams, $allFields) {
        $outListsData = [];
        for ($i=1; count($lists) > $i; $i++){
            $outListsData[] = [
                "title" => $lists[$i]['title'],
                "index" => $i
            ];
        }

        $itemsData = [];
        foreach ($lists as $listIndex => $listDetails){
            foreach ($listDetails['ids'] as $internalListIndex => $itemId){
                $itemsData[$itemId] = [
                    "list" => $listIndex,
                    "priority" => $internalListIndex
                ];
            }
        }

        $this->storeData($outListsData, $itemsData);
        $this->ensureListerData();
    }
}