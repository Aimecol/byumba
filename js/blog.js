// Blog Page Specific JavaScript

// Global variables
let currentPage = 1;
let isLoading = false;
let hasMorePosts = true;
let currentCategory = '';
let currentSearch = '';

document.addEventListener('DOMContentLoaded', function() {
    initializeBlogPage();
    initializeBlogSearch();
    initializeLoadMore();
    initializeNewsletterSubscription();
    initializeReadMoreLinks();
    initializeBlogLanguageHandling();
});

// Initialize blog page
function initializeBlogPage() {
    loadBlogCategories();
    loadFeaturedPosts();
    loadBlogPosts();
}

// Load blog categories from API
async function loadBlogCategories() {
    try {
        const response = await fetch('/new/byumba/api/index.php?endpoint=blog&action=categories');
        const result = await response.json();

        if (result.success) {
            populateBlogCategoryFilter(result.data);
        } else {
            console.error('Failed to load categories:', result.message);
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

// Populate blog category filter dropdown
function populateBlogCategoryFilter(categories) {
    const categoryFilter = document.getElementById('categoryFilter');
    if (!categoryFilter) return;

    // Clear existing options except "All Categories"
    categoryFilter.innerHTML = '<option value="">All Categories</option>';

    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.category_key;
        option.textContent = category.name;
        categoryFilter.appendChild(option);
    });
}

// Load featured posts
async function loadFeaturedPosts() {
    try {
        const response = await fetch('/new/byumba/api/index.php?endpoint=blog&action=featured&limit=1');
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            displayFeaturedPost(result.data[0]);
        }
    } catch (error) {
        console.error('Error loading featured posts:', error);
    }
}

// Display featured post
function displayFeaturedPost(post) {
    const featuredSection = document.querySelector('.featured-post');
    if (!featuredSection) return;

    featuredSection.innerHTML = `
        <div class="featured-content">
            <div class="featured-image">
                <img src="${post.featured_image || 'images/blog/default-featured.jpg'}" alt="${post.title}" class="featured-img">
                <div class="featured-badge">Featured</div>
            </div>
            <div class="featured-text">
                <div class="post-meta">
                    <span class="category ${post.category.key}">${post.category.name}</span>
                    <span class="date">${post.formatted_date}</span>
                </div>
                <h2 class="featured-title">${post.title}</h2>
                <p class="featured-excerpt">${post.excerpt}</p>
                <a href="#" class="read-more-btn" data-post-id="${post.id}" data-post-slug="${post.slug}">
                    <i class="fas fa-arrow-right"></i>
                    Read Full Article
                </a>
            </div>
        </div>
    `;
}

// Load blog posts
async function loadBlogPosts(page = 1, append = false) {
    if (isLoading) return;

    isLoading = true;
    const loadMoreBtn = document.getElementById('loadMoreBtn');

    // Show loading state
    if (loadMoreBtn && page > 1) {
        const originalText = loadMoreBtn.innerHTML;
        loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        loadMoreBtn.disabled = true;
    }

    try {
        const params = new URLSearchParams({
            page: page,
            limit: 8
        });

        if (currentCategory) params.append('category', currentCategory);
        if (currentSearch) params.append('search', currentSearch);

        const response = await fetch(`/new/byumba/api/index.php?endpoint=blog&action=posts&${params}`);
        const result = await response.json();

        if (result.success) {
            displayBlogPosts(result.data.posts, append);
            updatePaginationInfo(result.data.pagination);

            if (page === 1) {
                currentPage = 1;
            }
        } else {
            console.error('Failed to load posts:', result.message);
            showNotification('Failed to load blog posts', 'error');
        }
    } catch (error) {
        console.error('Error loading posts:', error);
        showNotification('Error loading blog posts', 'error');
    } finally {
        isLoading = false;

        // Reset load more button
        if (loadMoreBtn && page > 1) {
            loadMoreBtn.innerHTML = '<i class="fas fa-plus"></i> Load More Posts';
            loadMoreBtn.disabled = false;
        }
    }
}

// Display blog posts
function displayBlogPosts(posts, append = false) {
    const blogGrid = document.getElementById('blogPostsGrid');
    if (!blogGrid) return;

    if (!append) {
        blogGrid.innerHTML = '';
    }

    posts.forEach(post => {
        const article = createBlogPostElement(post);
        blogGrid.appendChild(article);
    });

    // Show no results message if no posts
    if (!append && posts.length === 0) {
        showNoResultsMessage(true);
    } else {
        showNoResultsMessage(false);
    }
}

