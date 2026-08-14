# AvtTrivia Game Engine Documentation

`AvtTrivia` is a reusable, self-contained picture trivia game engine for PHP. It handles dynamic item rendering, timer management, penalty/scoring rules, responsive UI, bilingual localization, and secure anti-cheat score registration.

---

## 🌟 Key Features

- **Single Method Invocation**: Instantiate `AvtTrivia` and call `$trivia->render()`. All HTML, CSS, and JS are rendered self-contained.
- **Dataset Deduplication**: Guarantees no item is presented as the target question twice within the same round.
- **Bilingual Localization**: Native support for **English (`'en'`)** and **Persian (`'fa'`)**. English is the default.
- **Responsive & Touch-Optimized**: Smart viewport height scaling (`clamp`), anti-flicker option buttons, prominent skip button, and mobile-friendly side margins.
- **Secure Anti-Cheat System**: Prevents client-side score forgery using a two-stage server token verification flow (`onFinished` and `onRegister`).

---

## 📦 Requirements & Datasets (`AvtEntityItem`)

The constructor accepts an array of objects implementing `AvtEntityItem` (or objects with equivalent properties/methods):

| Required Data | Method / Property Fallbacks |
| :--- | :--- |
| **Identifier** | `$item->getItemId()`, `$item->id`, `$item->alpha2` |
| **Name / Title** | `$item->getItemTitle()`, `$item->name`, `$item->short_name`, `$item->per_name` |
| **Image URL** | `$item->getItemImage()`, `$item->image`, `$item->flag` |

### Example Dataset (World Countries):
```php
use Avetify\Repo\Countries\World;

$countries = World::getAllCountries(); // Returns array of AvtCountry objects
```

---

## ⚙️ Configuration (`AvtTriviaConfig`)

You can pass configuration parameters as an associative array or an `AvtTriviaConfig` instance.

```php
use Avetify\Games\AvtTrivia\AvtTrivia;

$trivia = new AvtTrivia($items, [
    'lang' => 'en', // 'en' (default) or 'fa'
    'duration' => 120, // Game time limit in seconds (default: 120)
    'title' => 'Country Flags Trivia', // Custom header title
    'options_count' => 4, // Number of choices per question (default: 4)
    'correct_reward_score' => 3, // Score added per correct answer (default: 3)
    'wrong_penalty_score' => 1, // Score deducted per wrong answer (default: 1)
    'wrong_penalty_time' => 1.0, // Time deducted per wrong answer in seconds (default: 1.0)
    'skip_penalty' => 0.5, // Time deducted per skip in seconds (default: 0.5)
    'post_url' => '', // POST request target URL (defaults to current REQUEST_URI)
]);
```

---

## 🛡️ Anti-Cheat Score Registration System

`AvtTrivia` uses a two-stage callback architecture to eliminate client-side score manipulation:

```
┌─────────────────┐       (AJAX Finish)       ┌────────────────────────┐
│ Client JS Game  │ ────────────────────────> │ Server: onFinished     │
│ Time Up/Finished│ <──────────────────────── │ - Generates Token      │
└─────────────────┘       Token Response      │ - Saves Token => Score │
         │                                    └────────────────────────┘
         │ (User Submits Username + Token)
         ▼
┌─────────────────┐       (POST Register)     ┌────────────────────────┐
│ Leaderboard     │ ────────────────────────> │ Server: onRegister     │
│ Modal Form      │                           │ - Looks up Token Score │
└─────────────────┘                           │ - Saves Leaderboard    │
                                              └────────────────────────┘
```

### Callback Handlers:

1. **`onFinished(callable $callback)`**:
   - **Signature**: `function(string $token, int $score, array $stats): void`
   - **Trigger**: Called server-side as soon as the client notifies the server that a game round finished.
   - **Role**: Issues a secure random `$token` (`trv_...`) tied to the unalterable `$score`. Save this pair in your Database or Session.

2. **`onRegister(callable $callback)`**:
   - **Signature**: `function(string $token, string $username): ?string`
   - **Trigger**: Called when the user enters their name into the leaderboard modal.
   - **Role**: Receives ONLY `$token` and `$username` (no client score field is sent). Retrieve the score associated with `$token` from your storage and persist the record.

---

## 💻 Full Implementation Example

```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Avetify\Games\AvtTrivia\AvtTrivia;
use Avetify\Repo\Countries\World;

// Start session for demo score persistence
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['trivia_scores'])) {
    $_SESSION['trivia_scores'] = [];
}

// 1. Load dataset
$countries = World::getAllCountries();

// 2. Initialize Game Engine
$trivia = new AvtTrivia($countries, [
    'lang' => 'en',
    'duration' => 120,
    'title' => 'Flag Trivia Challenge',
]);

// 3. Register Anti-Cheat Server Hooks
$trivia->onFinished(function (string $token, int $score, array $stats) {
    // Save token => score mapping in Session/Database
    $_SESSION['trivia_scores'][$token] = [
        'score' => $score,
        'stats' => $stats,
    ];
});

$trivia->onRegister(function (string $token, string $username) {
    $cleanName = htmlspecialchars($username);

    // Retrieve verified score using token
    if (isset($_SESSION['trivia_scores'][$token])) {
        $score = $_SESSION['trivia_scores'][$token]['score'];

        // Save $cleanName and $score to DB / Leaderboard here
        
        return "Thank you $cleanName! Your score of $score has been registered securely.";
    }

    return "Score registration completed.";
});

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AvtTrivia Game Demo</title>
</head>
<body style="background:#090d16; margin:0; padding:20px; font-family:sans-serif;">

    <?php
    // 4. Render Game Component
    $trivia->render();
    ?>

</body>
</html>
```
