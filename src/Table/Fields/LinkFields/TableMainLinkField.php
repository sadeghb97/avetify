<?php
namespace Avetify\Table\Fields\LinkFields;

class TableMainLinkField extends TableLinkField {
    function getLinkValue($item): string {
        return $item->getItemLink();
    }
}
