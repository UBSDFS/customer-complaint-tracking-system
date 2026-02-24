<?php

require_once __DIR__ . '/../model/complaintModel.php';
require_once __DIR__ . '/../model/userModel.php';


class DashboardController
{
    private ComplaintModel $complaintModel;
    private UserModel $userModel;

    public function __construct($db)
    {
        $this->complaintModel = new ComplaintModel($db);
        $this->userModel = new UserModel($db);
    }

    public function show()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=showLogin');
            exit;
        }

        $role = $_SESSION['role'] ?? 'customer';


        if ($role === 'tech') {
            return $this->tech();
        }
        if ($role === 'admin') {
            header('Location: index.php?action=adminDashboard'); // 
            exit;
        }

        $userId = (int) $_SESSION['user_id'];

        $dbUser = $this->userModel->getUserById($userId);

        $user = [
            'firstName' => $_SESSION['firstName'] ?? ($dbUser['first_name'] ?? ''),
            'lastName' => $_SESSION['lastName'] ?? ($dbUser['last_name'] ?? ''),
            'email' => $_SESSION['email'] ?? ($dbUser['email'] ?? ''),
            'phoneNumber' => $_SESSION['phoneNumber'] ?? ($dbUser['phone'] ?? ''),
            'avatar_path' => $dbUser['avatar_path'] ?? null,
        ];
        $complaints = [];
        $summary = [
            'open' => 0,
            'resolved' => 0,
            'total' => 0
        ];

        if ($role === 'customer') {
            $result = $this->complaintModel->getComplaintsByCustomerId($userId);

            if ($result['ok']) {
                $complaints = $result['complaints'];
                $summary['total'] = count($complaints);

                foreach ($complaints as $c) {
                    if (in_array($c['status'], ['open', 'assigned', 'in_progress'], true)) {
                        $summary['open']++;
                    }
                    if ($c['status'] === 'resolved') {
                        $summary['resolved']++;
                    }
                }
            }
        }

        switch ($role) {
            case 'customer':
                include __DIR__ . '/../views/dashboard/customer.php';
                break;

            case 'tech':

                include __DIR__ . '/../views/dashboard/tech.php';
                break;

            case 'admin':
                include __DIR__ . '/../views/dashboard/admin.php';
                break;

            default:
                include __DIR__ . '/../views/dashboard/customer.php';
                break;
        }
    }

    public function tech()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=showLogin');
            exit;
        }

        $role = $_SESSION['role'] ?? null;
        $techId = (int) ($_SESSION['user_id'] ?? 0);

        if ($role !== 'tech') {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        $dbUser = $this->userModel->getUserById($techId);

        $tech = [
            'name' => trim(($_SESSION['firstName'] ?? '') . ' ' . ($_SESSION['lastName'] ?? '')),
            'email' => $_SESSION['email'] ?? ($dbUser['email'] ?? ''),
            'role' => 'tech',
            'avatar_path' => $dbUser['avatar_path'] ?? null,
        ];


        $complaints = $this->complaintModel->getComplaintsAssignedToTech($techId);

        // choose selected complaint
        $selectedId = isset($_GET['complaint_id']) ? (int) $_GET['complaint_id'] : 0;
        if ($selectedId <= 0 && !empty($complaints)) {
            $selectedId = (int) $complaints[0]['complaint_id'];
        }

        $selectedComplaint = null;
        if ($selectedId > 0) {
            $r = $this->complaintModel->getComplaintById($selectedId);
            if ($r['ok']) {

                if ((int) $r['complaint']['tech_id'] === $techId) {
                    $selectedComplaint = $r['complaint'];
                }
            }
        }


        $filterStatus = $_GET['status'] ?? '';

        require BASE_PATH . '/app/views/dashboard/tech.php';
    }
}
