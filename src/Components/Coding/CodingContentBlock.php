<?php
namespace Avetify\Components\Coding;

use Avetify\Models\DataModel;

class CodingContentBlock extends DataModel {
    public string $id = "";
    public string $wrapper = "";
    public string $contents = "";
    public string $dir = "ltr";
    public string $textAlign = "";

    public function __construct($data){
        parent::__construct($data);
        $this->dir = ($this->dir != "rtl") ? "ltr" : "rtl";
        $this->textAlign = ($this->dir != "ltr") ? "right" : "left";
    }
}
