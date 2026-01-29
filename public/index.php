<?php
//Temporary "front controller that just shows the login view
//No routing yet

require_once __DIR__ . '/../app/controller/authcontroller.php';


$action = $_GET['action'] ?? 'showLogin';

$controller = new AuthController();

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    // Handle unknown action
    echo "404 Not Found: " . htmlspecialchars($action);
}
