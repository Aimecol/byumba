<?php
/**
 * Setup Blog Translations
 * This script creates the blog_post_translations table and adds initial translation data
 */

require_once 'config/database.php';

try {
    echo "Setting up blog translations...\n";
    
    // Create blog_post_translations table
    $create_table_sql = "
    CREATE TABLE IF NOT EXISTS `blog_post_translations` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `blog_post_id` int(11) NOT NULL,
      `language_code` varchar(5) NOT NULL,
      `title` varchar(255) NOT NULL,
      `excerpt` text DEFAULT NULL,
      `content` longtext DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `unique_translation` (`blog_post_id`,`language_code`),
      KEY `language_code` (`language_code`),
      CONSTRAINT `blog_post_translations_ibfk_1` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
      CONSTRAINT `blog_post_translations_ibfk_2` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($create_table_sql);
    echo "✓ Created blog_post_translations table\n";
    
    // Check if translations already exist
    $check_sql = "SELECT COUNT(*) as count FROM blog_post_translations";
    $stmt = $db->prepare($check_sql);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        echo "✓ Translations already exist (" . $result['count'] . " records)\n";
        echo "Blog translations setup completed!\n";
        exit;
    }
    
    // Insert English translations (original content)
    $english_translations = [
        [
            'blog_post_id' => 1,
            'title' => 'New Online Certificate Application System Launched',
            'excerpt' => 'The Diocese of Byumba is pleased to announce the launch of our new online certificate application system, making it easier for parishioners to request important documents.',
            'content' => 'The Diocese of Byumba is pleased to announce the launch of our new online certificate application system. This digital platform will streamline the process of requesting baptism, confirmation, marriage, and other important certificates.\n\nKey features include:\n- Online application submission\n- Document upload capability\n- Real-time status tracking\n- Secure payment processing\n- Multi-language support\n\nParishioners can now apply for certificates from the comfort of their homes and track the progress of their applications online. This initiative is part of our ongoing efforts to modernize our services and better serve our community.\n\nTo access the system, visit our website and create an account. For assistance, please contact our office during business hours.'
        ],
        [
            'blog_post_id' => 2,
            'title' => 'Annual Diocese Youth Retreat 2024',
            'excerpt' => 'Join us for the Annual Diocese Youth Retreat from February 15-17, 2024, at Lake Ruhondo. A weekend of spiritual growth, fellowship, and fun activities for young Catholics.',
            'content' => 'The Diocese of Byumba invites all young Catholics aged 16-30 to participate in our Annual Youth Retreat from February 15-17, 2024, at the beautiful Lake Ruhondo retreat center.\n\nThis year\'s theme is "Called to Serve" and will feature:\n- Inspiring talks by guest speakers\n- Small group discussions\n- Adoration and Mass\n- Recreational activities\n- Cultural performances\n- Networking opportunities\n\nThe retreat aims to strengthen faith, build community, and inspire young people to take active roles in their parishes and communities.\n\nRegistration fee: RWF 25,000 (includes accommodation, meals, and materials)\nRegistration deadline: February 5, 2024\n\nTo register, contact your parish youth coordinator or visit our office. Limited spaces available - register early!'
        ],
        [
            'blog_post_id' => 3,
            'title' => 'Lenten Season Preparation and Activities',
            'excerpt' => 'As we approach the holy season of Lent, the Diocese of Byumba announces special programs and activities to help the faithful prepare for Easter.',
            'content' => 'The season of Lent is a time of prayer, fasting, and almsgiving as we prepare our hearts for the celebration of Easter. The Diocese of Byumba has prepared special programs to accompany the faithful during this holy season.\n\nLenten Activities:\n- Weekly Stations of the Cross (Fridays at 6:00 PM)\n- Lenten retreat for adults (March 2-3)\n- Children\'s Lenten program\n- Special confession schedules\n- Charity drives for the needy\n\nEach parish will also organize additional activities according to local needs. We encourage all parishioners to participate actively in these spiritual exercises.\n\nLet us use this Lenten season to grow closer to God through prayer, sacrifice, and service to others. May this be a time of spiritual renewal and preparation for the joy of Easter.'
        ],
        [
            'blog_post_id' => 4,
            'title' => 'Community Health Initiative Launch',
            'excerpt' => 'The Diocese of Byumba launches a new community health initiative in partnership with local health centers to improve healthcare access in rural areas.',
            'content' => 'The Diocese of Byumba is proud to announce the launch of our Community Health Initiative, a comprehensive program designed to improve healthcare access and health education in rural communities within our diocese.\n\nProgram Components:\n- Mobile health clinics visiting remote areas\n- Health education workshops\n- Maternal and child health programs\n- Nutrition education\n- Disease prevention campaigns\n- Mental health awareness\n\nThis initiative is implemented in partnership with local health centers, government agencies, and international health organizations. Our goal is to ensure that all members of our community have access to quality healthcare services.\n\nVolunteers are needed for various aspects of the program. If you have medical training or simply want to help your community, please contact our social services coordinator.\n\nTogether, we can build healthier communities and demonstrate God\'s love through caring for the sick and vulnerable.'
        ],
        [
            'blog_post_id' => 5,
            'title' => 'Adult Faith Formation Program Registration Open',
            'excerpt' => 'Registration is now open for the 2024 Adult Faith Formation Program. Deepen your understanding of Catholic teaching and grow in your relationship with God.',
            'content' => 'The Diocese of Byumba invites all adults to participate in our comprehensive Faith Formation Program starting February 1, 2024. This program is designed for Catholics who want to deepen their understanding of the faith and grow in their spiritual journey.\n\nProgram Features:\n- Scripture study sessions\n- Catholic doctrine classes\n- Liturgy and sacraments education\n- Prayer and spirituality workshops\n- Social justice teachings\n- Small group discussions\n\nClasses will be held every Thursday evening from 7:00-8:30 PM at the Diocese center. The program runs for 12 weeks and includes take-home materials for further study.\n\nWhether you\'re a lifelong Catholic or someone returning to the faith, this program offers something for everyone. Our experienced catechists will guide you through engaging discussions and practical applications of Catholic teaching.\n\nRegistration fee: RWF 15,000 (includes all materials)\nTo register, contact your parish office or call the Diocese at +250 788 123 456.'
        ]
    ];
    
    // Insert English translations
    $insert_sql = "INSERT INTO blog_post_translations (blog_post_id, language_code, title, excerpt, content) VALUES (?, 'en', ?, ?, ?)";
    $stmt = $db->prepare($insert_sql);
    
    foreach ($english_translations as $translation) {
        $stmt->execute([
            $translation['blog_post_id'],
            $translation['title'],
            $translation['excerpt'],
            $translation['content']
        ]);
    }
    
    echo "✓ Inserted English translations\n";
    
    echo "Blog translations setup completed!\n";
    echo "Note: French and Kinyarwanda translations need to be added separately.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