// Create blog post element
function createBlogPostElement(post) {
    const article = document.createElement('article');
    article.className = 'blog-post';
    article.setAttribute('data-category', post.category.key);
    article.style.animation = 'fadeIn 0.5s ease';

    const authorName = post.author.name || 'Diocese Communications';
    const imageUrl = post.featured_image || 'images/blog/default-post.jpg';

    article.innerHTML = `
        <div class="post-image">
            <img src="${imageUrl}" alt="${post.title}" class="post-img">
        </div>
        <div class="post-content">
            <div class="post-meta">
                <span class="category ${post.category.key}">${post.category.name}</span>
                <span class="date">${post.formatted_date}</span>
            </div>
            <h3 class="post-title">${post.title}</h3>
            <p class="post-excerpt">${post.excerpt}</p>
            <div class="post-footer">
                <div class="author">
                    <i class="fas fa-user"></i>
                    <span>${authorName}</span>
                </div>
                <a href="#" class="read-more" data-post-id="${post.id}" data-post-slug="${post.slug}">Read More</a>
            </div>
        </div>
    `;

    return article;
}

// Update pagination info
function updatePaginationInfo(pagination) {
    hasMorePosts = pagination.has_next;
    currentPage = pagination.current_page;

    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        if (hasMorePosts) {
            loadMoreBtn.style.display = 'block';
        } else {
            loadMoreBtn.style.display = 'none';
            if (pagination.total_posts > 0) {
                showEndMessage();
            }
        }
    }
}

// Blog Search and Filter Functionality
function initializeBlogSearch() {
    const searchInput = document.getElementById('blogSearch');
    const categoryFilter = document.getElementById('categoryFilter');

    if (searchInput) {
        // Debounce search input
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = this.value.trim();
                currentPage = 1;
                loadBlogPosts(1, false);
            }, 500);
        });
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            currentCategory = this.value;
            currentPage = 1;
            loadBlogPosts(1, false);
        });
    }
}

function showNoResultsMessage(show) {
    let noResultsMsg = document.getElementById('noBlogResultsMessage');
    
    if (show && !noResultsMsg) {
        noResultsMsg = document.createElement('div');
        noResultsMsg.id = 'noBlogResultsMessage';
        noResultsMsg.className = 'no-blog-results-message';
        noResultsMsg.innerHTML = `
            <div class="no-results-content">
                <i class="fas fa-search"></i>
                <h3>No Blog Posts Found</h3>
                <p>Try adjusting your search criteria or browse all categories.</p>
                <button onclick="clearBlogFilters()" class="clear-filters-btn">
                    <i class="fas fa-times"></i>
                    Clear Filters
                </button>
            </div>
        `;
        
        const blogGrid = document.getElementById('blogPostsGrid');
        blogGrid.parentNode.insertBefore(noResultsMsg, blogGrid.nextSibling);
        
        // Add styles for no results message
        const noResultsStyles = `
            .no-blog-results-message {
                text-align: center;
                padding: 60px 20px;
                background: white;
                border-radius: 15px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                margin-top: 30px;
            }
            .no-blog-results-message .no-results-content i {
                font-size: 48px;
                color: #dcdcdc;
                margin-bottom: 20px;
            }
            .no-blog-results-message .no-results-content h3 {
                color: #1e753f;
                margin-bottom: 10px;
                font-size: 24px;
            }
            .no-blog-results-message .no-results-content p {
                color: #666;
                font-size: 16px;
                margin-bottom: 25px;
            }
            .clear-filters-btn {
                background: #d14438;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 25px;
                font-weight: 600;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
            }
            .clear-filters-btn:hover {
                background: #b83c32;
                transform: translateY(-2px);
            }
        `;
        
        const styleSheet = document.createElement('style');
        styleSheet.textContent = noResultsStyles;
        document.head.appendChild(styleSheet);
    } else if (!show && noResultsMsg) {
        noResultsMsg.remove();
    }
}



// Load More Functionality
function initializeLoadMore() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            if (hasMorePosts && !isLoading) {
                loadBlogPosts(currentPage + 1, true);
            }
        });
    }
}

