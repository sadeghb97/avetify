<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/avetify.php';

use Avetify\AvetifyManager;

AvetifyManager::init(
    dirname(__DIR__),
    dirname(__DIR__),
    '/avetify/examples',
    '/avetify/assets',
);

define('MAIN_PROXY', '127.0.0.1:2081');

function examplesDir(string $path = ''): string
{
    $base = __DIR__;
    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function exampleUrl(string $slug): string
{
    return AvetifyManager::publicUrl($slug . '/');
}

/** @return list<string> */
function discoverExamples(): array
{
    $examples = [];

    foreach (scandir(examplesDir()) as $entry) {
        if ($entry[0] === '.') {
            continue;
        }

        $dir = examplesDir($entry);
        if (is_dir($dir) && is_file($dir . '/index.php')) {
            $examples[] = $entry;
        }
    }

    sort($examples);
    return $examples;
}

/** @return array{title: string, description: string} */
function exampleMeta(string $slug): array
{
    $metaFile = examplesDir($slug . '/meta.php');
    if (is_file($metaFile)) {
        $meta = require $metaFile;
        if (is_array($meta)) {
            return [
                'title' => (string) ($meta['title'] ?? $slug),
                'description' => (string) ($meta['description'] ?? ''),
            ];
        }
    }

    return [
        'title' => ucwords(str_replace(['-', '_'], ' ', $slug)),
        'description' => '',
    ];
}
