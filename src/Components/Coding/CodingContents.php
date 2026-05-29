<?php
namespace Avetify\Components\Coding;

use Avetify\Components\Containers\VertDiv;
use Avetify\Components\Markdown\MarkdownBox;
use Avetify\Interface\CSS\CSS;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;
use Avetify\Themes\Main\ColorScheme;

class CodingContents extends CodingBlocks implements Placeable {
    public function __construct(
        string $contents,
        public ColorScheme $colorScheme = ColorScheme::Light,
    ) {
        parent::__construct($contents);
    }

    public function place(WebModifier $webModifier = null) {
        $vertDiv = new VertDiv(8);
        $vertDiv->open($webModifier);

        foreach ($this->blocks as $block){
            $wrapper = strtolower($block->wrapper);
            $isPlain = $wrapper == "plain" || $wrapper == "output" || $wrapper == "";
            $blockContents = $block->contents;
            $dir = $block->dir;
            $textAlign = $block->textAlign;

            if ($wrapper === "markdown") {
                $markdown = self::extractMarkdownFromEditorHtml($blockContents);
                (new MarkdownBox($markdown, $this->colorScheme))->place();
            }
            else if(!$isPlain) {
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

    private static function extractMarkdownFromEditorHtml(string $html): string
    {
        $text = preg_replace('#<p[^>]*>(.*?)</p>#is', "$1\n", $html) ?? $html;
        $text = preg_replace('#<br\s*/?>#i', "\n", $text) ?? $text;
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');

        return trim($text);
    }
}
