<?php
abstract class SBLister extends SetModifier implements PageRenderer {
    use EntityManagerTrait;

    public ?ListerRenderer $listerRenderer = null;
    public array $initItemsMap = [];
    public bool $placeDefaultTriggers = true;

    public function __construct(string $key, array $items){
        parent::__construct($key);
        $this->listerRenderer = $this->getListerRenderer();
        $this->loadRawRecords($items);
    }

    public function getItemAlt($item) : ?string {
        return null;
    }

    public function getSecondarySortFactor($item){
        return null;
    }

    public function isSecondarySortAscending() : bool {
        return true;
    }

    public function getAlterSortFactor($item){
        return null;
    }

    public function isAlterSortAscending() : bool {
        return true;
    }

    //meghdari ke neshun dahande jaygahe record tuye list hast.
    //vali deghat konid indexe list azash moshtagh mishe va be khodie khod dg karbord nadare
    abstract public function getItemCategoryOriginalPk($item);

    //shomare balatarin list 0 ast va be hamin tartib masalan shomare dovomin list az bala yek ast.
    //in method shomare listi ke dar zamane load item dar an gharar migirad ra midahad.
    abstract public function catOrgPkToListIndex($item): int;

    //mojadadan baraye makus sazie methode bala estefade mishavad.
    //pas az submite list az an estefade mishavad ta db ra beruz konim.
    abstract public function listIndexToNewOrgPk($listIndex): int;

    public function getRecordListTitle($orgCatPk) : string | null {
        $listTitles = $this->getListTitles();
        $listIndex = $this->catOrgPkToListIndex($orgCatPk);
        if($listIndex >= 0 && $listIndex < count($listTitles)) return $listTitles[$listIndex];
        return null;
    }

    /**
     * @return string[] An array of MyClass instances
     */
    abstract public function getListTitles() : array;

    /**
     * @return SBListCategory[] An array of MyClass instances
     */
    public function getCategories() : array {
        $listTitles = $this->getListTitles();
        $categories = [];

        $index = 0;
        foreach ($listTitles as $listTitle){
            $categories[] = new SBListCategory($index++, $listTitle);
        }
        return $categories;
    }

    public function getListsCount() : int {
        return count($this->getListTitles());
    }

    abstract public function handleSubmittedList(array $lists, array $itemsParams, $allFields);

    //key, title, ValueGetter
    public function getItemFields() : array {
        return [];
    }

    public function getPermanentCategoriesCount() : int | null {
        return null;
    }

    function getListerRenderer() : ListerRenderer {
        return new ListerRenderer($this, new GreenTheme());
    }

    function initLists(){
        foreach ($this->currentRecords as $item){
            $this->initItemsMap[$this->getItemId($item)] = $this->catOrgPkToListIndex($item);
        }

        usort($this->currentRecords, function ($a, $b){
            $aValue = $this->initItemsMap[$this->getItemId($a)];
            $bValue = $this->initItemsMap[$this->getItemId($b)];

            if ($aValue == $bValue){
                $aSec = $this->getSecondarySortFactor($a);
                $bSec = $this->getSecondarySortFactor($b);
                if($aSec == $bSec){
                    $aAlt = $this->getAlterSortFactor($a);
                    $bAlt = $this->getAlterSortFactor($b);
                    $res = $aAlt > $bAlt ? 1 : -1;
                    return $this->isAlterSortAscending() ? $res : (-1 * $res);
                }
                $res = $aSec > $bSec ? 1 : -1;
                return $this->isSecondarySortAscending() ? $res : (-1 * $res);
            }
            return $aValue > $bValue ? 1 : -1;
        });
    }

    public function getDirectMenuCategoriesIndexList() : array {
        return [];
    }

    /**
     * @return SBListCategory[] An array of MyClass instances
     */
    public function getDirectMenuCategories() : array {
        $dmcIndexes = $this->getDirectMenuCategoriesIndexList();
        $allCategories = $this->getCategories();
        $out = [];

        foreach ($allCategories as $category){
            if(in_array($category-> index, $dmcIndexes)) $out[] = $category;
        }

        return $out;
    }

    public function getOpenImageWord() : string {
        return "Show Image";
    }

    public function getCopyImageWord() : string {
        return "Copy Link";
    }

    public function getRelegateWord() : string {
        return "Relegate";
    }

    public function getPromoteWord() : string {
        return "Promote";
    }

    public function getFirstWord() : string {
        return "First";
    }

    public function getLastWord() : string {
        return "Last";
    }

    public function getFastTransferWord() : string {
        return "Fast Transfer";
    }

    abstract public function getPageTitle() : string;

    public function isRelegateAndPromoteEnabled() : bool {
        return true;
    }

    public function isFirstAndLastEnabled() : bool {
        return true;
    }

    public function isOpenImageEnabled() : bool {
        return false;
    }

    public function isFastTransferEnabled() : bool {
        return true;
    }

