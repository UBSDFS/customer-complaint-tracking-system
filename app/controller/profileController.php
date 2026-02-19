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

    $userId = (int)$_SESSION['user_id'];

    
    $base = $this->userModel->getUserById($userId);
    if (!$base) {
        header("Location: index.php?action=dashboard");
        exit;
    }

    if ($base['role'] === 'customer') {
        $user = $this->userModel->getCustomerById($userId);
    } else {
        // tech/admin
        $user = $this->userModel->getEmployeeById($userId);
    }

    if (!$user) {
        header("Location: index.php?action=dashboard");
        exit;
    }

    require __DIR__ . '/../views/profile/edit.php';
}


public function update()
{
    if (empty($_SESSION['user_id'])) {
        header("Location: index.php?action=showLogin");
        exit;
    }

    $userId = (int)$_SESSION['user_id'];

    $base = $this->userModel->getUserById($userId);
    if (!$base) {
        header("Location: index.php?action=dashboard");
        exit;
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $this->userModel->updateUserEmail($userId, $email);

    if ($password !== '') {
        $this->userModel->updateUserPassword($userId, $password);
    }

    if ($base['role'] === 'customer') {
        $first  = $_POST['first_name'] ?? '';
        $last   = $_POST['last_name'] ?? '';
        $street = $_POST['street_address'] ?? '';
        $city   = $_POST['city'] ?? '';
        $state  = $_POST['state'] ?? '';
        $zip    = $_POST['zip'] ?? '';
        $phone  = $_POST['phone'] ?? '';

        $this->userModel->updateCustomerProfile($userId, $first, $last, $street, $city, $state, $zip, $phone);
    } else {
        // tech/admin
        $first    = $_POST['first_name'] ?? '';
        $last     = $_POST['last_name'] ?? '';
        $phoneExt = $_POST['phone_ext'] ?? null;

        $this->userModel->updateEmployeeSelfProfile($userId, $first, $last, $phoneExt);
    }

    header("Location: index.php?action=dashboard");
    exit;
}

}