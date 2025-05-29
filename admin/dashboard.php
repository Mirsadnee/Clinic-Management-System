<?php
require_once '../includes/User.php';

$user = new User();

// Check if user is logged in and is an admin
if (!$user->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
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
    <title>Admin Dashboard - Clinic Management</title>
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
                        <a class="nav-link" href="users.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clinics.php">Clinics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">Settings</a>
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
                        <h2 class="card-title">Welcome, <?php echo htmlspecialchars($userData['name']); ?>!</h2>
                        <p class="text-muted">Here's your admin dashboard overview</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Users Overview -->
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h4><i class="fas fa-users me-2"></i>Users</h4>
                        <p class="text-muted">Manage system users</p>
                        <a href="users.php" class="btn btn-primary">
                            <i class="fas fa-user-cog me-2"></i>Manage Users
                        </a>
                    </div>
                </div>
            </div>

            <!-- Clinics Overview -->
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h4><i class="fas fa-hospital me-2"></i>Clinics</h4>
                        <p class="text-muted">Manage clinic information</p>
                        <a href="clinics.php" class="btn btn-primary">
                            <i class="fas fa-hospital-alt me-2"></i>Manage Clinics
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Settings -->
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h4><i class="fas fa-cogs me-2"></i>Settings</h4>
                        <p class="text-muted">Configure system settings</p>
                        <a href="settings.php" class="btn btn-primary">
                            <i class="fas fa-wrench me-2"></i>System Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Statistics -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-chart-bar me-2"></i>System Statistics</h4>
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3>0</h3>
                                    <p class="text-muted">Total Users</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3>0</h3>
                                    <p class="text-muted">Active Clinics</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3>0</h3>
                                    <p class="text-muted">Today's Appointments</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3>0</h3>
                                    <p class="text-muted">Total Records</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 