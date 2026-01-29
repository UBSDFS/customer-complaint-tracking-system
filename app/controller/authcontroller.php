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
            'email' => '',
            'password' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = ($_POST['email']);
            $password = $_POST['password'];

            //Basic Validation
            if ($email == '') {
                $errors['email'] = 'Email required';
            }
            if ($password == '') {
                $errors['password'] = 'Password required';
            }

            //Later, if no errors, check DB, set session, redirect, etc
        }

        //After preparing variables show view
        require __DIR__ . '/../views/auth/login.php';
    }
}
