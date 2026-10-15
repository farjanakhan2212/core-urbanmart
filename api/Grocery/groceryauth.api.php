<?php

/**
 * UrbanMart Grocery API - authentication.
 * POST /api/Groceryauth/login    {username, password}
 * POST /api/Groceryauth/register {username?, email, password, confirm_password, terms}
 * GET  /api/Groceryauth/validate (Bearer token)
 * GET  /api/Groceryauth/me       (Bearer token)
 */
class GroceryauthApi
{
    public function __construct()
    {
    }

    public function register($data)
    {
        $input = is_object($data) ? (array)$data : (array)$data;
        $result = grocery_register_user($input);
        if ($result['success']) {
            http_response_code(201);
            echo json_encode([
                'success' => 1,
                'message' => $result['message'],
                'user' => $result['user'],
            ]);
            return;
        }
        $status = 422;
        $message = $result['message'];
        if (isset($result['errors']['email']) && $result['message'] === 'An account with this email already exists.') {
            $status = 409;
        }
        http_response_code($status);
        echo json_encode([
            'success' => 0,
            'message' => $message,
            'errors' => $result['errors'],
        ]);
    }

    public static function decodePayload($token)
    {
        if (!$token) return null;
        $part = explode('.', $token);
        if (!isset($part[1])) return null;
        $decoded = base64_decode(str_replace(['-', '_'], ['+', '/'], $part[1]));
        $payload = json_decode($decoded);
        return ($payload && isset($payload->exp)) ? $payload : null;
    }

    public static function verify()
    {
        $token = getBearerToken();
        if (!$token) return null;
        $jwt = new JWT();
        if (!$jwt->is_valid($token)) return null;
        return self::decodePayload($token);
    }

    public function login($data)
    {
        global $db, $tx;
        $username = trim(isset($data->username) ? $data->username : '');
        $password = isset($data->password) ? $data->password : '';
        if ($username === '' || $password === '') {
            echo json_encode(['success' => 0, 'message' => 'username and password are required']);
            return;
        }
        $sql = "SELECT u.id, u.name, u.email, u.password
                FROM {$tx}users u
                WHERE (u.name = ? OR u.email = ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_object();
        $stmt->close();

        if ($user && password_verify($password, $user->password)) {
            $jwt = new JWT();
            $jwt->min = 720;
            $payload = [
                'id' => (int)$user->id,
                'name' => $user->name,
                'email' => $user->email,
                'ip' => get_ip(),
                'iss' => 'jwt.server',
                'aud' => 'intels.co',
            ];
            $token = $jwt->generate($payload);
            echo json_encode([
                'success' => 1,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => (int)$user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        } else {
            echo json_encode(['success' => 0, 'message' => 'Invalid username or password']);
        }
    }

    public function validate($data)
    {
        $user = self::verify();
        if ($user) {
            echo json_encode(['success' => 1, 'message' => 'Token is valid', 'user' => ['id' => $user->id, 'name' => $user->name]]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => 0, 'message' => 'Invalid or expired token']);
        }
    }

    public function me($data)
    {
        $user = self::verify();
        if ($user) {
            echo json_encode(['success' => 1, 'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email ?? '',
            ]]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => 0, 'message' => 'Unauthorized']);
        }
    }
}