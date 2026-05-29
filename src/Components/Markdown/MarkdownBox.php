<?php
namespace Avetify\Components\Markdown;

use Avetify\Components\Containers\VertDiv;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;
use Avetify\Themes\Main\ThemesManager;

class MarkdownBox implements Placeable {
    private static bool $cssEmitted = false;

    public function __construct(
        public string $markdownContents,
        public bool $emitCss = true,
    ) {}

    public static function importStyles(): void
    {
        ThemesManager::importMarkdownTools();
    }

    public static function extractTitle(string $markdown): string
    {
        if (preg_match('/^#{1,6}\s+(.+)$/m', $markdown, $m)) {
            return self::plainText(trim($m[1]));
        }
        return 'README';
    }

    public function place(WebModifier $webModifier = null): void
    {
        if ($this->emitCss && !self::$cssEmitted) {
            self::importStyles();
            self::$cssEmitted = true;
        }

        $vertDiv = new VertDiv(8);
        $vertDiv->open($webModifier);

        echo '<article class="markdown-body">';
        echo self::toHtml($this->markdownContents);
        echo '</article>';

        $vertDiv->close();
    }

    private static function toHtml(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = self::normalizeBlockBreaks($text);
        $blocks = self::splitBlocks($text);
        $html = '';

        foreach ($blocks as $block) {
            $html .= self::renderBlock($block);
        }

        return $html;
    }

    private static function normalizeBlockBreaks(string $text): string
    {
        $text = preg_replace('/^(#{1,6}\s.+)$/m', "\n\n$1", $text) ?? $text;
        $text = preg_replace('/^(`{3,}|~{3,})/m', "\n\n$1", $text) ?? $text;
        $text = preg_replace('/^(<pre\b)/im', "\n\n$1", $text) ?? $text;
        return ltrim($text, "\n");
    }

    /** @return list<string> */
    private static function splitBlocks(string $text): array
    {
        $lines = explode("\n", $text);
        $blocks = [];
        $current = [];
        $inFence = false;
        $fenceChar = '';
        $inHtml = false;
        $htmlTag = '';

        $flush = static function () use (&$blocks, &$current): void {
            if ($current !== []) {
                $blocks[] = implode("\n", $current);
                $current = [];
            }
        };

        foreach ($lines as $line) {
            if ($inFence) {
                $current[] = $line;
                if (preg_match('/^(`{3,}|~{3,})\s*$/', $line, $m) && $m[1][0] === $fenceChar) {
                    $inFence = false;
                    $flush();
                }
                continue;
            }

            if ($inHtml) {
                $current[] = $line;
                if (preg_match('#</' . preg_quote($htmlTag, '#') . '>#i', $line)) {
                    $inHtml = false;
                    $flush();
                }
                continue;
            }

            if (preg_match('/^(`{3,}|~{3,})(.*)$/', $line, $m)) {
                $flush();
                $inFence = true;
                $fenceChar = $m[1][0];
                $current[] = $line;
                continue;
            }

            if (preg_match('/^<([a-z][a-z0-9]*)\b/i', $line, $m)) {
                $tag = strtolower($m[1]);
                if (in_array($tag, ['pre', 'div', 'table', 'style'], true)) {
                    $flush();
                    $inHtml = true;
                    $htmlTag = $tag;
                    $current[] = $line;
                    if (preg_match('#</' . preg_quote($htmlTag, '#') . '>#i', $line)) {
                        $inHtml = false;
                        $flush();
                    }
                    continue;
                }
            }

            if (trim($line) === '') {
                $flush();
                continue;
            }

            $current[] = $line;
        }

        $flush();
        return $blocks;
    }

    private static function renderBlock(string $block): string
    {
        $trimmed = trim($block);
        if ($trimmed === '') {
            return '';
        }

        if (preg_match('/^(`{3,}|~{3,})(\S*)\s*\n([\s\S]*?)\n\1\s*$/', $block, $m)) {
            return self::renderCodeBlock($m[3], strtolower($m[2]));
        }

        if (preg_match('#^<pre>([\s\S]*)</pre>$#i', $trimmed, $m)) {
            return self::renderCodeBlock($m[1], '');
        }

        if (self::isTableBlock($block)) {
            return self::renderTableBlock($block);
        }

        if (preg_match('/^(#{1,6})\s+(.+)$/', $trimmed, $m)) {
            $level = strlen($m[1]);
            $content = self::renderInline($m[2]);
            $slug = self::headingSlug(self::plainText($m[2]));
            $tag = 'h' . $level;
            $anchor = $slug !== ''
                ? '<a class="anchor" href="#' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true">#</a>'
                : '';
            $idAttr = $slug !== '' ? ' id="' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '"' : '';
            return "<{$tag}{$idAttr}>{$anchor}{$content}</{$tag}>\n";
        }

        $lines = explode("\n", $block);
        $out = '';
        $para = [];

        $flushPara = static function () use (&$para, &$out): void {
            if ($para === []) {
                return;
            }
            $text = implode(' ', $para);
            $out .= '<p>' . self::renderInline($text) . "</p>\n";
            $para = [];
        };

        foreach ($lines as $line) {
            if (trim($line) === '') {
                $flushPara();
                continue;
            }
            $para[] = trim($line);
        }
        $flushPara();

        return $out;
    }

