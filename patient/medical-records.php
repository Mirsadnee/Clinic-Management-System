<?php
require_once '../includes/User.php';
require_once '../includes/MedicalRecord.php';
require_once '../includes/Database.php';

$db = new Database();
$user = new User();
$medicalRecord = new MedicalRecord($db->getConnection());

// Check if user is logged in and is a patient
if (!$user->isLoggedIn() || $_SESSION['user_role'] !== 'patient') {
    header('Location: ../login.php');
    exit;
}

$userData = $user->getUserById($_SESSION['user_id']);

// Get specific appointment record if requested
$appointmentRecord = null;
if (isset($_GET['appointment_id'])) {
    $appointmentRecord = $medicalRecord->getAppointmentMedicalRecord($_GET['appointment_id'], $_SESSION['user_id']);
}

// Get all medical records
$medicalRecords = $medicalRecord->getPatientMedicalRecords($_SESSION['user_id']);
$allergies = $medicalRecord->getPatientAllergies($_SESSION['user_id']);
$medications = $medicalRecord->getPatientMedications($_SESSION['user_id']);
$conditions = $medicalRecord->getPatientConditions($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records - Clinic Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-hospital me-2"></i>Clinic Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointments.php">Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="medical-records.php">Medical Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Medical Records</h2>
                        <p class="text-muted">View your complete medical history</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($appointmentRecord): ?>
            <!-- Specific Appointment Record -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4><i class="fas fa-file-medical me-2"></i>Appointment Details</h4>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($appointmentRecord['appointment_date'])); ?></p>
                                    <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($appointmentRecord['appointment_time'])); ?></p>
                                    <p><strong>Doctor:</strong> <?php echo htmlspecialchars($appointmentRecord['doctor_name']); ?></p>
                                    <p><strong>Clinic:</strong> <?php echo htmlspecialchars($appointmentRecord['clinic_name']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Diagnosis:</strong> <?php echo htmlspecialchars($appointmentRecord['diagnosis'] ?? 'Not recorded'); ?></p>
                                    <p><strong>Treatment:</strong> <?php echo htmlspecialchars($appointmentRecord['treatment'] ?? 'Not recorded'); ?></p>
                                    <p><strong>Notes:</strong> <?php echo htmlspecialchars($appointmentRecord['notes'] ?? 'No additional notes'); ?></p>
                                </div>
                            </div>
                            <a href="medical-records.php" class="btn btn-primary mt-3">
                                <i class="fas fa-arrow-left me-2"></i>Back to All Records
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Medical History Overview -->
            <div class="row">
                <!-- Allergies -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h4><i class="fas fa-allergies me-2"></i>Allergies</h4>
                            <?php if (empty($allergies)): ?>
                                <p class="text-muted">No allergies recorded</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($allergies as $allergy): ?>
                                        <li class="list-group-item">
                                            <?php echo htmlspecialchars($allergy['allergy_name']); ?>
                                            <small class="text-muted d-block">Severity: <?php echo htmlspecialchars($allergy['severity']); ?></small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Current Medications -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h4><i class="fas fa-pills me-2"></i>Current Medications</h4>
                            <?php if (empty($medications)): ?>
                                <p class="text-muted">No active medications</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($medications as $medication): ?>
                                        <li class="list-group-item">
                                            <?php echo htmlspecialchars($medication['medication_name']); ?>
                                            <small class="text-muted d-block">
                                                Dosage: <?php echo htmlspecialchars($medication['dosage']); ?><br>
                                                Frequency: <?php echo htmlspecialchars($medication['frequency']); ?>
                                            </small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Medical Conditions -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h4><i class="fas fa-notes-medical me-2"></i>Medical Conditions</h4>
                            <?php if (empty($conditions)): ?>
                                <p class="text-muted">No active medical conditions</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($conditions as $condition): ?>
                                        <li class="list-group-item">
                                            <?php echo htmlspecialchars($condition['condition_name']); ?>
                                            <small class="text-muted d-block">
                                                Diagnosed: <?php echo date('M d, Y', strtotime($condition['diagnosed_date'])); ?><br>
                                                Status: <?php echo htmlspecialchars($condition['status']); ?>
                                            </small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Medical Records History -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4><i class="fas fa-history me-2"></i>Medical Records History</h4>
                            <div class="table-responsive mt-3">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Doctor</th>
                                            <th>Clinic</th>
                                            <th>Diagnosis</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($medicalRecords)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No medical records found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($medicalRecords as $record): ?>
                                                <tr>
                                                    <td><?php echo date('M d, Y', strtotime($record['appointment_date'])); ?></td>
                                                    <td><?php echo htmlspecialchars($record['doctor_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['clinic_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['diagnosis'] ?? 'Not recorded'); ?></td>
                                                    <td>
                                                        <a href="?appointment_id=<?php echo $record['appointment_id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye me-1"></i>View Details
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 