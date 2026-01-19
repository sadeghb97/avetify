<?php
namespace Avetify\Standings\Models;
class CandidateCompetitor {
    public float $overallScore;

    public function __construct(public string $candidateId){}
}
