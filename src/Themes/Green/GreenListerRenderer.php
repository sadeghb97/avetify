<?php
namespace Avetify\Themes\Green;

use Avetify\Interface\Attrs;
use Avetify\Interface\CSS;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Themes\Main\ListerRenderer;

class GreenListerRenderer extends ListerRenderer {
    public bool $focusMode = false;
    public bool $galleryMode = false;
    public bool $noCacheMode = false;
    public bool $mainLinksBlank = true;
    public int | null $cardImageWidth = null;
    public float | null $cardImageHeightMultiplier = null;
    public string $containerId;

    public function prepareContainerModifier() {
        parent::prepareContainerModifier();
        $this->containerId = "lister_" . time();
        $this->containerModifier->htmlModifier->pushModifier(Attrs::id, $this->containerId);
        $this->containerModifier->styler->pushClass("container");
        $this->containerModifier->styler->pushStyle(CSS::width, "90%");
        $this->containerModifier->styler->pushStyle(CSS::margin, "auto");

        if($this->galleryMode)$this->containerModifier->styler->pushClass("gallery");
        else if($this->focusMode)$this->containerModifier->styler->pushClass("focus");
    }

    public function appendRecordCardStyles(){
        if($this->cardImageWidth) {
            Styler::addStyle(CSS::width, ($this->cardImageWidth + 25) . "px");
        }
    }

    public function renderRecordMain($item, int $index) {
        $itemId = $this->lister->getItemId($item);
        $avatar = $this->lister->getItemImage($item);

        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("grid-square");
        Styler::closeAttribute();
        Styler::startAttribute();
        $this->appendRecordCardStyles();
        if($avatar) Styler::addStyle(CSS::minHeight, "180px");
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        if($avatar) {
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
        }

        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("grid-square-footer");
        Styler::closeAttribute();
        Styler::startAttribute();
        Styler::addStyle(CSS::marginTop, "12px");
        if(!$avatar) Styler::addStyle(CSS::marginBottom, "6px");
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        if ($this->lister->isPrintRankEnabled()) {
            $rankStyler = new Styler();
            $rankStyler->pushStyle("font-size", "0.875rem");
            $rankStyler->pushStyle("font-weight", "bold");
            echo '<span ';
            $rankStyler->applyStyles();
            $rankId = "lister-rank_" . $itemId;
            HTMLInterface::addAttribute("id", $rankId);
            echo ' >';
            echo $index;
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

        echo '</div>';
    }

    public function setCardImageDimension($cw, $hmp = 1.3){
        $this->cardImageWidth = $cw;
        $this->cardImageHeightMultiplier = $hmp;
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
            else if($this->galleryMode || $this->focusMode){
                $imageHeight = (int)(1 * $finalImageWidth);
                Styler::addStyle(CSS::height, $imageHeight . "px");
            }
            else Styler::addStyle(CSS::height, "auto");
        }
    }
}
