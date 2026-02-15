<?php
//Temporary "front controller that just shows the login view
//No routing yet

session_start();
define('BASE_PATH', realpath(__DIR__ . '/..'));
require_once __DIR__ . '/../app/model/database.php';
require_once __DIR__ . '/../app/model/userModel.php';
require_once __DIR__ . '/../app/controller/authController.php';
require_once __DIR__ . '/../app/controller/registrationController.php';
require_once __DIR__ . '/../app/controller/dashboardController.php'; //Connect to authcontroller once verified login
require_once __DIR__ . '/../app/controller/profileController.php';
require_once __DIR__ . '/../app/controller/complaintController.php';






// Initialize database connection
$database = new dataBase();
$db = $database->conn;
$userModel = new UserModel($db); // Connect to user model for profile updates
$profileController = new ProfileController($userModel); // Connect to profile controller for handling profile updates

$action = $_GET['action'] ?? 'showLogin';

switch ($action) {

    case 'showLogin':
        (new AuthController($db))->showLogin();
        break;

    case 'register':
        (new RegistrationController($db))->registration();
        break;

    case 'dashboard':
        (new DashboardController($db))->show();
        break;

    case 'profile':
        (new ProfileController($db))->edit();
        break;

    case 'updateProfile':
        (new ProfileController($db))->update();
        break;

    case 'newComplaint':
        (new ComplaintController($db))->create();
        break;

    case 'storeComplaint':
        (new ComplaintController($db))->store();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
}
