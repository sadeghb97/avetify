<?php
namespace Avetify\Searchers;

use Avetify\Entities\AvtEntityItem;
use Avetify\Searchers\Models\EntitySearchType;
use Avetify\Searchers\Utils\EntitySearcherUtils;
use Avetify\Themes\Green\GreenEntitySearcherRenderer;
use Avetify\Themes\Green\GreenTheme;
use Avetify\Themes\Main\EntitySearcherRenderer;
use Avetify\Themes\Main\ThemesManager;

abstract class EntitySearcher {
    public ?EntitySearcherRenderer $renderer = null;

    /** @var array<string, array<int, AvtEntityItem>> */
    private array $itemsByIdCache = [];

    public function __construct(){
        $this->renderer = $this->getEntitySearcherRenderer();
    }

    /** @return EntitySearchType[] */
    abstract public function getTypes(): array;

    public function getLookups(): array {
        return [];
    }

    public function getPageTitle(): string {
        return "Entities";
    }

    public function getPageSubtitle(): string {
        return "Client-side search (diacritics-insensitive, case-insensitive). Click a result to open its page.";
    }

    public function getSearchPlaceholder(): string {
        return "Start typing...";
    }

    public function getDefaultTypeId(): ?string {
        $types = $this->getTypes();
        return $types[0]->id ?? null;
    }

    public function getMaxResults(): int {
        return 80;
    }

    public function getPayloadVarName(): string {
        return "__ENTITY_SEARCHER_PAYLOAD__";
    }

    public function getTheme(): ThemesManager {
        $theme = new GreenTheme();
        $theme->includesBootstrap = true;
        $theme->includesEntitySearcherTools = true;
        return $theme;
    }

    public function getEntitySearcherRenderer(): EntitySearcherRenderer {
        return new GreenEntitySearcherRenderer($this, $this->getTheme());
    }

    public function buildPayload(): array {
        $entities = [];
        $tabs = [];
        foreach ($this->getTypes() as $type){
            $rawItems = ($type->itemsProvider)();
            $items = [];
            foreach ($rawItems as $raw){
                $items[] = ($type->itemMapper)($raw);
            }
            $entities[] = ["type" => $type->id, "items" => $items];
            $tabs[] = [
                "id" => $type->id,
                "label" => $type->tabLabel . " (" . count($items) . ")",
            ];
        }

        return [
            "generatedAt" => time(),
            "pageTitle" => $this->getPageTitle(),
            "pageSubtitle" => $this->getPageSubtitle(),
            "searchPlaceholder" => $this->getSearchPlaceholder(),
            "defaultTypeId" => $this->getDefaultTypeId(),
            "maxResults" => $this->getMaxResults(),
            "payloadVarName" => $this->getPayloadVarName(),
            "entities" => $entities,
            "tabs" => $tabs,
            "lookups" => $this->getLookups(),
        ];
    }

    public function renderPage(?string $title = null): void {
        $this->renderer->renderPage($title ?? $this->getPageTitle());
    }

    /**
     * @param AvtEntityItem[] $items
     * @return array<int, AvtEntityItem>
     */
    protected function itemsById(string $cacheKey, array $items): array {
        if(!isset($this->itemsByIdCache[$cacheKey])){
            $this->itemsByIdCache[$cacheKey] = EntitySearcherUtils::itemsById($items);
        }
        return $this->itemsByIdCache[$cacheKey];
    }
}