    public function isPrintRankEnabled() : bool {
        return true;
    }

    public function isRearrangeRanksEnabled() : bool {
        return true;
    }

    function initJsArgs(){
        echo '<script>';
        echo 'const jsArgs = {"lists_count": ' . count($this->getCategories()) . '}';
        echo '</script>';
    }

    function placeMenu() {
        $directCategories = $this->getDirectMenuCategories();

        $fullWidth = 230;
        $halfWidth = 115;
        echo '<div id="context-menu" class="context-menu-nice" ';
        if (count($directCategories) > 16) {
            $rowCount = (int)(count($directCategories) / 10);
            if ($rowCount > 3) $rowCount = 3;
            $menuWidth = $rowCount * $halfWidth;
            $fullWidth = $menuWidth;
            echo 'style="width: ' . $menuWidth . '"';
        }
        echo ' >';

        echo '<div class="context-menu-row">';
        if ($this->isOpenImageEnabled()) {
            echo '<div class="item" style="width:' . $halfWidth . 'px" onclick="action(5, jsArgs)">';
            echo $this->getOpenImageWord();
            echo '</div>';

            echo '<div class="item" style="width:' . $halfWidth . 'px" onclick="action(6, jsArgs)">';
            echo $this->getCopyImageWord();
            echo '</div>';
        }

        if ($this->isRelegateAndPromoteEnabled()) {
            echo '<div class="item" style="width:' . $halfWidth . 'px" onclick="action(2, jsArgs)">';
            echo $this->getPromoteWord();
            echo '</div>';

            echo '<div class="item" style="width:' . $halfWidth . 'px" onclick="action(3, jsArgs)">';
            echo $this->getRelegateWord();
            echo '</div>';
        }

        if ($this->isFirstAndLastEnabled()) {
            echo '<div class="item" style="width:' . $halfWidth . 'px" onclick="action(0, jsArgs)">';
            echo $this->getFirstWord();
            echo '</div>';

            echo '<div class="item" style="width:' . $halfWidth . 'px" onclick="action(1, jsArgs)">';
            echo $this->getLastWord();
            echo '</div>';
        }

        if ($this->isFastTransferEnabled()) {
            echo '<div class="item" style="width:' . $fullWidth . 'px" onclick="action(4, jsArgs)">';
            echo $this->getFastTransferWord();
            echo '</div>';
        }

        echo '</div>';

        if (count($directCategories) > 0) {
            echo '<div style="border: none; height: 1px; background-color: white; margin-top: 1px; margin-bottom: 1px;">';
            echo '</div>';
        }

        echo '<div class="context-menu-row" id="menu_directs">';
        for($i = 0; count($directCategories)>$i; $i++){
            $isLast = count($directCategories) <= ($i + 1);
            $isOddItem = ($i % 2) == 0;
            $w = $isLast && $isOddItem ? ($fullWidth . "px") : ($halfWidth . "px");
            echo '<div class="item" style="width: ' . $w . '" onclick="transfer(' .
                $directCategories[$i]->index . ')">';
            echo  $directCategories[$i]->title;
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }

    function initMenu(){
        echo '<script>';
        echo 'initMenu();';
        echo '</script>';
    }

    public function readyForm(){
        $allFieldIds = [];
        foreach ($this->getItemFields() as $field){
            foreach ($this->currentRecords as $item){
                $allFieldIds[] = $field['key'] . "_" . $this->getItemId($item);
            }
        }

        JSInterface::declareGlobalJSArgs("listerArgs");
        FormUtils::readyFormToCatchNoNamedFields(
            "listerArgs",
            "lister_form",
            "lister_params",
            json_encode($allFieldIds)
        );
    }

    function catchNewList(){
        if(!empty($_POST['newlist'])){
            $newList = $_POST['newlist'];
            $allRawLists = explode("##", $newList);
            $allLists = [];
            $itemsParams = [];

            foreach ($allRawLists as $key => $raw){
                $allLists[$key] = $raw ? explode(",", $raw) : [];

                for($i=0; count($allLists[$key])>$i; $i++){
                    $itemPk = substr($allLists[$key][$i], 12);
                    $allLists[$key][$i] = $itemPk;
                    if(!isset($itemsParams[$itemPk])) $itemsParams[$itemPk] = [];
                }
            }

            $listerParams = !empty($_POST['lister_params']) ?
                json_decode($_POST['lister_params'], true) : [];

            foreach (array_keys($itemsParams) as $itemPk){
                foreach ($this->getItemFields() as $field){
                    $fieldName = $field['key'] . '_' . $itemPk;
                    if(isset($listerParams[$fieldName])){
                        $itemsParams[$itemPk][$field['key']] = $listerParams[$fieldName];
                    }
                }
            }

            $this->handleSubmittedList($allLists, $itemsParams, $_POST);
        }
    }

    public function renderPage(?string $title = null) {
        if($this->listerRenderer){
            $this->listerRenderer->renderPage($title);
        }
    }
}