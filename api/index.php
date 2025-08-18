<?php
/**
 * Main API Router for Diocese of Byumba System
 */

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include database configuration
require_once '../config/database.php';

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Debug logging (remove in production)
// error_log("API Debug - Original path: " . $path);

// Remove the base path - handle different possible paths
$base_paths = ['/new/byumba/api', '/byumba/api', '/api'];
foreach ($base_paths as $base_path) {
    if (strpos($path, $base_path) !== false) {
        $path = str_replace($base_path, '', $path);
        // error_log("API Debug - Matched base path: " . $base_path . ", remaining path: " . $path);
        break;
    }
}

$path_parts = explode('/', trim($path, '/'));

// Get the endpoint - check query parameter first, then path
$endpoint = $_GET['endpoint'] ?? ($path_parts[0] ?? '');

// Debug logging
// error_log("API Debug - Endpoint: " . $endpoint . ", Method: " . $method . ", Query endpoint: " . ($_GET['endpoint'] ?? 'none'));

// Handle language switching
if ($endpoint === 'language' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $language_code = $input['language'] ?? '';
    
    if ($languageHelper->setLanguage($language_code)) {
        ResponseHelper::success(['language' => $language_code], 'Language updated successfully');
    } else {
        ResponseHelper::error('Invalid language code', 400);
    }
}

// Get current language
if ($endpoint === 'language' && $method === 'GET') {
    $languages = $languageHelper->getAvailableLanguages();
    $current = $languageHelper->getLanguage();
    
    ResponseHelper::success([
        'current' => $current,
        'available' => $languages
    ]);
}

// Route to specific API handlers
switch ($endpoint) {
    case 'dashboard':
        require_once 'dashboard.php';
        break;
        
    case 'applications':
        require_once 'applications.php';
        break;
        
    case 'meetings':
        require_once 'meetings.php';
        break;
        
    case 'notifications':
        require_once 'notifications.php';
        break;
        
    case 'profile':
        require_once 'profile.php';
        break;
        
    case 'certificates':
        require_once 'certificates.php';
        break;
        
    case 'jobs':
        require_once 'jobs.php';
        break;
        
    case 'blog':
        require_once 'blog.php';
        break;
        
    case 'auth':
        require_once 'auth.php';
        break;
        
    default:
        ResponseHelper::error('Endpoint not found: ' . $endpoint, 404);
}
?>
