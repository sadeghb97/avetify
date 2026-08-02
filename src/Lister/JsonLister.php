<?php
namespace Avetify\Lister;

use Avetify\Entities\EntityUtils;
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
            return;
        }

        $dataRawContents = file_get_contents($dataDilePath);
        $parsedListerData = json_decode($dataRawContents, true);

        $normalizeIndexRequired = false;
        foreach ($parsedListerData['lists'] as $listIndex => $listDetails){
            if(($listIndex + 1) != $listDetails['index']){
                $normalizeIndexRequired = true;
                break;
            }
        }

        if($normalizeIndexRequired){
            $normalizedLists = self::normalizeListIndexes($parsedListerData['lists']);
            $this->storeData($normalizedLists, $parsedListerData['items']);
            $this->ensureListerData();
            return;
        }

        $this->listerData = $parsedListerData;
        $this->listerData['ls_map'] = [];
        foreach ($this->listerData['lists'] as $listIndex => $list){
            $this->listerData['ls_map'][$list['id']] = $listIndex;
        }
    }

    private static function normalizeListIndexes($badListsData) : array {
        EntityUtils::simpleSort($badListsData, "index", true);
        for($i=0; count($badListsData) > $i; $i++){
            $badListsData[$i]['index'] = $i + 1;
        }
        return $badListsData;
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
        $listId = $this->getItemCategoryOriginalPk($item);
        if(!isset($this->listerData['ls_map'][$listId])) return 0;
        return $this->listerData['lists'][$this->listerData['ls_map'][$listId]]['index'] ?? 0;
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

    public function getListIds(): array {
        $listsData = $this->listerData['lists'];
        $ids = ["avt_zero_list"];
        foreach ($listsData as $ld){
            $ids[] = $ld['id'];
        }
        return $ids;
    }

    public function handleSubmittedList(array $lists, array $itemsParams, $allFields) {
        $outListsData = [];
        for ($i=0; (count($lists) - 1) > $i; $i++){
            $outListsData[] = [
                "id" => $lists[$i]['id'],
                "title" => $lists[$i]['title'],
                "index" => count($lists) - $i - 1
            ];
        }
        EntityUtils::simpleSort($outListsData, 'index', true);

        $itemsData = [];
        foreach ($lists as $listIndex => $listDetails){
            foreach ($listDetails['ids'] as $internalListIndex => $itemId){
                $targetOutListIndex = count($lists) - $listIndex - 2;
                $listId = $targetOutListIndex >= 0 ? $outListsData[$targetOutListIndex]['id'] : "avt_zero_list";

                $itemsData[$itemId] = [
                    "list" => $listId,
                    "priority" => $internalListIndex
                ];
            }
        }

        $this->storeData($outListsData, $itemsData);
        $this->ensureListerData();
    }
}