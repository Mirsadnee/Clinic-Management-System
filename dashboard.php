<?php
require_once 'includes/User.php';

$user = new User();

// Check if user is logged in
if (!$user->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get user data
$userData = $user->getUserById($_SESSION['user_id']);

// Redirect based on role
switch ($_SESSION['user_role']) {
    case 'admin':
        header('Location: admin/dashboard.php');
        break;
    case 'doctor':
        header('Location: doctor/dashboard.php');
        break;
    case 'patient':
        header('Location: patient/dashboard.php');
        break;
    default:
        // Handle unknown role
        session_destroy();
        header('Location: login.php');
        break;
}
exit; 