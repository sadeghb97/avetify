<?php
namespace Avetify\Themes\Modernix;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Components\Containers\VertDiv;
use Avetify\Entities\SetModifier;
use Avetify\Interface\CSS;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Modules\Printer;
use Avetify\Table\AvtTable;
use Avetify\Themes\Green\GreenTheme;
use Avetify\Themes\Main\SetRenderer;
use Avetify\Themes\Main\ThemesManager;

class ModernixRenderer extends SetRenderer {
    public bool $smallerTitle = false;
    public ?WebModifier $cardModifiers = null;
    public ?string $imageAspectRatio = null;

    public function __construct(SetModifier $setModifier, ThemesManager $theme,
                                string $title = "Set", bool|int $limit = 5000){
        parent::__construct($setModifier, $theme, $title, $limit);
    }

    public function renderRecordMain($item, $index) {
        $avatar = $this->setModifier->getItemImage($item);
        if($avatar) {
            echo '<div ';
            Styler::classStartAttribute();
            Styler::addClass("card__header");
            Styler::closeAttribute();
            HTMLInterface::closeTag();

            echo '<img ';
            Styler::classStartAttribute();
            Styler::addClass("card__image");
            Styler::closeAttribute();

            HTMLInterface::addAttribute("src", $avatar);

            Styler::startAttribute();
            if($this->imageAspectRatio) Styler::addStyle("aspect-ratio", $this->imageAspectRatio);
            Styler::closeAttribute();
            HTMLInterface::closeTag();
            echo '</div>';
        }

        $link = $this->setModifier->getItemLink($item);
        $title = $this->setModifier->getItemTitle($item);
        $description = $this->setModifier->getItemDescription($item);

        echo '<div class="card__body">';

        if($link){
            $linkModifier = WebModifier::createInstance();
            $linkModifier->pushStyle(CSS::textDecoration, "none");
            $linkModifier->pushStyle(CSS::color, "Black");
            if($this->blankLink) $linkModifier->pushModifier("target", "_blank");
            HTMLInterface::openLink($link, $linkModifier);
        }

        echo '<div ';
        Styler::startAttribute();
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        $row = "";
        if($this->printRowIndex){
            $rowFontSize = $this->smallerTitle ? "0.875rem" : "1rem";
            $row = '<span style="font-size: ' . $rowFontSize
                . '; font-weight: bold; color: grey;">' . ($index + 1) . ": " . '</span>';
        }

        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle("font-weight", "bold");
        Styler::addStyle("font-size", $this->smallerTitle ? "1rem" : "1.125rem");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        if($row) echo $row;
        echo $title;
        HTMLInterface::closeDiv();

        echo '</div>';

        if($link) HTMLInterface::closeLink();

        if($this->setModifier instanceof AvtTable){
            foreach ($this->setModifier->fields as $field){
                $vertDiv = new VertDiv(0);
                $vertDiv->open();
                $field->presentValue($item);
                HTMLInterface::closeDiv();
            }
        }

        if($description){
            echo '<p>' . $description . '</p>';
        }
        echo '</div>';

        $tags = $this->setModifier->getItemTags($item);
        if(count($tags) > 0) {
            echo '<div class="card__footer">';
            foreach ($tags as $tag) {
                echo '<span class="tag tag-blue">' . $tag . '</span>';
            }
            echo '</div>';
        }
    }

    public function openRecord($record){
        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("card");
        HTMLInterface::appendClasses($this->cardModifiers);
        Styler::closeAttribute();

        Styler::startAttribute();
        HTMLInterface::appendStyles($this->cardModifiers);
        Styler::closeAttribute();

        HTMLInterface::applyModifiers($this->cardModifiers);
        echo '>';
    }

    public function closeRecord($record){
        echo '</div>';
    }

    public function getTheme() : ThemesManager {
        return new GreenTheme();
    }

    public function openCollection(WebModifier $webModifier = null) {
        $niceDiv = new VertDiv(0);
        $niceDiv->addStyle("margin-top", "16px");
        $niceDiv->open();
        Printer::boldPrint($this->getTitle());
        $niceDiv->close();
        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("container");
        Styler::closeAttribute();
        Styler::startAttribute();
        Styler::addStyle(CSS::marginTop, "12px");
        Styler::addStyle(CSS::paddingBottom, "12px");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
    }

    public function closeCollection(WebModifier $webModifier = null) {
        echo '</div>';
    }
}
