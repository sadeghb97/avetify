<?php
namespace Avetify\Games\AvtTrivia;

use Avetify\Entities\AvtEntityItem;

class AvtTrivia {
    /** @var AvtEntityItem[] */
    private array $items = [];
    private AvtTriviaConfig $config;
    /** @var callable|null */
    private $onFinishedCallback = null;
    /** @var callable|null */
    private $onRegisterCallback = null;
    private static int $instanceCounter = 0;
    private string $instanceId = '';
    private string $submitMessage = '';
    private bool $submittedSuccess = false;

    /**
     * @param AvtEntityItem[] $items
     * @param array|AvtTriviaConfig $config
     */
    public function __construct(array $items, array|AvtTriviaConfig $config = []) {
        $this->items = array_values($items);
        
        if ($config instanceof AvtTriviaConfig) {
            $this->config = $config;
        } else {
            $this->config = new AvtTriviaConfig($config);
        }

        if ($this->config->onFinished !== null) {
            $this->onFinishedCallback = $this->config->onFinished;
        }

        if ($this->config->onRegister !== null) {
            $this->onRegisterCallback = $this->config->onRegister;
        }

        self::$instanceCounter++;
        $this->instanceId = 'avt_trivia_' . self::$instanceCounter;

        $this->handlePostRequest();
    }

    public function onFinished(callable $callback): self {
        $this->onFinishedCallback = $callback;
        return $this;
    }

    public function onRegister(callable $callback): self {
        $this->onRegisterCallback = $callback;
        return $this;
    }

    protected function getTranslations(string $lang): array {
        if ($lang === 'fa') {
            return [
                'dir' => 'rtl',
                'time_remaining' => 'زمان باقی مانده',
                'score' => 'امتیاز',
                'question_alt' => 'سوال چیست؟',
                'skip' => 'رد شدن (Skip) ⏭️',
                'info_badge' => 'درست: +3 امتیاز | اشتباه: -1 امتیاز (-1s) | Skip: (-0.5s)',
                'gameover_title' => '⏰ زمان یا رکوردها به پایان رسید!',
                'gameover_desc' => 'عملکرد شما در این دور به شرح زیر است:',
                'final_score' => 'امتیاز نهایی',
                'final_correct' => 'پاسخ درست',
                'final_skips' => 'رد شده',
                'play_again' => 'بازی مجدد',
                'submit_result' => 'ثبت نتیجه',
                'modal_title' => '🏆 ثبت رکورد نهایی',
                'modal_desc' => 'جهت درج نام شما در لیست برترین‌ها، لطفا نام خود را وارد نمایید.',
                'username_label' => 'نام و نام خانوادگی:',
                'username_placeholder' => 'مثلا: علی محمدی',
                'score_to_register' => 'امتیاز ثبت شده:',
                'duration_label' => 'مدت زمان:',
                'seconds' => 'ثانیه',
                'cancel' => 'انصراف',
                'submit' => 'ارسال و ثبت',
                'success_msg' => 'امتیاز و نام شما با موفقیت ثبت شد!',
            ];
        }

        return [
            'dir' => 'ltr',
            'time_remaining' => 'Time Remaining',
            'score' => 'Score',
            'question_alt' => 'What is this?',
            'skip' => 'Skip Question ⏭️',
            'info_badge' => 'Correct: +3 pts | Wrong: -1 pt (-1s) | Skip: (-0.5s)',
            'gameover_title' => '⏰ Game Completed!',
            'gameover_desc' => 'Here is your performance summary for this round:',
            'final_score' => 'Final Score',
            'final_correct' => 'Correct',
            'final_skips' => 'Skips',
            'play_again' => 'Play Again',
            'submit_result' => 'Submit Score',
            'modal_title' => '🏆 Register High Score',
            'modal_desc' => 'Enter your name to register your score on the leaderboard.',
            'username_label' => 'Full Name:',
            'username_placeholder' => 'e.g., John Doe',
            'score_to_register' => 'Recorded score:',
            'duration_label' => 'Duration:',
            'seconds' => 'seconds',
            'cancel' => 'Cancel',
            'submit' => 'Submit Score',
            'success_msg' => 'Your score has been registered successfully!',
        ];
    }

