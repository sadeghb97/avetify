<?php

class CodingContents extends CodingBlocks implements Placeable {
    public function place(WebModifier $webModifier = null) {
        $vertDiv = new VertDiv(8);
        $vertDiv->open();

        foreach ($this->blocks as $block){
            $wrapper = strtolower($block->wrapper);
            $isPlain = $wrapper == "plain" || $wrapper == "output";
            $blockContents = $block->contents;

            echo '<pre><code ';
            Styler::classStartAttribute();
            Styler::addClass("language-" . $wrapper);
            Styler::closeAttribute();
            Styler::startAttribute();
            Styler::addStyle("text-align", "left");
            Styler::closeAttribute();
            HTMLInterface::closeTag();
            echo $blockContents;
            echo '</code></pre>';
        }

        $vertDiv->close();
    }
}
