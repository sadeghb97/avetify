<?php

namespace Avetify\Crawling;

use Exception;

/**
 * Class Scrapper
 *
 * A high-performance, lightweight, stateful cursor-based string token parser.
 * Designed for crawling, scraping, and extracting data from structured or semi-structured text
 * (such as HTML, XML, JSON, JS scripts, logs, or plain text) using token anchor searching.
 *
 * ## Key Architecture & Usage Patterns:
 * 1. **Cursor-Based Parsing**:
 *    Moves an internal `$cursor` forward through `$contents` as tokens are found.
 *    Subsequent searches resume from where the previous search ended.
 * 2. **Loop Iteration**:
 *    Perform recurring item extractions using `find()` inside a `while ($scrapper->found)` loop.
 * 3. **Sub-scrapping & Context Chaining**:
 *    Isolate sub-sections of text using `pushClone()` or `cfind()` to extract child elements safely
 *    without mutating or altering the parent scrapper's state.
 * 4. **Token Anchoring**:
 *    Unlike DOM parsers, `Scrapper` relies on unique string delimiters/anchors rather than HTML tag balance.
 *
 * @package Avetify\Crawling
 */
class Scrapper
{
    /** @var string The main text content currently being parsed. */
    public string $contents = "";

    /** @var int The current character index position within $contents. */
    public int $cursor = 0;

    /** @var bool Indicates whether the last find/seek operation succeeded. */
    public bool $found = false;

    /** @var int Alternative cursor index set by safeFind() operations. */
    public int $altCursor = 0;

    /** @var bool Indicates whether the last safeFind() operation succeeded. */
    public bool $altFound = false;

    /** @var string Stores error messages or status info from safeFind() or find(). */
    public string $message = "";

    /** @var string The last extracted/scrapped substring segment. */
    public string $scrapped = "";

    /**
     * Scrapper constructor.
     *
     * @param string|null $contents Initial text content to parse.
     */
    public function __construct(?string $contents = "")
    {
        $this->contents = $contents ?? "";
        $this->cursor = 0;
    }

    /**
     * Resets the content and all state variables.
     *
     * @param string|null $contents New text content to parse.
     * @return void
     */
    public function setContents(?string $contents): void
    {
        $this->contents = $contents ?? "";
        $this->cursor = 0;
        $this->found = false;
        $this->message = "";
        $this->scrapped = "";
    }

    /**
     * Removes all occurrences of a specified substring from $contents.
     *
     * @param string $s Substring to remove.
     * @return void
     */
    public function prune(string $s): void
    {
        $this->contents = str_replace($s, "", $this->contents);
    }

    /**
     * Creates a new isolated Scrapper instance loaded with the last extracted `$scrapped` content.
     * Useful for extracting nested fields without disturbing the parent cursor state.
     *
     * @return Scrapper
     */
    public function pushClone(): Scrapper
    {
        return new Scrapper($this->scrapped);
    }

    /**
     * Replaces the current scrapper's contents with the last extracted `$scrapped` value
     * and resets the cursor to 0.
     *
     * @return void
     */
    public function push(): void
    {
        $this->setContents($this->scrapped);
    }

    /**
     * Truncates the contents to everything after the current `$cursor` index
     * and resets the cursor to 0.
     *
     * @return void
     */
    public function pushAfter(): void
    {
        if (strlen($this->contents) > ($this->cursor + 1)) {
            $this->setContents(substr($this->contents, $this->cursor + 1));
        } else {
            $this->setContents("");
        }
    }

    /**
     * Advances the cursor forward to the position immediately following the first match of `$str`.
     * Sets `$found = true` if `$str` was located, or `$found = false` otherwise.
     *
     * @param string $str Anchor string to find.
     * @return void
     */
    public function seek(string $str): void
    {
        $pos = strpos($this->contents, $str, $this->cursor);
        if ($pos !== false) {
            $this->found = true;
            $pos += strlen($str) - 1;
            if (strlen($this->contents) - 1 > $pos) {
                $pos++;
            }
            $this->cursor = $pos;
        } else {
            $this->found = false;
        }
    }