    public function handlePostRequest(): bool {
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['avt_trivia_action'])) {
            $action = $_POST['avt_trivia_action'];

            // 1. Handle Game Finished (Client notifies server game is completed)
            if ($action === 'finish') {
                $score = (int)($_POST['score'] ?? 0);
                $correct = (int)($_POST['correct'] ?? 0);
                $skips = (int)($_POST['skips'] ?? 0);
                $duration = (int)($_POST['duration'] ?? $this->config->duration);

                // Generate secure random token
                $token = 'trv_' . bin2hex(random_bytes(16));

                $stats = [
                    'score' => $score,
                    'correct' => $correct,
                    'skips' => $skips,
                    'duration' => $duration,
                    'finished_at' => date('Y-m-d H:i:s'),
                ];

                if (session_status() === PHP_SESSION_ACTIVE) {
                    $_SESSION['avt_trivia_scores'][$token] = $stats;
                }

                // Execute programmer onFinished callback (secure score registration)
                if (is_callable($this->onFinishedCallback)) {
                    call_user_func($this->onFinishedCallback, $token, $score, $stats);
                }

                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => true,
                    'token' => $token,
                    'score' => $score,
                ]);
                exit;
            }

            // 2. Handle Username Registration (Client sends ONLY token + username)
            if ($action === 'register') {
                $token = trim($_POST['game_token'] ?? '');
                $username = trim($_POST['username'] ?? '');

                $t = $this->getTranslations($this->config->lang);
                $message = $t['success_msg'];

                // Execute programmer onRegister callback (associates token with username)
                if (is_callable($this->onRegisterCallback)) {
                    $customMsg = call_user_func($this->onRegisterCallback, $token, $username);
                    if (is_string($customMsg) && !empty($customMsg)) {
                        $message = $customMsg;
                    }
                }

                $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                    || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => true,
                        'message' => $message,
                        'token' => $token,
                        'username' => $username,
                    ]);
                    exit;
                }

                $this->submitMessage = $message;
                $this->submittedSuccess = true;
                return true;
            }
        }
        return false;
    }

    protected function prepareItemData(): array {
        $extracted = [];
        $lang = $this->config->lang;

        foreach ($this->items as $item) {
            if (!$item instanceof AvtEntityItem && !is_object($item)) {
                continue;
            }

            $id = '';
            if (method_exists($item, 'getItemId')) {
                $id = $item->getItemId();
            } elseif (isset($item->id)) {
                $id = $item->id;
            } elseif (isset($item->alpha2)) {
                $id = $item->alpha2;
            }

            $name = '';
            if ($lang === 'fa' && !empty($item->per_name)) {
                $name = $item->per_name;
            } elseif (method_exists($item, 'getItemTitle')) {
                $name = $item->getItemTitle();
            } elseif (isset($item->name)) {
                $name = $item->name;
            } elseif (isset($item->short_name)) {
                $name = $item->short_name;
            } elseif (!empty($item->per_name)) {
                $name = $item->per_name;
            }

            $image = '';
            if (method_exists($item, 'getItemImage')) {
                $image = $item->getItemImage();
            } elseif (isset($item->image)) {
                $image = $item->image;
            } elseif (isset($item->flag)) {
                $image = $item->flag;
            }

            if (!empty($id) && !empty($name) && !empty($image)) {
                $extracted[] = [
                    'id' => (string) $id,
                    'name' => (string) $name,
                    'image' => (string) $image,
                ];
            }
        }
        return $extracted;
    }

    public function render(): void {
        $itemsData = $this->prepareItemData();
        $itemsJson = json_encode($itemsData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        $id = $this->instanceId;
        $config = $this->config;
        $t = $this->getTranslations($config->lang);
        $postUrl = !empty($config->postUrl) ? $config->postUrl : htmlspecialchars($_SERVER['REQUEST_URI'] ?? '', ENT_QUOTES, 'UTF-8');
        ?>
        <style>
            #<?php echo $id; ?>-wrapper {
                direction: <?php echo $t['dir']; ?>;
                font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                max-width: 680px;
                width: calc(100% - 32px);
                margin: 20px auto;
                padding: 24px;
                background: linear-gradient(145deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
                border-radius: 24px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 30px rgba(99, 102, 241, 0.15);
                color: #f8fafc;
                box-sizing: border-box;
                position: relative;
                overflow: hidden;
            }

            #<?php echo $id; ?>-wrapper * {
                box-sizing: border-box;
            }

            /* Responsive Header */
            #<?php echo $id; ?>-header {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 16px;
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                padding: 12px 18px;
            }

            .avt-title-badge {
                font-size: 1.1rem;
                font-weight: 700;
                background: linear-gradient(90deg, #a855f7, #6366f1);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .avt-stats-bar {
                display: flex;
                gap: 12px;
                align-items: center;
                flex-shrink: 0;
            }

            .avt-stat-box {
                display: flex;
                flex-direction: column;
                align-items: center;
                background: rgba(15, 23, 42, 0.7);
                border: 1px solid rgba(255, 255, 255, 0.12);
                padding: 6px 14px;
                border-radius: 12px;
                min-width: 90px;
            }

            .avt-stat-label {
                font-size: 0.75rem;
                color: #94a3b8;
                margin-bottom: 2px;
                white-space: nowrap;
            }

            .avt-stat-value {
                font-size: 1.2rem;
                font-weight: 800;
            }

            .avt-timer-value {
                color: #38bdf8;
                transition: color 0.3s;
            }

            .avt-timer-value.danger {
                color: #f43f5e;
                animation: pulse-danger 0.8s infinite alternate;
            }

            @keyframes pulse-danger {
                0% { transform: scale(1); }
                100% { transform: scale(1.08); }
            }

            .avt-score-value {
                color: #facc15;
            }

            /* Image Container - Smart Dynamic Height */
            #<?php echo $id; ?>-img-container {
                position: relative;
                width: 100%;
                height: clamp(200px, 30vh, 270px);
                background: rgba(15, 23, 42, 0.85);
                border: 2px solid rgba(99, 102, 241, 0.3);
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                margin-bottom: 16px;
                box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.5);
            }

            #<?php echo $id; ?>-img-container img {
                max-width: 90%;
                max-height: 85%;
                object-fit: contain;
                border-radius: 10px;
                transition: opacity 0.25s ease;
                filter: drop-shadow(0 8px 16px rgba(0,0,0,0.4));
            }

            /* Options Grid */
            #<?php echo $id; ?>-options {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 16px;
            }

            .avt-option-btn {
                background: rgba(255, 255, 255, 0.07);
                border: 1px solid rgba(255, 255, 255, 0.12);
                border-radius: 14px;
                padding: 12px 16px;
                color: #e2e8f0;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                min-height: clamp(52px, 7vh, 62px);
                user-select: none;
            }

            .avt-option-btn:hover:not(:disabled) {
                background: rgba(99, 102, 241, 0.28);
                border-color: #818cf8;
                color: #ffffff;
                box-shadow: 0 0 16px rgba(99, 102, 241, 0.4);
            }

            .avt-option-btn:active:not(:disabled) {
                background: rgba(99, 102, 241, 0.4);
            }

            .avt-option-btn.correct {
                background: linear-gradient(135deg, #10b981, #059669) !important;
                border-color: #34d399 !important;
                color: #ffffff !important;
                box-shadow: 0 0 20px rgba(16, 185, 129, 0.6) !important;
            }

            .avt-option-btn.wrong {
                background: linear-gradient(135deg, #ef4444, #dc2626) !important;
                border-color: #f87171 !important;
                color: #ffffff !important;
                animation: avt-shake 0.4s ease;
            }

            @keyframes avt-shake {
                0%, 100% { transform: translateX(0); }
                20%, 60% { transform: translateX(-6px); }
                40%, 80% { transform: translateX(6px); }
            }

            /* Redesigned Prominent Skip Button & Controls */
            #<?php echo $id; ?>-controls {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 12px;
                width: 100%;
            }

            .avt-skip-btn {
                width: 100%;
                max-width: 380px;
                background: linear-gradient(135deg, rgba(239, 68, 68, 0.22), rgba(225, 29, 72, 0.28));
                border: 1.5px solid rgba(239, 68, 68, 0.55);
                color: #ffe4e6;
                padding: 14px 24px;
                border-radius: 16px;
                font-size: 1.05rem;
                font-weight: 700;
                cursor: pointer;
                transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                box-shadow: 0 4px 14px rgba(239, 68, 68, 0.2);
            }

            .avt-skip-btn:hover:not(:disabled) {
                background: linear-gradient(135deg, #ef4444, #dc2626);
                border-color: #f87171;
                color: #ffffff;
                box-shadow: 0 6px 22px rgba(239, 68, 68, 0.5);
            }

            .avt-skip-btn:active:not(:disabled) {
                background: #b91c1c;
            }

            .avt-badge-info {
                font-size: 0.82rem;
                color: #94a3b8;
                text-align: center;
                line-height: 1.4;
            }

            /* Game Over Container */
            #<?php echo $id; ?>-gameover {
                display: none;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 30px 16px;
                text-align: center;
                animation: avt-fade-in 0.4s ease;
            }

            @keyframes avt-fade-in {
                from { opacity: 0; transform: scale(0.95); }
                to { opacity: 1; transform: scale(1); }
            }

            .avt-gameover-title {
                font-size: 1.7rem;
                font-weight: 800;
                margin-bottom: 12px;
                background: linear-gradient(90deg, #f43f5e, #fb7185);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .avt-score-summary {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                padding: 16px 20px;
                margin: 16px 0 24px 0;
                width: 100%;
                max-width: 400px;
                display: flex;
                justify-content: space-around;
                gap: 8px;
            }

            .avt-summary-item {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .avt-summary-num {
                font-size: 1.5rem;
                font-weight: 800;
                color: #facc15;
            }

            .avt-summary-lbl {
                font-size: 0.78rem;
                color: #94a3b8;
            }

            .avt-action-btns {
                display: flex;
                gap: 12px;
                width: 100%;
                max-width: 400px;
            }

            .avt-btn-primary {
                flex: 1;
                background: linear-gradient(135deg, #6366f1, #4f46e5);
                color: #ffffff;
                border: none;
                border-radius: 14px;
                padding: 14px 20px;
                font-size: 1rem;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.2s;
                box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .avt-btn-primary:hover {
                background: linear-gradient(135deg, #4f46e5, #4338ca);
                box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
            }

            .avt-btn-secondary {
                flex: 1;
                background: linear-gradient(135deg, #10b981, #059669);
                color: #ffffff;
                border: none;
                border-radius: 14px;
                padding: 14px 20px;
                font-size: 1rem;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.2s;
                box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .avt-btn-secondary:hover {
                background: linear-gradient(135deg, #059669, #047857);
                box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6);
            }

            /* Modal Styling */
            #<?php echo $id; ?>-modal-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.85);
                backdrop-filter: blur(8px);
                z-index: 99999;
                align-items: center;
                justify-content: center;
                padding: 16px;
            }

            .avt-modal-box {
                background: linear-gradient(145deg, #1e1b4b 0%, #0f172a 100%);
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 20px;
                padding: 24px;
                width: 100%;
                max-width: 440px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
                direction: <?php echo $t['dir']; ?>;
                color: #f8fafc;
                position: relative;
                animation: avt-modal-pop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            @keyframes avt-modal-pop {
                from { opacity: 0; transform: scale(0.85) translateY(20px); }
                to { opacity: 1; transform: scale(1) translateY(0); }
            }

            .avt-modal-close {
                position: absolute;
                top: 16px;
                <?php echo $t['dir'] === 'rtl' ? 'left' : 'right'; ?>: 16px;
                background: transparent;
                border: none;
                color: #94a3b8;
                font-size: 1.5rem;
                cursor: pointer;
                transition: color 0.2s;
            }

            .avt-modal-close:hover {
                color: #ffffff;
            }

            .avt-modal-title {
                font-size: 1.3rem;
                font-weight: 700;
                margin-bottom: 8px;
                color: #f8fafc;
            }

            .avt-modal-desc {
                font-size: 0.88rem;
                color: #94a3b8;
                margin-bottom: 20px;
            }

            .avt-input-group {
                margin-bottom: 20px;
            }

            .avt-input-label {
                display: block;
                font-size: 0.85rem;
                font-weight: 600;
                color: #cbd5e1;
                margin-bottom: 6px;
            }

            .avt-text-input {
                width: 100%;
                background: rgba(15, 23, 42, 0.7);
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 12px;
                padding: 12px 16px;
                color: #ffffff;
                font-size: 1rem;
                outline: none;
                transition: border-color 0.2s;
            }

            .avt-text-input:focus {
                border-color: #6366f1;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
            }

            .avt-alert-msg {
                background: rgba(16, 185, 129, 0.2);
                border: 1px solid rgba(16, 185, 129, 0.4);
                color: #6ee7b7;
                padding: 12px 16px;
                border-radius: 12px;
                font-size: 0.9rem;
                margin-bottom: 16px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            /* Responsive Breakpoints for Mobile Devices */
            @media (max-width: 580px) {
                #<?php echo $id; ?>-wrapper {
                    width: calc(100% - 24px); /* Clean side margin on mobile */
                    padding: 16px 14px;
                    border-radius: 20px;
                    margin: 8px auto;
                }
                #<?php echo $id; ?>-header {
                    padding: 8px 12px;
                    margin-bottom: 12px;
                    flex-wrap: nowrap;
                    gap: 8px;
                }
                .avt-title-badge {
                    font-size: 0.88rem;
                    width: auto;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: 46%;
                }
                .avt-stats-bar {
                    width: auto;
                    gap: 8px;
                    flex-shrink: 0;
                }
                .avt-stat-box {
                    flex: auto;
                    min-width: 64px;
                    padding: 5px 9px;
                    border-radius: 10px;
                }
                .avt-stat-label {
                    font-size: 0.68rem;
                    margin-bottom: 0;
                }
                .avt-stat-value {
                    font-size: 1.05rem;
                }
                #<?php echo $id; ?>-img-container {
                    height: clamp(210px, 33vh, 280px);
                    margin-bottom: 14px;
                    border-radius: 16px;
                }
                #<?php echo $id; ?>-options {
                    grid-template-columns: 1fr 1fr;
                    gap: 10px;
                    margin-bottom: 14px;
                }
                .avt-option-btn {
                    padding: 12px 10px;
                    min-height: clamp(52px, 7.5vh, 66px);
                    font-size: 0.92rem;
                    border-radius: 12px;
                }
                .avt-skip-btn {
                    padding: 13px 20px;
                    font-size: 0.98rem;
                    border-radius: 14px;
                }
                .avt-badge-info {
                    font-size: 0.78rem;
                }
                .avt-action-btns {
                    flex-direction: column;
                    gap: 10px;
                }
            }
        </style>

        <div id="<?php echo $id; ?>-wrapper">
            <?php if ($this->submittedSuccess && !empty($this->submitMessage)): ?>
                <div class="avt-alert-msg">
                    <span>✓</span> <?php echo htmlspecialchars($this->submitMessage, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <!-- Header Stats -->
            <div id="<?php echo $id; ?>-header">
                <div class="avt-title-badge">
                    <span>🎮</span> <?php echo htmlspecialchars($config->title, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="avt-stats-bar">
                    <div class="avt-stat-box">
                        <span class="avt-stat-label"><?php echo $t['time_remaining']; ?></span>
                        <span class="avt-stat-value avt-timer-value" id="<?php echo $id; ?>-timer">120.0</span>
                    </div>
                    <div class="avt-stat-box">
                        <span class="avt-stat-label"><?php echo $t['score']; ?></span>
                        <span class="avt-stat-value avt-score-value" id="<?php echo $id; ?>-score">0</span>
                    </div>
                </div>
            </div>

            <!-- Main Play Area -->
            <div id="<?php echo $id; ?>-playarea">
                <div id="<?php echo $id; ?>-img-container">
                    <img id="<?php echo $id; ?>-img" src="" alt="<?php echo htmlspecialchars($t['question_alt'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div id="<?php echo $id; ?>-options">
                    <!-- Dynamic option buttons -->
                </div>

                <div id="<?php echo $id; ?>-controls">
                    <button type="button" class="avt-skip-btn" id="<?php echo $id; ?>-skip-btn">
                        <?php echo $t['skip']; ?>
                    </button>
                    <div class="avt-badge-info">
                        <?php echo $t['info_badge']; ?>
                    </div>
                </div>
            </div>

            <!-- Game Over View -->
            <div id="<?php echo $id; ?>-gameover">
                <div class="avt-gameover-title"><?php echo $t['gameover_title']; ?></div>
                <p style="color:#cbd5e1; margin: 0 0 12px 0; font-size:0.92rem;"><?php echo $t['gameover_desc']; ?></p>

                <div class="avt-score-summary">
                    <div class="avt-summary-item">
                        <span class="avt-summary-num" id="<?php echo $id; ?>-final-score">0</span>
                        <span class="avt-summary-lbl"><?php echo $t['final_score']; ?></span>
                    </div>
                    <div class="avt-summary-item">
                        <span class="avt-summary-num" id="<?php echo $id; ?>-final-correct" style="color:#34d399;">0</span>
                        <span class="avt-summary-lbl"><?php echo $t['final_correct']; ?></span>
                    </div>
                    <div class="avt-summary-item">
                        <span class="avt-summary-num" id="<?php echo $id; ?>-final-skips" style="color:#f87171;">0</span>
                        <span class="avt-summary-lbl"><?php echo $t['final_skips']; ?></span>
                    </div>
                </div>

                <div class="avt-action-btns">
                    <button type="button" class="avt-btn-primary" id="<?php echo $id; ?>-retry-btn">
                        <span><?php echo $t['play_again']; ?></span> 🔄
                    </button>
                    <button type="button" class="avt-btn-secondary" id="<?php echo $id; ?>-submit-modal-btn">
                        <span><?php echo $t['submit_result']; ?></span> 🏆
                    </button>
                </div>
            </div>
        </div>

        <!-- Registration Modal (Secure Token Only) -->
        <div id="<?php echo $id; ?>-modal-overlay">
            <div class="avt-modal-box">
                <button type="button" class="avt-modal-close" id="<?php echo $id; ?>-modal-close-btn">&times;</button>
                <div class="avt-modal-title"><?php echo $t['modal_title']; ?></div>
                <div class="avt-modal-desc"><?php echo $t['modal_desc']; ?></div>

                <form id="<?php echo $id; ?>-modal-form" action="<?php echo $postUrl; ?>" method="POST">
                    <input type="hidden" name="avt_trivia_action" value="register">
                    <!-- Secure Token Field (No raw score field sent from client) -->
                    <input type="hidden" name="game_token" id="<?php echo $id; ?>-modal-token-input" value="">

                    <div class="avt-input-group">
                        <label class="avt-input-label" for="<?php echo $id; ?>-username"><?php echo $t['username_label']; ?></label>
                        <input type="text" id="<?php echo $id; ?>-username" name="username" class="avt-text-input" placeholder="<?php echo htmlspecialchars($t['username_placeholder'], ENT_QUOTES, 'UTF-8'); ?>" required autocomplete="off">
                    </div>

                    <div style="background: rgba(255,255,255,0.05); padding: 10px 14px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; color: #facc15;">
                        <?php echo $t['score_to_register']; ?> <strong id="<?php echo $id; ?>-modal-score-disp">0</strong> | <?php echo $t['duration_label']; ?> <?php echo $config->duration; ?> <?php echo $t['seconds']; ?>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" class="avt-skip-btn" id="<?php echo $id; ?>-modal-cancel-btn" style="max-width: fit-content; padding: 10px 20px; font-size: 0.9rem;"><?php echo $t['cancel']; ?></button>
                        <button type="submit" class="avt-btn-secondary" style="flex: inherit; width: auto; padding: 10px 24px; font-size: 0.9rem;"><?php echo $t['submit']; ?></button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        (function() {
            const rawItems = <?php echo $itemsJson; ?>;
            const config = {
                duration: <?php echo (float)$config->duration; ?>,
                optionsCount: <?php echo (int)$config->optionsCount; ?>,
                skipPenalty: <?php echo (float)$config->skipPenalty; ?>,
                wrongPenaltyTime: <?php echo (float)$config->wrongPenaltyTime; ?>,
                wrongPenaltyScore: <?php echo (int)$config->wrongPenaltyScore; ?>,
                correctRewardScore: <?php echo (int)$config->correctRewardScore; ?>
            };

            const instanceId = '<?php echo $id; ?>';
            const postUrl = '<?php echo $postUrl; ?>';
            
            // DOM Elements
            const elTimer = document.getElementById(instanceId + '-timer');
            const elScore = document.getElementById(instanceId + '-score');
            const elImg = document.getElementById(instanceId + '-img');
            const elOptions = document.getElementById(instanceId + '-options');
            const elSkipBtn = document.getElementById(instanceId + '-skip-btn');
            
            const elPlayArea = document.getElementById(instanceId + '-playarea');
            const elGameOver = document.getElementById(instanceId + '-gameover');
            const elFinalScore = document.getElementById(instanceId + '-final-score');
            const elFinalCorrect = document.getElementById(instanceId + '-final-correct');
            const elFinalSkips = document.getElementById(instanceId + '-final-skips');
            
            const elRetryBtn = document.getElementById(instanceId + '-retry-btn');
            const elSubmitModalBtn = document.getElementById(instanceId + '-submit-modal-btn');
            const elModalOverlay = document.getElementById(instanceId + '-modal-overlay');
            const elModalCloseBtn = document.getElementById(instanceId + '-modal-close-btn');
            const elModalCancelBtn = document.getElementById(instanceId + '-modal-cancel-btn');
            const elModalForm = document.getElementById(instanceId + '-modal-form');
            const elModalTokenInput = document.getElementById(instanceId + '-modal-token-input');
            const elModalScoreDisp = document.getElementById(instanceId + '-modal-score-disp');

            // Game State & Anti-Cheat Session
            let timeLeft = config.duration;
            let score = 0;
            let correctCount = 0;
            let skipsCount = 0;
            let timerInterval = null;
            let currentItem = null;
            let isGameOver = false;
            let usedItemIds = new Set();
            let secureToken = '';

            function shuffle(arr) {
                const a = [...arr];
                for (let i = a.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [a[i], a[j]] = [a[j], a[i]];
                }
                return a;
            }

            function startTimer() {
                clearInterval(timerInterval);

                timerInterval = setInterval(() => {
                    if (isGameOver) {
                        clearInterval(timerInterval);
                        return;
                    }
                    
                    timeLeft -= 0.1;
                    if (timeLeft <= 0) {
                        timeLeft = 0;
                        updateUI();
                        endGame();
                    } else {
                        updateUI();
                    }
                }, 100);
            }

            function updateUI() {
                elTimer.textContent = timeLeft.toFixed(1);
                elScore.textContent = score;

                if (timeLeft <= 15) {
                    elTimer.classList.add('danger');
                } else {
                    elTimer.classList.remove('danger');
                }
            }

            function nextQuestion() {
                if (isGameOver || rawItems.length === 0) return;

                // Ensure NO item is shown as correct answer twice
                const availableItems = rawItems.filter(item => !usedItemIds.has(item.id));

                // If all records in dataset have been used, end the game!
                if (availableItems.length === 0) {
                    endGame();
                    return;
                }

                // Pick 1 correct item randomly from remaining unused items
                const correctIdx = Math.floor(Math.random() * availableItems.length);
                currentItem = availableItems[correctIdx];
                usedItemIds.add(currentItem.id);

                // Pick distractors (different items from rawItems)
                const distractors = [];
                const pool = rawItems.filter(item => item.id !== currentItem.id);
                const shuffledPool = shuffle(pool);
                
                const neededDistractors = Math.min(config.optionsCount - 1, shuffledPool.length);
                for (let i = 0; i < neededDistractors; i++) {
                    distractors.push(shuffledPool[i]);
                }

                const optionsData = shuffle([currentItem, ...distractors]);

                // Render Image
                elImg.style.opacity = '0';
                setTimeout(() => {
                    elImg.src = currentItem.image;
                    elImg.style.opacity = '1';
                }, 150);

                // Render Options
                elOptions.innerHTML = '';
                optionsData.forEach(opt => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'avt-option-btn';
                    btn.textContent = opt.name;
                    btn.dataset.id = opt.id;

                    btn.addEventListener('click', function() {
                        if (isGameOver) return;
                        
                        // Disable all option buttons temporarily
                        const allBtns = elOptions.querySelectorAll('.avt-option-btn');
                        allBtns.forEach(b => b.disabled = true);

                        if (opt.id === currentItem.id) {
                            // Correct
                            btn.classList.add('correct');
                            score += config.correctRewardScore;
                            correctCount++;
                            updateUI();

                            setTimeout(() => {
                                nextQuestion();
                            }, 350);
                        } else {
                            // Wrong
                            btn.classList.add('wrong');
                            score -= config.wrongPenaltyScore;
                            timeLeft = Math.max(0, timeLeft - config.wrongPenaltyTime);
                            updateUI();

                            // Show correct choice as well
                            allBtns.forEach(b => {
                                if (b.dataset.id === currentItem.id) {
                                    b.classList.add('correct');
                                }
                            });

                            if (timeLeft <= 0) {
                                setTimeout(endGame, 500);
                            } else {
                                setTimeout(() => {
                                    nextQuestion();
                                }, 600);
                            }
                        }
                    });

                    elOptions.appendChild(btn);
                });
            }

            function handleSkip() {
                if (isGameOver) return;
                skipsCount++;
                timeLeft = Math.max(0, timeLeft - config.skipPenalty);
                updateUI();

                if (timeLeft <= 0) {
                    endGame();
                } else {
                    nextQuestion();
                }
            }

            function endGame() {
                if (isGameOver) return;
                isGameOver = true;
                clearInterval(timerInterval);
                
                elPlayArea.style.display = 'none';
                elGameOver.style.display = 'flex';

                elFinalScore.textContent = score;
                elFinalCorrect.textContent = correctCount;
                elFinalSkips.textContent = skipsCount;

                // Securely notify server game is finished and obtain token (onFinished trigger)
                const formData = new URLSearchParams();
                formData.append('avt_trivia_action', 'finish');
                formData.append('score', score);
                formData.append('correct', correctCount);
                formData.append('skips', skipsCount);
                formData.append('duration', config.duration);

                fetch(postUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData.toString()
                })
                .then(res => res.json())
                .then(data => {
                    if (data && data.success && data.token) {
                        secureToken = data.token;
                        elModalTokenInput.value = data.token;
                    }
                })
                .catch(err => {
                    console.error('Error notifying game finished:', err);
                });
            }

            function resetGame() {
                isGameOver = false;
                timeLeft = config.duration;
                score = 0;
                correctCount = 0;
                skipsCount = 0;
                usedItemIds.clear();
                secureToken = '';
                elModalTokenInput.value = '';

                elGameOver.style.display = 'none';
                elPlayArea.style.display = 'block';

                updateUI();
                nextQuestion();
                startTimer();
            }

            // Modal Handlers
            function openModal() {
                elModalScoreDisp.textContent = score;
                elModalOverlay.style.display = 'flex';
                document.getElementById(instanceId + '-username').focus();
            }

            function closeModal() {
                elModalOverlay.style.display = 'none';
            }

            // Event Listeners
            elSkipBtn.addEventListener('click', handleSkip);
            elRetryBtn.addEventListener('click', resetGame);
            elSubmitModalBtn.addEventListener('click', openModal);
            elModalCloseBtn.addEventListener('click', closeModal);
            elModalCancelBtn.addEventListener('click', closeModal);

            elModalOverlay.addEventListener('click', function(e) {
                if (e.target === elModalOverlay) closeModal();
            });

            // Initialize Game
            resetGame();
        })();
        </script>
        <?php
    }
}
