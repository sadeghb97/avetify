<?php
namespace Avetify\Searchers\Models;

class EntitySearchType {
    public function __construct(
        public readonly string $id,
        public readonly string $tabLabel,
        /** @var callable(): array */
        public readonly mixed $itemsProvider,
        /** @var callable(mixed): array */
        public readonly mixed $itemMapper,
    ) {}
}
