<?php

class ClassicMenu implements Placeable {
    public function __construct(public int $margin = 16, public string $fontSize = "1.25rem"){}

    /** @var ClassicMenuSection[] $sections */
    public array $sections = [];

    public function place(WebModifier $webModifier = null) {
        $vertDiv = new VertDiv(4);
        $vertDiv->addStyle("margin-top", $this->margin . "px");
        $vertDiv->addStyle("margin-bottom", $this->margin . "px");
        $vertDiv->addStyle("font-size", $this->fontSize);
        $vertDiv->open($webModifier);

        foreach ($this->sections as $section){
            $section->place();
        }

        $vertDiv->close();
    }

    public function pushSection(ClassicMenuSection $section){
        $this->sections[] = $section;
    }
}

class ClassicMenuSection implements Placeable {
    public function __construct(public string $color = ""){}

    /** @var NavigationBar[] */
    public array $menuLinks = [];

    public function pushLink(NavigationBar $link){
        $this->menuLinks[] = $link;
    }

    public function place(WebModifier $webModifier = null) {
        $niceDiv = new NiceDiv(0);
        $niceDiv->open($webModifier);

        foreach ($this->menuLinks as $linkIndex => $link){
            if($linkIndex > 0) $this->printSplitter();

            $linkModifier = WebModifier::createInstance();
            $linkModifier->styler->pushStyle(CSS::textDecoration, "none");
            $linkModifier->styler->pushStyle(CSS::fontWeight, "bold");
            $linkModifier->styler->pushStyle(CSS::color, $this->color);
            HTMLInterface::placeLink($link->link, $link->title, $linkModifier);
        }
        $niceDiv->close();
    }

    public function printSplitter(){
        $splitterContents = "&nbsp;&nbsp;|&nbsp;&nbsp;";
        $splitterModifier = WebModifier::createInstance();
        $splitterModifier->styler->pushStyle(CSS::fontWeight, "bold");
        $splitterModifier->styler->pushStyle(CSS::color, $this->color);
        HTMLInterface::placeText($splitterContents);
    }
}

class ClassicLabel implements Placeable {
    public function __construct(public string $label, public string $src,
                                public string $backgroundColor='black',
                                public string $color='Cyan'){}

    public function place(WebModifier $webModifier = null) {
        echo '<a ';
        Styler::startAttribute();
        Styler::addStyle(CSS::textDecoration, "none");
        Styler::closeAttribute();
        HTMLInterface::addAttribute(Attrs::href, $this->src);
        HTMLInterface::closeTag();

        $labelModifier = WebModifier::createInstance();
        $labelModifier->styler->pushClass("label");
        $labelModifier->styler->pushStyle(CSS::backgroundColor, $this->backgroundColor);
        $labelModifier->styler->pushStyle(CSS::color, $this->color);
        $labelModifier->styler->pushStyle(CSS::display,"inline-block");
        $labelModifier->styler->pushStyle(CSS::margin, "1.5px");
        HTMLInterface::placeSpan($this->label, $labelModifier);

        echo '</a>';
    }
}