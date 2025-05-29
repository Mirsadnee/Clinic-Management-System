<?php
require_once '../includes/User.php';
require_once '../includes/Appointment.php';
require_once '../includes/Database.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Check if clinic_id is provided
if (!isset($_GET['clinic_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Clinic ID is required']);
    exit;
}

$db = new Database();
$appointment = new Appointment($db->getConnection());

// Get doctors for the selected clinic
$doctors = $appointment->getDoctorsByClinic($_GET['clinic_id']);

// Return doctors as JSON
header('Content-Type: application/json');
echo json_encode($doctors); 