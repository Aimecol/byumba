<?php
/**
 * Blog API Endpoint for Diocese of Byumba System
 * Handles blog posts, categories, and related operations
 */

// Include database configuration
require_once '../config/database.php';

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Set content type
header('Content-Type: application/json');

try {
    switch ($method) {
        case 'GET':
            handleGetRequest($action);
            break;
        case 'POST':
            handlePostRequest($action);
            break;
        default:
            ResponseHelper::error('Method not allowed', 405);
    }
} catch (Exception $e) {
    ResponseHelper::error('Server error: ' . $e->getMessage(), 500);
}

/**
 * Handle GET requests
 */
function handleGetRequest($action) {
    global $db, $current_language;
    
    switch ($action) {
        case 'posts':
            getBlogPosts();
            break;
        case 'categories':
            getBlogCategories();
            break;
        case 'post':
            getBlogPost();
            break;
        case 'featured':
            getFeaturedPosts();
            break;
        default:
            ResponseHelper::error('Invalid action', 400);
    }
}

/**
 * Handle POST requests
 */
function handlePostRequest($action) {
    switch ($action) {
        case 'search':
            searchBlogPosts();
            break;
        default:
            ResponseHelper::error('Invalid action', 400);
    }
}

/**
 * Get blog posts with pagination and filtering
 */
