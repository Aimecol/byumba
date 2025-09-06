<?php
/**
 * Blog Posts API for Diocese of Byumba Website
 * Handles blog post retrieval with enhanced features for the new blog system
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get the action from the request
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_posts':
            getPosts($db);
            break;
        case 'get_post':
            getPost($db);
            break;
        case 'get_categories':
            getCategories($db);
            break;
        case 'get_related_posts':
            getRelatedPosts($db);
            break;
        case 'increment_views':
            incrementViews($db);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

/**
 * Get all published blog posts with pagination
 */
function getPosts($db) {
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = min(50, max(1, intval($_GET['limit'] ?? 10)));
    $offset = ($page - 1) * $limit;
    $category = $_GET['category'] ?? '';
    $featured_only = $_GET['featured'] ?? false;
    
    $where_conditions = ['bp.is_published = 1'];
    $params = [];
    
    if ($category) {
        $where_conditions[] = 'bc.category_key = :category';
        $params[':category'] = $category;
    }
    
    if ($featured_only) {
        $where_conditions[] = 'bp.is_featured = 1';
    }
    
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    
    // Get total count
    $count_query = "
        SELECT COUNT(*) as total 
        FROM blog_posts bp 
        JOIN blog_categories bc ON bp.blog_category_id = bc.id 
        $where_clause
    ";
    
    $count_stmt = $db->prepare($count_query);
    foreach ($params as $key => $value) {
        $count_stmt->bindValue($key, $value);
    }
    $count_stmt->execute();
    $total = $count_stmt->fetch()['total'];
    
    // Get posts
    $query = "
        SELECT 
            bp.id,
            bp.title,
            bp.slug,
            bp.excerpt,
            bp.reading_time,
            bp.featured_image,
            bp.is_featured,
            bp.published_at,
            bp.views_count,
            bc.category_key,
            bct.name as category_name,
            bc.icon as category_icon,
            u.first_name,
            u.last_name,
            GROUP_CONCAT(bt.tag_name SEPARATOR ', ') as tags
        FROM blog_posts bp
        JOIN blog_categories bc ON bp.blog_category_id = bc.id
        LEFT JOIN blog_category_translations bct ON bc.id = bct.blog_category_id AND bct.language_code = 'en'
        LEFT JOIN users u ON bp.author_id = u.id
        LEFT JOIN blog_post_tags bpt ON bp.id = bpt.blog_post_id
        LEFT JOIN blog_tags bt ON bpt.blog_tag_id = bt.id
        $where_clause
        GROUP BY bp.id
        ORDER BY bp.published_at DESC, bp.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $posts = $stmt->fetchAll();
    
    // Format the response
    foreach ($posts as &$post) {
        $post['author'] = trim(($post['first_name'] ?? '') . ' ' . ($post['last_name'] ?? '')) ?: 'Diocese Administration';
        $post['date'] = date('F j, Y', strtotime($post['published_at']));
        $post['tags'] = $post['tags'] ? explode(', ', $post['tags']) : [];
        unset($post['first_name'], $post['last_name']);
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'posts' => $posts,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'total_posts' => $total,
                'per_page' => $limit
            ]
        ]
    ]);
}

/**
 * Get a single blog post by slug or ID
 */
function getPost($db) {
    $slug = $_GET['slug'] ?? '';
    $id = $_GET['id'] ?? '';
    
    if (!$slug && !$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Post slug or ID is required']);
        return;
    }
    
    $where_condition = $slug ? 'bp.slug = :identifier' : 'bp.id = :identifier';
    $identifier = $slug ?: $id;
    
    $query = "
        SELECT 
            bp.id,
            bp.title,
            bp.slug,
            bp.excerpt,
            bp.content,
            bp.reading_time,
            bp.featured_image,
            bp.is_featured,
            bp.published_at,
            bp.views_count,
            bp.seo_description,
            bp.seo_keywords,
            bc.category_key,
            bct.name as category_name,
            bc.icon as category_icon,
            u.first_name,
            u.last_name,
            GROUP_CONCAT(bt.tag_name SEPARATOR ', ') as tags
        FROM blog_posts bp
        JOIN blog_categories bc ON bp.blog_category_id = bc.id
        LEFT JOIN blog_category_translations bct ON bc.id = bct.blog_category_id AND bct.language_code = 'en'
        LEFT JOIN users u ON bp.author_id = u.id
        LEFT JOIN blog_post_tags bpt ON bp.id = bpt.blog_post_id
        LEFT JOIN blog_tags bt ON bpt.blog_tag_id = bt.id
        WHERE $where_condition AND bp.is_published = 1
        GROUP BY bp.id
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':identifier', $identifier);
    $stmt->execute();
    
    $post = $stmt->fetch();
    
    if (!$post) {
        http_response_code(404);
        echo json_encode(['error' => 'Post not found']);
        return;
    }
    
    // Format the response
    $post['author'] = trim(($post['first_name'] ?? '') . ' ' . ($post['last_name'] ?? '')) ?: 'Diocese Administration';
    $post['date'] = date('F j, Y', strtotime($post['published_at']));
    $post['tags'] = $post['tags'] ? explode(', ', $post['tags']) : [];
    unset($post['first_name'], $post['last_name']);
    
    echo json_encode([
        'success' => true,
        'data' => $post
    ]);
}

