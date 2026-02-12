<?php
//Temporary "front controller that just shows the login view
//No routing yet

session_start();
define('BASE_PATH', realpath(__DIR__ . '/..'));

require_once __DIR__ . '/../app/model/database.php';
require_once __DIR__ . '/../app/controller/authcontroller.php';
require_once __DIR__ . '/../app/controller/registrationController.php';
require_once __DIR__ . '/../app/controller/dashboardController.php'; //Connect to authcontroller once verified login




// Initialize database connection
$database = new dataBase();
$db = $database->conn;

$action = $_GET['action'] ?? 'showLogin';
//Switch statement for routing based on action. User login or registration of new user
switch ($action) {
    case 'showLogin':
        (new AuthController($db))->showLogin();
        break;

    case 'register':
        (new RegistrationController($db))->registration();
        break;

    case 'dashboard':
        (new DashboardController())->show();
        break;
    
    case 'createComplaint':
        


    default:
        http_response_code(404);
        echo "404 Not Found";
}
