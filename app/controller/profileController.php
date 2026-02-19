<?php
class ProfileController
{
    private $userModel;

    public function __construct($db)
    {
        $this->userModel = new UserModel($db);
    }

    public function edit()
    {
        if (empty($_SESSION['user_id'])) {
            header("Location: index.php?action=showLogin");
            exit;
        }

        $user = $this->userModel->getUserById($_SESSION['user_id']);
        require __DIR__ . '/../views/profile/edit.php';
    }

    public function update()
    {
        if (empty($_SESSION['user_id'])) {
            header("Location: index.php?action=showLogin");
            exit;
        }

        $userId = (int) $_SESSION['user_id'];

        $email = $_POST['email'] ?? '';
        $first = $_POST['first_name'] ?? '';
        $last = $_POST['last_name'] ?? '';
        $street_address = $_POST['street_address'] ?? '';
        $city = $_POST['city'] ?? '';
        $state = $_POST['state'] ?? '';
        $zip = $_POST['zip'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';


        if (!empty($password)) {
            $this->userModel->updateUserPassword($userId, $password);
        }

        $this->userModel->updateUserEmail($userId, $email);
        $this->userModel->updateCustomerProfile(
            $userId,
            $first,
            $last,
            $street_address,
            $city,
            $state,
            $zip,
            $phone
        );

        header("Location: index.php?action=dashboard");
        exit;
    }
}