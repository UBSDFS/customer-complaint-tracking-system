<?php

class RegistrationController
{
    private $userModel;

    public function __construct($db)
    {
        require_once __DIR__ . '/../model/userModel.php';
        $this->userModel = new UserModel($db);
    }

    public function registration()
    {
        //Initialize variables

        $email = '';
        $firstName = '';
        $lastName = '';
        $streetAddress = '';
        $city = '';
        $state = '';
        $zipCode = '';
        $phoneNumber = '';
        $password = '';

        $errors = [
            'firstName' => '',
            'lastName' => '',
            'email' => '',
            'streetAddress' => '',
            'city' => '',
            'state' => '',
            'zipCode' => '',
            'phoneNumber' => '',
            'password' => ''
        ];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $firstName = ($_POST['firstName']);
            $lastName = ($_POST['lastName']);
            $email = ($_POST['email']);
            $streetAddress = ($_POST['streetAddress']);
            $city = ($_POST['city']);
            $state = ($_POST['state']);
            $zipCode = ($_POST['zipCode']);
            $phoneNumber = ($_POST['phoneNumber']);
            $password = $_POST['password'];

            //Basic Validation
            if ($firstName == '') {
                $errors['firstName'] = 'First Name required';
            }
            if ($lastName == '') {
                $errors['lastName'] = 'Last Name required';
            }
            if ($email == '') {
                $errors['email'] = 'Email required';
            }
            if ($streetAddress == '') {
                $errors['streetAddress'] = 'Street Address required';
            }
            if ($city == '') {
                $errors['city'] = 'City required';
            }
            if ($state == '') {
                $errors['state'] = 'State required';
            }
            if ($zipCode == '') {
                $errors['zipCode'] = 'Zip Code required';
            }
            if ($phoneNumber == '') {
                $errors['phoneNumber'] = 'Phone Number required';
            }
            if ($password == '') {
                $errors['password'] = 'Password required';
            }

            // Check if all validations passed
            $hasErrors = false;
            foreach ($errors as $error) {
                if ($error !== '') {
                    $hasErrors = true;
                    break;
                }
            }

            if (!$hasErrors) {
                // Check if email already exists
                $existingUser = $this->userModel->getUserByEmail($email);
                if ($existingUser) {
                    $errors['email'] = 'Email already registered';
                } else {
                    // Register the user
                    $result = $this->userModel->registerCustomer(
                        $email, 
                        $password, 
                        $firstName, 
                        $lastName, 
                        $streetAddress, 
                        $city, 
                        $state, 
                        $zipCode, 
                        $phoneNumber
                    );

                    if ($result['success']) {
                        // Set session variables
                        $_SESSION['user_id'] = $result['user_id'];
                        $_SESSION['role'] = 'customer';
                        $_SESSION['email'] = $email;
                        $_SESSION['name'] = $firstName . ' ' . $lastName;

                        // Redirect to dashboard
                        header('Location: index.php?action=dashboard');
                        exit;
                    } else {
                        $errors['email'] = 'Registration failed: ' . $result['error'];
                    }
                }
            }
        }

        //After preparing variables show view
        require __DIR__ . '/../views/auth/register.php';
    }
}
