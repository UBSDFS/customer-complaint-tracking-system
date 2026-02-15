<?php

class AuthController
{
    private $userModel;

    public function __construct($db)
    {
        require_once __DIR__ . '/../model/userModel.php';
        $this->userModel = new UserModel($db);
    }

    public function showLogin()
    {
        $email = '';
        $password = '';

        $errors = [
            'email_error' => '',
            'password_error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = strtolower(trim($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            // Validate input
            $emailError = $this->emailValidation($email);
            if ($emailError !== '') {
                $errors['email_error'] = $emailError;
            }

            $passwordError = $this->passwordValidation($password);
            if ($passwordError !== '') {
                $errors['password_error'] = $passwordError;
            }

            // If validation passes, check DB
            if ($errors['email_error'] === '' && $errors['password_error'] === '') {

                $user = $this->getUserByEmail($email);

                if (!$user) {
                    $errors['email_error'] = 'Invalid email.';
                } elseif (!password_verify($password, $user['password_hash'])) {
                    $errors['password_error'] = 'Invalid password.';
                } else {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['role'] = $user['role']; // 'customer' | 'tech' | 'admin'
                    $_SESSION['email'] = $user['email'];

                    header('Location: index.php?action=dashboard');
                    exit;
                }
            }
        }

        
        require __DIR__ . '/../views/auth/login.php';
    }

    public function emailValidation($email)
    {
        if (empty($email)) {
            return 'E-Mail is required.';
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL)
            ? ''
            : 'Invalid email format.';
    }

    public function passwordValidation($password)
    {
        return empty($password) ? 'Password is required.' : '';
    }

    public function getUserByEmail(string $email)
    {
        return $this->userModel->getUserByEmail($email);
    }

    public function logout()
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        header("Location: index.php?action=showLogin");
        exit;
    }
}
