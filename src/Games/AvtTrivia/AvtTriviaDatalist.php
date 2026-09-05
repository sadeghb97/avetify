<?php

namespace Avetify\Games\AvtTrivia;

use Avetify\Entities\AvtEntityItem;
use Avetify\Entities\BasicProperties\EntityProfile;
use Avetify\Entities\BasicProperties\Traits\EntityProfileTrait;
use Avetify\Models\Traits\Tagged;

class AvtTriviaDatalist implements EntityProfile {
    use Tagged;
    use EntityProfileTrait;

    /** @param AvtTriviaItem[]|AvtEntityItem[] $records */
    public function __construct(public array $records, public string $key) {}

    public function getItemId(): string {
        return $this->key;
    }

    public function getItemImage(): string {
        // TODO: Implement getItemImage() method.
    }

    public function getItemLink(): string {
        // TODO: Implement getItemLink() method.
    }
}