    /**
     * Scans for an HTML element tag matching `$elementName` whose `$filterAttrName` attribute contains `$filterAttrValue`.
     * Returns a new Scrapper containing the full matched tag if found, or `null` if not found.
     *
     * @param string $elementName HTML tag name (e.g. "img", "div").
     * @param string $filterAttrName Attribute name (e.g. "class", "id").
     * @param string $filterAttrValue Substring value to match inside the attribute.
     * @return Scrapper|null
     */
    public function pursueSingleElement(string $elementName, string $filterAttrName, string $filterAttrValue): ?Scrapper
    {
        $start = "<$elementName";
        $end = ">";

        $curs = $this->cursor;
        while (true) {
            $sResult = $this->safeFind($start, $end, $curs);
            if (!$this->altFound || $sResult === null) {
                return null;
            }
            $curs = $this->altCursor;

            $innerScrapper = new Scrapper($sResult);
            $innerScrapper->find($filterAttrName . '="', '"');
            if (!$innerScrapper->found) {
                continue;
            }

            $wholeAttrValue = $innerScrapper->trs();
            if (str_contains($wholeAttrValue, $filterAttrValue)) {
                $this->cursor = $curs;
                return new Scrapper($sResult);
            }
        }
    }

    /**
     * Returns the remaining unparsed content from the current `$cursor` index to the end of `$contents`.
     *
     * @return string
     */
    public function remains(): string
    {
        if (strlen($this->contents) > $this->cursor) {
            return substr($this->contents, $this->cursor);
        }
        return "";
    }

    /**
     * Finds content bounded between `$startStr` and `$endStr` starting from the current `$cursor`.
     * On success: sets `$found = true`, populates `$scrapped`, and moves `$cursor` past `$endStr` (unless `$fixedCursor` is true).
     * On failure: sets `$found = false` and populates `$message` with details.
     *
     * @param string $startStr Opening delimiter token.
     * @param string $endStr Closing delimiter token.
     * @param bool $fixedCursor If true, cursor will not advance even if found.
     * @return void
     */
    public function find(string $startStr, string $endStr, bool $fixedCursor = false): void
    {
        $scr = $this->safeFind($startStr, $endStr, $this->cursor);
        if ($this->altFound && $scr !== null) {
            $this->found = true;
            if (!$fixedCursor) {
                $this->cursor = $this->altCursor;
            }
            $this->scrapped = $scr;
        } else {
            $this->found = false;
            $this->scrapped = "";
        }
    }

    /**
     * Executes `find()`. If content is found, invokes `$callback` with a cloned `Scrapper` containing the extracted content.
     *
     * @param string $startStr Opening delimiter token.
     * @param string $endStr Closing delimiter token.
     * @param callable $callback Function receiving `(Scrapper $subScrapper)`.
     * @return void
     */
    public function cfind(string $startStr, string $endStr, callable $callback): void
    {
        $this->find($startStr, $endStr);
        if ($this->found) {
            $callback($this->pushClone());
        }
    }

    /**
     * Extracts everything after `$startStr` from the current `$cursor` to the end of `$contents`.
     *
     * @param string|null $startStr Opening delimiter token.
     * @param bool $fixedCursor If true, cursor will not advance even if found.
     * @return void
     */
    public function after(?string $startStr = null, bool $fixedCursor = false): void
    {
        if ($startStr === null) {
            $this->found = false;
            return;
        }

        $pos = strpos($this->contents, $startStr, $this->cursor);
        if ($pos !== false) {
            $pos += strlen($startStr);
            $this->found = true;
            if (!$fixedCursor) {
                $this->cursor = $pos;
            }
            $this->scrapped = substr($this->contents, $pos);
        } else {
            $this->found = false;
            $this->scrapped = "";
        }
    }

    /**
     * Extracts everything from the start of `$contents` up to `$startStr`.
     *
     * @param string $startStr Delimiter token.
     * @param bool $fixedCursor If true, cursor will not advance even if found.
     * @return void
     */
    public function before(string $startStr, bool $fixedCursor = false): void
    {
        $pos = strpos($this->contents, $startStr, $this->cursor);
        if ($pos !== false) {
            $this->found = true;
            if (!$fixedCursor) {
                $this->cursor = $pos;
            }
            $this->scrapped = substr($this->contents, 0, $pos);
        } else {
            $this->found = false;
            $this->scrapped = "";
        }
    }

