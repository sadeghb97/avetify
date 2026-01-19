<?php
namespace Avetify\Standings\Models;

use Avetify\Entities\EntityUtils;

class DateStatItem {
    /** @var CandidateCompetitor[] */
    public array $candidates = [];

    public function __construct(public int $year, public int $month){}

    public function sortCandidates(string $sortFactor) : void {
        EntityUtils::simpleSort($this->candidates, $sortFactor, false);
    }
}
