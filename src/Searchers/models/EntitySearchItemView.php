<?php
namespace Avetify\Searchers\Models;

use Avetify\Searchers\Utils\EntitySearcherUtils;

class EntitySearchItemView {
    public function __construct(
        public string $title = "",
        public string $subtitle = "",
        public array $meta = [],
        public array $labels = [],
        public string $copyName = "",
    ) {}

    public function toArray(): array {
        return [
            "title" => $this->title,
            "subtitle" => $this->subtitle,
            "meta" => $this->meta,
            "labels" => $this->labels,
            "copyName" => $this->copyName,
        ];
    }

    /** @param string[] $searchTokens */
    public function toItemPayload(int $pk, string $img, string $link, array $searchTokens): array {
        return [
            "pk" => $pk,
            "img" => $img,
            "link" => $link,
            "searchIndex" => EntitySearcherUtils::buildSearchIndex($searchTokens),
            "display" => $this->toArray(),
        ];
    }
}