    /**
     * Pure extraction helper. Finds text between `$startStr` and `$endStr` starting at `$startIndex`.
     * Sets `$altFound` and `$altCursor` internally.
     * Returns extracted string on success, or `null` on failure (storing error message in `$message`).
     *
     * @param string $startStr Opening delimiter token.
     * @param string $endStr Closing delimiter token.
     * @param int $startIndex Index in $contents to start searching from.
     * @return string|null Extracted string on success, or null on failure.
     */
    public function safeFind(string $startStr, string $endStr, int $startIndex): ?string
    {
        $startPos = strpos($this->contents, $startStr, $startIndex);
        if ($startPos === false) {
            $this->altFound = false;
            $this->message = "Start marker not found: '$startStr'";
            return null;
        }

        $endPos = strpos($this->contents, $endStr, $startPos + strlen($startStr));
        if ($endPos === false) {
            $this->altFound = false;
            $this->message = "End marker not found: '$endStr'";
            return null;
        }

        $sLength = strlen($startStr);
        try {
            $scr = substr($this->contents, $startPos + $sLength, $endPos - $startPos - $sLength);
            $this->altFound = true;
            $this->altCursor = $endPos + strlen($endStr);
            $this->message = "";
            return $scr;
        } catch (Exception $ex) {
            $this->altFound = false;
            $this->message = "Substring extraction error: " . $ex->getMessage();
            return null;
        }
    }

    /**
     * Tries multiple opening tokens against a single ending token.
     * Stops on the first successful match.
     *
     * @param array<string> $startTokens Array of possible opening delimiter tokens.
     * @param string $endToken Closing delimiter token.
     * @return void
     */
    public function multipleFind(array $startTokens, string $endToken): void
    {
        foreach ($startTokens as $startToken) {
            $this->find($startToken, $endToken);
            if ($this->found) {
                return;
            }
        }
    }

    /**
     * Returns `$scrapped` content trimmed of leading/trailing whitespace.
     *
     * @return string
     */
    public function trs(): string
    {
        return trim($this->scrapped);
    }

    /**
     * Strips HTML tags and returns `$scrapped` content trimmed of whitespace.
     *
     * @return string
     */
    public function stripTrs(): string
    {
        return strip_tags(trim($this->scrapped));
    }

    /**
     * Removes all occurrences of `$separator` from `$scrapped` and returns it trimmed.
     *
     * @param string $separator Character or string to remove from scrapped content.
     * @return string
     */
    public function pruneTrs(string $separator): string
    {
        $scr = $this->scrapped;
        if (str_contains($scr, $separator)) {
            $scr = str_replace($separator, "", $scr);
        }

        return trim($scr);
    }

    /**
     * Checks if `$this->contents` contains `$needle`.
     *
     * @param string $needle Substring to search for.
     * @return bool
     */
    public function contains(string $needle): bool
    {
        return str_contains($this->contents, $needle);
    }

    /**
     * Extracts inner HTML/content bounded between the first closing angle bracket '>'
     * and the last opening angle bracket '<' in remaining content.
     *
     * @return Scrapper
     */
    public function innerClone(): Scrapper
    {
        $rem = $this->remains();
        $firstClosePos = strpos($rem, '>');
        $lastOpenPos = strrpos($rem, '<');

        if ($firstClosePos !== false && $lastOpenPos !== false && $lastOpenPos > $firstClosePos) {
            return new Scrapper(substr($rem, $firstClosePos + 1, $lastOpenPos - $firstClosePos - 1));
        }

        return $this;
    }

    /**
     * Resets state variables (cursor, flags, scrapped content) to initial zero/empty states.
     * Note: Does not clear `$contents`.
     *
     * @return void
     */
    public function reset(): void
    {
        $this->cursor = 0;
        $this->found = false;
        $this->altCursor = 0;
        $this->altFound = false;
        $this->message = "";
        $this->scrapped = "";
    }
}
