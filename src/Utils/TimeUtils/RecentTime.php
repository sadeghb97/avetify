<?php
namespace Avetify\Utils\TimeUtils;

class RecentTime {
    public function __construct(
        public int $years, public int $months, public int $days,
        public int $hours, public int $minutes, public int $seconds
    ){}
}
