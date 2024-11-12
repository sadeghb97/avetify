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
