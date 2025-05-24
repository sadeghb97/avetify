<?php
namespace Avetify\Components;

use Avetify\Interface\CSS;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class CodingContents extends CodingBlocks implements Placeable {
    public function place(WebModifier $webModifier = null) {
        $vertDiv = new VertDiv(8);
        $vertDiv->open();

        foreach ($this->blocks as $block){
            $wrapper = strtolower($block->wrapper);
            $isPlain = $wrapper == "plain" || $wrapper == "output" || $wrapper == "";
            $blockContents = $block->contents;
            $dir = $block->dir;
            $textAlign = $block->textAlign;

            if(!$isPlain) {
                echo '<pre><code ';
                Styler::classStartAttribute();
                Styler::addClass("language-" . $wrapper);
                Styler::closeAttribute();
                Styler::startAttribute();
                Styler::addStyle("direction", "ltr");
                Styler::addStyle(CSS::textAlign, "left");
                Styler::closeAttribute();
                HTMLInterface::closeTag();
                echo preg_replace('#<p[^>]*>(.*?)</p>#is', "\n$1", $blockContents);
                echo '</code></pre>';
            }
            else {
                $plainModifier = WebModifier::createInstance();
                $plainModifier->pushStyle("direction", $dir);
                $plainModifier->pushStyle(CSS::textAlign, $textAlign);
                HTMLInterface::placeDiv($blockContents, $plainModifier);
            }
        }

        $vertDiv->close();
    }
}
