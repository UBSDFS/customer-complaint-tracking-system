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

        $userId = (int) $_SESSION['user_id'];


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

        $userId = (int) $_SESSION['user_id'];

        $base = $this->userModel->getUserById($userId);

        // --- Avatar upload (optional) ---
        $errors = [];
        $avatarPath = (string) ($base['avatar_path'] ?? ''); // keep existing by default

        if (!empty($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Profile picture upload failed.";
            } else {
                $tmp = $_FILES['avatar']['tmp_name'];
                $info = @getimagesize($tmp);

                if ($info === false) {
                    $errors[] = "Uploaded file is not a valid image.";
                } else {
                    $mime = (string) ($info['mime'] ?? '');
                    $allowed = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp',
                    ];

                    if (!isset($allowed[$mime])) {
                        $errors[] = "Only JPG, PNG, or WEBP images are allowed.";
                    } else {
                        $uploadDir = __DIR__ . '/../../public/uploads/avatars';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        $ext = $allowed[$mime];
                        $filename = 'u_' . $userId . '_' . time() . '.' . $ext;
                        $dest = $uploadDir . '/' . $filename;

                        if (!move_uploaded_file($tmp, $dest)) {
                            $errors[] = "Could not save uploaded profile picture.";
                        } else {
                            $avatarPath = '/customer-complaint-tracking-system/public/uploads/avatars/' . $filename;
                        }
                    }
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            header("Location: index.php?action=profile");
            exit;
        }

        if ($avatarPath !== (string) ($base['avatar_path'] ?? '')) {
            $this->userModel->updateUserAvatarPath($userId, $avatarPath);
        }

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
            $first = $_POST['first_name'] ?? '';
            $last = $_POST['last_name'] ?? '';
            $street = $_POST['street_address'] ?? '';
            $city = $_POST['city'] ?? '';
            $state = $_POST['state'] ?? '';
            $zip = $_POST['zip'] ?? '';
            $phone = $_POST['phone'] ?? '';

            $this->userModel->updateCustomerProfile($userId, $first, $last, $street, $city, $state, $zip, $phone);
        } else {
            // tech/admin
            $first = $_POST['first_name'] ?? '';
            $last = $_POST['last_name'] ?? '';
            $phoneExt = $_POST['phone_ext'] ?? null;

            $this->userModel->updateEmployeeSelfProfile($userId, $first, $last, $phoneExt);
        }

        header("Location: index.php?action=dashboard");
        exit;
    }

}