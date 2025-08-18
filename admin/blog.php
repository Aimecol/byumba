<?php
/**
 * Blog Management
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('manage_blog');

// Page configuration
$page_title = 'Blog Management';
$breadcrumbs = [
    ['title' => 'Blog']
];

// Handle actions
$action = $_GET['action'] ?? 'list';
$post_id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'toggle_publish' && $post_id) {
        try {
            $is_published = $_POST['is_published'] ? 1 : 0;
            $published_at = $is_published ? 'NOW()' : 'NULL';
            
            $query = "UPDATE blog_posts SET is_published = :is_published, published_at = $published_at WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':is_published', $is_published);
            $stmt->bindParam(':id', $post_id);
            $stmt->execute();
            
            $status = $is_published ? 'published' : 'unpublished';
            logAdminActivity('toggle_blog_post', "Post $post_id $status");
            $message = "Post $status successfully.";
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Error updating post: ' . $e->getMessage();
        }
    }
    
    if ($action === 'delete' && $post_id) {
        try {
            $query = "DELETE FROM blog_posts WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $post_id);
            $stmt->execute();
            
            logAdminActivity('delete_blog_post', "Deleted post ID: $post_id");
            $message = 'Post deleted successfully.';
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Error deleting post: ' . $e->getMessage();
        }
    }
}

// Get blog posts data
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$per_page = 25;
$offset = ($page - 1) * $per_page;

$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(bp.title LIKE :search OR bp.excerpt LIKE :search OR bp.post_number LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($category_filter) {
    $where_conditions[] = "bp.blog_category_id = :category";
    $params[':category'] = $category_filter;
}

if ($status_filter) {
    if ($status_filter === 'published') {
        $where_conditions[] = "bp.is_published = 1";
    } elseif ($status_filter === 'draft') {
        $where_conditions[] = "bp.is_published = 0";
    } elseif ($status_filter === 'featured') {
        $where_conditions[] = "bp.is_featured = 1";
    }
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total 
                FROM blog_posts bp
                JOIN blog_categories bc ON bp.blog_category_id = bc.id
                $where_clause";
$count_stmt = $db->prepare($count_query);
foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total_posts = $count_stmt->fetch()['total'];
$total_pages = ceil($total_posts / $per_page);

// Get blog posts
$query = "SELECT bp.*, 
                 bct.name as category_name,
                 u.first_name, u.last_name
          FROM blog_posts bp
          JOIN blog_categories bc ON bp.blog_category_id = bc.id
          JOIN blog_category_translations bct ON bc.id = bct.blog_category_id
          LEFT JOIN users u ON bp.author_id = u.id
          $where_clause AND bct.language_code = 'en'
          ORDER BY bp.created_at DESC
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// Get categories for filter
$cat_query = "SELECT bc.id, bct.name 
              FROM blog_categories bc
              JOIN blog_category_translations bct ON bc.id = bct.blog_category_id
              WHERE bct.language_code = 'en' AND bc.is_active = 1
              ORDER BY bct.name";
$cat_stmt = $db->prepare($cat_query);
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0">Blog Management</h1>
        <p class="text-muted">Manage blog posts and announcements</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <a href="blog_form.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>New Post
            </a>
            <a href="blog_categories.php" class="btn btn-outline-secondary">
                <i class="fas fa-tags me-2"></i>Categories
            </a>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search Posts</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search by title, excerpt, or post number" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" 
                                <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Posts</option>
                    <option value="published" <?php echo $status_filter === 'published' ? 'selected' : ''; ?>>Published</option>
                    <option value="draft" <?php echo $status_filter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="featured" <?php echo $status_filter === 'featured' ? 'selected' : ''; ?>>Featured</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Blog Posts Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            Blog Posts 
            <span class="badge bg-primary"><?php echo number_format($total_posts); ?></span>
        </h5>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTable('postsTable', 'blog_posts')">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printPage()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($posts)): ?>
            <div class="text-center py-5">
                <i class="fas fa-blog text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">No Posts Found</h4>
                <p class="text-muted">No blog posts match your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="postsTable">
                    <thead>
                        <tr>
                            <th>Post</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-start">
                                        <?php if ($post['featured_image']): ?>
                                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                                 alt="Featured Image" class="me-3" 
                                                 style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div class="me-3 bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 40px; border-radius: 4px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                            <?php if ($post['is_featured']): ?>
                                                <span class="badge bg-warning ms-2">Featured</span>
                                            <?php endif; ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($post['post_number']); ?></small>
                                            <?php if ($post['excerpt']): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($post['excerpt'], 0, 80)) . '...'; ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($post['category_name']); ?></span>
                                </td>
                                <td>
                                    <?php if ($post['author_id']): ?>
                                        <?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">System</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($post['is_published']): ?>
                                        <span class="badge bg-success">Published</span>
                                        <?php if ($post['published_at']): ?>
                                            <br><small class="text-muted"><?php echo formatDisplayDate($post['published_at']); ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo number_format($post['views_count']); ?></span>
                                </td>
                                <td>
                                    <small><?php echo formatDisplayDate($post['created_at']); ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="blog_view.php?id=<?php echo $post['id']; ?>" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="blog_form.php?id=<?php echo $post['id']; ?>" 
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-<?php echo $post['is_published'] ? 'warning' : 'success'; ?>" 
                                                onclick="togglePublish(<?php echo $post['id']; ?>, <?php echo $post['is_published'] ? 'false' : 'true'; ?>)" 
                                                title="<?php echo $post['is_published'] ? 'Unpublish' : 'Publish'; ?>">
                                            <i class="fas fa-<?php echo $post['is_published'] ? 'eye-slash' : 'eye'; ?>"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deletePost(<?php echo $post['id']; ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_posts); ?> 
                        of <?php echo number_format($total_posts); ?> posts
                    </div>
                    <?php 
                    $base_url = 'blog.php?search=' . urlencode($search) . '&category=' . urlencode($category_filter) . '&status=' . urlencode($status_filter);
                    echo generatePagination($page, $total_pages, $base_url); 
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Publish/Unpublish Form -->
<form id="publishForm" method="POST" style="display: none;">
    <input type="hidden" name="is_published" id="publishStatus">
</form>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
</form>

<?php
$additional_js = '
<script>
function togglePublish(postId, publish) {
    if (confirm("Are you sure you want to " + (publish ? "publish" : "unpublish") + " this post?")) {
        document.getElementById("publishStatus").value = publish ? "1" : "0";
        document.getElementById("publishForm").action = "blog.php?action=toggle_publish&id=" + postId;
        document.getElementById("publishForm").submit();
    }
}

function deletePost(postId) {
    if (confirm("Are you sure you want to delete this post? This action cannot be undone.")) {
        document.getElementById("deleteForm").action = "blog.php?action=delete&id=" + postId;
        document.getElementById("deleteForm").submit();
    }
}
</script>
';

include 'includes/footer.php';
?>
