<?php
/**
 * Jobs API Endpoint
 */

if ($method === 'GET') {
    try {
        // Get query parameters
        $category = $_GET['category'] ?? 'all';
        $employment_type = $_GET['employment_type'] ?? 'all';
        $location = $_GET['location'] ?? 'all';
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 20);
        $offset = ($page - 1) * $limit;
        
        // Build query conditions
        $where_conditions = ['j.is_active = 1', 'jct.language_code = :language'];
        $params = [':language' => $current_language];
        
        if ($category !== 'all') {
            $where_conditions[] = 'jc.category_key = :category';
            $params[':category'] = $category;
        }
        
        if ($employment_type !== 'all') {
            $where_conditions[] = 'j.employment_type = :employment_type';
            $params[':employment_type'] = $employment_type;
        }
        
        if ($location !== 'all') {
            $where_conditions[] = 'j.location LIKE :location';
            $params[':location'] = '%' . $location . '%';
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        
        // Get total count
        $count_query = "SELECT COUNT(*) as total 
                        FROM jobs j 
                        JOIN job_categories jc ON j.job_category_id = jc.id 
                        JOIN job_category_translations jct ON jc.id = jct.job_category_id 
                        $where_clause";
        
        $stmt = $db->prepare($count_query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $total = $stmt->fetch()['total'];
        
        // Get jobs with category and job translations
        // Handle both old and new parishes table schema
        $query = "SELECT j.*, jc.category_key, jc.icon as category_icon,
                         jct.name as category_name, jct.description as category_description,
                         COALESCE(p.name_en, p.name) as parish_name,
                         COALESCE(jt.title, j.title) as translated_title,
                         COALESCE(jt.description, j.description) as translated_description,
                         COALESCE(jt.requirements, j.requirements) as translated_requirements
                  FROM jobs j
                  JOIN job_categories jc ON j.job_category_id = jc.id
                  JOIN job_category_translations jct ON jc.id = jct.job_category_id
                  LEFT JOIN job_translations jt ON j.id = jt.job_id AND jt.language_code = :job_language
                  LEFT JOIN parishes p ON j.parish_id = p.id
                  $where_clause
                  ORDER BY j.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        // Bind the language parameter for job translations
        $stmt->bindValue(':job_language', $current_language);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $jobs = [];
        while ($row = $stmt->fetch()) {
            // Use translated content if available, fallback to original
            $title = $row['translated_title'] ?: $row['title'];
            $description = $row['translated_description'] ?: $row['description'];
            $requirements_text = $row['translated_requirements'] ?: $row['requirements'];

            // Parse requirements if it's JSON
            if ($requirements_text && isJson($requirements_text)) {
                $requirements = json_decode($requirements_text, true);
            } else {
                // Convert text to array if it's not JSON
                $requirements = $requirements_text ? explode("\n", $requirements_text) : [];
            }

            // Format employment type for display
            $employment_type_display = ucwords(str_replace('_', ' ', $row['employment_type']));

            // Calculate days since posted
            $posted_date = new DateTime($row['created_at']);
            $current_date = new DateTime();
            $days_ago = $current_date->diff($posted_date)->days;

            // Format deadline
            $deadline_formatted = null;
            if ($row['application_deadline']) {
                $deadline = new DateTime($row['application_deadline']);
                $deadline_formatted = $deadline->format('M j, Y');

                // Check if deadline has passed
                $is_expired = $deadline < $current_date;
            } else {
                $is_expired = false;
            }

            $jobs[] = [
                'id' => $row['id'],
                'job_number' => $row['job_number'],
                'title' => $title,
                'description' => $description,
                'requirements' => $requirements,
                'salary_range' => $row['salary_range'],
                'employment_type' => $row['employment_type'],
                'employment_type_display' => $employment_type_display,
                'location' => $row['location'] ?: ($row['parish_name'] ?: 'Diocese Office'),
                'application_deadline' => $row['application_deadline'],
                'deadline_formatted' => $deadline_formatted,
                'is_expired' => $is_expired,
                'contact_email' => $row['contact_email'],
                'contact_phone' => $row['contact_phone'],
                'category_key' => $row['category_key'],
                'category_name' => $row['category_name'],
                'category_description' => $row['category_description'],
                'category_icon' => $row['category_icon'] ?: 'fa-briefcase',
                'posted_date' => $row['created_at'],
                'days_ago' => $days_ago,
                'posted_formatted' => $posted_date->format('M j, Y')
            ];
        }
        
        // Get job categories for filter
        $categories_query = "SELECT jc.*, jct.name, jct.description 
                            FROM job_categories jc 
                            JOIN job_category_translations jct ON jc.id = jct.job_category_id 
                            WHERE jc.is_active = 1 AND jct.language_code = :language 
                            ORDER BY jct.name";
        
        $stmt = $db->prepare($categories_query);
        $stmt->bindParam(':language', $current_language);
        $stmt->execute();
        
        $categories = [];
        while ($row = $stmt->fetch()) {
            $categories[] = [
                'key' => $row['category_key'],
                'name' => $row['name'],
                'description' => $row['description'],
                'icon' => $row['icon']
            ];
        }
        
        ResponseHelper::success([
            'jobs' => $jobs,
            'categories' => $categories,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'total_items' => $total,
                'items_per_page' => $limit
            ]
        ]);
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to load jobs: ' . $e->getMessage(), 500);
    }
}

// Helper function to check if string is JSON
function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}
?>
