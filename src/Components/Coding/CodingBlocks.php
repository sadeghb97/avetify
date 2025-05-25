<?php
namespace Avetify\Components\Coding;

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
