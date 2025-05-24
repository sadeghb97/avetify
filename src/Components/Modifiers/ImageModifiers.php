<?php
namespace Avetify\Components\Modifiers;

use Avetify\Interface\HTMLModifier;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class ImageModifiers {
    public static function imageWithWidthModifier(string $size) : WebModifier {
        $styler = new Styler();
        $styler->pushStyle("width", $size);
        $styler->pushStyle("height", "auto");
        return new WebModifier(new HTMLModifier(), $styler);
    }

    public static function imageWithHeightModifier(string $size) : WebModifier {
        $styler = new Styler();
        $styler->pushStyle("height", $size);
        $styler->pushStyle("width", "auto");
        return new WebModifier(new HTMLModifier(), $styler);
    }

    public static function imageSquareModifier(string $size) : WebModifier {
        $styler = new Styler();
        $styler->pushStyle("width", $size);
        $styler->pushStyle("height", $size);
        return new WebModifier(new HTMLModifier(), $styler);
    }
}