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
