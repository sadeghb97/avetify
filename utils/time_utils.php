<?php

function getShamsiYear(int $time): int {
    return jdate("Y", $time, '', 'Asia/Tehran', 'en');
}

function getShamsiMonth(int $time): int {
    return jdate("n", $time, '', 'Asia/Tehran', 'en');
}

function getYear(int $unixTime): int {
    return (int) date("Y", $unixTime);
}

function getMonth(int $unixTime): int {
    return (int) date("m", $unixTime);
}

function formattedTimeToUnixTimestamp($date, $time, $separator = '-', $timezone = 'UTC') : int {
    $formattedDate = $separator != "-" ? str_replace($separator, '-', $date) : $date;
    $datetime = $formattedDate . ' ' . $time;
    return strtotime($datetime . ' ' . $timezone);
}

function formattedDateToUnixTimestamp($date, $separator = '-', $timezone = 'UTC') : int {
    $formattedDate = $separator != "-" ? str_replace($separator, '-', $date) : $date;
    return strtotime($formattedDate . ' ' . $timezone);
}

function getIRSimpleDate(int $time) : string {
    return jdate("Y-m-d", $time, '', 'Asia/Tehran', 'en');
}

function getRecentTime(int $now, int $time) : RecentTime {
    $recent = $now - $time;
    $minuteLength = 60;
    $hourLength = 3600;
    $dayLength = 24 * $hourLength;
    $monthLength = 30 * $dayLength;
    $yearLength = 365 * $dayLength;

    $y = (int) ($recent / $yearLength);
    $rem = $recent % $yearLength;
    $m = (int) ($rem / $monthLength);
    $rem = $rem % $monthLength;
    $d = (int) ($rem / $dayLength);
    $rem = $rem % $dayLength;
    $h = (int) ($rem / $hourLength);
    $rem = $rem % $hourLength;
    $min = (int) ($rem / $minuteLength);
    $s = $rem % $minuteLength;
    return new RecentTime($y, $m, $d, $h, $min, $s);
}

function getFormattedRecentTime(int $now, int $time, bool $isSummary = true) : string {
    $recent = getRecentTime($now, $time);

    if($recent->years > 0){
        if($isSummary){
            $formatted = _formatRecentTime($recent->years, $recent->months,
                "y", "m", "");
        }
        else {
            $formatted = _formatRecentTime($recent->years, $recent->months,
                "Year", "Month", "s");
        }
    }

    else if($recent->months > 0){
        if($isSummary){
            $formatted = _formatRecentTime($recent->months, $recent->days,
                "m", "d", "");
        }
        else {
            $formatted = _formatRecentTime($recent->months, $recent->days,
                "Month", "Day", "s");
        }
    }

    else if($recent->days > 0){
        if($isSummary){
            $formatted = _formatRecentTime($recent->days, $recent->hours,
                "d", "h", "");
        }
        else {
            $formatted = _formatRecentTime($recent->days, $recent->hours,
                "Day", "Hour", "s");
        }
    }

    else if($recent->hours > 0){
        if($isSummary){
            $formatted = _formatRecentTime($recent->hours, $recent->minutes,
                "h", "m", "");
        }
        else{
            $formatted = _formatRecentTime($recent->hours, $recent->minutes,
                "Hour", "Minute", "s");
        }
    }

    else if($recent->minutes > 0){
        if($isSummary){
            $formatted = _formatRecentTime($recent->minutes, $recent->seconds,
                "m", "s", "");
        }
        else{
            $formatted = _formatRecentTime($recent->minutes, $recent->seconds,
                "Minute", "Second", "s");
        }
    }

    else if($recent->seconds > 0){
        if($isSummary){
            $formatted = _formatRecentTime($recent->seconds, 0,
                "s", "", "");
        }
        else{
            $formatted = _formatRecentTime($recent->seconds, 0,
                "Second", "", "s");
        }
    }
    else return "Just Now";

    return $formatted . " ago";
}

function getIRFormattedRecentTime(int $now, int $time) : string {
    $recent = getRecentTime($now, $time);

    if($recent->years > 0){
        return _formatRecentTime($recent->years, $recent->months,
            "سال", "ماه");
    }

    else if($recent->months > 0){
        return _formatRecentTime($recent->months,
            $recent->days, "ماه", "روز");
    }

    else if($recent->days > 0){
        return _formatRecentTime($recent->days, $recent->hours,
            "روز", "ساعت");
    }

    else if($recent->hours > 0){
        return _formatRecentTime($recent->hours, $recent->minutes,
            "ساعت", "دقیقه");
    }

    else if($recent->minutes > 0){
        return _formatRecentTime($recent->minutes, $recent->seconds,
            "دقیقه", "ثانیه");
    }

    else if($recent->seconds > 0){
        return _formatRecentTime($recent->seconds, 0,
            "ثانیه", "");
    }
    else return "همین الان";
}

function _formatRecentTime($bigValue, $smallValue, $bigUnit, $smallUnit, $pluralSymbol = "") : string {
    $out = $bigValue;
    if(strlen($bigUnit) > 1) $out .= " ";
    $out .= $bigUnit;
    if($bigValue > 1) $out .= $pluralSymbol;

    if($smallValue > 0){
        $out .= " and ";
        $out .= $smallValue;
        if(strlen($smallUnit) > 1) $out .= " ";
        $out .= $smallUnit;
        if($out > 1) $out .= $pluralSymbol;
    }

    return $out;
}

class RecentTime {
    public function __construct(
        public int $years, public int $months, public int $days,
        public int $hours, public int $minutes, public int $seconds
    ){}
}