// Show end message when no more posts
function showEndMessage() {
    const endMessage = document.createElement('div');
    endMessage.className = 'end-message';
    endMessage.innerHTML = `
        <div class="end-content">
            <i class="fas fa-check-circle"></i>
            <p>You've reached the end of our blog posts. Check back soon for more updates!</p>
        </div>
    `;

    const loadMoreSection = document.querySelector('.load-more-section');
    if (loadMoreSection) {
        loadMoreSection.appendChild(endMessage);
    }

    // Add styles
    const endStyles = `
        .end-message {
            text-align: center;
            padding: 30px;
            background: #f0f0f0;
            border-radius: 15px;
            margin-top: 20px;
        }
        .end-content i {
            font-size: 32px;
            color: #1e753f;
            margin-bottom: 15px;
        }
        .end-content p {
            color: #666;
            margin: 0;
            font-size: 16px;
        }
    `;

    const styleSheet = document.createElement('style');
    styleSheet.textContent = endStyles;
    document.head.appendChild(styleSheet);
}

// Newsletter Subscription
function initializeNewsletterSubscription() {
    const newsletterForm = document.getElementById('newsletterForm');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const submitBtn = this.querySelector('button[type="submit"]');
            const email = emailInput.value;
            
            // Show loading state
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
            submitBtn.disabled = true;
            
            // Simulate subscription process
            setTimeout(() => {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                // Clear input
                emailInput.value = '';
                
                // Show success message
                showNotification(`Successfully subscribed ${email} to our newsletter!`, 'success');
            }, 2000);
        });
    }
}

// Read More Links
function initializeReadMoreLinks() {
    // Use event delegation to handle dynamically added links
    document.addEventListener('click', function(e) {
        if (e.target.matches('.read-more, .read-more-btn') || e.target.closest('.read-more, .read-more-btn')) {
            e.preventDefault();

            const link = e.target.matches('.read-more, .read-more-btn') ? e.target : e.target.closest('.read-more, .read-more-btn');
            const postId = link.getAttribute('data-post-id');
            const postSlug = link.getAttribute('data-post-slug');

            if (postId || postSlug) {
                loadAndShowBlogPost(postId, postSlug);
            }
        }
    });
}

// Load and show blog post details
async function loadAndShowBlogPost(postId, postSlug) {
    try {
        const params = new URLSearchParams();
        if (postId) params.append('id', postId);
        if (postSlug) params.append('slug', postSlug);

        const response = await fetch(`/new/byumba/api/index.php?endpoint=blog&action=post&${params}`);
        const result = await response.json();

        if (result.success) {
            showBlogPostModal(result.data);
        } else {
            showNotification('Failed to load post details', 'error');
        }
    } catch (error) {
        console.error('Error loading post:', error);
        showNotification('Error loading post details', 'error');
    }
}

