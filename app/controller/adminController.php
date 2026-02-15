<?php

require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../model/complaintModel.php';

class AdminController
{
    private $userModel;
    private $complaintModel;

    public function __construct($db)
    {
        $this->userModel = new UserModel($db);
        $this->complaintModel = new ComplaintModel($db);
    }

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=showLogin');
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo "Forbidden";
            exit;
        }
    }

    public function dashboard()
    {
        $this->requireAdmin();
        require BASE_PATH . '/app/views/dashboard/admin.php';
    }

    public function customers()
    {
        $this->requireAdmin();
        $customers = $this->userModel->getAllCustomersWithProfile();
        require BASE_PATH . '/app/views/admin/customers.php';
    }

    public function employees()
    {
        $this->requireAdmin();
        $employees = $this->userModel->getAllEmployeesWithProfile();
        require BASE_PATH . '/app/views/admin/employees.php';
    }

    public function unassignedComplaints()
    {
        $this->requireAdmin();

        $result = $this->complaintModel->getUnassignedOpenComplaints();
        $complaints = $result['ok'] ? $result['complaints'] : [];

        $techs = $this->userModel->getTechnicians();

        require BASE_PATH . '/app/views/admin/unassigned_complaints.php';
    }

    public function assignComplaint()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        $complaintId = (int)($_POST['complaint_id'] ?? 0);
        $techId = (int)($_POST['tech_id'] ?? 0);

        if ($complaintId <= 0 || $techId <= 0) {
            http_response_code(400);
            echo "Invalid complaint_id or tech_id";
            return;
        }

        $r = $this->complaintModel->assignTech($complaintId, $techId);
        if (!$r['ok']) {
            $_SESSION['flash_error'] = $r['error'];
        } else {
            $_SESSION['flash_success'] = "Complaint #{$complaintId} assigned.";
        }

        header("Location: index.php?action=adminUnassignedComplaints");
        exit;
    }

    public function techWorkload()
    {
        $this->requireAdmin();
        $workload = $this->userModel->getTechniciansWithOpenCount();
        require BASE_PATH . '/app/views/admin/workload.php';
    }
    public function employeeCreate()
    {
        $this->requireAdmin();
        $employee = null; // blank form
        require BASE_PATH . '/app/views/admin/employee_form.php';
    }

    public function employeeStore()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $first = trim($_POST['first_name'] ?? '');
        $last  = trim($_POST['last_name'] ?? '');
        $phoneExt = trim($_POST['phone_ext'] ?? '');
        $level = trim($_POST['level'] ?? 'tech'); // tech|admin

        $r = $this->userModel->createEmployee($email, $password, $first, $last, $phoneExt !== '' ? $phoneExt : null, $level);

        if (!$r['success']) {
            $_SESSION['flash_error'] = $r['error'] ?? 'Failed to create employee.';
            header("Location: index.php?action=adminEmployeeCreate");
            exit;
        }

        $_SESSION['flash_success'] = "Employee created (User ID: {$r['user_id']}).";
        header("Location: index.php?action=adminEmployees");
        exit;
    }

    public function employeeEdit()
    {
        $this->requireAdmin();

        $userId = (int)($_GET['user_id'] ?? 0);
        if ($userId <= 0) {
            http_response_code(400);
            echo "Invalid user_id";
            return;
        }

        $employee = $this->userModel->getEmployeeById($userId);
        if (!$employee) {
            http_response_code(404);
            echo "Employee not found";
            return;
        }

        require BASE_PATH . '/app/views/admin/employee_form.php';
    }

    public function employeeUpdate()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $email = strtolower(trim($_POST['email'] ?? ''));
        $first = trim($_POST['first_name'] ?? '');
        $last  = trim($_POST['last_name'] ?? '');
        $phoneExt = trim($_POST['phone_ext'] ?? '');
        $level = trim($_POST['level'] ?? 'tech'); // tech|admin

        if ($userId <= 0) {
            $_SESSION['flash_error'] = "Invalid user id.";
            header("Location: index.php?action=adminEmployees");
            exit;
        }

        // update email (users)
        $e = $this->userModel->updateUserEmail($userId, $email);
        if (!$e['success']) {
            $_SESSION['flash_error'] = $e['error'];
            header("Location: index.php?action=adminEmployeeEdit&user_id={$userId}");
            exit;
        }

        // update employee profile
        $p = $this->userModel->updateEmployeeProfile($userId, $first, $last, $phoneExt !== '' ? $phoneExt : null, $level);
        if (!$p['success']) {
            $_SESSION['flash_error'] = $p['error'];
            header("Location: index.php?action=adminEmployeeEdit&user_id={$userId}");
            exit;
        }

        // ensure users.role matches level
        $rr = $this->userModel->syncUserRoleToEmployeeLevel($userId, $level);
        if (!$rr['success']) {
            $_SESSION['flash_error'] = $rr['error'];
            header("Location: index.php?action=adminEmployeeEdit&user_id={$userId}");
            exit;
        }

        $_SESSION['flash_success'] = "Employee updated.";
        header("Location: index.php?action=adminEmployees");
        exit;
    }

    public function customerEdit()
    {
        $this->requireAdmin();

        $userId = (int)($_GET['user_id'] ?? 0);
        if ($userId <= 0) {
            http_response_code(400);
            echo "Invalid user_id";
            return;
        }

        $customer = $this->userModel->getCustomerById($userId);
        if (!$customer) {
            http_response_code(404);
            echo "Customer not found";
            return;
        }

        require BASE_PATH . '/app/views/admin/customer_form.php';
    }

    public function customerUpdate()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        $userId = (int)($_POST['user_id'] ?? 0);

        $email = strtolower(trim($_POST['email'] ?? ''));
        $first = trim($_POST['first_name'] ?? '');
        $last  = trim($_POST['last_name'] ?? '');
        $street = trim($_POST['street_address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = strtoupper(trim($_POST['state'] ?? ''));
        $zip = trim($_POST['zip'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($userId <= 0) {
            $_SESSION['flash_error'] = "Invalid user id.";
            header("Location: index.php?action=adminCustomers");
            exit;
        }

        $e = $this->userModel->updateUserEmail($userId, $email);
        if (!$e['success']) {
            $_SESSION['flash_error'] = $e['error'];
            header("Location: index.php?action=adminCustomerEdit&user_id={$userId}");
            exit;
        }

        $p = $this->userModel->updateCustomerProfile($userId, $first, $last, $street, $city, $state, $zip, $phone);
        if (!$p['success']) {
            $_SESSION['flash_error'] = $p['error'];
            header("Location: index.php?action=adminCustomerEdit&user_id={$userId}");
            exit;
        }

        $_SESSION['flash_success'] = "Customer updated.";
        header("Location: index.php?action=adminCustomers");
        exit;
    }

    public function roleUpdate()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $newRole = trim($_POST['role'] ?? '');

        $r = $this->userModel->changeUserRole($userId, $newRole);

        if (!$r['success']) {
            $_SESSION['flash_error'] = $r['error'] ?? 'Role update failed.';
        } else {
            $_SESSION['flash_success'] = "Role updated.";
        }

        header("Location: index.php?action=adminDashboard");
        exit;
    }
    public function openComplaints()
    {
        $this->requireAdmin();

        $result = $this->complaintModel->getOpenComplaintsWithTech();
        $complaints = $result['ok'] ? $result['complaints'] : [];

        require BASE_PATH . '/app/views/admin/open_complaints.php';
    }
}
