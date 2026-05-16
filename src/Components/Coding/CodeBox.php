<?php
namespace Avetify\Components\Coding;

use Avetify\Components\Containers\VertDiv;
use Avetify\Interface\CSS\CSS;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;

class CodeBox implements Placeable {
    public function __construct(public string $wrapper, public string $codeContents) {}

    public function place(WebModifier $webModifier = null): void {
        $vertDiv = new VertDiv(8);
        $vertDiv->open($webModifier);
        $wrapper = strtolower($this->wrapper);

        echo '<pre><code ';
        Styler::classStartAttribute();
        Styler::addClass("language-" . $wrapper);
        Styler::closeAttribute();
        Styler::startAttribute();
        Styler::addStyle("direction", "ltr");
        Styler::addStyle(CSS::textAlign, "left");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo preg_replace('#<p[^>]*>(.*?)</p>#is', "\n$1", $this->codeContents);
        echo '</code></pre>';

        $vertDiv->close();
    }
}
