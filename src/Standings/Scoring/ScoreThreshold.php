<?php
namespace Avetify\Standings\Scoring;

class ScoreThreshold {
    public function __construct(public int $value, public int $score){}
}
