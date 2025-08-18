<?php
/**
 * Update Certificate Types Script
 * This script empties the certificate_types and certificate_type_translations tables
 * and adds the new certificate types with their translations
 */

// Include database configuration
require_once '../config/database.php';
require_once 'auth.php';

// Check if user is logged in and has permission
$auth = new AdminAuth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('manage_applications')) {
    header('Location: login.php');
    exit;
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_certificates'])) {
    try {
        // Start transaction
        $db->beginTransaction();

        // Check if there are existing applications
        $check_apps_query = "SELECT COUNT(*) as app_count FROM applications";
        $check_apps_stmt = $db->prepare($check_apps_query);
        $check_apps_stmt->execute();
        $app_count = $check_apps_stmt->fetch()['app_count'];

        if ($app_count > 0) {
            // Temporarily disable foreign key checks
            $db->exec("SET FOREIGN_KEY_CHECKS = 0");
        }

        // First, empty the tables
        $db->exec("DELETE FROM certificate_type_translations");
        $db->exec("DELETE FROM certificate_types");

        // Reset auto increment
        $db->exec("ALTER TABLE certificate_types AUTO_INCREMENT = 1");
        $db->exec("ALTER TABLE certificate_type_translations AUTO_INCREMENT = 1");
        
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
        }

        // Re-enable foreign key checks if they were disabled
        if ($app_count > 0) {
            $db->exec("SET FOREIGN_KEY_CHECKS = 1");

            // Update existing applications to use the new 'marriage' certificate type (ID 7) as default
            $update_apps_query = "UPDATE applications SET certificate_type_id = 7 WHERE certificate_type_id NOT IN (SELECT id FROM certificate_types)";
            $db->exec($update_apps_query);
        }

        // Commit transaction
        $db->commit();
        
        $success_message = "Certificate types have been successfully updated! " . count($certificate_types) . " certificate types added with translations.";
        
        // Log admin activity
        $admin_id = $_SESSION['admin_id'];
        $activity_query = "INSERT INTO admin_activity_log (admin_id, action, details, ip_address, created_at) 
                          VALUES (:admin_id, 'certificate_types_updated', :details, :ip, NOW())";
        $activity_stmt = $db->prepare($activity_query);
        $activity_stmt->bindParam(':admin_id', $admin_id);
        $details = "Updated certificate types: " . implode(', ', array_column($certificate_types, 'type_key'));
        $activity_stmt->bindParam(':details', $details);
        $activity_stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $activity_stmt->execute();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        try {
            $db->exec("SET FOREIGN_KEY_CHECKS = 1");
        } catch (Exception $fk_error) {
            // Ignore foreign key re-enable errors during rollback
        }
        $db->rollback();
        $error_message = "Error updating certificate types: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Update Certificate Types</h3>
                </div>
                <div class="card-body">
                    <?php if ($success_message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($success_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action will permanently delete all existing certificate types and their translations, 
                        then add the new certificate types listed below. This action cannot be undone.
                    </div>
                    
                    <h5>New Certificate Types to be Added:</h5>
                    <ul class="list-group mb-4">
                        <li class="list-group-item">Abasheshakanguhe</li>
                        <li class="list-group-item">Ebenezer</li>
                        <li class="list-group-item">Father's Union</li>
                        <li class="list-group-item">Icyemezo cyo gusura kwa korare</li>
                        <li class="list-group-item">Icyemezo cyuko winjiye mumuryango wa GFS</li>
                        <li class="list-group-item">Icyemezo cyumukirisitu</li>
                        <li class="list-group-item">Marriage</li>
                        <li class="list-group-item">Mother's Union</li>
                        <li class="list-group-item">Youth Union</li>
                    </ul>
                    
                    <form method="POST" onsubmit="return confirm('Are you sure you want to update the certificate types? This will delete all existing data.');">
                        <button type="submit" name="update_certificates" class="btn btn-danger">
                            <i class="fas fa-sync me-2"></i>Update Certificate Types
                        </button>
                        <a href="applications.php" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Applications
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
