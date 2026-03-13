<?php
namespace Avetify\Utils;

Class NumberUtils {
    public static function _formatMegaNumber($number, $unit, $unitPower, $decimals) : string {
        $decimalsMp = pow(10, $decimals);
        $unitValue = pow(10, $unitPower);
        $val = ((int)(($number * $decimalsMp) / $unitValue)) / $decimalsMp;
        return "$val" . $unit;
    }

    public static function formatMegaNumber($number) : string {
        if($number > 10000000000) return self::_formatMegaNumber($number, "B", 9, 0);
        if($number > 1000000000) return self::_formatMegaNumber($number, "B", 9, 1);

        if($number > 10000000) return self::_formatMegaNumber($number, "M", 6, 0);
        if($number > 1000000) return self::_formatMegaNumber($number, "M", 6, 1);

        if($number > 10000) return self::_formatMegaNumber($number, "K", 3, 0);
        if($number > 1000) return self::_formatMegaNumber($number, "K", 3, 1);

        return (int) $number;
    }

    public static function formatDecimals(float $number, int $decimals) : float {
        $p = pow(10, $decimals);
        return ((int)($number * $p)) / $p;
    }

    public static function numberTitle(int $number) : string {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'Negative ';
        $decimal     = ' point ';
        $dictionary  = [
            0                   => 'Zero',
            1                   => 'One',
            2                   => 'Two',
            3                   => 'Three',
            4                   => 'Four',
            5                   => 'Five',
            6                   => 'Six',
            7                   => 'Seven',
            8                   => 'Eight',
            9                   => 'Nine',
            10                  => 'Ten',
            11                  => 'Eleven',
            12                  => 'Twelve',
            13                  => 'Thirteen',
            14                  => 'Fourteen',
            15                  => 'Fifteen',
            16                  => 'Sixteen',
            17                  => 'Seventeen',
            18                  => 'Eighteen',
            19                  => 'Nineteen',
            20                  => 'Twenty',
            30                  => 'Thirty',
            40                  => 'Forty',
            50                  => 'Fifty',
            60                  => 'Sixty',
            70                  => 'Seventy',
            80                  => 'Eighty',
            90                  => 'Ninety',
            100                 => 'Hundred',
            1000                => 'Thousand',
            1000000             => 'Million',
            1000000000          => 'Billion'
        ];

        if (!is_numeric($number)) {
            return false;
        }

        if ($number < 0) {
            return $negative . self::numberTitle(abs($number));
        }

        $string = $fraction = null;

        if (str_contains((string)$number, '.')) {
            list($number, $fraction) = explode('.', (string)$number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int)($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = (int)($number / 100);
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::numberTitle($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::numberTitle($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::numberTitle($remainder);
                }
                break;
        }

        if (is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string)$fraction) as $digit) {
                $words[] = $dictionary[$digit];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}