function showBlogPostModal(post) {
    const authorName = post.author.name || 'Diocese Communications';
    const imageUrl = post.featured_image || 'images/blog/default-post.jpg';

    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'blog-modal-overlay';
    modalOverlay.innerHTML = `
        <div class="blog-modal-content">
            <div class="blog-modal-header">
                <h3>${post.title}</h3>
                <button class="blog-modal-close">&times;</button>
            </div>
            <div class="blog-modal-body">
                <div class="modal-post-meta">
                    <span class="modal-author">
                        <i class="fas fa-user"></i>
                        ${authorName}
                    </span>
                    <span class="modal-date">
                        <i class="fas fa-calendar"></i>
                        ${post.formatted_date}
                    </span>
                    <span class="modal-reading-time">
                        <i class="fas fa-clock"></i>
                        ${post.reading_time} min read
                    </span>
                    <span class="modal-category">
                        <i class="${post.category.icon || 'fas fa-tag'}"></i>
                        ${post.category.name}
                    </span>
                </div>

                <div class="modal-post-image">
                    <img src="${imageUrl}" alt="${post.title}" class="modal-post-img">
                </div>

                <div class="modal-post-content">
                    ${post.content || post.excerpt}
                </div>

                <div class="modal-post-footer">
                    <div class="post-stats">
                        <span class="views-count">
                            <i class="fas fa-eye"></i>
                            ${post.views_count} views
                        </span>
                    </div>
                    <div class="share-buttons">
                        <h4>Share this post:</h4>
                        <div class="share-links">
                            <a href="#" class="share-link facebook" onclick="sharePost('facebook', '${post.title}', '${window.location.origin}/blog/${post.slug}')">
                                <i class="fab fa-facebook"></i>
                                Facebook
                            </a>
                            <a href="#" class="share-link twitter" onclick="sharePost('twitter', '${post.title}', '${window.location.origin}/blog/${post.slug}')">
                                <i class="fab fa-twitter"></i>
                                Twitter
                            </a>
                            <a href="#" class="share-link whatsapp" onclick="sharePost('whatsapp', '${post.title}', '${window.location.origin}/blog/${post.slug}')">
                                <i class="fab fa-whatsapp"></i>
                                WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modalOverlay);

    // Add modal styles
    const modalStyles = `
        .blog-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            overflow-y: auto;
            padding: 20px;
        }
        .blog-modal-content {
            background: white;
            border-radius: 15px;
            width: 100%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        .blog-modal-header {
            padding: 25px;
            border-bottom: 1px solid #dcdcdc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1e753f;
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .blog-modal-header h3 {
            margin: 0;
            font-size: 20px;
        }
        .blog-modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: white;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .blog-modal-body {
            padding: 30px;
        }
        .modal-post-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .modal-post-meta span {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 14px;
        }
        .modal-post-meta i {
            color: #1e753f;
        }
        .modal-post-image {
            margin-bottom: 25px;
        }
        .modal-post-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }
        .modal-image-placeholder {
            height: 300px;
            background: linear-gradient(135deg, #1e753f, #2a8f4f);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: rgba(255, 255, 255, 0.3);
        }
        .modal-post-content {
            line-height: 1.8;
            color: #333;
        }
        .modal-post-content h4 {
            color: #1e753f;
            margin: 25px 0 15px;
        }
        .modal-post-content ul {
            margin: 15px 0;
            padding-left: 20px;
        }
        .modal-post-content li {
            margin-bottom: 8px;
        }
        .modal-post-content blockquote {
            background: #f0f0f0;
            border-left: 4px solid #1e753f;
            padding: 20px;
            margin: 25px 0;
            font-style: italic;
            color: #666;
        }
        .modal-post-footer {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #f0f0f0;
        }
        .share-buttons h4 {
            color: #1e753f;
            margin-bottom: 15px;
        }
        .share-links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .share-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .share-link.facebook {
            background: #1877f2;
            color: white;
        }
        .share-link.twitter {
            background: #1da1f2;
            color: white;
        }
        .share-link.whatsapp {
            background: #25d366;
            color: white;
        }
        .share-link:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
    `;

    const styleSheet = document.createElement('style');
    styleSheet.textContent = modalStyles;
    document.head.appendChild(styleSheet);

    // Close modal functionality
    const closeBtn = modalOverlay.querySelector('.blog-modal-close');
    closeBtn.addEventListener('click', () => {
        document.body.removeChild(modalOverlay);
        document.head.removeChild(styleSheet);
    });

    // Close on overlay click
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            document.body.removeChild(modalOverlay);
            document.head.removeChild(styleSheet);
        }
    });
}

// Share post functionality
function sharePost(platform, title, url) {
    const encodedTitle = encodeURIComponent(title);
    const encodedUrl = encodeURIComponent(url);

    let shareUrl = '';

    switch (platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?text=${encodedTitle}&url=${encodedUrl}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${encodedTitle}%20${encodedUrl}`;
            break;
    }

    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

// Clear blog filters
function clearBlogFilters() {
    const searchInput = document.getElementById('blogSearch');
    const categoryFilter = document.getElementById('categoryFilter');

    if (searchInput) searchInput.value = '';
    if (categoryFilter) categoryFilter.value = '';

    currentSearch = '';
    currentCategory = '';
    currentPage = 1;

    // Reload posts
    loadBlogPosts(1, false);
}

// Utility function for notifications (if not already defined)
if (typeof showNotification === 'undefined') {
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 1001;
            animation: slideIn 0.3s ease;
            background: ${type === 'success' ? '#1e753f' : type === 'error' ? '#d14438' : '#f2c97e'};
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
}

// Blog Language Handling
function initializeBlogLanguageHandling() {
    // Listen for language change events
    document.addEventListener('languageChanged', function(event) {
        const newLanguage = event.detail.language;
        console.log('Blog: Language changed to', newLanguage);

        // Reload blog content with new language
        reloadBlogContent();

        // Update translatable elements specific to blog
        updateBlogTranslatableElements();
    });
}

// Reload all blog content when language changes
function reloadBlogContent() {
    // Reset pagination
    currentPage = 1;

    // Reload categories
    loadBlogCategories();

    // Reload featured posts
    loadFeaturedPosts();

    // Reload blog posts
    loadBlogPosts(1, false);
}

// Update blog-specific translatable elements
function updateBlogTranslatableElements() {
    // Get translations for blog-specific elements
    const translations = getBlogTranslations();

    // Update elements with data-translate attributes
    document.querySelectorAll('[data-translate]').forEach(element => {
        const key = element.getAttribute('data-translate');
        if (translations[key]) {
            element.textContent = translations[key];
        }
    });

    // Update placeholders
    document.querySelectorAll('[data-translate-placeholder]').forEach(element => {
        const key = element.getAttribute('data-translate-placeholder');
        if (translations[key]) {
            element.placeholder = translations[key];
        }
    });

    // Update featured badge
    const featuredBadges = document.querySelectorAll('.featured-badge');
    featuredBadges.forEach(badge => {
        badge.textContent = translations.featured || 'Featured';
    });

    // Update read more buttons
    const readMoreBtns = document.querySelectorAll('.read-more-btn .fas + span, .read-more');
    readMoreBtns.forEach(btn => {
        if (btn.textContent.includes('Read') || btn.textContent.includes('Lire') || btn.textContent.includes('Soma')) {
            btn.textContent = translations.read_more || 'Read More';
        }
    });

    // Update load more button
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        const icon = loadMoreBtn.querySelector('i');
        const iconHTML = icon ? icon.outerHTML : '<i class="fas fa-plus"></i>';
        loadMoreBtn.innerHTML = iconHTML + ' ' + (translations.load_more_posts || 'Load More Posts');
    }
}

