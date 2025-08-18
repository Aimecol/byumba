<?php
/**
 * Certificates API Endpoint
 */

if ($method === 'GET') {
    try {
        // Get all certificate types with translations
        $query = "SELECT ct.*, ctt.name, ctt.description, ctt.required_documents 
                  FROM certificate_types ct 
                  JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id 
                  WHERE ct.is_active = 1 AND ctt.language_code = :language 
                  ORDER BY ctt.name";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':language', $current_language);
        $stmt->execute();
        
        $certificates = [];
        while ($row = $stmt->fetch()) {
            $certificates[] = [
                'id' => $row['id'],
                'type_key' => $row['type_key'],
                'name' => $row['name'],
                'description' => $row['description'],
                'fee' => 'RWF ' . number_format($row['fee']),
                'fee_amount' => $row['fee'],
                'processing_days' => $row['processing_days'],
                'icon' => $row['icon'],
                'required_documents' => json_decode($row['required_documents'], true)
            ];
        }
        
        ResponseHelper::success(['certificates' => $certificates]);
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to load certificates: ' . $e->getMessage(), 500);
    }
}
?>
