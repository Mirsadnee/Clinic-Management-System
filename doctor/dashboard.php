<?php
require_once '../includes/User.php';

$user = new User();

// Check if user is logged in and is a doctor
if (!$user->isLoggedIn() || $_SESSION['user_role'] !== 'doctor') {
    header('Location: ../login.php');
    exit;
}

$userData = $user->getUserById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Clinic Management</title>
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointments.php">Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="patients.php">Patients</a>
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
                        <h2 class="card-title">Welcome, Dr. <?php echo htmlspecialchars($userData['name']); ?>!</h2>
                        <p class="text-muted">Here's your doctor dashboard overview</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Today's Appointments -->
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h4><i class="fas fa-calendar-day me-2"></i>Today's Appointments</h4>
                        <p class="text-muted">You have no appointments today</p>
                        <a href="appointments.php" class="btn btn-primary">
                            <i class="fas fa-calendar me-2"></i>View Schedule
                        </a>
                    </div>
                </div>
            </div>

            <!-- Patient Records -->
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h4><i class="fas fa-users me-2"></i>Patient Records</h4>
                        <p class="text-muted">Manage your patient records</p>
                        <a href="patients.php" class="btn btn-primary">
                            <i class="fas fa-user-md me-2"></i>View Patients
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-history me-2"></i>Recent Activity</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Patient</th>
                                        <th>Activity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" class="text-center">No recent activity</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 