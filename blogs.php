<?php
// Include the shared header
include 'includes/header.php';
require_once 'config.php';

// Blogs fallback static database in case DB is offline/empty
$featured_blog_fallback = [
    'id' => 1,
    'image' => 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?q=80&w=1000',
    'category' => 'HERITAGE',
    'date' => 'July 15, 2026',
    'title' => 'Preserving Bengali Culture in Modern Times',
    'excerpt' => 'How diaspora communities keep traditional language, literature, and art forms alive for the next generation.',
    'content' => 'Living away from Bengal, diaspora families face the unique challenge of keeping their children connected to their linguistic and cultural roots. The Bengali Cultural Association addresses this by running weekly language schools, hosting classical music workshops, and creating platforms for children to perform on stage. In this article, we explore the methods parents use—such as celebrating festivals at home, playing Rabindra Sangeet, and preparing traditional cuisines—to foster a sense of identity and pride in their heritage.'
];

$standard_blogs_fallback = [
    [
        'id' => 2,
        'image' => 'https://images.unsplash.com/photo-1502134249126-9f3755a50d78?q=80&w=600',
        'category' => 'CULTURE',
        'date' => 'October 02, 2026',
        'title' => 'The Significance of Durga Puja Artistry',
        'excerpt' => 'Exploring the craft of clay sculpting and decorative sholapith art in pandal making.',
        'content' => 'Durga Puja is not just a religious event; it is an open-air art gallery. The creation of the clay idols of Goddess Durga and her children is a sacred, centuries-old art form passed down through generations. Artisans in Kumartuli, Kolkata, spend months shaping clay from the Hooghly River over straw frames. Additionally, the ornate pandals showcase traditional crafts like sholapith (pith wood carvings), terracotta tiles, and intricate lighting decorations. Maintaining these art forms in our community celebrations is vital to honoring the craftsmen.'
    ],
    [
        'id' => 3,
        'image' => 'https://images.unsplash.com/photo-1536304997881-a372c179924b?q=80&w=600',
        'category' => 'FESTIVALS',
        'date' => 'April 10, 2026',
        'title' => 'Celebrating Poila Boishakh Together',
        'excerpt' => 'The rich history behind Nababarsho and how we can celebrate it with environmental awareness.',
        'content' => 'Poila Boishakh, the first day of the Bengali calendar, is celebrated with joy, new clothing, and gatherings. Historically linked to agricultural tax collections under Emperor Akbar, it marks a fresh start. In modern times, the festival is a beautiful showcase of folk singing, Rabindra Sangeet, and traditional food. As an association, we promote eco-friendly celebrations by using clay serving pots (bhar), paper decorations, and planting trees to welcome the new year.'
    ],
    [
        'id' => 4,
        'image' => 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80&w=600',
        'category' => 'CULTURE',
        'date' => 'May 25, 2026',
        'title' => 'Our Cultural Journey: 25 Years of Memories',
        'excerpt' => 'A reflective essay on how our community grew from a dozen families to a landmark association.',
        'content' => 'Reflecting on our 25-year journey, we are filled with gratitude. What began in 2001 as a small drawing-room gathering of Bengali families has transformed into a registered cultural institution. We have hosted musical icons, organized massive food festivals, funded scholarships, and built a library of Bengali literature. Our success is built entirely on the tireless hours of volunteers and the generous subscriptions of our members. Here is to another 25 years of harmony, art, and service.'
    ]
];

// Fetch all blogs and categories from database
$all_blogs = [];
$blog_categories = [];
$total_blog_posts = 0;
$category_counts = [];

try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT * FROM `blogs` ORDER BY `date` DESC");
        $db_blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($db_blogs)) {
            foreach ($db_blogs as $db_b) {
                $b = $db_b;
                $b['full'] = $b['content'];
                $b['date'] = date('F d, Y', strtotime($b['date']));
                $all_blogs[] = $b;
            }
        }
        
        $blog_categories = $pdo->query("SELECT * FROM `blog_categories` ORDER BY `name` ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Fail silently
}

