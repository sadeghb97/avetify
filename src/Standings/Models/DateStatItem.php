<?php
namespace Avetify\Standings\Models;

use Avetify\Entities\EntityUtils;

abstract class DateStatItem {
    /** @var CandidateCompetitor[] */
    public array $candidates = [];
    private array $cndMap = [];

    public abstract function applyRecord($record);

    protected function getNewCandidateObject(string $candidateId) : CandidateCompetitor {
        return new CandidateCompetitor($candidateId);
    }

    public function findOrCreateCandidate(string $candidateId) : CandidateCompetitor {
        if(isset($this->cndMap[$candidateId])) return $this->candidates[$this->cndMap[$candidateId]];
        $newCandidate = $this->getNewCandidateObject($candidateId);
        $this->candidates[] = $newCandidate;
        $this->cndMap[$candidateId] = count($this->candidates) - 1;
        return $newCandidate;
    }

    public function __construct(public int $year, public int $month){}

    public function sortCandidates(string $sortFactor) : void {
        EntityUtils::simpleSort($this->candidates, $sortFactor, false);
        $this->cndMap = [];
        for ($i = 0; count($this->candidates) > $i; $i++){
            $this->cndMap[$this->candidates[$i]->candidateId] = $i;
        }
    }
}
