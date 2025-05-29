<?php
require_once 'includes/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Drop existing tables if they exist
    $tables = [
        'medical_records',
        'appointments',
        'users',
        'clinics'
    ];

    foreach ($tables as $table) {
        $conn->exec("DROP TABLE IF EXISTS $table");
    }

    // Read and execute the SQL file
    $sql = file_get_contents('database.sql');
    $conn->exec($sql);

    echo "Database reset successfully!\n";
    echo "You can now run create_doctor.php to create the doctor user.\n";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} 