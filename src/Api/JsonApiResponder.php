<?php
namespace Avetify\Api;

class JsonApiResponder
{
    private static $data = null;
    private static $errors = [];
    private static $responded = false;

    public static function init(){
        header('Content-Type: application/json');
        ini_set('display_errors', 0);
        error_reporting(E_ALL);

        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function respond(array $data){
        self::$data = $data;
        self::$responded = true;
    }

    public static function fail(string $message){
        self::$errors[] = [
            'type' => 'manual',
            'message' => $message,
            'file' => null,
            'line' => null
        ];
        self::$responded = false;
    }

    public static function handleError($errno, $errstr, $errfile, $errline){
        self::$errors[] = [
            'type' => 'error',
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline
        ];
        return true;
    }

    public static function handleShutdown(){
        $last_error = error_get_last();
        if ($last_error && in_array($last_error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::$errors[] = [
                'type' => 'fatal',
                'message' => $last_error['message'],
                'file' => $last_error['file'],
                'line' => $last_error['line']
            ];
        }

        $response = [
            'success' => empty(self::$errors) && self::$responded,
            'data' => self::$responded ? self::$data : null,
            'errors' => self::$errors
        ];

        echo json_encode($response);
    }
}
