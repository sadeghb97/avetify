<?php
namespace Avetify\Themes\Main;

use Avetify\AvetifyManager;
use Avetify\Components\Buttons\PrimaryButton;
use Avetify\Components\Modals\AvtModal;
use Avetify\Interface\CSS\CSS;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\Attrs;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;
use Avetify\Lister\AvtLister;
use Avetify\Lister\Components\ArrangeListsModal;
use Avetify\Lister\Components\BatchTransferModal;
use Avetify\Lister\Components\ManageListsModal;
use Avetify\Lister\ListerCategory;
use Avetify\Themes\Green\GreenTheme;

abstract class ListerRenderer extends BaseSetRenderer {
    public AvtLister $lister;
    public ?ArrangeListsModal $arrangeModal = null;
    public ?ManageListsModal $manageModal = null;
    public ?BatchTransferModal $transferModal = null;
    public int $triggersGap = 70;

    public function postConstruct() {
        /** @var AvtLister $l */
        $l = $this->setModifier;
        $this->lister = $l;
        $this->manageModal = new ManageListsModal();
        $this->arrangeModal = new ArrangeListsModal();
        $this->transferModal = new BatchTransferModal();
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

        $triggersMoreMarginBottom = $this->theme ? $this->theme->containerMarginBottom : 0;
        $triggersMoreMarginTop = $this->theme ? $this->theme->containerMarginTop : 0;
        $leftBottomMargin = 20 + $triggersMoreMarginBottom;
        $rightTopMargin = 20 + $triggersMoreMarginTop;

        if($this->lister->placeDefaultTriggers) {
            if ($this->lister->isPrintRankEnabled() && $this->lister->isRearrangeRanksEnabled()) {
                HTMLInterface::addAbsoluteIconButton(AvetifyManager::imageUrl('arrange.png'),
                    [
                        "inset-inline-start" => "20px",
                        "bottom" => $leftBottomMargin . "px"
                    ],
                    "rearrangeRanks()");
                $leftBottomMargin += $this->triggersGap;
            }

            $primaryButton = new PrimaryButton("listerSubmit(jsArgs); submitForm('lister_form');");
            $primaryButton->place();
        }

        if($this->lister->placeCreateListTrigger){
            HTMLInterface::addAbsoluteIconButton(AvetifyManager::imageUrl('add_box.svg'),
                [
                    "inset-inline-start" => "20px",
                    "bottom" => $leftBottomMargin . "px"
                ],
                "addNewList()");
            $leftBottomMargin += $this->triggersGap;
        }

        if($this->lister->placeReorderListsTrigger){
            $this->arrangeModal->attachTemplate();
            HTMLInterface::addAbsoluteIconButton(AvetifyManager::imageUrl('swap_vert.svg'),
                [
                    "inset-inline-start" => "20px",
                    "bottom" => $leftBottomMargin . "px"
                ],
                $this->arrangeModal->openScript());
            $leftBottomMargin += $this->triggersGap;
        }

        if($this->lister->placeManageListsTrigger){
            $this->manageModal->attachTemplate();
            HTMLInterface::addAbsoluteIconButton(AvetifyManager::imageUrl('delete_sweep.svg'),
                [
                    "inset-inline-start" => "20px",
                    "bottom" => $leftBottomMargin . "px"
                ],
                $this->manageModal->openScript());
            $leftBottomMargin += $this->triggersGap;
        }

        if($this->lister->placeBatchTransferTrigger){
            $this->transferModal->attachTemplate();
            HTMLInterface::addAbsoluteIconButton(AvetifyManager::imageUrl('sync_alt.svg'),
                [
                    "inset-inline-start" => "20px",
                    "bottom" => $leftBottomMargin . "px"
                ],
                $this->transferModal->openScript());
            $leftBottomMargin += $this->triggersGap;
        }

        if($this->lister->placeResetListsTrigger){
            $this->manageModal->attachTemplate();
            HTMLInterface::addAbsoluteIconButton(AvetifyManager::imageUrl('remove_from_queue.svg'),
                [
                    "inset-inline-start" => "20px",
                    "bottom" => $leftBottomMargin . "px"
                ],
                'resetLists()');
            $leftBottomMargin += $this->triggersGap;
        }

        if($this->lister->placeToggleUnlistedTrigger){
            $modifier = WebModifier::createInstance();
            $modifier->pushModifier("data-next-image", AvetifyManager::imageUrl("collapse_content.svg"));
            HTMLInterface::addAbsoluteIconButton(AvetifyManager::imageUrl('expand_content.svg'),
                [
                    "inset-inline-start" => "20px",
                    "bottom" => $leftBottomMargin . "px"
                ],
                'toggleUnlisted(this)', $modifier);
            $leftBottomMargin += $this->triggersGap;
        }

        if($this->lister->placeNavListsTriggers){
            HTMLInterface::addAbsoluteIconButton(AvetifyManager::imageUrl('key_arrow_up.svg'),
                [
                    "inset-inline-end" => "20px",
                    "top" => $rightTopMargin . "px"
                ],
                "navigateBetweenLists(-1)");
            $rightTopMargin += $this->triggersGap;

            HTMLInterface::addAbsoluteIconButton(AvetifyManager::imageUrl('key_arrow_down.svg'),
                [
                    "inset-inline-end" => "20px",
                    "top" => $rightTopMargin . "px"
                ],
                "navigateBetweenLists(1)");
            $rightTopMargin += $this->triggersGap;
        }

        $this->initListers();
        $this->lister->initMenu();
        $this->lister->readyForm();
        $this->moreBodyContents();

        $this->closePage();
    }

