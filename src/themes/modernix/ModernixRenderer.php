<?php

class ModernixRenderer extends SetRenderer {
    public bool $smallerTitle = false;

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
            HTMLInterface::addAttribute("width", "600");

            Styler::startAttribute();
            Styler::closeAttribute();
            HTMLInterface::closeTag();
            echo '</div>';
        }

        $link = $this->setModifier->getItemLink($item);
        $title = $this->setModifier->getItemTitle($item);

        echo '<div class="card__body">';

        if($link){
            $linkModifier = WebModifier::createInstance();
            $linkModifier->pushStyle(CSS::textDecoration, "none");
            $linkModifier->pushStyle(CSS::color, "Black");
            HTMLInterface::openLink($link, $linkModifier);
        }

        echo '<div ';
        Styler::startAttribute();
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        if(!$this->smallerTitle) echo '<h4>' . $title . '</h4>';
        else {
            echo '<div ';
            Styler::startAttribute();
            Styler::addStyle("font-weight", "bold");
            Styler::addStyle("font-size", "0.92rem");
            Styler::closeAttribute();
            HTMLInterface::closeTag();
            echo $title;
            HTMLInterface::closeDiv();
        }
        echo '</div>';

        if($link) HTMLInterface::closeLink();

        echo '</div>';
    }

    public function openRecord($record){
        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("card");
        Styler::closeAttribute();

        Styler::startAttribute();
        Styler::closeAttribute();
        echo '>';
    }

    public function closeRecord($record){
        echo '</div>';
    }

    public function getTheme() : ThemesManager {
        return new GreenTheme();
    }

    public function openContainer() {
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

    public function closeContainer() {
        echo '</div>';
    }
}
