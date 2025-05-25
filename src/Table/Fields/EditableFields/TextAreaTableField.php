<?php
namespace Avetify\Table\Fields\EditableFields;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;

class TextAreaTableField extends EditableField {
    public int $rows = 10;
    public int $columns = 50;

    public function presentValue($item) {
        echo '<textarea ';
        HTMLInterface::addAttribute("placeholder", $this->title);
        HTMLInterface::addAttribute("rows", $this->rows);
        HTMLInterface::addAttribute("cols", $this->columns);
        $this->appendMainAttributes($item);
        Styler::startAttribute();
        $this->appendMainStyles($item);
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        if($item != null) {
            echo $this->getValue($item);
        }

        echo '</textarea>';
    }

    public function setRows(int $rows) : TextAreaTableField {
        $this->rows = $rows;
        return $this;
    }

    public function setColumns(int $columns) : TextAreaTableField {
        $this->columns = $columns;
        return $this;
    }
}
