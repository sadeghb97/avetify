<?php

class GreenNavigationRenderer extends NavigationRenderer {
    public bool $autoActiveDetect = true;

    public function place(WebModifier $webModifier = null) {
        if(count($this->navigation->sections) == 0) return;
        $this->openNavbar();
        $this->navigations();
        $this->closeNavbar();
    }

    public function headImports(){
        ThemesManager::importStyle(AvetifyManager::assetUrl("themes/green/navbar/styles.css"));
        ThemesManager::importJS(AvetifyManager::assetUrl("themes/green/navbar/init.js"));
    }

    public function openNavbar(){
        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("navbar-wrapper");
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        echo '<button ';
        Styler::classStartAttribute();
        Styler::addClass("navbar-scroll-btn");
        Styler::addClass("navbar-left");
        Styler::addClass("hidden");
        Styler::closeAttribute();
        HTMLInterface::addAttribute(Attrs::id, "navbarScrollLeft");
        HTMLInterface::closeTag();
        echo '◀';
        echo '</button>';

        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("navbar-scroll-container");
        Styler::closeAttribute();
        HTMLInterface::addAttribute(Attrs::id, "navbarScrollContainer");
        HTMLInterface::closeTag();

        echo '<ul ';
        Styler::classStartAttribute();
        Styler::addClass("navbar-list");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
    }

    public function closeNavbar(){
        echo '</ul>';
        echo '</div>';

        echo '<button ';
        Styler::classStartAttribute();
        Styler::addClass("navbar-scroll-btn");
        Styler::addClass("navbar-right");
        Styler::addClass("hidden");
        Styler::closeAttribute();
        HTMLInterface::addAttribute(Attrs::id, "navbarScrollRight");
        HTMLInterface::closeTag();
        echo '▶';
        echo '</button>';

        echo '</div>';
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
            $equalFn = $serverFn == $linkFn;
            $isActive = $nav->isActive;

            if($this->autoActiveDetect && !$isActive && $equalFn){
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

            $this->addMenu($title, $link, $nav->modifier, $isActive);
        }
    }

    public function addMenu(string $title, string $link, ?WebModifier $modifier = null,
                            bool $isActive = false, bool $floatRight = false){
        echo '<li ';
        Styler::startAttribute();
        if($floatRight) Styler::addStyle("float", "right");
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        echo '<a ';
        Styler::startAttribute();
        HTMLInterface::appendStyles($modifier);
        Styler::closeAttribute();
        Styler::classStartAttribute();
        Styler::addClass("navbar-link");
        if($isActive) Styler::addClass("active");
        HTMLInterface::appendClasses($modifier);
        Styler::closeAttribute();
        HTMLInterface::addAttribute("href", $link);
        HTMLInterface::applyModifiers($modifier);
        HTMLInterface::closeTag();
        echo $title;
        echo '</a>';

        echo '</li>';
    }
}
