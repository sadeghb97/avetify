<?php
namespace Avetify\Lister;

use Avetify\Utils\StringUtils;

class ListerCategory {
    public array $records = [];
    public string $identifier = "";

    public function __construct(public int $index, public string $title, ?string $id = null){
        $this->identifier = $id ?: StringUtils::generateUUID();
    }
}