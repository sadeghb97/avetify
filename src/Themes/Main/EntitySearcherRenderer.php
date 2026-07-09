<?php
namespace Avetify\Themes\Main;

use Avetify\Searchers\EntitySearcher;
use Avetify\Interface\PageRenderer;

abstract class EntitySearcherRenderer implements PageRenderer {
    public EntitySearcher $searcher;

    public function __construct(EntitySearcher $searcher, public ThemesManager $theme){
        $this->searcher = $searcher;
        $this->postConstruct();
    }

    public function postConstruct(){}

    public function getTheme(): ThemesManager {
        return $this->theme;
    }

    public function renderPage(?string $title = null): void {
        $this->theme->openPage($title ?? $this->searcher->getPageTitle());
        $this->renderBody();
        $this->theme->lateImports();
        ThemesManager::closeBody();
    }

    abstract public function renderBody(): void;
}
