<?php

require_once __DIR__ . '/../model/complaintModel.php';

class DashboardController
{
    private ComplaintModel $complaintModel;

    public function __construct($db)
    {
        $this->complaintModel = new ComplaintModel($db);
    }

    public function show()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=showLogin');
            exit;
        }

        $role = $_SESSION['role'] ?? 'customer';
        $userId = (int)$_SESSION['user_id'];

        $user = [
            'firstName'   => $_SESSION['firstName'] ?? '',
            'lastName'    => $_SESSION['lastName'] ?? '',
            'email'       => $_SESSION['email'] ?? '',
            'phoneNumber' => $_SESSION['phoneNumber'] ?? ''
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
                // optional: you can load assigned complaints here too, or call $this->tech()
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
        $techId = (int)($_SESSION['user_id'] ?? 0);

        if ($role !== 'tech') {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        $tech = [
            'name'  => trim(($_SESSION['firstName'] ?? '') . ' ' . ($_SESSION['lastName'] ?? '')),
            'email' => $_SESSION['email'] ?? '',
            'role'  => 'tech',
        ];

        // assigned complaints (NOTE: your model currently returns raw rows)
        $complaints = $this->complaintModel->getComplaintsAssignedToTech($techId);

        // choose selected complaint
        $selectedId = isset($_GET['complaint_id']) ? (int)$_GET['complaint_id'] : 0;
        if ($selectedId <= 0 && !empty($complaints)) {
            $selectedId = (int)$complaints[0]['complaint_id'];
        }

        $selectedComplaint = null;
        if ($selectedId > 0) {
            $r = $this->complaintModel->getComplaintById($selectedId);
            if ($r['ok']) {
                // enforce: tech can only open assigned
                if ((int)$r['complaint']['tech_id'] === $techId) {
                    $selectedComplaint = $r['complaint'];
                }
            }
        }

        // simple GET filter
        $filterStatus = $_GET['status'] ?? '';

        require BASE_PATH . '/app/views/dashboard/tech.php';
    }
}