/**
 * Get all blog categories
 */
function getCategories($db) {
    $query = "
        SELECT 
            bc.id,
            bc.category_key,
            bc.icon,
            bct.name,
            bct.description,
            COUNT(bp.id) as post_count
        FROM blog_categories bc
        LEFT JOIN blog_category_translations bct ON bc.id = bct.blog_category_id AND bct.language_code = 'en'
        LEFT JOIN blog_posts bp ON bc.id = bp.blog_category_id AND bp.is_published = 1
        WHERE bc.is_active = 1
        GROUP BY bc.id
        ORDER BY bct.name
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $categories
    ]);
}

/**
 * Get related posts based on category and tags
 */
function getRelatedPosts($db) {
    $post_id = $_GET['post_id'] ?? '';
    $limit = min(10, max(1, intval($_GET['limit'] ?? 3)));
    
    if (!$post_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Post ID is required']);
        return;
    }
    
    $query = "
        SELECT DISTINCT
            bp2.id,
            bp2.title,
            bp2.slug,
            bp2.excerpt,
            bp2.reading_time,
            bp2.featured_image,
            bp2.published_at,
            bc2.category_key,
            bct2.name as category_name,
            u2.first_name,
            u2.last_name,
            (
                CASE 
                    WHEN bp2.blog_category_id = bp1.blog_category_id THEN 3
                    ELSE 0
                END +
                (
                    SELECT COUNT(*)
                    FROM blog_post_tags bpt1
                    JOIN blog_post_tags bpt2 ON bpt1.blog_tag_id = bpt2.blog_tag_id
                    WHERE bpt1.blog_post_id = bp1.id AND bpt2.blog_post_id = bp2.id
                )
            ) as relevance_score
        FROM blog_posts bp1
        JOIN blog_posts bp2 ON bp2.id != bp1.id
        JOIN blog_categories bc2 ON bp2.blog_category_id = bc2.id
        LEFT JOIN blog_category_translations bct2 ON bc2.id = bct2.blog_category_id AND bct2.language_code = 'en'
        LEFT JOIN users u2 ON bp2.author_id = u2.id
        WHERE bp1.id = :post_id 
        AND bp2.is_published = 1
        AND (
            bp2.blog_category_id = bp1.blog_category_id
            OR EXISTS (
                SELECT 1 
                FROM blog_post_tags bpt1
                JOIN blog_post_tags bpt2 ON bpt1.blog_tag_id = bpt2.blog_tag_id
                WHERE bpt1.blog_post_id = bp1.id AND bpt2.blog_post_id = bp2.id
            )
        )
        ORDER BY relevance_score DESC, bp2.published_at DESC
        LIMIT :limit
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $posts = $stmt->fetchAll();
    
    // Format the response
    foreach ($posts as &$post) {
        $post['author'] = trim(($post['first_name'] ?? '') . ' ' . ($post['last_name'] ?? '')) ?: 'Diocese Administration';
        $post['date'] = date('F j, Y', strtotime($post['published_at']));
        unset($post['first_name'], $post['last_name'], $post['relevance_score']);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $posts
    ]);
}

/**
 * Increment post view count
 */
function incrementViews($db) {
    $post_id = $_GET['post_id'] ?? '';
    
    if (!$post_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Post ID is required']);
        return;
    }
    
    $query = "UPDATE blog_posts SET views_count = views_count + 1 WHERE id = :post_id";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'View count updated'
    ]);
}
?>
