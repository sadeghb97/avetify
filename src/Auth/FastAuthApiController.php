<?php

namespace Avetify\Auth;

use Avetify\Api\APIHelper;
use Avetify\DB\DBConnection;
use Throwable;

class FastAuthApiController {
    private array $jsonParams;

    public function __construct(public DBConnection $conn, public AvtAuth $auth) {
        $this->jsonParams = APIHelper::getRequestJSONParams();
    }

    private function methodNotAllowed(): void {
        http_response_code(405);
        echo json_encode(['error' => 'method_not_allowed']);
    }

    private function getCredentials(): array|null {
        $username = trim((string)APIHelper::getPostParam($this->jsonParams, 'username'));
        $password = (string)APIHelper::getPostParam($this->jsonParams, 'password');

        if ($username === '' || $password === '') {
            http_response_code(400);
            echo json_encode(['error' => 'missing_fields']);
            return null;
        }

        return [$username, $password];
    }

    public function login(): void {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->methodNotAllowed();
            return;
        }

        $credentials = $this->getCredentials();
        if ($credentials === null) return;

        [$username, $password] = $credentials;

        try {
            $res = $this->auth->login($this->conn, $username, $password);

            if (!($res['ok'] ?? false)) {
                $err = (string)($res['error'] ?? 'login_failed');

                if ($err === 'username_not_found') http_response_code(404);
                else http_response_code(400);

                echo json_encode(['error' => $err]);
                return;
            }

            echo json_encode($res);

        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'server_error']);
        }
    }

    public function register(): void {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->methodNotAllowed();
            return;
        }

        $credentials = $this->getCredentials();
        if ($credentials === null) return;

        [$username, $password] = $credentials;

        if (strlen($username) < 3) {
            http_response_code(400);
            echo json_encode(['error' => 'username_too_short']);
            return;
        }

        if (strlen($password) < 4) {
            http_response_code(400);
            echo json_encode(['error' => 'password_too_short']);
            return;
        }

        try {
            $res = $this->auth->register($this->conn, $username, $password);

            if (!($res['ok'] ?? false)) {
                $err = (string)($res['error'] ?? 'register_failed');

                if ($err === 'username_exists') http_response_code(409);
                else http_response_code(400);

                echo json_encode(['error' => $err]);
                return;
            }

            echo json_encode($res);

        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'server_error']);
        }
    }
}
