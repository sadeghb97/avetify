<?php
namespace Avetify\Forms;

class AvtSession {
    public static function with_session(callable $fn) {
        session_start();
        $result = $fn($_SESSION);
        session_write_close();
        return $result;
    }
}