<?php
namespace Avetify\Utils\TimeUtils;

use Avetify\Externals\JDF;

class TimeUtils {
    public static function getShamsiYear(int $time): int {
        return JDF::jdate("Y", $time, '', 'Asia/Tehran', 'en');
    }

    public static function getShamsiMonth(int $time): int {
        return JDF::jdate("n", $time, '', 'Asia/Tehran', 'en');
    }

    public static function getYear(int $unixTime): int {
        return (int) date("Y", $unixTime);
    }

    public static function getMonth(int $unixTime): int {
        return (int) date("m", $unixTime);
    }

    public static function defaultShamsiFormat(int $unixTime) : string {
        return JDF::jdate("Y/m/d - H:i:s", $unixTime, '', 'Asia/Tehran', 'en');
    }

    public static function formattedTimeToUnixTimestamp($date, $time, $separator = '-', $timezone = 'UTC') : int {
        $formattedDate = $separator != "-" ? str_replace($separator, '-', $date) : $date;
        $datetime = $formattedDate . ' ' . $time;
        return strtotime($datetime . ' ' . $timezone);
    }

    public static function formattedDateToUnixTimestamp($date, $separator = '-', $timezone = 'UTC') : int {
        $formattedDate = $separator != "-" ? str_replace($separator, '-', $date) : $date;
        return strtotime($formattedDate . ' ' . $timezone);
    }

    public static function getIRSimpleDate(int $time, string $separator = "-") : string {
        return JDF::jdate("Y{$separator}m{$separator}d", $time, '', 'Asia/Tehran', 'en');
    }

    public static function getIRYearMonthDate(int $time, string $separator = "-") : string {
        return JDF::jdate("Y{$separator}m", $time, '', 'Asia/Tehran', 'en');
    }

    public static function getRecentTimeFromDuration(int $duration) : RecentTime {
        $minuteLength = 60;
        $hourLength = 3600;
        $dayLength = 24 * $hourLength;
        $monthLength = 30 * $dayLength;
        $yearLength = 365 * $dayLength;

        $y = (int) ($duration / $yearLength);
        $rem = $duration % $yearLength;
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

    public static function getRecentTime(int $now, int $time) : RecentTime {
        return self::getRecentTimeFromDuration($now - $time);
    }

    public static function getSummaryRecent(int $duration) : string {
        $recent = self::getRecentTimeFromDuration($duration);
        if($recent->days <= 0 && $recent->hours <= 0 && $recent->minutes <= 0) return 'Just Now';

        if($recent->days > 0){
            if($recent->hours > 0) return $recent->days . "d," . $recent->hours . 'h ago';
            return ($recent->days . 'd ago');
        }

        if($recent->hours > 0){
            if($recent->minutes > 0) return $recent->hours . "h," . $recent->minutes . 'm ago';
            return ($recent->hours . 'h ago');
        }

        return $recent->minutes . 'm ago';
    }

    public static function getFormattedDurationTime(int $duration, bool $isSummary = true, bool $durationMode = true) : string {
        $recent = self::getRecentTimeFromDuration($duration);

        if($recent->years > 0){
            if($isSummary){
                $formatted = self::_formatRecentTime($recent->years, $recent->months,
                    "Y", "M", "");
            }
            else {
                $formatted = self::_formatRecentTime($recent->years, $recent->months,
                    "Year", "Month", "s");
            }
        }

        else if($recent->months > 0){
            if($isSummary){
                $formatted = self::_formatRecentTime($recent->months, $recent->days,
                    "M", "D", "");
            }
            else {
                $formatted = self::_formatRecentTime($recent->months, $recent->days,
                    "Month", "Day", "s");
            }
        }

        else if($recent->days > 0){
            if($isSummary){
                $formatted = self::_formatRecentTime($recent->days, $recent->hours,
                    "D", "h", "");
            }
            else {
                $formatted = self::_formatRecentTime($recent->days, $recent->hours,
                    "Day", "Hour", "s");
            }
        }

        else if($recent->hours > 0){
            if($isSummary){
                $formatted = self::_formatRecentTime($recent->hours, $recent->minutes,
                    "h", "m", "");
            }
            else{
                $formatted = self::_formatRecentTime($recent->hours, $recent->minutes,
                    "Hour", "Minute", "s");
            }
        }

        else if($recent->minutes > 0){
            if($isSummary){
                $formatted = self::_formatRecentTime($recent->minutes, $recent->seconds,
                    "m", "s", "");
            }
            else{
                $formatted = self::_formatRecentTime($recent->minutes, $recent->seconds,
                    "Minute", "Second", "s");
            }
        }

        else if($recent->seconds > 0){
            if($isSummary){
                $formatted = self::_formatRecentTime($recent->seconds, 0,
                    "s", "", "");
            }
            else{
                $formatted = self::_formatRecentTime($recent->seconds, 0,
                    "Second", "", "s");
            }
        }
        else {
            if($durationMode) return "0s";
            return "Just Now";
        }

        return $formatted . ($durationMode ? "" : " ago");
    }

    public static function getIRFormattedDurationTime(int $duration) : string {
        $recent = self::getRecentTimeFromDuration($duration);

        if($recent->years > 0){
            return self::_formatRecentTime($recent->years, $recent->months,
                "سال", "ماه");
        }

        else if($recent->months > 0){
            return self::_formatRecentTime($recent->months,
                $recent->days, "ماه", "روز");
        }

        else if($recent->days > 0){
            return self::_formatRecentTime($recent->days, $recent->hours,
                "روز", "ساعت");
        }

        else if($recent->hours > 0){
            return self::_formatRecentTime($recent->hours, $recent->minutes,
                "ساعت", "دقیقه");
        }

        else if($recent->minutes > 0){
            return self::_formatRecentTime($recent->minutes, $recent->seconds,
                "دقیقه", "ثانیه");
        }

        else if($recent->seconds > 0){
            return self::_formatRecentTime($recent->seconds, 0,
                "ثانیه", "");
        }
        else return "همین الان";
    }

    public static function getFormattedRecentTime(int $now, int $time, bool $isSummary = true, bool $durationMode = false) : string {
        return self::getFormattedDurationTime($now - $time, $isSummary, $durationMode);
    }

    public static function getIRFormattedRecentTime(int $now, int $time) : string {
        return self::getIRFormattedDurationTime($now - $time);
    }

    public static function _formatRecentTime($bigValue, $smallValue, $bigUnit, $smallUnit, $pluralSymbol = "") : string {
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
}


