<?php
namespace Avetify\Themes\Classic;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Components\Containers\VertDiv;
use Avetify\Interface\CSS;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\WebModifier;
use Avetify\Themes\Main\Navigations\NavigationRenderer;
use Avetify\Themes\Main\Navigations\NavigationSection;

class ClassicNavigationRenderer extends NavigationRenderer {
    public function place(WebModifier $webModifier = null) {
        $margin = $this->navigation->getDetail("margin");
        if(!$margin) $margin = 16;

        $fontSize = $this->navigation->getDetail("font_size");
        if(!$fontSize) $fontSize = "1.25rem";

        $vertDiv = new VertDiv(4);
        $vertDiv->addStyle("margin-top", $margin . "px");
        $vertDiv->addStyle("margin-bottom", $margin . "px");
        $vertDiv->addStyle("font-size", $fontSize);
        $vertDiv->open($webModifier);

        foreach ($this->navigation->sections as $section){
            $this->placeSection($section);
        }

        $vertDiv->close();
    }

    public function placeSection(NavigationSection $section, WebModifier $webModifier = null) {
        $niceDiv = new NiceDiv(0);
        $niceDiv->open($webModifier);

        $color = $section->getDetail("color");
        if(!$color) $color = "Black";

        foreach ($section->menuLinks as $linkIndex => $link){
            if($linkIndex > 0) $this->printSplitter($color);

            $linkModifier = WebModifier::createInstance();
            $linkModifier->styler->pushStyle(CSS::textDecoration, "none");
            $linkModifier->styler->pushStyle(CSS::fontWeight, "bold");
            $linkModifier->styler->pushStyle(CSS::color, $color);
            HTMLInterface::placeLink($link->link, $link->title, $linkModifier);
        }
        $niceDiv->close();
    }

    public function printSplitter(string $color){
        $splitterContents = "&nbsp;&nbsp;|&nbsp;&nbsp;";
        $splitterModifier = WebModifier::createInstance();
        $splitterModifier->styler->pushStyle(CSS::fontWeight, "bold");
        $splitterModifier->styler->pushStyle(CSS::color, $color);
        HTMLInterface::placeText($splitterContents);
    }
}
