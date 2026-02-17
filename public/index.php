<?php
session_start();

define('BASE_PATH', realpath(__DIR__ . '/..'));

// SECURITY
//require_once __DIR__ . '/../app/util/security.php';
//Security::checkHTTPS();


// MODELS
require_once __DIR__ . '/../app/model/database.php';
require_once __DIR__ . '/../app/model/userModel.php';
require_once __DIR__ . '/../app/model/complaintModel.php';

// CONTROLLERS
require_once __DIR__ . '/../app/controller/authController.php';
require_once __DIR__ . '/../app/controller/registrationController.php';
require_once __DIR__ . '/../app/controller/dashboardController.php';
require_once __DIR__ . '/../app/controller/profileController.php';
require_once __DIR__ . '/../app/controller/complaintController.php';
require_once __DIR__ . '/../app/controller/adminController.php';

//  INIT DATABASE 
$database = new dataBase();
$db = $database->conn;

// ROUTING 
$action = $_GET['action'] ?? 'showLogin';

switch ($action) {


    // AUTH

    case 'showLogin':
        (new AuthController($db))->showLogin();
        break;

    case 'logout':
        (new AuthController($db))->logout();
        break;

    case 'register':
        (new RegistrationController($db))->registration();
        break;


    // DASHBOARDS

    case 'dashboard':
        (new DashboardController($db))->show();
        break;

    case 'techDashboard':
        (new DashboardController($db))->tech();
        break;

    case 'adminDashboard':
        (new AdminController($db))->dashboard();
        break;


    // PROFILE

    case 'profile':
        (new ProfileController($db))->edit();
        break;

    case 'updateProfile':
        (new ProfileController($db))->update();
        break;


    // CUSTOMER COMPLAINTS

    case 'newComplaint':
        (new ComplaintController($db))->create();
        break;

    case 'storeComplaint':
        (new ComplaintController($db))->store();
        break;

    case 'showComplaint':
        (new ComplaintController($db))->show();
        break;

    case 'editComplaint':
        (new ComplaintController($db))->edit();
        break;

    case 'updateComplaint':
        (new ComplaintController($db))->update();
        break;


    // ADMIN ACTIONS

    case 'adminCustomers':
        (new AdminController($db))->customers();
        break;

    case 'adminEmployees':
        (new AdminController($db))->employees();
        break;

    case 'adminUnassignedComplaints':
        (new AdminController($db))->unassignedComplaints();
        break;

    case 'adminAssignComplaint':
        (new AdminController($db))->assignComplaint();
        break;

    case 'adminWorkload':
        (new AdminController($db))->techWorkload();
        break;
    case 'adminEmployeeCreate':
        (new AdminController($db))->employeeCreate();
        break;

    case 'adminEmployeeStore':
        (new AdminController($db))->employeeStore();
        break;

    case 'adminEmployeeEdit':
        (new AdminController($db))->employeeEdit();
        break;

    case 'adminEmployeeUpdate':
        (new AdminController($db))->employeeUpdate();
        break;

    case 'adminCustomerEdit':
        (new AdminController($db))->customerEdit();
        break;

    case 'adminCustomerUpdate':
        (new AdminController($db))->customerUpdate();
        break;

    case 'adminRoleUpdate':
        (new AdminController($db))->roleUpdate();
        break;

    case 'adminOpenComplaints':
        (new AdminController($db))->openComplaints();
        break;




    // DEFAULT

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
