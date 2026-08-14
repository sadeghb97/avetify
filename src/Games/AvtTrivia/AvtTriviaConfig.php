<?php
namespace Avetify\Games\AvtTrivia;

class AvtTriviaConfig {
    public string $lang = 'en'; // 'en' or 'fa'
    public int $duration = 120;
    public string $title = '';
    public int $optionsCount = 4;
    public float $skipPenalty = 0.5;
    public float $wrongPenaltyTime = 1.0;
    public int $wrongPenaltyScore = 1;
    public int $correctRewardScore = 3;
    public string $postUrl = '';
    public string $theme = 'dark';
    /** @var callable|null */
    public $onFinished = null;
    /** @var callable|null */
    public $onRegister = null;

    public function __construct(array $config = []) {
        if (isset($config['lang'])) {
            $this->lang = strtolower((string)$config['lang']) === 'fa' ? 'fa' : 'en';
        }
        
        if (isset($config['title'])) {
            $this->title = (string) $config['title'];
        } else {
            $this->title = $this->lang === 'fa' ? 'بازی حدس تصویر (AvtTrivia)' : 'Image Trivia Game (AvtTrivia)';
        }

        if (isset($config['duration'])) $this->duration = (int) $config['duration'];
        if (isset($config['options_count'])) $this->optionsCount = (int) $config['options_count'];
        if (isset($config['skip_penalty'])) $this->skipPenalty = (float) $config['skip_penalty'];
        if (isset($config['wrong_penalty_time'])) $this->wrongPenaltyTime = (float) $config['wrong_penalty_time'];
        if (isset($config['wrong_penalty_score'])) $this->wrongPenaltyScore = (int) $config['wrong_penalty_score'];
        if (isset($config['correct_reward_score'])) $this->correctRewardScore = (int) $config['correct_reward_score'];
        if (isset($config['post_url'])) $this->postUrl = (string) $config['post_url'];
        if (isset($config['theme'])) $this->theme = (string) $config['theme'];

        if (isset($config['onFinished']) && is_callable($config['onFinished'])) {
            $this->onFinished = $config['onFinished'];
        }
        if (isset($config['onRegister']) && is_callable($config['onRegister'])) {
            $this->onRegister = $config['onRegister'];
        }
    }

    public static function create(array $config = []): self {
        return new self($config);
    }
}