function getBlogPosts() {
    global $db, $current_language;
    
    try {
        // Get parameters
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, min(50, intval($_GET['limit'] ?? 8))); // Max 50 posts per page
        $category = $_GET['category'] ?? '';
        $search = $_GET['search'] ?? '';
        $offset = ($page - 1) * $limit;
        
        // Build query
        $where_conditions = ['bp.is_published = 1'];
        $params = [];
        
        if (!empty($category)) {
            $where_conditions[] = 'bc.category_key = :category';
            $params[':category'] = $category;
        }
        
        if (!empty($search)) {
            $where_conditions[] = '(bp.title LIKE :search OR bp.excerpt LIKE :search OR bp.content LIKE :search OR bpt.title LIKE :search OR bpt.excerpt LIKE :search OR bpt.content LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        // Get total count
        $count_query = "
            SELECT COUNT(DISTINCT bp.id) as total
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id
            LEFT JOIN blog_category_translations bct ON bc.id = bct.blog_category_id AND bct.language_code = :lang
            LEFT JOIN blog_post_translations bpt ON bp.id = bpt.blog_post_id AND bpt.language_code = :lang
            WHERE {$where_clause}
        ";
        
        $count_stmt = $db->prepare($count_query);
        $count_stmt->bindParam(':lang', $current_language);
        foreach ($params as $key => $value) {
            $count_stmt->bindValue($key, $value);
        }
        $count_stmt->execute();
        $total = $count_stmt->fetch()['total'];
        
        // Get posts
        $query = "
            SELECT
                bp.id,
                bp.post_number,
                COALESCE(bpt.title, bp.title) as title,
                bp.slug,
                COALESCE(bpt.excerpt, bp.excerpt) as excerpt,
                bp.featured_image,
                bp.is_featured,
                bp.published_at,
                bp.views_count,
                bc.category_key,
                bc.icon as category_icon,
                COALESCE(bct.name, bc.category_key) as category_name,
                u.first_name,
                u.last_name,
                u.email as author_email
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id
            LEFT JOIN blog_category_translations bct ON bc.id = bct.blog_category_id AND bct.language_code = :lang
            LEFT JOIN blog_post_translations bpt ON bp.id = bpt.blog_post_id AND bpt.language_code = :lang
            LEFT JOIN users u ON bp.author_id = u.id
            WHERE {$where_clause}
            ORDER BY bp.published_at DESC, bp.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':lang', $current_language);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        $posts = $stmt->fetchAll();
        
        // Format posts
        $formatted_posts = array_map('formatBlogPost', $posts);
        
        // Calculate pagination info
        $total_pages = ceil($total / $limit);
        $has_next = $page < $total_pages;
        $has_prev = $page > 1;
        
        ResponseHelper::success([
            'posts' => $formatted_posts,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_posts' => $total,
                'posts_per_page' => $limit,
                'has_next' => $has_next,
                'has_prev' => $has_prev
            ]
        ]);
        
    } catch (PDOException $e) {
        ResponseHelper::error('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Get blog categories with translations
 */
function getBlogCategories() {
    global $db, $current_language;

    try {
        $query = "
            SELECT
                bc.id,
                bc.category_key,
                bc.icon,
                COALESCE(bct.name, bc.category_key) as name,
                bct.description,
                COUNT(bp.id) as post_count
            FROM blog_categories bc
            LEFT JOIN blog_category_translations bct ON bc.id = bct.blog_category_id AND bct.language_code = :lang
            LEFT JOIN blog_posts bp ON bc.id = bp.blog_category_id AND bp.is_published = 1
            WHERE bc.is_active = 1
            GROUP BY bc.id, bc.category_key, bc.icon, bct.name, bct.description
            ORDER BY bct.name ASC, bc.category_key ASC
        ";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':lang', $current_language);
        $stmt->execute();

        $categories = $stmt->fetchAll();

        ResponseHelper::success($categories);

    } catch (PDOException $e) {
        ResponseHelper::error('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Get a single blog post by ID or slug
 */
function getBlogPost() {
    global $db, $current_language;

    try {
        $id = $_GET['id'] ?? '';
        $slug = $_GET['slug'] ?? '';

        if (empty($id) && empty($slug)) {
            ResponseHelper::error('Post ID or slug is required', 400);
        }

        $where_condition = !empty($id) ? 'bp.id = :identifier' : 'bp.slug = :identifier';
        $identifier = !empty($id) ? $id : $slug;

        $query = "
            SELECT
                bp.id,
                bp.post_number,
                COALESCE(bpt.title, bp.title) as title,
                bp.slug,
                COALESCE(bpt.excerpt, bp.excerpt) as excerpt,
                COALESCE(bpt.content, bp.content) as content,
                bp.featured_image,
                bp.is_featured,
                bp.published_at,
                bp.views_count,
                bp.created_at,
                bp.updated_at,
                bc.category_key,
                bc.icon as category_icon,
                COALESCE(bct.name, bc.category_key) as category_name,
                u.first_name,
                u.last_name,
                u.email as author_email
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id
            LEFT JOIN blog_category_translations bct ON bc.id = bct.blog_category_id AND bct.language_code = :lang
            LEFT JOIN blog_post_translations bpt ON bp.id = bpt.blog_post_id AND bpt.language_code = :lang
            LEFT JOIN users u ON bp.author_id = u.id
            WHERE {$where_condition} AND bp.is_published = 1
        ";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':lang', $current_language);
        $stmt->bindParam(':identifier', $identifier);
        $stmt->execute();

        $post = $stmt->fetch();

        if (!$post) {
            ResponseHelper::error('Post not found', 404);
        }

        // Increment view count
        $update_query = "UPDATE blog_posts SET views_count = views_count + 1 WHERE id = :id";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bindParam(':id', $post['id']);
        $update_stmt->execute();

        // Format post
        $formatted_post = formatBlogPost($post, true);

        ResponseHelper::success($formatted_post);

    } catch (PDOException $e) {
        ResponseHelper::error('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Get featured blog posts
 */
function getFeaturedPosts() {
    global $db, $current_language;

    try {
        $limit = max(1, min(10, intval($_GET['limit'] ?? 3))); // Max 10 featured posts

        $query = "
            SELECT
                bp.id,
                bp.post_number,
                COALESCE(bpt.title, bp.title) as title,
                bp.slug,
                COALESCE(bpt.excerpt, bp.excerpt) as excerpt,
                bp.featured_image,
                bp.is_featured,
                bp.published_at,
                bp.views_count,
                bc.category_key,
                bc.icon as category_icon,
                COALESCE(bct.name, bc.category_key) as category_name,
                u.first_name,
                u.last_name,
                u.email as author_email
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id
            LEFT JOIN blog_category_translations bct ON bc.id = bct.blog_category_id AND bct.language_code = :lang
            LEFT JOIN blog_post_translations bpt ON bp.id = bpt.blog_post_id AND bpt.language_code = :lang
            LEFT JOIN users u ON bp.author_id = u.id
            WHERE bp.is_published = 1 AND bp.is_featured = 1
            ORDER BY bp.published_at DESC, bp.created_at DESC
            LIMIT :limit
        ";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':lang', $current_language);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $posts = $stmt->fetchAll();

        // Format posts
        $formatted_posts = array_map('formatBlogPost', $posts);

        ResponseHelper::success($formatted_posts);

    } catch (PDOException $e) {
        ResponseHelper::error('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Search blog posts
 */
function searchBlogPosts() {
    global $db, $current_language;

    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $search_term = $input['search'] ?? '';
        $category = $input['category'] ?? '';
        $page = max(1, intval($input['page'] ?? 1));
        $limit = max(1, min(50, intval($input['limit'] ?? 8)));
        $offset = ($page - 1) * $limit;

        if (empty($search_term)) {
            ResponseHelper::error('Search term is required', 400);
        }

        // Build query
        $where_conditions = ['bp.is_published = 1'];
        $params = [':search' => '%' . $search_term . '%'];

        $where_conditions[] = '(bp.title LIKE :search OR bp.excerpt LIKE :search OR bp.content LIKE :search OR bpt.title LIKE :search OR bpt.excerpt LIKE :search OR bpt.content LIKE :search)';

        if (!empty($category)) {
            $where_conditions[] = 'bc.category_key = :category';
            $params[':category'] = $category;
        }

        $where_clause = implode(' AND ', $where_conditions);

        // Get total count
        $count_query = "
            SELECT COUNT(DISTINCT bp.id) as total
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id
            LEFT JOIN blog_category_translations bct ON bc.id = bct.blog_category_id AND bct.language_code = :lang
            LEFT JOIN blog_post_translations bpt ON bp.id = bpt.blog_post_id AND bpt.language_code = :lang
            WHERE {$where_clause}
        ";

        $count_stmt = $db->prepare($count_query);
        $count_stmt->bindParam(':lang', $current_language);
        foreach ($params as $key => $value) {
            $count_stmt->bindValue($key, $value);
        }
        $count_stmt->execute();
        $total = $count_stmt->fetch()['total'];

        // Get posts
        $query = "
            SELECT
                bp.id,
                bp.post_number,
                COALESCE(bpt.title, bp.title) as title,
                bp.slug,
                COALESCE(bpt.excerpt, bp.excerpt) as excerpt,
                bp.featured_image,
                bp.is_featured,
                bp.published_at,
                bp.views_count,
                bc.category_key,
                bc.icon as category_icon,
                COALESCE(bct.name, bc.category_key) as category_name,
                u.first_name,
                u.last_name,
                u.email as author_email
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id
            LEFT JOIN blog_category_translations bct ON bc.id = bct.blog_category_id AND bct.language_code = :lang
            LEFT JOIN blog_post_translations bpt ON bp.id = bpt.blog_post_id AND bpt.language_code = :lang
            LEFT JOIN users u ON bp.author_id = u.id
            WHERE {$where_clause}
            ORDER BY bp.published_at DESC, bp.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':lang', $current_language);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        $posts = $stmt->fetchAll();

        // Format posts
        $formatted_posts = array_map('formatBlogPost', $posts);

        // Calculate pagination info
        $total_pages = ceil($total / $limit);
        $has_next = $page < $total_pages;
        $has_prev = $page > 1;

        ResponseHelper::success([
            'posts' => $formatted_posts,
            'search_term' => $search_term,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_posts' => $total,
                'posts_per_page' => $limit,
                'has_next' => $has_next,
                'has_prev' => $has_prev
            ]
        ]);

    } catch (PDOException $e) {
        ResponseHelper::error('Database error: ' . $e->getMessage(), 500);
    }
}

/**
 * Format blog post data
 */
function formatBlogPost($post, $include_content = false) {
    // Fix image path - convert /uploads/blog/ to images/blog/
    $featured_image = $post['featured_image'];
    if ($featured_image && strpos($featured_image, '/uploads/blog/') === 0) {
        $featured_image = str_replace('/uploads/blog/', 'images/blog/', $featured_image);
    }

    $formatted = [
        'id' => intval($post['id']),
        'post_number' => $post['post_number'],
        'title' => $post['title'],
        'slug' => $post['slug'],
        'excerpt' => $post['excerpt'],
        'featured_image' => $featured_image,
        'is_featured' => boolval($post['is_featured']),
        'published_at' => $post['published_at'],
        'views_count' => intval($post['views_count']),
        'category' => [
            'key' => $post['category_key'],
            'name' => $post['category_name'],
            'icon' => $post['category_icon']
        ],
        'author' => [
            'name' => trim(($post['first_name'] ?? '') . ' ' . ($post['last_name'] ?? '')),
            'email' => $post['author_email'] ?? null
        ],
        'formatted_date' => formatDate($post['published_at'], 'F j, Y'),
        'reading_time' => calculateReadingTime($post['excerpt'] . ($include_content ? $post['content'] ?? '' : ''))
    ];

    if ($include_content && isset($post['content'])) {
        $formatted['content'] = $post['content'];
        $formatted['created_at'] = $post['created_at'];
        $formatted['updated_at'] = $post['updated_at'];
    }

    return $formatted;
}

/**
 * Calculate estimated reading time
 */
function calculateReadingTime($text) {
    $word_count = str_word_count(strip_tags($text));
    $reading_speed = 200; // Average words per minute
    $minutes = ceil($word_count / $reading_speed);
    return max(1, $minutes); // Minimum 1 minute
}

?>
