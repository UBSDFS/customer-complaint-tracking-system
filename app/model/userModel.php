<?php
class UserModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }


    public function getUserByEmail(string $email)
    {
        $sql = "SELECT u.user_id, u.email, u.password_hash, u.role,
                       c.first_name, c.last_name
                FROM users u
                LEFT JOIN customer_profiles c ON u.user_id = c.user_id
                WHERE u.email = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;

        $stmt->close();
        return $row;
    }

    // Get user by id (auth info + customer profile if present)
    public function getUserById(int $userId)
    {
        $sql = "SELECT u.user_id, u.email, u.role,
                       c.first_name, c.last_name, c.street_address, c.city, c.state, c.zip, c.phone
                FROM users u
                LEFT JOIN customer_profiles c ON u.user_id = c.user_id
                WHERE u.user_id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;

        $stmt->close();
        return $row;
    }

    public function updateUserEmail(int $userId, string $email): array
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email address.'];
        }

        // 
        $checkSql = "SELECT user_id FROM users WHERE email = ? AND user_id <> ?";
        $check = $this->db->prepare($checkSql);
        if ($check) {
            $check->bind_param("si", $email, $userId);
            $check->execute();
            $r = $check->get_result();
            if ($r && $r->num_rows > 0) {
                $check->close();
                return ['success' => false, 'error' => 'That email is already in use.'];
            }
            $check->close();
        }

        $sql = "UPDATE users SET email = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return ['success' => false, 'error' => $this->db->error];

        $stmt->bind_param("si", $email, $userId);

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            return ['success' => false, 'error' => $err];
        }

        $stmt->close();
        return ['success' => true];
    }


    public function updateCustomerProfile(
        int $userId,
        string $firstName,
        string $lastName,
        string $streetAddress,
        string $city,
        string $state,
        string $zip,
        string $phone
    ): array {
        $firstName = trim($firstName);
        $lastName  = trim($lastName);

        if ($firstName === '' || $lastName === '') {
            return ['success' => false, 'error' => 'First and last name are required.'];
        }

        $sql = "UPDATE customer_profiles
                SET first_name = ?, last_name = ?, street_address = ?, city = ?, state = ?, zip = ?, phone = ?
                WHERE user_id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return ['success' => false, 'error' => $this->db->error];

        $stmt->bind_param("sssssssi", $firstName, $lastName, $streetAddress, $city, $state, $zip, $phone, $userId);

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            return ['success' => false, 'error' => $err];
        }

        $stmt->close();
        return ['success' => true];
    }

    /**
     * TECH profile update
     */
    public function updateTechProfile(int $userId, array $data): array
    {
        return ['success' => false, 'error' => 'Tech profile update not implemented (no tech_profiles table wired yet).'];
    }


    public function getAllUsers(): array
    {
        $sql = "SELECT user_id, email, role
                FROM users
                ORDER BY role, user_id";

        $result = $this->db->query($sql);
        if (!$result) return [];

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Admin: delete user
    public function deleteUserById(int $userId): array
    {
        if ($userId <= 0) return ['success' => false, 'error' => 'Invalid user id'];



        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return ['success' => false, 'error' => $this->db->error];

        $stmt->bind_param("i", $userId);

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            return ['success' => false, 'error' => $err];
        }

        $stmt->close();
        return ['success' => true];
    }


    public function registerCustomer(string $email, string $password, string $firstName, string $lastName, string $streetAddress, string $city, string $state, string $zip, string $phone)
    {
        $this->db->begin_transaction();

        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $userSql = "INSERT INTO users (email, password_hash, role) VALUES (?, ?, 'customer')";
            $userStmt = $this->db->prepare($userSql);
            if (!$userStmt) {
                throw new Exception("Prepare failed: " . $this->db->error);
            }

            $userStmt->bind_param("ss", $email, $passwordHash);
            if (!$userStmt->execute()) {
                throw new Exception("Failed to insert user: " . $userStmt->error);
            }

            $userId = $this->db->insert_id;

            $profileSql = "INSERT INTO customer_profiles (user_id, first_name, last_name, street_address, city, state, zip, phone)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $profileStmt = $this->db->prepare($profileSql);
            if (!$profileStmt) {
                throw new Exception("Prepare failed: " . $this->db->error);
            }

            $profileStmt->bind_param("isssssss", $userId, $firstName, $lastName, $streetAddress, $city, $state, $zip, $phone);
            if (!$profileStmt->execute()) {
                throw new Exception("Failed to insert customer profile: " . $profileStmt->error);
            }

            $this->db->commit();

            $userStmt->close();
            $profileStmt->close();

            return ['success' => true, 'user_id' => $userId];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
