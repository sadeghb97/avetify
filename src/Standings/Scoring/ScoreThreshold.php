<?php
namespace Avetify\Standings\Scoring;

class ScoreThreshold {
    public function __construct(public float $value, public float $score){}
}
