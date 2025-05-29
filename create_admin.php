<?php
require_once 'includes/Database.php';
require_once 'includes/User.php';

$db = new Database();
$user = new User();

// Admin user details
$adminData = [
    'name' => 'Admin User',
    'email' => 'admin@1clinic.com',
    'password' => 'admin123', // This will be hashed
    'role' => 'admin'
];

// Create admin user
$result = $user->register($adminData['name'], $adminData['email'], $adminData['password'], $adminData['role']);

if ($result) {
    echo "Admin user created successfully!\n";
    echo "Email: " . $adminData['email'] . "\n";
    echo "Password: " . $adminData['password'] . "\n";
} else {
    echo "Error creating admin user.\n";
} 