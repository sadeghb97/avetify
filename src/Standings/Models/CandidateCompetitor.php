<?php
namespace Avetify\Standings\Models;
class CandidateCompetitor {
    public float $overallScore = 0;

    public function __construct(public string $candidateId){}
}