// Fallback logic if database is empty
if (empty($all_blogs)) {
    $fallback_featured = $featured_blog_fallback;
    $fallback_featured['full'] = $fallback_featured['content'];
    $all_blogs[] = $fallback_featured;
    foreach ($standard_blogs_fallback as $b) {
        $b['full'] = $b['content'];
        $all_blogs[] = $b;
    }
}

if (empty($blog_categories)) {
    $blog_categories = [
        ['name' => 'HERITAGE'],
        ['name' => 'CULTURE'],
        ['name' => 'FESTIVALS']
    ];
}

$total_blog_posts = count($all_blogs);
foreach ($all_blogs as $b) {
    $catLower = strtolower($b['category']);
    if (!isset($category_counts[$catLower])) {
        $category_counts[$catLower] = 0;
    }
    $category_counts[$catLower]++;
}
?>

<style>
    /* ==========================================================================
       BLOGS PAGE SPECIFIC STYLES
       ========================================================================== */
    .blog-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .blog-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(circle at 20% 50%, rgba(201, 154, 46, 0.15) 0%, transparent 50%),
                          radial-gradient(circle at 80% 50%, rgba(200, 59, 45, 0.15) 0%, transparent 50%);
        z-index: 1;
    }

    .blog-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .blog-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .blog-sec {
        padding: 6.5rem 0;
        background-color: var(--primary-bg);
    }

    /* Filter pills container at the top */
    .blog-filters {
        display: flex;
        justify-content: center;
        gap: 0.8rem;
        margin-bottom: 4rem;
        flex-wrap: wrap;
        background-color: var(--sand);
        padding: 1.5rem;
        border-radius: var(--border-radius-lg);
        border: 1px solid var(--border-color);
    }

    .blog-filter-btn {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        padding: 0.5rem 1.2rem;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--dark);
        border-radius: 30px;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-family: 'Outfit', sans-serif;
    }

    .blog-filter-btn:hover {
        border-color: var(--gold);
        color: var(--gold);
    }

    .blog-filter-btn.active {
        background-color: #BFDBFE !important;
        color: #1E3A8A !important;
        border-color: #93C5FD !important;
    }

    .blog-filter-btn.active i {
        color: #1E3A8A !important;
    }

    /* Grid layout for Blog Cards */
    .blog-list-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }

    .blog-item-card {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        transition: var(--transition-slow);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .blog-item-card.hide {
        display: none;
    }

    .blog-item-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(201, 154, 46, 0.4);
    }

    .blog-item-img-box {
        height: 220px;
        background-color: var(--secondary-bg);
        overflow: hidden;
    }

    .blog-item-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition-slow);
    }

    .blog-item-card:hover .blog-item-img {
        transform: scale(1.06);
    }

    .blog-item-body {
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    /* Dark category badge layout */
    .blog-item-cat-badge {
        background-color: var(--dark);
        color: var(--white);
        font-size: 0.68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.2rem 0.6rem;
        border-radius: 4px;
        display: inline-block;
        margin-bottom: 0.8rem;
        width: fit-content;
    }

    .blog-item-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
    }

    .blog-item-meta span {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .blog-item-meta i {
        color: var(--gold);
    }

    .blog-item-title {
        font-size: 1.3rem;
        margin-bottom: 0.75rem;
        line-height: 1.4;
        color: var(--dark);
        font-weight: 700;
    }

    .blog-item-excerpt {
        font-size: 0.9rem;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* Expandable Content Panel */
    .blog-expand-box {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.6s cubic-bezier(0, 1, 0, 1);
    }

    .blog-expand-inner {
        padding-top: 1.5rem;
        margin-top: 1.5rem;
        border-top: 1px dashed var(--border-color);
        font-size: 0.95rem;
        line-height: 1.7;
        color: var(--text-muted);
    }

    .blog-expand-btn {
        background: none;
        border: none;
        color: var(--red);
        font-family: var(--font-headings);
        font-weight: 700;
        cursor: pointer;
        padding: 0;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: auto;
        padding-top: 1.5rem;
        font-size: 0.9rem;
        transition: var(--transition);
        width: fit-content;
    }

    .blog-expand-btn:hover {
        color: var(--gold);
    }

    .blog-expand-btn i {
        font-size: 0.8rem;
        transition: transform 0.3s ease;
    }

    .blog-item-card.expanded .blog-expand-btn i {
        transform: rotate(180deg);
    }

    /* Empty state */
    .blog-empty {
        display: none;
        text-align: center;
        padding: 4rem 2rem;
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        width: 100%;
        grid-column: span 3;
    }

    .blog-empty i {
        font-size: 3rem;
        color: var(--gold);
        margin-bottom: 1.5rem;
    }

    .blog-empty h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .blog-empty p {
        color: var(--text-muted);
    }

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .blog-list-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .blog-empty {
            grid-column: span 2;
        }
    }

    @media (max-width: 768px) {
        .blog-list-grid {
            grid-template-columns: 1fr;
            max-width: 420px;
            margin: 0 auto;
        }
    /* Client-side Pagination Styles */
    .blog-pagination-btn {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        padding: 0.6rem 1.1rem;
        border-radius: var(--border-radius);
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .blog-pagination-btn:hover {
        border-color: var(--red);
        color: var(--red);
        background-color: rgba(139, 30, 30, 0.02);
    }

    .blog-pagination-btn.active {
        background-color: var(--red);
        border-color: var(--red);
        color: var(--white);
        box-shadow: 0 4px 10px rgba(139, 30, 30, 0.15);
    }

    .blog-pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
</style>

<!-- Banner Header -->
<section class="blog-banner">
    <div class="container">
        <h1 class="blog-banner-title">Community Blog</h1>
        <span class="blog-banner-subtitle">Stories, Traditions & Personal Essays</span>
    </div>
</section>

<!-- Blog Main Section -->
<section class="blog-sec">
    <div class="container">
        
        <!-- Dynamic Blog Category Filters -->
        <div class="blog-filters">
            <button class="blog-filter-btn active" data-filter="all">
                <i class="fa-solid fa-book-open"></i> ALL STORIES (<?php echo $total_blog_posts; ?>)
            </button>
            <?php foreach ($blog_categories as $cat): 
                $catNameLower = strtolower($cat['name']);
                $count = isset($category_counts[$catNameLower]) ? $category_counts[$catNameLower] : 0;
            ?>
                <button class="blog-filter-btn" data-filter="<?php echo htmlspecialchars($cat['name']); ?>">
                    <i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($cat['name']); ?> (<?php echo $count; ?>)
                </button>
            <?php endforeach; ?>
        </div>

        <!-- UNIFIED BLOG LIST GRID -->
        <div class="blog-list-grid">
            <?php foreach ($all_blogs as $post): 
                $word_count = str_word_count(strip_tags($post['full']));
                $read_time = max(1, ceil($word_count / 180));
            ?>
                <div class="blog-item-card" data-category="<?php echo htmlspecialchars($post['category']); ?>">
                    <div class="blog-item-img-box">
                        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="blog-item-img" loading="lazy">
                    </div>
                    <div class="blog-item-body">
                        <!-- Dark category tag badge -->
                        <span class="blog-item-cat-badge"><?php echo htmlspecialchars($post['category']); ?></span>
                        
                        <div class="blog-item-meta">
                            <span><i class="fa-regular fa-calendar"></i> <?php echo htmlspecialchars($post['date']); ?></span>
                            <span><i class="fa-regular fa-clock"></i> <?php echo $read_time; ?> Min Read</span>
                        </div>
                        
                        <h3 class="blog-item-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p class="blog-item-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>

                        <a href="blog-details.php?id=<?php echo $post['id']; ?>" class="blog-expand-btn">
                            <span>Read Article</span>
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Fallback Empty State Card -->
            <div class="blog-empty" id="blog-empty-card">
                <i class="fa-solid fa-feather-pointed"></i>
                <h3>No Articles Found</h3>
                <p>There are no blog posts published under this category yet. Check back later!</p>
            </div>
        </div>

        <!-- Client-side Pagination Controls -->
        <div class="blog-pagination" id="blog-pagination" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 3.5rem;"></div>
    </div>
</section>

<!-- Vanilla JS Filters & Expanders -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Category Filtering & Pagination Setup
        const filterBtns = document.querySelectorAll('.blog-filter-btn');
        const blogCards = document.querySelectorAll('.blog-item-card');
        const emptyCard = document.getElementById('blog-empty-card');
        const paginationContainer = document.getElementById('blog-pagination');

        let activeFilter = 'all';
        let currentPage = 1;
        const limitPerPage = 12; // 4 rows of 3 columns on desktop

        function paginateBlogs() {
            // Get all matching cards for current category filter
            const filteredCards = [];
            blogCards.forEach(card => {
                const cat = card.getAttribute('data-category');
                if (activeFilter === 'all' || (cat && cat.toLowerCase() === activeFilter.toLowerCase())) {
                    filteredCards.push(card);
                } else {
                    card.style.display = 'none';
                    card.classList.add('hide');
                }
            });

            const totalItems = filteredCards.length;
            const totalPages = Math.ceil(totalItems / limitPerPage);

            if (totalItems === 0) {
                emptyCard.style.display = 'block';
                paginationContainer.style.display = 'none';
                return;
            } else {
                emptyCard.style.display = 'none';
            }

            // Keep currentPage within bounds
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            // Show cards for the current page, hide others
            filteredCards.forEach((card, index) => {
                const start = (currentPage - 1) * limitPerPage;
                const end = start + limitPerPage;
                if (index >= start && index < end) {
                    card.style.display = 'flex';
                    card.classList.remove('hide');
                } else {
                    card.style.display = 'none';
                    card.classList.add('hide');
                }
            });

            // Render pagination buttons
            renderPaginationControls(totalPages);
        }

        function renderPaginationControls(totalPages) {
            paginationContainer.innerHTML = '';
            if (totalPages <= 1) {
                paginationContainer.style.display = 'none';
                return;
            }
            paginationContainer.style.display = 'flex';

            // Previous Button
            const prevBtn = document.createElement('button');
            prevBtn.className = 'blog-pagination-btn';
            prevBtn.innerHTML = '<i class="fa-solid fa-angle-left"></i> Prev';
            prevBtn.disabled = currentPage === 1;
            prevBtn.addEventListener('click', () => {
                currentPage--;
                paginateBlogs();
                document.querySelector('.blog-filters').scrollIntoView({ behavior: 'smooth' });
            });
            paginationContainer.appendChild(prevBtn);

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = 'blog-pagination-btn';
                if (i === currentPage) {
                    pageBtn.classList.add('active');
                }
                pageBtn.innerText = i;
                pageBtn.addEventListener('click', () => {
                    currentPage = i;
                    paginateBlogs();
                    document.querySelector('.blog-filters').scrollIntoView({ behavior: 'smooth' });
                });
                paginationContainer.appendChild(pageBtn);
            }

            // Next Button
            const nextBtn = document.createElement('button');
            nextBtn.className = 'blog-pagination-btn';
            nextBtn.innerHTML = 'Next <i class="fa-solid fa-angle-right"></i>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.addEventListener('click', () => {
                currentPage++;
                paginateBlogs();
                document.querySelector('.blog-filters').scrollIntoView({ behavior: 'smooth' });
            });
            paginationContainer.appendChild(nextBtn);
        }

        // Initialize pagination
        paginateBlogs();

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                activeFilter = this.getAttribute('data-filter');
                currentPage = 1; // Reset to page 1 on category switch
                paginateBlogs();
            });
        });
    });
</script>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
