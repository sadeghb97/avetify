<?php

class ListerRenderer extends BaseSetRenderer {
    public SBLister $lister;
    public bool $focusMode = false;
    public bool $galleryMode = false;
    public bool $noCacheMode = false;
    public bool $mainLinksBlank = true;
    protected int | null $cardImageWidth = null;
    protected float | null $cardImageHeightMultiplier = null;
    public WebModifier $containerModifier;
    public string $containerId;

    public function postConstruct() {
        /** @var SBLister $l */
        $l = $this->setModifier;
        $this->lister = $l;

        $this->containerId = "lister_" . time();
        $this->containerModifier = WebModifier::createInstance();
        $this->containerModifier->htmlModifier->pushModifier(Attrs::id, $this->containerId);
        $this->containerModifier->styler->pushClass("container");
        $this->containerModifier->styler->pushStyle(CSS::width, "90%");
        $this->containerModifier->styler->pushStyle(CSS::margin, "auto");

        if($this->galleryMode)$this->containerModifier->styler->pushClass("gallery");
        else if($this->focusMode)$this->containerModifier->styler->pushClass("focus");
    }

    public function setCardImageDimension($cw, $hmp = 1.3){
        $this->cardImageWidth = $cw;
        $this->cardImageHeightMultiplier = $hmp;
    }

    public function getTitle(): string {
        return $this->lister->getPageTitle();
    }

    public function openContainer() {
        $this->lister->catchNewList();
        $this->lister->initLists();
        $this->lister->initJsArgs();
        $this->lister->placeMenu();

        echo '<div ';
        HTMLInterface::applyClasses($this->containerModifier);
        HTMLInterface::applyStyles($this->containerModifier);
        HTMLInterface::applyModifiers($this->containerModifier);
        HTMLInterface::closeTag();

        echo '<div id="grid" class="col">';
    }

    public function closeContainer() {
        $this->formMoreFields();
        echo '<input type="hidden" id="newlist" name="newlist">
              <input type="hidden" id="lister_params" name="lister_params">
        </form>';

        if($this->lister->placeDefaultTriggers) {
            if ($this->lister->isPrintRankEnabled() && $this->lister->isRearrangeRanksEnabled()) {
                HTMLInterface::addAbsoluteIconButton(AssetsManager::getImage('arrange.png'),
                    [
                        "inset-inline-start" => "20px",
                        "bottom" => "20px"
                    ],
                    "rearrangeRanks()");
            }

            $primaryButton = new PrimaryButton("listerSubmit(jsArgs); submitForm('lister_form');");
            $primaryButton->place();
        }

        ThemesManager::importJS(AssetsManager::getAsset('components/lister/init_lister.js'));
        $this->lister->initMenu();
        $this->lister->readyForm();
        $this->moreBodyContents();

        $this->closePage();
    }

    public function openCollection(WebModifier $webModifier = null) {
        echo '<form method="post" id="lister_form" name="lister_form">';
    }

    public function closeCollection(WebModifier $webModifier = null) {
        echo '</div>';
    }

    public function renderSet() {
        $this->openCollection();
        $this->renderAllCategories();
        $this->closeCollection();
    }

    public function renderRecordMain($item, int $index) {
        // TODO: Implement renderRecordMain() method.
    }

