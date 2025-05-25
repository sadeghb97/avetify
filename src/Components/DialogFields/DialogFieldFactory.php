<?php
namespace Avetify\Components\DialogFields;

abstract class DialogFieldFactory {
    public function __construct(public string $title){}
    abstract public function makeDialogField($id, $value) : DialogField;
}
