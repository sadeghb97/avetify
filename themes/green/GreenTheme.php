<?php

class GreenTheme extends ThemesManager {
    public function moreHeaderTags(){
        self::importStyle(Routing::browserPathFromAventador("themes/green/styles.css"));
        self::importJS(Routing::browserPathFromAventador("themes/green/scripts.js"));
    }

    public function openNavbar(){
        echo '<ul>';
    }

    public function closeNavbar(){
        echo '</ul>';
    }

    public function navigations(){
        $navs = $this->getNavigations();

        $serverFn = $this->getBaseUrlFilename($_SERVER['REQUEST_URI']);
        foreach ($navs as $nav){
            $title = $nav[0];
            $link = $nav[1];
            $linkFn = $this->getBaseUrlFilename($link);
            $isActive = false;
            $equalFn = $serverFn == $linkFn;

            if($equalFn){
                $isActive = true;
                if(count($nav) > 2){
                    $params = $nav[2];

                    foreach ($params as $param){
                        if(!isset($_GET[$param[0]]) || $_GET[$param[0]] != $param[1]){
                            $isActive = false;
                            break;
                        }
                    }
                }
            }

            $this->addMenu($title, $link, $isActive);
        }
    }

    public function addHorizontalNavbar(){
        if(count($this->getNavigations()) == 0) return;
        $this->openNavbar();
        $this->navigations();
        $this->closeNavbar();
    }

    public function loadHeaderElements() {
        $this->addHorizontalNavbar();
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

    public function getBaseUrlFilename(string $url) : string {
        if(str_contains($url, "/")){
            $pos = strrpos($url, "/");
            $url = substr($url, $pos + 1);
        }

        if(str_contains($url, "?")){
            $pos = strpos($url, "?");
            $url = substr($url, 0, $pos);
        }

        return $url;
    }

    function getNavigations() : array {
        return [];
    }
}
