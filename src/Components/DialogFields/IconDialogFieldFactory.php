<?php
namespace Avetify\Components\DialogFields;

class IconDialogFieldFactory extends DialogFieldFactory {
    public function __construct(string $title, public string $src){
        parent::__construct($title);
    }

    public function makeDialogField($id, $value) : DialogField {
        return new IconDialogField($id, $this->title, $value, $this->src);
    }
}
