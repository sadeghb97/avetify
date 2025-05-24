<?php
namespace Avetify\Utils;

function _formatMegaNumber($number, $unit, $unitPower, $decimals) : string {
    $decimalsMp = pow(10, $decimals);
    $unitValue = pow(10, $unitPower);
    $val = ((int)(($number * $decimalsMp) / $unitValue)) / $decimalsMp;
    return "$val" . $unit;
}

function formatMegaNumber($number) : string {
    if($number > 10000000000) return _formatMegaNumber($number, "B", 9, 0);
    if($number > 1000000000) return _formatMegaNumber($number, "B", 9, 1);

    if($number > 10000000) return _formatMegaNumber($number, "M", 6, 0);
    if($number > 1000000) return _formatMegaNumber($number, "M", 6, 1);

    if($number > 10000) return _formatMegaNumber($number, "K", 3, 0);
    if($number > 1000) return _formatMegaNumber($number, "K", 3, 1);

    return (int) $number;
}

function formatDecimals(float $number, int $decimals) : float {
    $p = pow(10, $decimals);
    return ((int)($number * $p)) / $p;
}
