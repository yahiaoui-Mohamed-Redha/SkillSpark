<?php
session_start();
require_once 'database.php';

class Auth {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Register new user
    public function register($data) {
        try {
            // Check if email already exists
            $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $data['email']);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Check if username already exists
            $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $data['username']);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Username already exists'];
            }

            // Hash password
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

            // Insert user
            $query = "INSERT INTO " . $this->table_name . " 
                     (first_name, last_name, email, phone, country, username, password, role, bio, specialization, newsletter) 
                     VALUES (:first_name, :last_name, :email, :phone, :country, :username, :password, :role, :bio, :specialization, :newsletter)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':first_name', $data['firstName']);
            $stmt->bindParam(':last_name', $data['lastName']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':country', $data['country']);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':bio', $data['bio']);
            $stmt->bindParam(':specialization', $data['specialization']);
            $stmt->bindParam(':newsletter', $data['newsletter']);

            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Registration successful'];
            } else {
                return ['success' => false, 'message' => 'Registration failed'];
            }

        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Login user
    public function login($email, $password) {
        try {
            $query = "SELECT id, first_name, last_name, email, username, password, role, profile_image FROM " . $this->table_name . " WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                
                if(password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['profile_image'] = $user['profile_image'];
                    $_SESSION['logged_in'] = true;

                    return ['success' => true, 'message' => 'Login successful', 'user' => $user];
                } else {
                    return ['success' => false, 'message' => 'Invalid password'];
                }
            } else {
                return ['success' => false, 'message' => 'User not found'];
            }

        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // Logout user
    public function logout() {
        session_destroy();
        return true;
    }

    // Get current user data
    public function getCurrentUser() {
        if($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'first_name' => $_SESSION['first_name'],
                'last_name' => $_SESSION['last_name'],
                'email' => $_SESSION['email'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'profile_image' => $_SESSION['profile_image']
            ];
        }
        return null;
    }

    // Redirect based on user role
    public function redirectByRole() {
        if($this->isLoggedIn()) {
            $role = $_SESSION['role'];
            if($role === 'student') {
                header('Location: student-dashboard.php');
            } elseif($role === 'business') {
                header('Location: instructor-dashboard.php');
            } elseif($role === 'admin') {
                header('Location: admin-dashboard.php');
            }
            exit();
        }
    }
}
?>
