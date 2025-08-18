<?php
/**
 * Simple Authentication API
 * Diocese of Byumba System
 */

// Basic error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Database connection
try {
    $db = new PDO(
        'mysql:host=localhost;dbname=diocese_byumba;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$action = $_GET['action'] ?? '';

// Simple response functions
function success($data = null, $message = 'Success') {
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
    exit;
}

function error($message = 'Error', $errors = null) {
    echo json_encode(['success' => false, 'message' => $message, 'errors' => $errors]);
    exit;
}

// Handle actions
if ($action === 'register') {
    handleRegistration($db, $input);
} elseif ($action === 'login') {
    handleLogin($db, $input);
} elseif ($action === 'logout') {
    handleLogout();
} elseif ($action === 'check-session') {
    checkSession();
} else {
    error('Invalid action');
}

function handleRegistration($db, $input) {
    // Basic validation
    $required = ['firstName', 'lastName', 'email', 'phone', 'password', 'confirmPassword', 'parish'];
    $errors = [];

    foreach ($required as $field) {
        if (empty($input[$field])) {
            $errors[$field] = ucfirst($field) . ' is required';
        }
    }

    if (!filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (($input['password'] ?? '') !== ($input['confirmPassword'] ?? '')) {
        $errors['confirmPassword'] = 'Passwords do not match';
    }

    if (strlen($input['password'] ?? '') < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }

    // Check if email exists
    if (!empty($input['email'])) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$input['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Email already registered';
        }
    }

    if (!empty($errors)) {
        error('Validation failed', $errors);
    }

    // Create user
    try {
        $db->beginTransaction();

        $password_hash = password_hash($input['password'], PASSWORD_DEFAULT);

        // Insert user
        $stmt = $db->prepare("
            INSERT INTO users (first_name, last_name, email, phone, password_hash, preferred_language, created_at)
            VALUES (?, ?, ?, ?, ?, 'en', NOW())
        ");

        $stmt->execute([
            trim($input['firstName']),
            trim($input['lastName']),
            strtolower(trim($input['email'])),
            trim($input['phone']),
            $password_hash
        ]);

        $user_id = $db->lastInsertId();

        // Add parish membership
        $parish_map = ['st-mary' => 1, 'st-joseph' => 2, 'st-peter' => 3, 'holy-family' => 4, 'st-paul' => 35];
        $parish_id = $parish_map[$input['parish']] ?? 1;

        $stmt = $db->prepare("
            INSERT INTO user_parish_membership (user_id, parish_id, membership_date, role, is_active, created_at)
            VALUES (?, ?, ?, 'member', 1, NOW())
        ");

        $stmt->execute([$user_id, $parish_id, date('Y-m-d')]);

        $db->commit();

        success([
            'user_id' => $user_id,
            'email' => $input['email'],
            'name' => $input['firstName'] . ' ' . $input['lastName']
        ], 'Registration successful!');

    } catch (PDOException $e) {
        $db->rollback();
        error('Registration failed: ' . $e->getMessage());
    }
}

function handleLogin($db, $input) {
    if (empty($input['email']) || empty($input['password'])) {
        error('Email and password are required');
    }

    try {
        $stmt = $db->prepare("SELECT id, first_name, last_name, email, password_hash FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$input['email']]);
        $user = $stmt->fetch();

        if ($user && password_verify($input['password'], $user['password_hash'])) {
            session_start();
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];

            success([
                'user_id' => $user['id'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email']
            ], 'Login successful');
        } else {
            error('Invalid email or password');
        }

    } catch (PDOException $e) {
        error('Login failed');
    }
}

// Handle logout
function handleLogout() {
    session_start();

    // Clear all session variables
    $_SESSION = array();

    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();

    success(null, 'Logout successful');
}

// Check session status
function checkSession() {
    session_start();

    if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
        success([
            'logged_in' => true,
            'user_id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email']
        ], 'User is logged in');
    } else {
        success([
            'logged_in' => false
        ], 'User is not logged in');
    }
}
?>
