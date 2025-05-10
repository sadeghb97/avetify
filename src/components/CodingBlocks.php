<?php

class CodingBlocks {
    /** @var CodingContentBlock[] */
    public array $blocks = [];

    public function __construct(public string $contents) {
        if($this->contents){
            $blocksList = json_decode($this->contents);
            foreach ($blocksList as $rawBlock){
                $block = new CodingContentBlock($rawBlock);
                $this->blocks[] = $block;
            }
        }
    }
}

class CodingContentBlock extends DataModel {
    public string $id = "";
    public string $wrapper = "";
    public string $contents = "";
}
