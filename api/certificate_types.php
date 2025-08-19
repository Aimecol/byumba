<?php
/**
 * Certificate Types API Endpoint
 * Provides certificate types with their IDs and details
 */

require_once '../config/database.php';

// Set JSON response header
header('Content-Type: application/json');

// Allow GET requests only
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ResponseHelper::error('Method not allowed', 405);
}

try {
    // Get language parameter (default to English)
    $language = $_GET['lang'] ?? 'en';
    
    // Validate language
    $allowed_languages = ['en', 'rw', 'fr'];
    if (!in_array($language, $allowed_languages)) {
        $language = 'en';
    }
    
    // Query to get certificate types with translations
    $query = "SELECT 
                ct.id,
                ct.type_key,
                ct.fee,
                ct.processing_days,
                ct.icon,
                ct.is_active,
                ctt.name,
                ctt.description,
                ctt.required_documents
              FROM certificate_types ct
              LEFT JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id
              WHERE ct.is_active = 1 AND ctt.language_code = :language
              ORDER BY ct.id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':language', $language);
    $stmt->execute();
    
    $certificate_types = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $required_documents = [];
        if ($row['required_documents']) {
            $required_documents = json_decode($row['required_documents'], true) ?? [];
        }
        
        $certificate_types[] = [
            'id' => (int)$row['id'],
            'type_key' => $row['type_key'],
            'name' => $row['name'],
            'description' => $row['description'],
            'fee' => (float)$row['fee'],
            'processing_days' => (int)$row['processing_days'],
            'icon' => $row['icon'],
            'required_documents' => $required_documents,
            'is_active' => (bool)$row['is_active']
        ];
    }
    
    if (empty($certificate_types)) {
        ResponseHelper::error('No certificate types found', 404);
    }
    
    ResponseHelper::success([
        'certificate_types' => $certificate_types,
        'language' => $language,
        'total' => count($certificate_types)
    ]);
    
} catch (Exception $e) {
    ResponseHelper::error('Failed to fetch certificate types: ' . $e->getMessage(), 500);
}
?>
