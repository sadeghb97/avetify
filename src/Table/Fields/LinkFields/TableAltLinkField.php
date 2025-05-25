<?php
namespace Avetify\Table\Fields\LinkFields;

class TableAltLinkField extends TableLinkField {
    function getLinkValue($item): string {
        return $item->getItemAltLink();
    }
}