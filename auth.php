<?php
require_once 'config.php';
session_start();

class Auth {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function signup($firstName, $lastName, $email, $password) {
        try {
            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return false;
            }

            // Create new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare(
                "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);
            
            // Log the user in after successful signup
            $userId = $this->pdo->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $firstName;
            
            return true;
        } catch (PDOException $e) {
            error_log("Signup error: " . $e->getMessage());
            return false;
        }
    }

    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Check if user is banned
                if ($user['banned']) {
                    return ['success' => false, 'message' => 'Your account has been banned. Please contact support.'];
                }
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'];
                // Admin detection using is_admin column
                if ($user['is_admin']) {
                    $_SESSION['is_admin'] = true;
                } else {
                    unset($_SESSION['is_admin']);
                }
                return ['success' => true];
            }
            return ['success' => false, 'message' => 'Invalid email or password'];
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred during login'];
        }
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }

    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        try {
            $stmt = $this->pdo->prepare("SELECT id, first_name, last_name, email, phone_number, address, city, country, postal_code, is_admin FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get current user error: " . $e->getMessage());
            return null;
        }
    }

    public function updateProfile($userId, $data) {
        try {
            $updates = [];
            $params = [];

            // Handle text fields
            $fields = ['first_name', 'last_name', 'phone_number', 'address', 'city', 'country', 'postal_code'];
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }

            

            if (empty($updates)) {
                return ['success' => false, 'message' => 'No changes to update'];
            }

            $params[] = $userId;
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return ['success' => true];
        } catch (PDOException $e) {
            error_log("Profile update error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
    }

    public function makeAdmin($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET is_admin = 1 WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Make admin error: " . $e->getMessage());
            return false;
        }
    }

    public function removeAdmin($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET is_admin = 0 WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Remove admin error: " . $e->getMessage());
            return false;
        }
    }

    public function banUser($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET banned = 1 WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Ban user error: " . $e->getMessage());
            return false;
        }
    }

    public function unbanUser($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET banned = 0 WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Unban user error: " . $e->getMessage());
            return false;
        }
    }

    public function isBanned($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT banned FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return $result && $result['banned'] == 1;
        } catch (PDOException $e) {
            error_log("Check ban status error: " . $e->getMessage());
            return false;
        }
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $auth = new Auth();
        
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
        } else {
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? '';
        }

        switch ($action) {
            case 'login':
                $email = $input['email'] ?? '';
                $password = $input['password'] ?? '';
                if (empty($email) || empty($password)) {
                    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
                    exit;
                }
                if ($auth->login($email, $password)) {
                    $isAdmin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true);
                    echo json_encode(['success' => true, 'isAdmin' => $isAdmin]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
                }
                break;

            case 'register':
                $firstName = $input['firstName'] ?? '';
                $lastName = $input['lastName'] ?? '';
                $email = $input['email'] ?? '';
                $password = $input['password'] ?? '';
                
                if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
                    echo json_encode(['success' => false, 'message' => 'All fields are required']);
                    exit;
                }
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                    exit;
                }
                
                if (strlen($password) < 8) {
                    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
                    exit;
                }
                
                if ($auth->signup($firstName, $lastName, $email, $password)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Email already exists']);
                }
                break;

            case 'updateProfile':
                if (!isset($_SESSION['user_id'])) {
                    echo json_encode(['success' => false, 'message' => 'Not logged in']);
                    exit;
                }
                
                $result = $auth->updateProfile($_SESSION['user_id'], $_POST);
                echo json_encode($result);
                break;

            case 'logout':
                $auth->logout();
                echo json_encode(['success' => true]);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    } catch (Exception $e) {
        error_log("Auth error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred']);
    }
    exit;
}
?> 