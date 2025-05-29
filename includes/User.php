<?php
require_once 'Database.php';

class User {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register($name, $email, $password, $role = 'patient', $clinic_id = null) {
        try {
            // Check if email already exists
            $stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                return false;
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $this->conn->prepare(
                "INSERT INTO {$this->table} (name, email, password, role, clinic_id, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())"
            );
            
            return $stmt->execute([$name, $email, $hashedPassword, $role, $clinic_id]);
        } catch(PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }

    public function login($email, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE email = ? AND status = 'active'");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch();
                
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    return true;
                }
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function getAllUsers() {
        try {
            $stmt = $this->conn->query("
                SELECT u.*, c.name as clinic_name 
                FROM users u 
                LEFT JOIN clinics c ON u.clinic_id = c.id 
                ORDER BY u.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching users: " . $e->getMessage());
            return [];
        }
    }

    public function getUserById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, c.name as clinic_name 
                FROM users u 
                LEFT JOIN clinics c ON u.clinic_id = c.id 
                WHERE u.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($id, $name, $email, $role, $clinic_id, $password = null) {
        try {
            // Check if email exists for other users
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->rowCount() > 0) {
                return false;
            }

            if ($password) {
                // Update with new password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("
                    UPDATE users 
                    SET name = ?, email = ?, password = ?, role = ?, clinic_id = ? 
                    WHERE id = ?
                ");
                return $stmt->execute([$name, $email, $hashedPassword, $role, $clinic_id, $id]);
            } else {
                // Update without changing password
                $stmt = $this->conn->prepare("
                    UPDATE users 
                    SET name = ?, email = ?, role = ?, clinic_id = ? 
                    WHERE id = ?
                ");
                return $stmt->execute([$name, $email, $role, $clinic_id, $id]);
            }
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUser($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }

    public function toggleUserStatus($id) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE users 
                SET status = CASE 
                    WHEN status = 'active' THEN 'inactive' 
                    ELSE 'active' 
                END 
                WHERE id = ?
            ");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error toggling user status: " . $e->getMessage());
            return false;
        }
    }

    public function getAllClinics() {
        try {
            $stmt = $this->conn->query("SELECT * FROM clinics ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching clinics: " . $e->getMessage());
            return [];
        }
    }
} 