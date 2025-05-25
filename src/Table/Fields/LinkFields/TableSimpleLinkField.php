<?php
namespace Avetify\Table\Fields\LinkFields;

use Avetify\Entities\EntityUtils;

class TableSimpleLinkField extends TableLinkField {
    public function __construct(string $title, string $key, public string $linkKey){
        parent::__construct($title, $key);
    }

    public function getValue($item): string {
        return EntityUtils::getSimpleValue($item, $this->key);
    }

    function getLinkValue($item): string {
        return EntityUtils::getSimpleValue($item, $this->linkKey);
    }
}
