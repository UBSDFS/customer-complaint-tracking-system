<?php

class DashboardController
{
    public function show()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=showLogin');
            exit;
        }

        $role = $_SESSION['role'] ?? 'tech';

        $user = [
            'name' => $_SESSION['name'] ?? 'Ulysses',
            'email' => $_SESSION['email'] ?? 'tech@example.com'
        ];

        $complaints = [];

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


/*<?php
class DashboardController
{
    //function to show the dashboard view
    public function show()
    {
        $role = $_SESSION['role'] ?? 'customer'; //placeholder for role

        $user = [
            'name' => $_SESSION['name'] ?? 'Customer',
            'email' => $_SESSION['email'] ?? 'customer@example.com'
        ];

        $complaints = []; //will come from complaints model once Db is connected

        // Load the appropriate dashboard view based on role
        switch ($role) {
            case 'customer':
                include_once '../app/views/dashboard/customer.php';
                break;
            case 'technician':
            case 'tech':
                include_once '/../views/dashboard/tech.php';
                break;
            case 'admin':
                include_once '../app/views/dashboard/admin.php';
                break;
            default:
                // Default to customer dashboard if role is unknown
                include_once '../app/views/dashboard/customer.php';
                break;
        }
    }
}*/