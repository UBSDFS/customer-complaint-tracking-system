<?php
//Temporary "front controller that just shows the login view
//No routing yet

require_once __DIR__ . '/../app/controller/authcontroller.php';
require_once __DIR__ . '/../app/controller/registrationController.php';
require_once __DIR__ . '/../app/controller/dashboardController.php'; //Connect to authcontroller once verified login
$action = $_GET['action'] ?? 'showLogin';
//Switch statement for routing based on action. User login or registration of new user
switch ($action) {
    case 'showLogin':
        (new AuthController())->showLogin();
        break;

    case 'register':
        (new RegistrationController())->registration();
        break;

    case 'dashboard':
        (new DashboardController())->show();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
}
