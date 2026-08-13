# Avetify Crawling Component: `Scrapper` Class Guide

The `Avetify\Crawling\Scrapper` class is a high-performance, stateful, cursor-based string token parser. It is designed for crawling, scraping, and extracting data from web pages (HTML, XML, JSON, JS scripts, plain text) using token anchor searching.

---

## Architecture & Design Philosophy

### 1. Token-Anchored Search vs. DOM Parsers
Traditional DOM parsers (like `DOMDocument` or `Symfony DomCrawler`) construct an in-memory tree of the entire HTML document. While powerful, DOM parsers suffer from performance overhead, memory bloat, and fail when encountering unclosed or malformed HTML tags.

`Scrapper` operates as a **lightweight, cursor-driven string scanner**:
- It searches for unique **delimiter tokens** (e.g. `class="player-row"`, `href="`, `title="`).
- It maintains an internal **character cursor pointer** (`$cursor`), moving forward as tokens are matched.
- It is resilient to invalid HTML or non-HTML text structures.

### 2. Stateful Execution & Progression
When a method like `find($startToken, $endToken)` is called:
1. `Scrapper` scans `$contents` starting from index `$cursor`.
2. If `$startToken` and `$endToken` are located in sequence, `$scrapped` holds the substring between them.
3. `$cursor` automatically advances to the position immediately following `$endToken`.
4. The `$found` flag is set to `true`. If matching fails, `$found` is set to `false`, and `$cursor` remains unchanged.

---

## Class Properties

| Property | Type | Description |
| :--- | :--- | :--- |
| `$contents` | `string` | The active string content being parsed. |
| `$cursor` | `int` | Current zero-indexed character position pointer in `$contents`. |
| `$found` | `bool` | `true` if the last find/seek operation succeeded; `false` otherwise. |
| `$scrapped` | `string` | Holds the substring extracted from the most recent `find()`, `before()`, or `after()` operation. |
| `$message` | `string` | Error detail or status description (set when `safeFind()` fails). |
| `$altCursor` | `int` | Helper cursor pointer maintained by `safeFind()`. |
| `$altFound` | `bool` | Helper boolean flag maintained by `safeFind()`. |

---

## Method Reference

### 1. Initialization & State Reset

#### `__construct(?string $contents = "")`
Initializes a new `Scrapper` with optional string content and sets cursor to `0`.

#### `setContents(?string $contents): void`
Replaces current content, resets cursor to `0`, sets `$found = false`, and clears `$scrapped` and `$message`.

#### `reset(): void`
Resets state variables (`cursor = 0`, `found = false`, `scrapped = ""`, `message = ""`), preserving `$contents`.

---

### 2. Searching & Extraction

#### `find(string $startStr, string $endStr, bool $fixedCursor = false): void`
Extracts text bounded between `$startStr` and `$endStr` starting from current `$cursor`.
- **On Success**: `$found = true`, `$scrapped` contains extracted substring, `$cursor` moves past `$endStr` (unless `$fixedCursor` is `true`).
- **On Failure**: `$found = false`, `$scrapped = ""`, error details stored in `$message`.

#### `safeFind(string $startStr, string $endStr, int $startIndex): ?string`
Low-level extraction starting at `$startIndex`. Returns the extracted substring on success, or `null` on failure (populating `$this->message` with error details and setting `$altFound = false`).

#### `cfind(string $startStr, string $endStr, callable $callback): void`
Executes `find()`. If found, immediately invokes `$callback($subScrapper)` passing a cloned `Scrapper` instance loaded with `$scrapped`.

#### `multipleFind(array $startTokens, string $endToken): void`
Tries an array of possible opening tokens against `$endToken`. Stops at the first token that matches.

#### `seek(string $str): void`
Advances `$cursor` to the position immediately following `$str`. Sets `$found = true` if located.

#### `before(string $startStr, bool $fixedCursor = false): void`
Extracts content from start of `$contents` up to `$startStr`.

#### `after(?string $startStr, bool $fixedCursor = false): void`
Extracts content from `$startStr` to the end of `$contents`.

#### `pursueSingleElement(string $elementName, string $filterAttrName, string $filterAttrValue): ?Scrapper`
Locates an HTML tag `<$elementName ...>` whose attribute `$filterAttrName` contains `$filterAttrValue`. Returns a new `Scrapper` containing the full element tag on success, or `null` if absent.

---

### 3. Context Sub-scraping & Chaining

#### `pushClone(): Scrapper`
Returns a **new `Scrapper` instance** containing `$this->scrapped`. This is the primary way to isolate child nodes without mutating parent state.

