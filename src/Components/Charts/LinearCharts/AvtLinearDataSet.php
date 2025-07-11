<?php
namespace Avetify\Components\Charts\LinearCharts;

class AvtLinearDataSet {
    public function __construct(
        public string $label,
        public array $data = [],
        public string $backgroundColor = "#702bad",
        public string $borderColor = "#7fdbb2",
    ){}
}