    private static function isTableBlock(string $block): bool
    {
        $lines = array_values(array_filter(explode("\n", trim($block)), static fn(string $l): bool => trim($l) !== ''));
        if (count($lines) < 2) {
            return false;
        }
        return str_contains($lines[0], '|') && preg_match('/^\|?\s*:?-{3,}/', $lines[1]) === 1;
    }

    private static function renderTableBlock(string $block): string
    {
        $lines = array_values(array_filter(explode("\n", trim($block)), static fn(string $l): bool => trim($l) !== ''));
        $header = self::parseTableRow($lines[0]);
        $align = self::parseTableAlignment($lines[1]);
        $bodyLines = array_slice($lines, 2);

        $html = "<table>\n<thead>\n<tr>\n";
        foreach ($header as $i => $cell) {
            $style = self::tableAlignStyle($align[$i] ?? '');
            $html .= '<th' . $style . '>' . self::renderInline($cell) . "</th>\n";
        }
        $html .= "</tr>\n</thead>\n<tbody>\n";

        foreach ($bodyLines as $line) {
            $cells = self::parseTableRow($line);
            $html .= "<tr>\n";
            foreach ($header as $i => $_) {
                $cell = $cells[$i] ?? '';
                $style = self::tableAlignStyle($align[$i] ?? '');
                $html .= '<td' . $style . '>' . self::renderInline($cell) . "</td>\n";
            }
            $html .= "</tr>\n";
        }

        $html .= "</tbody>\n</table>\n";
        return $html;
    }

    /** @return list<string> */
    private static function parseTableRow(string $line): array
    {
        $line = trim($line, " \t|");
        $parts = explode('|', $line);
        return array_map(static fn(string $c): string => trim($c), $parts);
    }

    /** @return list<string> */
    private static function parseTableAlignment(string $line): array
    {
        $cells = self::parseTableRow($line);
        $align = [];
        foreach ($cells as $cell) {
            $cell = trim($cell);
            if (str_starts_with($cell, ':') && str_ends_with($cell, ':')) {
                $align[] = 'center';
            } elseif (str_ends_with($cell, ':')) {
                $align[] = 'right';
            } elseif (str_starts_with($cell, ':')) {
                $align[] = 'left';
            } else {
                $align[] = '';
            }
        }
        return $align;
    }

    private static function tableAlignStyle(string $align): string
    {
        return match ($align) {
            'left' => ' style="text-align:left"',
            'right' => ' style="text-align:right"',
            'center' => ' style="text-align:center"',
            default => '',
        };
    }

    private static function renderCodeBlock(string $code, string $lang): string
    {
        $escaped = htmlspecialchars($code, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $langClass = $lang !== '' ? ' class="language-' . htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') . '"' : '';
        $highlight = $lang !== '' ? ' highlight-source-' . htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') : '';

        return '<div class="highlight' . $highlight . '"><pre><code' . $langClass . '>' . $escaped . "</code></pre></div>\n";
    }

    private static function renderInline(string $text): string
    {
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $text = preg_replace_callback(
            '/\[([^\]]+)\]\(([^)]+)\)/',
            static function (array $m): string {
                $label = $m[1];
                $href = $m[2];
                if (str_starts_with($href, '#')) {
                    $slug = ltrim($href, '#');
                    return '<a href="#' . $slug . '">' . $label . '</a>';
                }
                return '<a href="' . $href . '" rel="nofollow">' . $label . '</a>';
            },
            $text
        ) ?? $text;

        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text) ?? $text;
        $text = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text) ?? $text;

        return $text;
    }

    private static function plainText(string $text): string
    {
        $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text) ?? $text;
        $text = preg_replace('/`([^`]+)`/', '$1', $text) ?? $text;
        $text = preg_replace('/\*\*([^*]+)\*\*/', '$1', $text) ?? $text;
        return trim($text);
    }

    private static function headingSlug(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s-]+/u', '', $text) ?? $text;
        $text = preg_replace('/\s+/', '-', trim($text)) ?? $text;
        return $text;
    }
}