    public function printItemCard($item, SBListCategory | null $category, $itemRank){
        $itemId = $this->lister->getItemId($item);
        echo '<div ';
        HTMLInterface::addAttribute(Attrs::id,'lister-item_' . $itemId);
        Styler::classStartAttribute();
        Styler::addClass("grid-square");
        Styler::closeAttribute();
        Styler::startAttribute();
        $this->appendCardWidthStyles();
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        $avatar = $this->lister->getItemImage($item);
        if($this->noCacheMode) $avatar .= ("?" . time());

        echo '<img ';
        HTMLInterface::addAttribute(Attrs::src, $avatar);
        Styler::classStartAttribute();
        Styler::addClass("lister-item-img");
        Styler::closeAttribute();
        Styler::startAttribute();
        $this->appendImageWidthStyles();
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();

        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("grid-square-footer");
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        HTMLInterface::placeVerticalDivider(12);
        if ($this->lister->isPrintRankEnabled()) {
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

        $link = $this->lister->getItemLink($item);
        if ($link) {
            echo '<a ';
            HTMLInterface::addAttribute("href", $link);
            if($this->mainLinksBlank) HTMLInterface::addAttribute("target", "_blank");
            HTMLInterface::addAttribute("class", "lister-item-link");
            HTMLInterface::closeTag();
        }

        $finalTitle = $this->lister->getAdjustedTitle($item);
        echo '<span class="lister-item-name">' . $finalTitle . '</span>';
        if ($link) HTMLInterface::closeLink();

        $alt = $this->lister->getItemAlt($item);
        if ($alt) echo '<span class="lister-item-rate"> (' . $alt . ')</span>';
        echo '</div>';

        $plainFields = [];
        $dialogFields = [];

        foreach ($this->lister->getItemFields() as $field) {
            if (isset($field['factory'])) $dialogFields[] = $field;
            else $plainFields[] = $field;
        }

        if (count($dialogFields) > 0) {
            $niceDiv = new NiceDiv("10px");
            $niceDiv->addStyle("width", "95%");
            $niceDiv->open();

            foreach ($dialogFields as $field) {
                $fieldId = $field['key'] . '_' . $itemId;
                $dialogField = $field['factory']->makeDialogField($fieldId, $field['value']($item));
                $niceDiv->placeItem($dialogField);
                if ($dialogField instanceof MaghamField) $niceDiv->resetItemsCount();
            }

            $niceDiv->close();

            if (count($plainFields) > 0) {
                HTMLInterface::placeVerticalDivider(6);
            }
        }

        foreach ($plainFields as $field) {
            $fieldId = $field['key'] . '_' . $itemId;
            HTMLInterface::placeVerticalDivider(4);
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

    public function renderAllCategories(){
        $cursor = 0;
        $categories = $this->lister->getCategories();
        $perm = $this->lister->getPermanentCategoriesCount();
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
        while($cursor < count($this->lister->currentRecords) &&
            $this->lister->initItemsMap[$this->lister->getItemId($this->lister->currentRecords[$cursor])] <= $category->index) {
            $this->printItemCard($this->lister->currentRecords[$cursor], $category, $itemRank);
            $cursor++;
            $itemRank++;
        }
    }

    public function appendCardWidthStyles(){
        if($this->cardImageWidth) {
            Styler::addStyle(CSS::width, ($this->cardImageWidth + 25) . "px");
        }
    }

    public function appendImageWidthStyles(){
        if($this->cardImageWidth != null){
            $finalImageWidth = ($this->focusMode || $this->galleryMode) ? $this->cardImageWidth + 25 :
                $this->cardImageWidth;

            Styler::addStyle(CSS::width, $finalImageWidth . "px");
            if($this->cardImageHeightMultiplier > 0){
                $imageHeight = (int)($this->cardImageHeightMultiplier * $finalImageWidth);
                Styler::addStyle(CSS::height, $imageHeight . "px");
            }
            else Styler::addStyle(CSS::height, $this->cardImageWidth . "px");
        }
    }

    public function openPage(string $title = ""){
        $finalTitle = $title ? $title : $this->getTitle();
        if(!$this->theme) $this->theme = $this->getTheme();
        $theme = $this->getTheme();
        $theme->placeHeader($finalTitle);
        echo '<body>';
        $theme->loadHeaderElements();
    }

    public function closePage(){
        if(!$this->theme) $this->theme = $this->getTheme();
        $this->theme->lateImports();
        echo '</body>';
    }

    public function getTheme() : ThemesManager {
        $theme = new GreenTheme();
        $theme->includesListerTools = true;
        return $theme;
    }

    function renderPage(?string $title = null){
        $this->openPage();
        $this->renderBody();
        $this->closePage();
    }

    public function itemMorePresent() {}

    public function formMoreFields() {}

    public function moreBodyContents() {}
}
