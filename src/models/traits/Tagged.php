<?php

trait Tagged {
    private string $_additional_tag = "";

    public function isTagged() : bool {
        return $this->_additional_tag;
    }

    public function isTicked() : bool {
        return $this->_additional_tag == "ticked";
    }

    public function tick(){
        $this->_additional_tag = "ticked";
    }

    public function removeTag(){
        $this->_additional_tag = "";
    }

    public function stickTag(string $tag){
        $this->_additional_tag = $tag;
    }

    public function getStickerTag() : string {
        return $this->_additional_tag;
    }
}
