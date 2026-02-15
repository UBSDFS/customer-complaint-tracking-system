<?php

class UserModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /*AUTH / USER LOOKUPS*/

    public function getUserByEmail(string $email)
    {
        $sql = "SELECT u.user_id, u.email, u.password_hash, u.role,
                       COALESCE(c.first_name, e.first_name) AS first_name,
                       COALESCE(c.last_name,  e.last_name)  AS last_name
                FROM users u
                LEFT JOIN customer_profiles c ON u.user_id = c.user_id
                LEFT JOIN employee_profiles e ON u.user_id = e.user_id
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

    public function getUserById(int $userId)
    {
        $sql = "SELECT u.user_id, u.email, u.role,
                       COALESCE(c.first_name, e.first_name) AS first_name,
                       COALESCE(c.last_name,  e.last_name)  AS last_name,
                       c.street_address, c.city, c.state, c.zip, c.phone
                FROM users u
                LEFT JOIN customer_profiles c ON u.user_id = c.user_id
                LEFT JOIN employee_profiles e ON u.user_id = e.user_id
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

    public function getAllUsers(): array
    {
        $sql = "SELECT user_id, email, role
                FROM users
                ORDER BY role, user_id";

        $result = $this->db->query($sql);
        if (!$result) return [];

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /*USER UPDATES (USERS TABLE)*/

    public function updateUserEmail(int $userId, string $email): array
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email address.'];
        }

        // Ensure unique email (excluding current user)
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

    /*CUSTOMER PROFILE (customer_profiles) */

    public function getCustomerById(int $userId): ?array
    {
        $sql = "SELECT u.user_id, u.email, u.role,
                       c.first_name, c.last_name, c.street_address, c.city, c.state, c.zip, c.phone
                FROM users u
                JOIN customer_profiles c ON u.user_id = c.user_id
                WHERE u.user_id = ? AND u.role = 'customer'";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();

        $row = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
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
        $streetAddress = trim($streetAddress);
        $city = trim($city);
        $state = strtoupper(trim($state));
        $zip = trim($zip);
        $phone = trim($phone);

        if ($firstName === '' || $lastName === '') {
            return ['success' => false, 'error' => 'First and last name are required.'];
        }
        if ($streetAddress === '' || $city === '' || $state === '' || $zip === '' || $phone === '') {
            return ['success' => false, 'error' => 'All customer profile fields are required.'];
        }
        if (!preg_match('/^[A-Z]{2}$/', $state)) {
            return ['success' => false, 'error' => 'State must be 2 letters (ex: NC).'];
        }
        if (!preg_match('/^\d{5}$/', $zip)) {
            return ['success' => false, 'error' => 'ZIP must be 5 digits.'];
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

    public function getAllCustomersWithProfile(): array
    {
        $sql = "SELECT u.user_id, u.email, u.role,
                       c.first_name, c.last_name, c.street_address, c.city, c.state, c.zip, c.phone
                FROM users u
                JOIN customer_profiles c ON u.user_id = c.user_id
                WHERE u.role = 'customer'
                ORDER BY c.last_name, c.first_name";

        $result = $this->db->query($sql);
        if (!$result) return [];

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /*EMPLOYEE PROFILE (employee_profiles) */

    public function getEmployeeById(int $userId): ?array
    {
        $sql = "SELECT u.user_id, u.email, u.role,
                       e.first_name, e.last_name, e.phone_ext, e.level
                FROM users u
                JOIN employee_profiles e ON u.user_id = e.user_id
                WHERE u.user_id = ? AND u.role IN ('tech','admin')";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();

        $row = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }

    public function updateEmployeeProfile(
        int $userId,
        string $firstName,
        string $lastName,
        ?string $phoneExt,
        string $level
    ): array {
        $firstName = trim($firstName);
        $lastName  = trim($lastName);
        $phoneExt  = $phoneExt !== null ? trim($phoneExt) : null;

        if ($firstName === '' || $lastName === '') {
            return ['success' => false, 'error' => 'First and last name are required.'];
        }
        if (!in_array($level, ['tech', 'admin'], true)) {
            return ['success' => false, 'error' => 'Invalid employee level.'];
        }

        $sql = "UPDATE employee_profiles
                SET first_name = ?, last_name = ?, phone_ext = ?, level = ?
                WHERE user_id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return ['success' => false, 'error' => $this->db->error];

        $stmt->bind_param("ssssi", $firstName, $lastName, $phoneExt, $level, $userId);

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            return ['success' => false, 'error' => $err];
        }

        $stmt->close();
        return ['success' => true];
    }

    public function getAllEmployeesWithProfile(): array
    {
        $sql = "SELECT u.user_id, u.email, u.role,
                       e.first_name, e.last_name, e.phone_ext, e.level
                FROM users u
                JOIN employee_profiles e ON u.user_id = e.user_id
                WHERE u.role IN ('tech', 'admin')
                ORDER BY e.level, e.last_name, e.first_name";

        $result = $this->db->query($sql);
        if (!$result) return [];

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTechnicians(): array
    {
        $sql = "SELECT u.user_id, u.email, e.first_name, e.last_name
                FROM users u
                JOIN employee_profiles e ON u.user_id = e.user_id
                WHERE u.role = 'tech' AND e.level = 'tech'
                ORDER BY e.last_name, e.first_name";

        $result = $this->db->query($sql);
        if (!$result) return [];

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTechniciansWithOpenCount(): array
    {
        $sql = "SELECT u.user_id, u.email, e.first_name, e.last_name,
                       COUNT(c.complaint_id) AS open_count
                FROM users u
                JOIN employee_profiles e ON u.user_id = e.user_id
                LEFT JOIN complaints c
                  ON c.tech_id = u.user_id
                 AND c.status IN ('open','assigned','in_progress')
                WHERE u.role = 'tech' AND e.level = 'tech'
                GROUP BY u.user_id, u.email, e.first_name, e.last_name
                ORDER BY open_count DESC, e.last_name, e.first_name";

        $result = $this->db->query($sql);
        if (!$result) return [];

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createEmployee(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        ?string $phoneExt,
        string $level
    ): array {
        $email = strtolower(trim($email));
        $firstName = trim($firstName);
        $lastName = trim($lastName);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email.'];
        }
        if ($password === '') {
            return ['success' => false, 'error' => 'Password is required.'];
        }
        if ($firstName === '' || $lastName === '') {
            return ['success' => false, 'error' => 'First and last name are required.'];
        }
        if (!in_array($level, ['tech', 'admin'], true)) {
            return ['success' => false, 'error' => 'Invalid level.'];
        }

        $role = $level; // keep users.role aligned with employee level

        $this->db->begin_transaction();

        try {
            // Ensure email unique
            $check = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
            if (!$check) throw new Exception($this->db->error);
            $check->bind_param("s", $email);
            $check->execute();
            $r = $check->get_result();
            if ($r && $r->num_rows > 0) {
                $check->close();
                throw new Exception("That email is already in use.");
            }
            $check->close();

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $u = $this->db->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)");
            if (!$u) throw new Exception($this->db->error);
            $u->bind_param("sss", $email, $hash, $role);
            if (!$u->execute()) throw new Exception($u->error);

            $userId = $this->db->insert_id;
            $u->close();

            $p = $this->db->prepare(
                "INSERT INTO employee_profiles (user_id, first_name, last_name, phone_ext, level)
                 VALUES (?, ?, ?, ?, ?)"
            );
            if (!$p) throw new Exception($this->db->error);
            $p->bind_param("issss", $userId, $firstName, $lastName, $phoneExt, $level);
            if (!$p->execute()) throw new Exception($p->error);
            $p->close();

            // If this email existed as a customer in old data, remove customer profile row
            $d = $this->db->prepare("DELETE FROM customer_profiles WHERE user_id = ?");
            if (!$d) throw new Exception($this->db->error);
            $d->bind_param("i", $userId);
            $d->execute();
            $d->close();

            $this->db->commit();
            return ['success' => true, 'user_id' => $userId];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function syncUserRoleToEmployeeLevel(int $userId, string $level): array
    {
        if (!in_array($level, ['tech', 'admin'], true)) {
            return ['success' => false, 'error' => 'Invalid level.'];
        }

        $sql = "UPDATE users SET role = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return ['success' => false, 'error' => $this->db->error];

        $stmt->bind_param("si", $level, $userId);

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            return ['success' => false, 'error' => $err];
        }

        $stmt->close();
        return ['success' => true];
    }

    /*REGISTRATION (CUSTOMER CREATE)*/

    public function registerCustomer(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        string $streetAddress,
        string $city,
        string $state,
        string $zip,
        string $phone
    ) {
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

    /*ADMIN: DELETE USER*/

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

    /*ADMIN: ROLE CHANGE */

    public function changeUserRole(int $userId, string $newRole): array
    {
        if ($userId <= 0) return ['success' => false, 'error' => 'Invalid user id.'];
        if (!in_array($newRole, ['customer', 'tech', 'admin'], true)) {
            return ['success' => false, 'error' => 'Invalid role.'];
        }

        $this->db->begin_transaction();

        try {
            // Get current role + any available names
            $current = $this->getUserById($userId);
            if (!$current) {
                throw new Exception("User not found.");
            }

            $first = trim((string)($current['first_name'] ?? ''));
            $last  = trim((string)($current['last_name'] ?? ''));

            if ($first === '') $first = 'TBD';
            if ($last === '')  $last  = 'TBD';

            // Update users.role
            $u = $this->db->prepare("UPDATE users SET role = ? WHERE user_id = ?");
            if (!$u) throw new Exception($this->db->error);
            $u->bind_param("si", $newRole, $userId);
            if (!$u->execute()) throw new Exception($u->error);
            $u->close();

            if ($newRole === 'customer') {
                // Remove employee profile
                $d = $this->db->prepare("DELETE FROM employee_profiles WHERE user_id = ?");
                if (!$d) throw new Exception($this->db->error);
                $d->bind_param("i", $userId);
                $d->execute();
                $d->close();

                // Ensure customer profile exists (customer_profiles has NOT NULL fields)
                // Use placeholders that make it obvious admin must update later.
                $ins = $this->db->prepare(
                    "INSERT INTO customer_profiles (user_id, first_name, last_name, street_address, city, state, zip, phone)
                     VALUES (?, ?, ?, 'TBD', 'TBD', 'NA', '00000', '0000000000')
                     ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), last_name = VALUES(last_name)"
                );
                if (!$ins) throw new Exception($this->db->error);
                $ins->bind_param("iss", $userId, $first, $last);
                if (!$ins->execute()) throw new Exception($ins->error);
                $ins->close();
            } else {
                // tech/admin
                $level = $newRole; // tech|admin

                // Remove customer profile
                $d = $this->db->prepare("DELETE FROM customer_profiles WHERE user_id = ?");
                if (!$d) throw new Exception($this->db->error);
                $d->bind_param("i", $userId);
                $d->execute();
                $d->close();

                // Ensure employee profile exists (employee_profiles first/last are NOT NULL)
                $ins = $this->db->prepare(
                    "INSERT INTO employee_profiles (user_id, first_name, last_name, phone_ext, level)
                     VALUES (?, ?, ?, NULL, ?)
                     ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), last_name = VALUES(last_name), level = VALUES(level)"
                );
                if (!$ins) throw new Exception($this->db->error);
                $ins->bind_param("isss", $userId, $first, $last, $level);
                if (!$ins->execute()) throw new Exception($ins->error);
                $ins->close();
            }

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