#### `push(): void`
Replaces `$this->contents` with `$this->scrapped` and resets cursor to `0`.

#### `pushAfter(): void`
Truncates `$this->contents` to everything after current `$cursor` and resets cursor to `0`.

#### `innerClone(): Scrapper`
Extracts content between the first `>` and last `<` in remaining unparsed text, returning a new `Scrapper`.

---

### 4. Sanitization & Helper Methods

#### `trs(): string`
Returns `$scrapped` trimmed of whitespace (`trim($this->scrapped)`).

#### `stripTrs(): string`
Strips HTML tags and trims whitespace from `$scrapped` (`strip_tags(trim($this->scrapped))`).

#### `pruneTrs(string $separator): string`
Removes all instances of `$separator` from `$scrapped` and returns trimmed string.

#### `prune(string $s): void`
Removes all occurrences of `$s` from `$contents`.

#### `remains(): string`
Returns unparsed string from current `$cursor` to end of `$contents`.

#### `contains(string $needle): bool`
Returns `true` if `$needle` exists anywhere inside `$contents`.

---

## Practical Usage Examples

### Pattern 1: Iterating Through Recurring Lists or Tables
To stream-process a recurring list of items (e.g. table rows or cards), use `find()` inside a `while ($scrapper->found)` loop combined with `pushClone()`:

```php
use Avetify\Crawling\Scrapper;

$html = getHtmlContent();
$mainScrapper = new Scrapper($html);

// Find the first player row
$mainScrapper->find('class="player-row"', '</tr>');

while ($mainScrapper->found) {
    // Spawn an isolated child scrapper for this row
    $rowScrapper = $mainScrapper->pushClone();
    
    // Extract player name inside this row
    $rowScrapper->find('class="player-name">', '</span>');
    $playerName = $rowScrapper->trs();
    
    // Extract player rating
    $rowScrapper->find('class="player-rating">', '</div>');
    $playerRating = $rowScrapper->trs();

    echo "Player: $playerName | Rating: $playerRating\n";

    // Advance main scrapper to next player row
    $mainScrapper->find('class="player-row"', '</tr>');
}
```

---

### Pattern 2: Extracting HTML Attributes & Links
Extract values from specific tag attributes (e.g., `href`, `src`, `title`):

```php
$scrapper = new Scrapper('<a href="/player/123" title="Lionel Messi"><img src="messi.png"></a>');

$scrapper->find('href="', '"');
if ($scrapper->found) {
    $link = $scrapper->trs(); // "/player/123"
}

$scrapper->find('title="', '"');
if ($scrapper->found) {
    $title = $scrapper->trs(); // "Lionel Messi"
}
```

---

### Pattern 3: Handling Fallback HTML Formats with `multipleFind()` & `reset()`
When target websites change class names across different versions or themes:

```php
$cardScrapper = new Scrapper($cardHtml);

// Try multiple class names for rating
$cardScrapper->multipleFind([
    'class="playercard-26-rating">',
    'class="playercard-s-26-rating">',
    'class="old-rating-style">'
], '</div>');

if ($cardScrapper->found) {
    $rating = $cardScrapper->trs();
}

// Reset cursor back to start if order of elements is uncertain
$cardScrapper->reset();

$cardScrapper->find('class="player-position">', '</span>');
if ($cardScrapper->found) {
    $position = $cardScrapper->trs();
}
```

---

### Pattern 4: Functional Scrapping with `cfind()`

```php
$scrapper = new Scrapper('<div class="profile"><h1>John Doe</h1></div>');

$scrapper->cfind('<div class="profile">', '</div>', function(Scrapper $profileScrapper) {
    $profileScrapper->find('<h1>', '</h1>');
    if ($profileScrapper->found) {
        echo "Found profile name: " . $profileScrapper->trs();
    }
});
```

---

## Guidelines for AI Agents & Developers

1. **Always Check `$scrapper->found`**: Before reading `$scrapper->trs()` or `$scrapper->scrapped`, verify `if ($scrapper->found)`.
2. **Use `pushClone()` for Sub-Contexts**: Never mutate the parent `Scrapper` when extracting child fields. Always call `pushClone()` to create an isolated workspace for child extraction.
3. **Reset When Searching Out of Order**: `Scrapper` cursor moves forward. If you need to extract a field that appears earlier in the HTML snippet than the current cursor, call `$scrapper->reset()` first.
4. **Choose Unique Delimiters**: Pick opening and closing string tokens that uniquely frame your target data.