// Get blog-specific translations
function getBlogTranslations() {
    // Try to get from global language manager first
    if (window.languageManager && typeof window.languageManager.getStaticTranslations === 'function') {
        return window.languageManager.getStaticTranslations();
    }

    // Fallback translations
    const currentLang = getCurrentLanguage();

    const translations = {
        en: {
            featured: 'Featured',
            read_more: 'Read More',
            read_full_article: 'Read Full Article',
            load_more_posts: 'Load More Posts',
            search_blog_posts: 'Search blog posts...',
            all_categories: 'All Categories',
            no_posts_found: 'No blog posts found',
            diocese_blog: 'Diocese Blog',
            stay_updated: 'Stay updated with the latest news, events, and spiritual reflections from the Diocese of Byumba',
            subscribe: 'Subscribe',
            enter_email: 'Enter your email address'
        },
        rw: {
            featured: 'Byagaranzwe',
            read_more: 'Soma Byinshi',
            read_full_article: 'Soma Inyandiko Yose',
            load_more_posts: 'Shyira Andi Makuru',
            search_blog_posts: 'Shakisha amakuru...',
            all_categories: 'Ibyiciro Byose',
            no_posts_found: 'Nta makuru yabonetse',
            diocese_blog: 'Amakuru ya Diyosezi',
            stay_updated: 'Komeza uhabwa amakuru mashya, ibirori, n\'amateka y\'umwuka kuva muri Diyosezi ya Byumba',
            subscribe: 'Iyandikishe',
            enter_email: 'Shyira aderesi yawe ya imeyili'
        },
        fr: {
            featured: 'En Vedette',
            read_more: 'Lire Plus',
            read_full_article: 'Lire l\'Article Complet',
            load_more_posts: 'Charger Plus d\'Articles',
            search_blog_posts: 'Rechercher des articles...',
            all_categories: 'Toutes les Catégories',
            no_posts_found: 'Aucun article trouvé',
            diocese_blog: 'Blog du Diocèse',
            stay_updated: 'Restez informé des dernières nouvelles, événements et réflexions spirituelles du Diocèse de Byumba',
            subscribe: 'S\'abonner',
            enter_email: 'Entrez votre adresse e-mail'
        }
    };

    return translations[currentLang] || translations.en;
}

// Helper function to get current language
function getCurrentLanguage() {
    // Try to get from global language manager
    if (window.languageManager && window.languageManager.currentLanguage) {
        return window.languageManager.currentLanguage;
    }

    // Try to get from document language attribute
    const docLang = document.documentElement.lang;
    if (docLang) {
        return docLang;
    }

    // Default to English
    return 'en';
}
