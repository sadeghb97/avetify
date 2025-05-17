<?php

class CodingBlocks {
    /** @var CodingContentBlock[] */
    public array $blocks = [];

    public function __construct(public string $contents) {
        if($this->contents){
            $blocksList = json_decode($this->contents, true);
            foreach ($blocksList as $rawBlock){
                $block = new CodingContentBlock($rawBlock);
                $this->blocks[] = $block;
            }
        }
    }
}

class CodingContentBlock extends DataModel {
    public string $id = "";
    public string $wrapper = "ltr";
    public string $contents = "";
    public string $dir = "";
    public string $textAlign = "";

    public function __construct($data){
        parent::__construct($data);
        $this->dir = ($this->dir != "rtl") ? "ltr" : "rtl";
        $this->textAlign = ($this->dir != "ltr") ? "right" : "left";
    }
}
