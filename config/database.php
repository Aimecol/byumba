<?php
/**
 * Database Configuration for Diocese of Byumba System
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'diocese_byumba';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 30,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'",
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            // Don't echo the error, just log it and return null
            $this->conn = null;
        }

        return $this->conn;
    }

    /**
     * Check if connection is alive and reconnect if needed
     */
    public function reconnectIfNeeded() {
        try {
            // Test the connection with a simple query
            if ($this->conn) {
                $this->conn->query('SELECT 1');
            } else {
                $this->getConnection();
            }
        } catch(PDOException $e) {
            // Connection is dead, reconnect
            $this->getConnection();
        }

        return $this->conn;
    }

    /**
     * Execute query with automatic reconnection on failure
     */
    public function executeWithRetry($query, $params = []) {
        $maxRetries = 2;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                $stmt = $this->conn->prepare($query);
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
                $stmt->execute();
                return $stmt;
            } catch(PDOException $e) {
                if (($e->getCode() == 2006 || strpos($e->getMessage(), 'server has gone away') !== false) && $retryCount < $maxRetries - 1) {
                    $this->reconnectIfNeeded();
                    $retryCount++;
                } else {
                    throw $e;
                }
            }
        }

        return false;
    }
}

/**
 * Language Helper Class
 */
class LanguageHelper {
    private $db;
    private $current_language;
    
    public function __construct($database) {
        $this->db = $database;
        $this->current_language = $this->getCurrentLanguage();
    }
    
    public function getCurrentLanguage() {
        // Check session first
        if (isset($_SESSION['language'])) {
            return $_SESSION['language'];
        }
        
        // Check cookie
        if (isset($_COOKIE['language'])) {
            $_SESSION['language'] = $_COOKIE['language'];
            return $_COOKIE['language'];
        }
        
        // Check browser language
        $browser_lang = $this->getBrowserLanguage();
        if ($browser_lang) {
            $_SESSION['language'] = $browser_lang;
            return $browser_lang;
        }
        
        // Default to English
        $_SESSION['language'] = 'en';
        return 'en';
    }
    
    public function setLanguage($language_code) {
        // Validate language code
        if ($this->isValidLanguage($language_code)) {
            $_SESSION['language'] = $language_code;
            setcookie('language', $language_code, time() + (86400 * 30), '/'); // 30 days
            $this->current_language = $language_code;
            return true;
        }
        return false;
    }
    
    public function getLanguage() {
        return $this->current_language;
    }
    
    private function getBrowserLanguage() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($browser_languages as $lang) {
                $lang_code = substr(trim($lang), 0, 2);
                if ($this->isValidLanguage($lang_code)) {
                    return $lang_code;
                }
            }
        }
        return null;
    }
    
    private function isValidLanguage($language_code) {
        $valid_languages = ['en', 'rw', 'fr'];
        return in_array($language_code, $valid_languages);
    }
    
    public function getAvailableLanguages() {
        try {
            if ($this->db === null) {
                // Return default languages if no database connection
                return [
                    ['code' => 'en', 'name' => 'English', 'native_name' => 'English'],
                    ['code' => 'rw', 'name' => 'Kinyarwanda', 'native_name' => 'Ikinyarwanda'],
                    ['code' => 'fr', 'name' => 'French', 'native_name' => 'Français']
                ];
            }

            $query = "SELECT code, name, native_name FROM languages WHERE is_active = 1 ORDER BY name";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Language loading error: " . $e->getMessage());
            return [
                ['code' => 'en', 'name' => 'English', 'native_name' => 'English'],
                ['code' => 'rw', 'name' => 'Kinyarwanda', 'native_name' => 'Ikinyarwanda'],
                ['code' => 'fr', 'name' => 'French', 'native_name' => 'Français']
            ];
        }
    }
}

/**
 * Translation Helper Class
 */
class TranslationHelper {
    private $db;
    private $language;
    private $translations = [];
    
    public function __construct($database, $language_code) {
        $this->db = $database;
        $this->language = $language_code;
        if ($this->db !== null) {
            $this->loadTranslations();
        }
    }

    private function loadTranslations() {
        // Load system setting translations
        try {
            if ($this->db === null) {
                return; // Skip if no database connection
            }

            $query = "SELECT setting_key, setting_value FROM system_setting_translations WHERE language_code = :lang";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':lang', $this->language);
            $stmt->execute();

            while ($row = $stmt->fetch()) {
                $this->translations['settings'][$row['setting_key']] = $row['setting_value'];
            }
        } catch(PDOException $e) {
            // Handle error silently
            error_log("Translation loading error: " . $e->getMessage());
        }
    }
    
    public function translate($key, $default = '') {
        $keys = explode('.', $key);
        $value = $this->translations;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default ?: $key;
            }
        }
        
        return $value ?: $default ?: $key;
    }
    
    public function getSiteName() {
        return $this->translate('settings.site_name', 'Diocese of Byumba');
    }
    
    public function getSiteDescription() {
        return $this->translate('settings.site_description', 'Official website of the Diocese of Byumba');
    }
}

