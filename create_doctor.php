<?php
require_once 'includes/Database.php';
require_once 'includes/User.php';

$db = new Database();
$user = new User();

try {
    // First, check if clinic exists
    $stmt = $db->getConnection()->prepare("SELECT id FROM clinics WHERE id = ?");
    $stmt->execute([1]);
    $clinicExists = $stmt->fetch();

    // If clinic doesn't exist, create it
    if (!$clinicExists) {
        $stmt = $db->getConnection()->prepare("
            INSERT INTO clinics (name, address, phone, email, status, created_at) 
            VALUES (?, ?, ?, ?, 'active', NOW())
        ");
        $stmt->execute([
            'Main Clinic',
            '123 Medical Center Drive',
            '555-0123',
            'contact@mainclinic.com'
        ]);
        echo "Clinic created successfully!\n";
    }

    // Doctor user details
    $doctorData = [
        'name' => 'Dr. John Smith',
        'email' => 'doctor@clinic.com',
        'password' => 'doctor123', // This will be hashed
        'role' => 'doctor',
        'clinic_id' => 1
    ];

    // Create doctor user
    $result = $user->register(
        $doctorData['name'],
        $doctorData['email'],
        $doctorData['password'],
        $doctorData['role'],
        $doctorData['clinic_id']
    );

    if ($result) {
        echo "Doctor user created successfully!\n";
        echo "Email: " . $doctorData['email'] . "\n";
        echo "Password: " . $doctorData['password'] . "\n";
    } else {
        echo "Error creating doctor user. The email might already exist.\n";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} 