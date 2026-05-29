<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/IndexTheme.php';

$examples = discoverExamples();

(new IndexTheme())->render('Avetify Examples', function () use ($examples) {
    echo '<main class="markdown-body">';
    echo '<h1>Avetify Examples</h1>';
    echo '<p>Sample pages demonstrating framework components and usage patterns.</p>';
    echo '<ul>';

    foreach ($examples as $slug) {
        $meta = exampleMeta($slug);
        $url = exampleUrl($slug);
        echo '<li>';
        echo '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">';
        echo htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8');
        echo '</a>';

        if ($meta['description'] !== '') {
            echo ' — ' . htmlspecialchars($meta['description'], ENT_QUOTES, 'UTF-8');
        }

        echo '</li>';
    }

    echo '</ul>';
    echo '</main>';
});
