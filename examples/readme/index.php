<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';
require_once __DIR__ . '/ReadmeTheme.php';

use Avetify\Components\Markdown\MarkdownBox;

const README_PATH = __DIR__ . '/../../README.md';
$markdown = file_get_contents(README_PATH);
$title = MarkdownBox::extractTitle($markdown);

(new ReadmeTheme())->render($title, function () use ($markdown) {
    (new MarkdownBox($markdown, MarkdownBox::CODE_THEME_LIGHT))->place();
});
