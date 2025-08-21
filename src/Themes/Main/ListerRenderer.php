<?php
namespace Avetify\Themes\Main;

use Avetify\AvetifyManager;
use Avetify\Components\Buttons\PrimaryButton;
use Avetify\Interface\Attrs;
use Avetify\Interface\CSS;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Lister\AvtLister;
use Avetify\Lister\ListerCategory;
use Avetify\Themes\Green\GreenTheme;

abstract class ListerRenderer extends BaseSetRenderer {
    public AvtLister $lister;

    public function postConstruct() {
        /** @var AvtLister $l */
        $l = $this->setModifier;
        $this->lister = $l;
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
                HTMLInterface::addAbsoluteIconButton(AvetifyManager::imageUrl('arrange.png'),
                    [
                        "inset-inline-start" => "20px",
                        "bottom" => "20px"
                    ],
                    "rearrangeRanks()");
            }

            $primaryButton = new PrimaryButton("listerSubmit(jsArgs); submitForm('lister_form');");
            $primaryButton->place();
        }

        ThemesManager::importJS(AvetifyManager::assetUrl('components/lister/init_lister.js'));
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

    public function appendCardStyles(){
        Styler::addStyle(CSS::display, "inline-block");
        Styler::addStyle(CSS::margin, "12px");
    }

    public function openRecord($record, int $index) {
        $itemId = $this->lister->getItemId($record);
        echo '<div ';
        HTMLInterface::addAttribute(Attrs::id,'lister-item_' . $itemId);
        Styler::classStartAttribute();
        Styler::closeAttribute();
        Styler::startAttribute();
        $this->appendCardStyles();
        Styler::closeAttribute();
        HTMLInterface::closeTag();
    }

    public function closeRecord($record, int $index) {
        echo '</div>';
    }

    public function printItemCard($item, ListerCategory | null $category, $itemRank){
        $this->openRecord($item, $itemRank);
        $this->renderRecordMain($item, $itemRank);
        $this->moreRecordFields($item, $itemRank);
        $this->closeRecord($item, $itemRank);
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

    public function printCategorySection(ListerCategory $category, &$cursor, $hide = false){
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

    public function printCategoryCards(ListerCategory $category, &$cursor){
        $itemRank = 1;
        while($cursor < count($this->lister->currentRecords) &&
            $this->lister->initItemsMap[$this->lister->getItemId($this->lister->currentRecords[$cursor])] <= $category->index) {
            $this->printItemCard($this->lister->currentRecords[$cursor], $category, $itemRank);
            $cursor++;
            $itemRank++;
        }
    }

    public function openPage(string $title = ""){
        $finalTitle = $title ? $title : $this->getTitle();
        if(!$this->theme) $this->theme = $this->defaultTheme();
        $this->theme->placeHeader($finalTitle);
        echo '<body>';
        $this->theme->loadHeaderElements();
    }

    public function closePage(){
        if(!$this->theme) $this->theme = $this->defaultTheme();
        $this->theme->lateImports();
        echo '</body>';
    }

    public function defaultTheme() : ThemesManager {
        $theme = new GreenTheme();
        $theme->includesListerTools = true;
        return $theme;
    }

    function renderPage(?string $title = null){
        $this->openPage();
        $this->renderBody();
        $this->closePage();
    }

    public function formMoreFields() {}

    public function moreBodyContents() {}
}
