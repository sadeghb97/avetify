<?php

class APIHelper {
    public static function initJsonApi(){
        header('Content-Type: application/json');
        $GLOBALS['__json_api_errors'] = [];

        ini_set('display_errors', 0);
        error_reporting(E_ALL);

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $GLOBALS['__json_api_errors'][] = [
                'type' => 'error',
                'message' => $errstr,
                'file' => $errfile,
                'line' => $errline
            ];
            return true;
        });

        register_shutdown_function(function () {
            $last_error = error_get_last();
            if ($last_error && in_array($last_error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                $GLOBALS['__json_api_errors'][] = [
                    'type' => 'fatal',
                    'message' => $last_error['message'],
                    'file' => $last_error['file'],
                    'line' => $last_error['line']
                ];
            }

            $response = [
                'success' => empty($GLOBALS['__json_api_errors']),
                'data' => $GLOBALS['responseData'] ?? null,
                'errors' => $GLOBALS['__json_api_errors']
            ];

            echo json_encode($response);
        });
    }

    public static function requestJSONParams(){
        $paramsRaw = file_get_contents("php://input");
        return json_decode($paramsRaw, true);
    }
}
