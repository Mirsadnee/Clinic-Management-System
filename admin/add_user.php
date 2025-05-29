<?php
require_once '../includes/User.php';
require_once '../includes/Database.php';

$db = new Database();
$user = new User();

// Check if user is logged in and is an admin
if (!$user->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    $clinic_id = $_POST['clinic_id'] ?? null;

    // Validate input
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($password)) $errors[] = "Password is required";
    if (empty($role)) $errors[] = "Role is required";
    if ($role === 'doctor' && empty($clinic_id)) $errors[] = "Clinic is required for doctors";

    if (empty($errors)) {
        $result = $user->register($name, $email, $password, $role, $clinic_id);
        if ($result) {
            $success = "User created successfully!";
        } else {
            $error = "Error creating user. Email might already exist.";
        }
    }
}

// Get all clinics for doctor selection
$clinics = $user->getAllClinics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Clinic Management</title>
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
                        <a class="nav-link active" href="users.php">Users</a>
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
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="card-title">Add New User</h2>
                            <p class="text-muted">Create a new user account</p>
                        </div>
                        <a href="users.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    <option value="doctor" <?php echo (isset($_POST['role']) && $_POST['role'] === 'doctor') ? 'selected' : ''; ?>>Doctor</option>
                                    <option value="patient" <?php echo (isset($_POST['role']) && $_POST['role'] === 'patient') ? 'selected' : ''; ?>>Patient</option>
                                </select>
                            </div>

                            <div class="mb-3" id="clinicSelect" style="display: none;">
                                <label for="clinic_id" class="form-label">Clinic</label>
                                <select class="form-select" id="clinic_id" name="clinic_id">
                                    <option value="">Select Clinic</option>
                                    <?php foreach ($clinics as $clinic): ?>
                                        <option value="<?php echo $clinic['id']; ?>" 
                                                <?php echo (isset($_POST['clinic_id']) && $_POST['clinic_id'] == $clinic['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($clinic['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Create User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide clinic selection based on role
        document.getElementById('role').addEventListener('change', function() {
            const clinicSelect = document.getElementById('clinicSelect');
            const clinicInput = document.getElementById('clinic_id');
            
            if (this.value === 'doctor') {
                clinicSelect.style.display = 'block';
                clinicInput.required = true;
            } else {
                clinicSelect.style.display = 'none';
                clinicInput.required = false;
            }
        });

        // Trigger change event on page load
        document.getElementById('role').dispatchEvent(new Event('change'));
    </script>
</body>
</html> 