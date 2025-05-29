<?php
require_once '../includes/User.php';
require_once '../includes/Appointment.php';
require_once '../includes/Database.php';

$db = new Database();
$user = new User();
$appointment = new Appointment($db->getConnection());

// Check if user is logged in and is a patient
if (!$user->isLoggedIn() || $_SESSION['user_role'] !== 'patient') {
    header('Location: ../login.php');
    exit;
}

$userData = $user->getUserById($_SESSION['user_id']);

// Handle form submission for new appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_appointment'])) {
    $clinic_id = $_POST['clinic_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];
    
    $success = $appointment->scheduleAppointment(
        $_SESSION['user_id'],
        $doctor_id,
        $clinic_id,
        $appointment_date,
        $appointment_time,
        $reason
    );
}

// Handle appointment cancellation
if (isset($_GET['cancel']) && isset($_GET['id'])) {
    $success = $appointment->cancelAppointment($_GET['id'], $_SESSION['user_id']);
}

// Get appointments data
$upcomingAppointments = $appointment->getUpcomingAppointments($_SESSION['user_id']);
$pastAppointments = $appointment->getPastAppointments($_SESSION['user_id']);
$clinics = $appointment->getClinics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Clinic Management</title>
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
                        <a class="nav-link active" href="appointments.php">Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="medical-records.php">Medical Records</a>
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
        <?php if (isset($success)): ?>
            <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $success ? 'Operation completed successfully!' : 'An error occurred. Please try again.'; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Appointments</h2>
                        <p class="text-muted">Manage your medical appointments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule New Appointment -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-calendar-plus me-2"></i>Schedule New Appointment</h4>
                        <form method="POST" action="" class="mt-3">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="clinic" class="form-label">Select Clinic</label>
                                    <select class="form-select" id="clinic" name="clinic_id" required>
                                        <option value="">Choose a clinic...</option>
                                        <?php foreach ($clinics as $clinic): ?>
                                            <option value="<?php echo $clinic['id']; ?>"><?php echo htmlspecialchars($clinic['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="doctor" class="form-label">Select Doctor</label>
                                    <select class="form-select" id="doctor" name="doctor_id" required>
                                        <option value="">Choose a doctor...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="appointment_date" class="form-label">Appointment Date</label>
                                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="appointment_time" class="form-label">Appointment Time</label>
                                    <input type="time" class="form-control" id="appointment_time" name="appointment_time" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason for Visit</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                            </div>
                            <button type="submit" name="schedule_appointment" class="btn btn-primary">
                                <i class="fas fa-calendar-check me-2"></i>Schedule Appointment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-calendar-alt me-2"></i>Upcoming Appointments</h4>
                        <div class="table-responsive mt-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Doctor</th>
                                        <th>Clinic</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($upcomingAppointments)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No upcoming appointments</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($upcomingAppointments as $appointment): ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                                <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['clinic_name']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $appointment['status'] === 'scheduled' ? 'success' : 'danger'; ?>">
                                                        <?php echo ucfirst($appointment['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($appointment['status'] === 'scheduled'): ?>
                                                        <a href="?cancel=1&id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                            <i class="fas fa-times me-1"></i>Cancel
                                                        </a>
                                                    <?php endif; ?>
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

        <!-- Past Appointments -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-history me-2"></i>Past Appointments</h4>
                        <div class="table-responsive mt-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Doctor</th>
                                        <th>Clinic</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($pastAppointments)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No past appointments</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($pastAppointments as $appointment): ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                                <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['clinic_name']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $appointment['status'] === 'completed' ? 'success' : 'danger'; ?>">
                                                        <?php echo ucfirst($appointment['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="medical-records.php?appointment_id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-file-medical me-1"></i>View Records
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add JavaScript for dynamic clinic-doctor relationship
        document.getElementById('clinic').addEventListener('change', function() {
            const clinicId = this.value;
            const doctorSelect = document.getElementById('doctor');
            
            // Clear current options
            doctorSelect.innerHTML = '<option value="">Choose a doctor...</option>';
            
            if (clinicId) {
                // Fetch doctors for selected clinic
                fetch(`get_doctors.php?clinic_id=${clinicId}`)
                    .then(response => response.json())
                    .then(doctors => {
                        doctors.forEach(doctor => {
                            const option = document.createElement('option');
                            option.value = doctor.id;
                            option.textContent = doctor.name;
                            doctorSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching doctors:', error));
            }
        });
    </script>
</body>
</html> 