<?php
/**
 * Direct Certificate Types Update Script
 * This script can be run directly to update certificate types
 * Run this from the browser: http://localhost/byumba/update_certificates_direct.php
 */

// Include database configuration
require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Certificate Types Update</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; }
        .warning { color: orange; background: #fff3cd; padding: 10px; border-radius: 5px; }
        .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>";

echo "<h1>Diocese of Byumba - Certificate Types Update</h1>";

try {
    // Check if database connection exists
    if (!$db) {
        throw new Exception("Database connection failed. Please check your database configuration.");
    }
    
    echo "<div class='info'>Starting certificate types update process...</div><br>";
    
    // Start transaction
    $db->beginTransaction();
    
    echo "<div class='info'>Step 1: Clearing existing certificate types and translations...</div>";
    
    // First, empty the tables
    $db->exec("DELETE FROM certificate_type_translations");
    echo "<div class='success'>✓ Cleared certificate_type_translations table</div>";
    
    $db->exec("DELETE FROM certificate_types");
    echo "<div class='success'>✓ Cleared certificate_types table</div>";
    
    // Reset auto increment
    $db->exec("ALTER TABLE certificate_types AUTO_INCREMENT = 1");
    $db->exec("ALTER TABLE certificate_type_translations AUTO_INCREMENT = 1");
    echo "<div class='success'>✓ Reset auto increment counters</div><br>";
    
    echo "<div class='info'>Step 2: Adding new certificate types...</div>";
    
    // Define the new certificate types
    $certificate_types = [
        [
            'type_key' => 'abasheshakanguhe',
            'fee' => 2000.00,
            'processing_days' => 7,
            'icon' => 'fa-certificate',
            'translations' => [
                'en' => [
                    'name' => 'Abasheshakanguhe',
                    'description' => 'Certificate for Abasheshakanguhe members',
                    'required_documents' => '["National ID Copy", "Membership Proof", "Passport Photo"]'
                ],
                'rw' => [
                    'name' => 'Abasheshakanguhe',
                    'description' => 'Icyemezo cy\'abanyamuryango ba Abasheshakanguhe',
                    'required_documents' => '["Kopi y\'indangamuntu", "Icyemezo cy\'ubwiyunge", "Ifoto y\'pasiporo"]'
                ],
                'fr' => [
                    'name' => 'Abasheshakanguhe',
                    'description' => 'Certificat pour les membres Abasheshakanguhe',
                    'required_documents' => '["Copie de la carte d\'identité", "Preuve d\'adhésion", "Photo de passeport"]'
                ]
            ]
        ],
        [
            'type_key' => 'ebenezer',
            'fee' => 2000.00,
            'processing_days' => 7,
            'icon' => 'fa-star',
            'translations' => [
                'en' => [
                    'name' => 'Ebenezer',
                    'description' => 'Certificate for Ebenezer group members',
                    'required_documents' => '["National ID Copy", "Group Membership Proof", "Passport Photo"]'
                ],
                'rw' => [
                    'name' => 'Ebenezer',
                    'description' => 'Icyemezo cy\'abanyamuryango ba Ebenezer',
                    'required_documents' => '["Kopi y\'indangamuntu", "Icyemezo cy\'ubwiyunge mu itsinda", "Ifoto y\'pasiporo"]'
                ],
                'fr' => [
                    'name' => 'Ebenezer',
                    'description' => 'Certificat pour les membres du groupe Ebenezer',
                    'required_documents' => '["Copie de la carte d\'identité", "Preuve d\'adhésion au groupe", "Photo de passeport"]'
                ]
            ]
        ],
        [
            'type_key' => 'fathers_union',
            'fee' => 2500.00,
            'processing_days' => 5,
            'icon' => 'fa-users',
            'translations' => [
                'en' => [
                    'name' => 'Father\'s Union',
                    'description' => 'Certificate for Father\'s Union members',
                    'required_documents' => '["National ID Copy", "Marriage Certificate", "Passport Photo"]'
                ],
                'rw' => [
                    'name' => 'Umuryango w\'Ababyeyi',
                    'description' => 'Icyemezo cy\'abanyamuryango b\'umuryango w\'ababyeyi',
                    'required_documents' => '["Kopi y\'indangamuntu", "Icyemezo cy\'ubukwe", "Ifoto y\'pasiporo"]'
                ],
                'fr' => [
                    'name' => 'Union des Pères',
                    'description' => 'Certificat pour les membres de l\'Union des Pères',
                    'required_documents' => '["Copie de la carte d\'identité", "Certificat de mariage", "Photo de passeport"]'
                ]
            ]
        ],
        [
            'type_key' => 'icyemezo_gusura_korare',
            'fee' => 1500.00,
            'processing_days' => 3,
            'icon' => 'fa-home',
            'translations' => [
                'en' => [
                    'name' => 'Icyemezo cyo gusura kwa korare',
                    'description' => 'Certificate for visiting korare',
                    'required_documents' => '["National ID Copy", "Request Letter", "Passport Photo"]'
                ],
                'rw' => [
                    'name' => 'Icyemezo cyo gusura kwa korare',
                    'description' => 'Icyemezo cyo gusura kwa korare',
                    'required_documents' => '["Kopi y\'indangamuntu", "Ibaruwa y\'ubusabe", "Ifoto y\'pasiporo"]'
                ],
                'fr' => [
                    'name' => 'Certificat de visite korare',
                    'description' => 'Certificat pour visiter korare',
                    'required_documents' => '["Copie de la carte d\'identité", "Lettre de demande", "Photo de passeport"]'
                ]
            ]
        ],
        [
            'type_key' => 'icyemezo_gfs',
            'fee' => 2000.00,
            'processing_days' => 5,
            'icon' => 'fa-female',
            'translations' => [
                'en' => [
                    'name' => 'Icyemezo cyuko winjiye mumuryango wa GFS',
                    'description' => 'Certificate for joining GFS organization',
                    'required_documents' => '["National ID Copy", "Application Form", "Passport Photo"]'
                ],
                'rw' => [
                    'name' => 'Icyemezo cyuko winjiye mumuryango wa GFS',
                    'description' => 'Icyemezo cyuko winjiye mumuryango wa GFS',
                    'required_documents' => '["Kopi y\'indangamuntu", "Ifishi y\'ubusabe", "Ifoto y\'pasiporo"]'
                ],
                'fr' => [
                    'name' => 'Certificat d\'adhésion à l\'organisation GFS',
                    'description' => 'Certificat pour rejoindre l\'organisation GFS',
                    'required_documents' => '["Copie de la carte d\'identité", "Formulaire de demande", "Photo de passeport"]'
                ]
            ]
        ],
        [
            'type_key' => 'icyemezo_umukirisitu',
            'fee' => 1500.00,
            'processing_days' => 3,
            'icon' => 'fa-cross',
            'translations' => [
                'en' => [
                    'name' => 'Icyemezo cyumukirisitu',
                    'description' => 'Christian certificate',
                    'required_documents' => '["National ID Copy", "Baptism Certificate", "Passport Photo"]'
                ],
                'rw' => [
                    'name' => 'Icyemezo cyumukirisitu',
                    'description' => 'Icyemezo cyumukirisitu',
                    'required_documents' => '["Kopi y\'indangamuntu", "Icyemezo cy\'ubwiyunge", "Ifoto y\'pasiporo"]'
                ],
                'fr' => [
                    'name' => 'Certificat de chrétien',
                    'description' => 'Certificat chrétien',
                    'required_documents' => '["Copie de la carte d\'identité", "Certificat de baptême", "Photo de passeport"]'
                ]
            ]
        ],
        [
            'type_key' => 'marriage',
            'fee' => 5000.00,
            'processing_days' => 7,
            'icon' => 'fa-ring',
            'translations' => [
                'en' => [
                    'name' => 'Marriage',
                    'description' => 'Marriage certificate',
                    'required_documents' => '["National ID Copy", "Birth Certificate", "Passport Photo", "Medical Certificate"]'
                ],
                'rw' => [
                    'name' => 'Ubukwe',
                    'description' => 'Icyemezo cy\'ubukwe',
                    'required_documents' => '["Kopi y\'indangamuntu", "Icyemezo cy\'amavuko", "Ifoto y\'pasiporo", "Icyemezo cy\'ubuzima"]'
                ],
                'fr' => [
                    'name' => 'Mariage',
                    'description' => 'Certificat de mariage',
                    'required_documents' => '["Copie de la carte d\'identité", "Certificat de naissance", "Photo de passeport", "Certificat médical"]'
                ]
            ]
        ],
        [
            'type_key' => 'mothers_union',
            'fee' => 2500.00,
            'processing_days' => 5,
            'icon' => 'fa-heart',
            'translations' => [
                'en' => [
                    'name' => 'Mother\'s Union',
                    'description' => 'Certificate for Mother\'s Union members',
                    'required_documents' => '["National ID Copy", "Marriage Certificate", "Passport Photo"]'
                ],
                'rw' => [
                    'name' => 'Umuryango w\'Ababyeyi b\'abagore',
                    'description' => 'Icyemezo cy\'abanyamuryango b\'umuryango w\'ababyeyi b\'abagore',
                    'required_documents' => '["Kopi y\'indangamuntu", "Icyemezo cy\'ubukwe", "Ifoto y\'pasiporo"]'
                ],
                'fr' => [
                    'name' => 'Union des Mères',
                    'description' => 'Certificat pour les membres de l\'Union des Mères',
                    'required_documents' => '["Copie de la carte d\'identité", "Certificat de mariage", "Photo de passeport"]'
                ]
            ]
        ],
        [
            'type_key' => 'youth_union',
            'fee' => 1500.00,
            'processing_days' => 3,
            'icon' => 'fa-graduation-cap',
            'translations' => [
                'en' => [
                    'name' => 'Youth Union',
                    'description' => 'Certificate for Youth Union members',
                    'required_documents' => '["National ID Copy", "School Certificate", "Passport Photo"]'
                ],
                'rw' => [
                    'name' => 'Umuryango w\'Urubyiruko',
                    'description' => 'Icyemezo cy\'abanyamuryango b\'umuryango w\'urubyiruko',
                    'required_documents' => '["Kopi y\'indangamuntu", "Icyemezo cy\'ishuri", "Ifoto y\'pasiporo"]'
                ],
                'fr' => [
                    'name' => 'Union de la Jeunesse',
                    'description' => 'Certificat pour les membres de l\'Union de la Jeunesse',
                    'required_documents' => '["Copie de la carte d\'identité", "Certificat scolaire", "Photo de passeport"]'
                ]
            ]
        ]
    ];
    
    // Insert new certificate types
    $count = 0;
    foreach ($certificate_types as $cert_type) {
        // Insert certificate type
        $query = "INSERT INTO certificate_types (type_key, fee, processing_days, icon, is_active, created_at) 
                 VALUES (:type_key, :fee, :processing_days, :icon, 1, NOW())";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':type_key', $cert_type['type_key']);
        $stmt->bindParam(':fee', $cert_type['fee']);
        $stmt->bindParam(':processing_days', $cert_type['processing_days']);
        $stmt->bindParam(':icon', $cert_type['icon']);
        $stmt->execute();
        
        $certificate_type_id = $db->lastInsertId();
        
        // Insert translations
        foreach ($cert_type['translations'] as $lang_code => $translation) {
            $trans_query = "INSERT INTO certificate_type_translations 
                           (certificate_type_id, language_code, name, description, required_documents) 
                           VALUES (:cert_id, :lang_code, :name, :description, :required_docs)";
            $trans_stmt = $db->prepare($trans_query);
            $trans_stmt->bindParam(':cert_id', $certificate_type_id);
            $trans_stmt->bindParam(':lang_code', $lang_code);
            $trans_stmt->bindParam(':name', $translation['name']);
            $trans_stmt->bindParam(':description', $translation['description']);
            $trans_stmt->bindParam(':required_docs', $translation['required_documents']);
            $trans_stmt->execute();
        }
        
        $count++;
        echo "<div class='success'>✓ Added certificate type: " . htmlspecialchars($cert_type['translations']['en']['name']) . "</div>";
    }
    
    // Commit transaction
    $db->commit();
    
    echo "<br><div class='success'><strong>SUCCESS!</strong> Certificate types have been successfully updated!</div>";
    echo "<div class='info'>Total certificate types added: $count</div>";
    echo "<div class='info'>Each certificate type includes translations in English, Kinyarwanda, and French.</div>";
    
    // Verify the data
    echo "<br><div class='info'>Step 3: Verifying the update...</div>";
    
    $verify_query = "SELECT COUNT(*) as total_types FROM certificate_types";
    $verify_stmt = $db->prepare($verify_query);
    $verify_stmt->execute();
    $total_types = $verify_stmt->fetch()['total_types'];
    
    $verify_trans_query = "SELECT COUNT(*) as total_translations FROM certificate_type_translations";
    $verify_trans_stmt = $db->prepare($verify_trans_query);
    $verify_trans_stmt->execute();
    $total_translations = $verify_trans_stmt->fetch()['total_translations'];
    
    echo "<div class='success'>✓ Total certificate types in database: $total_types</div>";
    echo "<div class='success'>✓ Total translations in database: $total_translations</div>";
    
    echo "<br><div class='info'><strong>Update completed successfully!</strong></div>";
    echo "<div class='info'>You can now access the admin panel to manage these certificate types.</div>";
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($db) {
        $db->rollback();
    }
    echo "<div class='error'><strong>ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='warning'>The update was rolled back. No changes were made to the database.</div>";
}

echo "<br><div style='margin-top: 20px;'>
    <a href='admin/' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Panel</a>
    <a href='index.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Go to Main Site</a>
</div>";

echo "</body></html>";
?>
