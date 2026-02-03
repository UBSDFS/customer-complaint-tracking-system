<?php
//Simple controller
class AuthController
{
    public function showLogin()
    {
        //Initialize variables
        $email = '';
        $password = '';


        $errors = [
            'email_error' => '',
            'password_error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            //Validation
            $emailError = $this->emailValidation($email);
            if ($emailError != '') {
                $errors['email_error'] = $emailError;
            }
            $passwordError = $this->passwordValidation($password);
            if ($passwordError != '') {
                $errors['password_error'] = $passwordError;
            }

            //Later, if no errors, check DB, set session, redirect, etc
        }

        //After preparing variables show view
        require __DIR__ . '/../views/auth/login.php';
    }

    public function emailValidation($email){
        if (empty($email)) {
            return 'E-Mail is required.';
        }
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? '' : 'Invalid email format.';
    }
    public function passwordValidation($password){
        if (empty($password)) {
            return 'Password is required.';
        }
        // Regex pattern: at least 6 characters, at least one uppercase letter, at least one special character
        $pattern = '/^(?=.{6,})(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]).*$/';
        if (!preg_match($pattern, $password)) {
            return 'Password must be at least 6 characters long, contain at least one uppercase letter, and at least one special character.';
        }
        return '';
    }
}
