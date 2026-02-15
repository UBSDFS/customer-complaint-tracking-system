<?php

require_once __DIR__ . '/../model/complaintModel.php';

class DashboardController
{
    private $complaintModel;

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

        // Basic user info (expand later if needed)
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
                    if (
                        $c['status'] === 'open' ||
                        $c['status'] === 'assigned' ||
                        $c['status'] === 'in_progress'
                    ) {
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
                include_once __DIR__ . '/../views/dashboard/customer.php';
                break;

            case 'tech':
                include_once __DIR__ . '/../views/dashboard/tech.php';
                break;

            case 'admin':
                include_once __DIR__ . '/../views/dashboard/admin.php';
                break;

            default:
                include_once __DIR__ . '/../views/dashboard/customer.php';
                break;
        }
    }
}