/**
 * Response Helper Class
 */
class ResponseHelper {
    public static function json($data, $status_code = 200) {
        http_response_code($status_code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    public static function success($data = null, $message = 'Success') {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }
    
    public static function error($message = 'Error', $status_code = 400, $data = null) {
        self::json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }
}

/**
 * Utility Functions
 */
function generateUniqueNumber($prefix, $length = 6) {
    $number = $prefix . str_pad(mt_rand(1, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    return $number;
}

function formatDate($date, $format = 'Y-m-d') {
    if ($date instanceof DateTime) {
        return $date->format($format);
    }
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = 'Y-m-d H:i:s') {
    if ($datetime instanceof DateTime) {
        return $datetime->format($format);
    }
    return date($format, strtotime($datetime));
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    // Rwanda phone number validation
    $pattern = '/^(\+250|250)?[0-9]{9}$/';
    return preg_match($pattern, $phone);
}

/**
 * Handle database connection recovery
 */
function handleDatabaseError($e, $retryCallback = null) {
    global $database, $db;

    // Check if it's a connection timeout error
    if ($e->getCode() == 2006 || strpos($e->getMessage(), 'server has gone away') !== false) {
        error_log("Database connection lost, attempting to reconnect: " . $e->getMessage());

        // Attempt to reconnect
        try {
            $db = $database->reconnectIfNeeded();
            $GLOBALS['db'] = $db;

            // If a retry callback is provided, execute it
            if ($retryCallback && is_callable($retryCallback)) {
                return $retryCallback();
            }

            return true;
        } catch(PDOException $reconnect_e) {
            error_log("Failed to reconnect to database: " . $reconnect_e->getMessage());
            return false;
        }
    } else {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone
date_default_timezone_set('Africa/Kigali');

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize language helper (works even without database)
$languageHelper = new LanguageHelper($db);
$current_language = $languageHelper->getLanguage();

// Initialize translation helper (handles null database gracefully)
$translationHelper = new TranslationHelper($db, $current_language);

// Make helpers available globally
$GLOBALS['db'] = $db;
$GLOBALS['database'] = $database;
$GLOBALS['languageHelper'] = $languageHelper;
$GLOBALS['translationHelper'] = $translationHelper;
$GLOBALS['current_language'] = $current_language;

// Check if database connection failed and show appropriate message
if ($db === null) {
    error_log("Database connection failed. Please check if MySQL server is running.");

    // If this is an admin page request, show a user-friendly error
    if (defined('ADMIN_PAGE') && ADMIN_PAGE === true) {
        showDatabaseError();
    }
}

/**
 * Show database connection error page
 */
function showDatabaseError() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Connection Error - Diocese of Byumba</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h4 class="mb-0"><i class="fas fa-database me-2"></i>Database Connection Error</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>Cannot Connect to Database</h5>
                                <p class="mb-0">The system cannot connect to the MySQL database. This usually means the MySQL server is not running.</p>
                            </div>

                            <h6>Troubleshooting Steps:</h6>
                            <ol>
                                <li><strong>Start XAMPP MySQL Service:</strong>
                                    <ul>
                                        <li>Open XAMPP Control Panel</li>
                                        <li>Click "Start" next to MySQL</li>
                                        <li>Wait for the status to turn green</li>
                                    </ul>
                                </li>
                                <li><strong>Check Database Configuration:</strong>
                                    <ul>
                                        <li>Verify database credentials in <code>config/database.php</code></li>
                                        <li>Ensure database name is correct: <code>diocese_byumba</code></li>
                                    </ul>
                                </li>
                                <li><strong>Import Database:</strong>
                                    <ul>
                                        <li>Open phpMyAdmin (http://localhost/phpmyadmin)</li>
                                        <li>Import <code>database/diocese_byumba.sql</code></li>
                                        <li>Import <code>database/admin_activity_log.sql</code></li>
                                    </ul>
                                </li>
                            </ol>

                            <div class="mt-4">
                                <button onclick="location.reload()" class="btn btn-primary">
                                    <i class="fas fa-sync-alt me-2"></i>Try Again
                                </button>
                                <a href="test_connection.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-tools me-2"></i>Test Connection
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>
