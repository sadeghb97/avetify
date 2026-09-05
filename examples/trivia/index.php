<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use Avetify\Games\AvtTrivia\AvtTrivia;
use Avetify\Games\AvtTrivia\AvtTriviaDatalist;
use Avetify\Repo\Countries\World;

// 1. Fetch countries dataset and wrap into AvtTriviaDatalist
$countries = World::getAllCountries();
$datalist = new class($countries, 'world_countries') extends AvtTriviaDatalist {};

// In-memory session score storage for demonstration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['trivia_scores'])) {
    $_SESSION['trivia_scores'] = [];
}

// 2. Instantiate AvtTrivia with Datalist and configuration
$trivia = new AvtTrivia($datalist, [
    'lang' => 'fa',
    'duration' => 120,
    'title' => 'Country Flags Trivia',
]);

// 3. Register onFinished callback (Secure: receives server-issued token + score + stats containing difficulty & key)
$trivia->onFinished(function (string $token, int $score, array $stats) {
    // Save score server-side tied to unique token (cannot be tampered by client)
    $_SESSION['trivia_scores'][$token] = [
        'score' => $score,
        'difficulty' => $stats['difficulty'] ?? 2,
        'key' => $stats['key'] ?? 'world_countries',
        'stats' => $stats,
        'username' => null,
    ];
});

// 4. Register onRegister callback (Secure: receives token + username + dataset key)
$trivia->onRegister(function (string $token, string $username, ?string $datalistKey = null) {
    $cleanName = htmlspecialchars($username);

    // Retrieve verified score from server storage using token
    if (isset($_SESSION['trivia_scores'][$token])) {
        $_SESSION['trivia_scores'][$token]['username'] = $cleanName;
        $score = $_SESSION['trivia_scores'][$token]['score'];
        $difficulty = $_SESSION['trivia_scores'][$token]['difficulty'] ?? 2;
        $key = $datalistKey ?? $_SESSION['trivia_scores'][$token]['key'] ?? 'unknown';
        return "Thank you $cleanName! Your score of $score (Level $difficulty on '$key') has been registered securely.";
    }

    return "Thank you $cleanName! Your submission was recorded.";
});

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AvtTrivia Game Example</title>
    <style>
        body {
            background-color: #090d16;
            margin: 0;
            padding: 16px 8px;
            color: #ffffff;
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 16px;
        }
        .header h1 {
            color: #a855f7;
            margin-bottom: 8px;
        }
        .header p {
            color: #94a3b8;
            margin: 0;
        }
        @media (max-width: 580px) {
            body {
                padding: 6px 4px;
            }
            .header {
                margin-bottom: 6px;
            }
            .header h1 {
                font-size: 1.1rem;
                margin-bottom: 0;
            }
            .header p {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AvtTrivia Game Example</h1>
            <p>Demonstrating AvtTrivia component using country flags (World::getAllCountries dataset).</p>
        </div>

        <?php
        // 5. Render the trivia game
        $trivia->render();
        ?>
    </div>
</body>
</html>
