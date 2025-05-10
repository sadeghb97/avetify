<?php

class GreenNavigationRenderer extends NavigationRenderer {
    public function place(WebModifier $webModifier = null) {
        if(count($this->navigation->sections) == 0) return;
        $this->openNavbar();
        $this->navigations();
        $this->closeNavbar();
    }

    public function openNavbar(){
        echo '<ul>';
    }

    public function closeNavbar(){
        echo '</ul>';
    }

    public function navigations(){
        /** @var NavigationLink[] $navs */
        $navs = [];

        foreach ($this->navigation->sections as $section){
            foreach ($section->menuLinks as $link){
                $navs[] = $link;
            }
        }

        $serverFn = Routing::getBaseUrlFilename($_SERVER['REQUEST_URI']);
        foreach ($navs as $nav){
            $title = $nav->title;
            $link = $nav->link;
            $linkFn = Routing::getBaseUrlFilename($link);
            $isActive = false;
            $equalFn = $serverFn == $linkFn;

            if($equalFn){
                $isActive = true;
                if(count($nav->params) > 0){
                    $params = $nav->params;

                    foreach ($params as $paramKey => $paramValue){
                        if(!isset($_GET[$paramKey]) || $_GET[$paramKey] != $paramValue){
                            $isActive = false;
                            break;
                        }
                    }
                }
            }

            $this->addMenu($title, $link, $isActive);
        }
    }

    public function addMenu(string $title, string $link, bool $isActive = false, bool $floatRight = false){
        echo '<li ';
        Styler::startAttribute();
        if($floatRight) Styler::addStyle("float", "right");
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        echo '<a ';
        $classes = "navlink";
        if($isActive) $classes .= " active";
        HTMLInterface::addAttribute("href", $link);
        HTMLInterface::addAttribute("class", $classes);
        HTMLInterface::closeTag();
        echo $title;
        echo '</a>';

        echo '</li>';
    }
}
