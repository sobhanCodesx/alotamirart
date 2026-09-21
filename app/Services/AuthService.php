<?php
namespace App\Services;

use App\Core\Database;

class AuthService
{
    private $db;
    private $userLoaded = false;
    private $user;

    public function __construct(Database $db) { $this->db = $db; }

    public function user()
    {
        if ($this->userLoaded) return $this->user;
        $this->userLoaded = true;

        if (empty($_SESSION['id'])) {
            $this->user = null;
            return null;
        }

        $this->user = $this->db->fetch('SELECT * FROM users WHERE id = ? LIMIT 1', [(int) $_SESSION['id']]);
        return $this->user;
    }

    public function attempt($username, $password)
    {
        $user = $this->db->fetch('SELECT * FROM users WHERE user_name = ? LIMIT 1', [$username]);
        if (!$user) return false;

        $stored = isset($user['password']) ? (string) $user['password'] : '';
        $info = password_get_info($stored);
        $legacy = false;

        if (!empty($info['algo'])) $valid = password_verify($password, $stored);
        else {
            $valid = hash_equals($stored, (string) $password);
            $legacy = $valid;
        }

        if (!$valid) return false;

        if ($legacy || password_needs_rehash($stored, PASSWORD_DEFAULT)) {
            $this->db->updateById('users', $user['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        $this->setSession($user);
        $this->userLoaded = true;
        $this->user = $user;
        return true;
    }

    public function loginUser(array $user)
    {
        $this->setSession($user);
        $this->userLoaded = true;
        $this->user = $user;
    }

    public function logout()
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) session_destroy();
        $this->userLoaded = true;
        $this->user = null;
    }

    public function requireAdmin()
    {
        $user = $this->user();
        if (!$user || (int) $user['role'] !== 1) redirect('/');
        return $user;
    }

    public function requireWriter()
    {
        $user = $this->user();
        if (!$user || (int) $user['writer'] !== 1) redirect('/');
        return $user;
    }

    public function refreshSession()
    {
        $this->userLoaded = false;
        $user = $this->user();
        if ($user) $this->setSession($user);
    }

    private function setSession(array $user)
    {
        session_regenerate_id(true);
        $_SESSION['id'] = isset($user['id']) ? $user['id'] : null;
        $_SESSION['role'] = isset($user['role']) ? $user['role'] : 0;
        $_SESSION['name'] = isset($user['name']) ? $user['name'] : '';
        $_SESSION['w'] = isset($user['writer']) ? $user['writer'] : 0;
        $_SESSION['writer'] = isset($user['writer']) ? $user['writer'] : 0;
        $_SESSION['user_name'] = isset($user['user_name']) ? $user['user_name'] : '';
        if (!empty($user['img'])) $_SESSION['img'] = $user['img'];
    }
}
