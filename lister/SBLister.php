<?php
abstract class SBLister {
    public array $allItems = [];
    public array $initItemsMap = [];
    private int | null $cardImageWidth = null;
    private float | null $cardImageHeightMultiplier = null;

    public function __construct($items){
        $this->setItems($items);
    }

    public function setItems($items){
        $this->allItems = $items;
    }

    abstract public function getItemId($item) : string;

    abstract public function getItemImage($item) : string;

    abstract public function getItemTitle($item) : string;

    public function setCardImageDimension($cw, $hmp = 1.3){
        $this->cardImageWidth = $cw;
        $this->cardImageHeightMultiplier = $hmp;
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

    /**
     * @return SBListCategory[] An array of MyClass instances
     */
    abstract public function getCategories() : array;

    abstract public function handleSubmittedList(array $lists, array $itemsParams, $allFields);

    //key, title, ValueGetter
    public function getItemFields() : array {
        return [];
    }

    public function getPermanentCategoriesCount() : int | null {
        return null;
    }

    function initLists(){
        foreach ($this->allItems as $item){
            $this->initItemsMap[$this->getItemId($item)] = $this->catOrgPkToListIndex($item);
        }

        usort($this->allItems, function ($a, $b){
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

    public function itemMorePresent() {}

    public function formMoreFields() {}

    public function moreBodyContents() {}

    public function getOpenImageWord() : string {
        return "Show Image";
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
            echo '<div class="item" style="width:' . $fullWidth . 'px" onclick="action(5, jsArgs)">';
            echo $this->getOpenImageWord();
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

    public function renderAllCategories(){
        $cursor = 0;
        $categories = $this->getCategories();
        $perm = $this->getPermanentCategoriesCount();
        if($perm == null) $perm = count($categories);

        for($i=0; count($categories)>$i; $i++){
            $this->printCategorySection($categories[$i], $cursor, $perm <= $i);
        }
    }

    public function printCategorySection(SBListCategory $category, &$cursor, $hide = false){
        $categoryTitle = $category->title;
        $msecID = "msec_" . $category->index;
        $msecTitleID = "msec_title_" . $category->index;
        echo '<div class="magham-section" id="' . $msecID . '" ';
        echo ' style="display: ' . (!$hide ? "block" : "none") . ';"';
        echo ' >';
        echo '<div class="magham-box">';
        echo '<span class="magham-degree" id="' . $msecTitleID . '">' . $categoryTitle . '</span>';
        echo '</div>';
        echo '<div id="gridDemo' . $category->index . '" class="row" ';
        Styler::startAttribute();
        Styler::addStyle("overflow", "auto");
        Styler::addStyle("position", "relative");
        Styler::addStyle("justify-content", "center");
        Styler::closeAttribute();
        echo ' >';
        $this->printCategoryCards($category, $cursor);
        echo '</div>';
        echo '<hr />';
        echo '</div>';
    }

    public function printCategoryCards(SBListCategory $category, &$cursor){
        $itemRank = 1;
        while($cursor < count($this->allItems) && $this->initItemsMap[$this->getItemId($this->allItems[$cursor])] <= $category->index) {
            $this->printItemCard($this->allItems[$cursor], $category, $itemRank);
            $cursor++;
            $itemRank++;
        }
    }

    public function printItemCard($item, SBListCategory | null $category, $itemRank){
        $itemId = $this->getItemId($item);
        echo '<div class="grid-square" id="lister-item_' . $itemId . '"';
        if($this->cardImageWidth != null){
            $boxWidth = $this->cardImageWidth + 25;
            echo ' style="width: ' . $boxWidth . 'px;"';
        }
        echo '>';

        $avatar = $this->getItemImage($item);
        echo '<img src="' . $avatar . '" class="lister-item-img" ';
        if($this->cardImageWidth != null){
            $imageHeight = (int)($this->cardImageHeightMultiplier * $this->cardImageWidth);
            echo ' style="width: ' . $this->cardImageWidth . 'px; height: ' . $imageHeight . 'px; "';
        }
        echo '/>';

        heightMargin(4);
        if($this->isPrintRankEnabled()){
            $rankStyler = new Styler();
            $rankStyler->pushStyle("font-size", "0.875rem");
            $rankStyler->pushStyle("font-weight", "bold");
            echo '<span ';
            $rankStyler->applyStyles();
            $rankId = "lister-rank_" . $itemId;
            HTMLInterface::addAttribute("id", $rankId);
            echo ' >';
            echo $itemRank;
            echo '</span>';
            echo '<span ';
            $rankStyler->applyStyles();
            echo '>: </span>';
        }
        echo '<span class="lister-item-name">' . $this->getItemTitle($item) . '</span>';

        $alt = $this->getItemAlt($item);
        if($alt) echo '<span class="lister-item-rate"> (' . $item['kingrate'] . ')</span>';

        $plainFields = [];
        $dialogFields = [];

        foreach ($this->getItemFields() as $field){
            if(isset($field['factory'])) $dialogFields[] = $field;
            else $plainFields[] = $field;
        }

        if(count($dialogFields) > 0){
            $niceDiv = new NiceDiv("10px");
            $niceDiv->addStyle("width", "95%");
            $niceDiv->open();

            foreach ($dialogFields as $field){
                $fieldId = $field['key'] . '_' . $itemId;
                $dialogField = $field['factory']->makeDialogField($fieldId, $field['value']($item));
                $niceDiv->placeItem($dialogField);
                if($dialogField instanceof MaghamField) $niceDiv->resetItemsCount();
            }

            $niceDiv->close();

            if(count($plainFields) > 0){
                heightMargin(6);
            }
        }

        foreach ($plainFields as $field){
            $fieldId = $field['key'] . '_' . $itemId;
            heightMargin(4);
            echo '<div style="display: flex">';
            echo '<span style="font-size: 10pt; margin-right: 6px;">' . $field['title'] . ': </span>';
            echo '<input type="text" id="' . $fieldId . '"'
                . ' value="' . $field['value']($item)
                . '" placeholder="' . $field['title']
                . '" class="empty" style="width: 80%; height: 30px; font-size: 11pt; margin-top: -4px;"'
                . ' onfocus="this.select();" />';
            echo '</div>';
        }

        echo '<div>';
        $this->itemMorePresent();
        echo '</div>';
        echo '</div>';
    }

    public function readyForm(){
        $allFieldIds = [];
        foreach ($this->getItemFields() as $field){
            foreach ($this->allItems as $item){
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

    public function openPage(){
        $theme = $this->getTheme();
        $theme->placeHeader($this->getPageTitle());
        $theme->loadHeaderElements();
    }

    public function getTheme() : ThemesManager {
        $theme = new GreenTheme();
        $theme->includesListerTools = true;
        return $theme;
    }

    function renderBody(){
        echo '<body>';
        $this->catchNewList();
        $this->initLists();
        $this->initJsArgs();
        $this->placeMenu();

        echo '<div class="container" style="width: 90%; max-width: 90%; margin: auto;">
		    <div id="grid" class="col">';

        echo '<form method="post" id="lister_form" name="lister_form">';
        $this->renderAllCategories();
        echo '</div>';

        $this->formMoreFields();
        echo '<input type="hidden" id="newlist" name="newlist">
              <input type="hidden" id="lister_params" name="lister_params">
        </form>';

        if($this->isPrintRankEnabled() && $this->isRearrangeRanksEnabled()){
            HTMLInterface::addAbsoluteIconButton(Routing::getAventadorRoot()
                . 'assets/img/arrange.png',
                [
                    "right" => "20px",
                    "bottom" => "20px"
                ],
                "rearrangeRanks()");
        }

        $primaryButton = new PrimaryButton("listerSubmit(jsArgs); submitForm('lister_form');");
        $primaryButton->place();

        ThemesManager::importJS(Routing::getAventadorRoot() . 'lister/lister.js');
        $this->initMenu();
        $this->readyForm();
        $this->moreBodyContents();
        echo '</html>';
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

    function renderPage(){
        $this->openPage();
        $this->renderBody();
    }
}