    public function initListers() : void {
        echo '<script>';
        echo 'initListers()';
        echo '</script>';
    }

    public function openCollection(?WebModifier $webModifier = null) {
        echo '<form method="post" id="lister_form" name="lister_form">';

        $stIdentifier = "submit_type";
        echo '<input type="hidden" id="' . $stIdentifier
            . '" name="' . $stIdentifier . '" value="' . 'normal' . '">';
    }

    public function closeCollection(?WebModifier $webModifier = null) {
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
        Styler::addClass("js__avt-list-item");
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
        $categories = $this->lister->getCategories();
        $perm = $this->lister->getPermanentCategoriesCount();
        if($perm == null) $perm = count($categories);

        $cateCount = count($categories);
        for($i=0; $cateCount>$i; $i++){
            $categoryIndex = !$this->lister->renderListsInReverseOrder ? $i : ($cateCount - $i - 1);
            $this->printCategorySection($categories[$categoryIndex], $perm <= $i);
        }
    }

    public function printCategorySection(ListerCategory $category, $hide = false){
        $categoryTitle = $category->title;
        $categoryId = $category->identifier;
        $categoryGridId = "grid_" . $categoryId;
        $msecID = "msec_" . $category->index;
        $msecTitleID = "msec_title_" . $category->index;

        echo '<div class="magham-section js__avt-lister" id="' . $msecID . '" ';
        HTMLInterface::addNormalizedAttribute('data-list-title', $categoryTitle);
        HTMLInterface::addNormalizedAttribute('data-list-id', $categoryId);
        echo ' style="display: ' . (!$hide ? "block" : "none") . ';"';
        echo ' >';

        echo '<section ';
        Styler::classStartAttribute();
        Styler::addClass("js__avt-listers-section");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo '</section>';

        echo '<div class="magham-box">';
        echo '<span class="magham-degree js__avt-lister-title" id="' . $msecTitleID . '">' . $categoryTitle . '</span>';
        echo '</div>';
        echo '<div class="row js__avt-grid" ';
        HTMLInterface::addAttribute("id", $categoryGridId);
        Styler::startAttribute();
        Styler::addStyle("overflow", "auto");
        Styler::addStyle("position", "relative");
        Styler::addStyle("justify-content", "center");
        Styler::closeAttribute();
        echo ' >';
        $this->printCategoryCards($category);
        echo '</div>';
        echo '<hr />';
        echo '</div>';
    }

    public function printCategoryCards(ListerCategory $category){
        foreach ($category->records as $recordIndex => $record){
            $itemRank = $recordIndex + 1;
            $this->printItemCard($record, $category, $itemRank);
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
