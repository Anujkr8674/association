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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$post = null;
$recent_posts = [];

// 1. Fetch current blog post
try {
    if (isset($pdo)) {
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT * FROM `blogs` WHERE `id` = ?");
            $stmt->execute([$id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Fetch recent posts
        $stmt_recent = $pdo->query("SELECT * FROM `blogs` ORDER BY `date` DESC LIMIT 6");
        $db_recents = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);
        foreach ($db_recents as $db_r) {
            $r = $db_r;
            $r['date'] = date('M d, Y', strtotime($r['date']));
            $recent_posts[] = $r;
        }
    }
} catch (PDOException $e) {
    // Fail silently
}

// Fallback current post if empty or not found in DB
if (empty($post)) {
    // Attempt to match static items
    if ($id === 1) {
        $post = $featured_blog_fallback;
    } else {
        foreach ($standard_blogs_fallback as $item) {
            if ($item['id'] === $id) {
                $post = $item;
                break;
            }
        }
    }
    // Default fallback if no ID matched
    if (empty($post)) {
        $post = $featured_blog_fallback;
    }
    $post['date'] = date('F d, Y', strtotime($post['date']));
} else {
    $post['date'] = date('F d, Y', strtotime($post['date']));
}

// Fallback recents if empty
if (empty($recent_posts)) {
    $fallback_featured = $featured_blog_fallback;
    $fallback_featured['date'] = date('M d, Y', strtotime($fallback_featured['date']));
    $recent_posts[] = $fallback_featured;
    foreach ($standard_blogs_fallback as $item) {
        $item['date'] = date('M d, Y', strtotime($item['date']));
        $recent_posts[] = $item;
    }
}
?>

<style>
    /* ==========================================================================
       BLOG DETAILS PAGE SPECIFIC STYLES
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
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1.2rem;
        position: relative;
        z-index: 2;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.3;
        padding: 0 1rem;
    }

    .blog-banner-subtitle {
        font-size: 0.88rem;
        color: var(--gold);
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.2rem;
        flex-wrap: wrap;
    }

    .blog-banner-subtitle span {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .blog-details-sec {
        padding: 5rem 0;
        background-color: var(--primary-bg);
    }

    .blog-details-grid {
        display: grid;
        grid-template-columns: 2.2fr 1fr;
        gap: 3.5rem;
    }

    /* Left column content styling */
    .blog-details-main-img {
        width: 100%;
        max-height: 520px;
        object-fit: cover;
        border-radius: var(--border-radius-lg);
        margin-bottom: 3rem;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
    }

    .blog-details-content p {
        font-size: 1.08rem;
        line-height: 1.85;
        margin-bottom: 1.6rem;
        color: var(--dark);
        text-align: justify;
    }

    .blog-details-content p:first-of-type {
        font-size: 1.25rem;
        color: var(--red);
        font-weight: 500;
        line-height: 1.75;
        text-align: left;
    }

    /* Right column sticky sidebar */
    .blog-details-sidebar {
        position: sticky;
        top: 110px;
        height: fit-content;
    }

    .sidebar-card {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        border: 1px solid var(--border-color);
        padding: 2.2rem;
        box-shadow: var(--shadow-sm);
    }

    .sidebar-card-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1.8rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 2px solid var(--gold);
        padding-bottom: 0.6rem;
    }

    .sidebar-card-title i {
        color: var(--red);
        font-size: 1.1rem;
    }

    .recent-post-list {
        display: flex;
        flex-direction: column;
        gap: 1.4rem;
    }

    .recent-post-item {
        display: grid;
        grid-template-columns: 64px 1fr;
        gap: 1.1rem;
        align-items: center;
    }

    .recent-post-thumb {
        width: 64px;
        height: 52px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background-color: var(--secondary-bg);
    }

    .recent-post-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .recent-post-info-title {
        font-size: 0.9rem;
        font-weight: 700;
        line-height: 1.35;
        color: var(--dark);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: var(--transition);
        text-decoration: none;
    }

    .recent-post-info-title:hover {
        color: var(--red);
    }

    .recent-post-info-date {
        font-size: 0.76rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .recent-post-info-date i {
        color: var(--gold);
        font-size: 0.7rem;
    }

    .btn-back-blogs {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--red);
        font-family: var(--font-headings);
        font-weight: 700;
        font-size: 1.05rem;
        margin-top: 3rem;
        transition: var(--transition);
        text-decoration: none;
    }

    .btn-back-blogs:hover {
        color: var(--gold);
        transform: translateX(-4px);
    }

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .blog-details-grid {
            grid-template-columns: 1fr;
            gap: 4rem;
        }
        .blog-details-sidebar {
            position: relative;
            top: 0;
        }
        .blog-details-main-img {
            margin-bottom: 2rem;
        }
    }
</style>

<!-- Banner Header -->
<section class="blog-banner">
    <div class="container">
        <h1 class="blog-banner-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="blog-banner-subtitle">
            <span><i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($post['category']); ?></span>
            <span><i class="fa-regular fa-calendar"></i> <?php echo htmlspecialchars($post['date']); ?></span>
            <?php if (!empty($post['author'])): ?>
                <span><i class="fa-regular fa-user"></i> By <?php echo htmlspecialchars($post['author']); ?></span>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Blog Details Content Section -->
<section class="blog-details-sec">
    <div class="container">
        <div class="blog-details-grid">
            <!-- Left Column: Main Cover Image and Paragraph content -->
            <div class="blog-details-left">
                <?php if (!empty($post['image'])): ?>
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="blog-details-main-img">
                <?php endif; ?>
                
                <div class="blog-details-content">
                    <?php
                    $paragraphs = explode("\n", $post['content']);
                    foreach ($paragraphs as $p) {
                        $p = trim($p);
                        if (!empty($p)) {
                            echo "<p>" . htmlspecialchars($p) . "</p>";
                        }
                    }
                    ?>
                </div>

                <?php if (!empty($post['additional_images'])): ?>
                    <div class="blog-gallery-section" style="margin-top: 3.5rem;">
                        <h3 style="font-family: var(--font-headings); font-size: 1.6rem; color: var(--dark); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
                            <i class="fa-solid fa-images" style="color: var(--red);"></i> Story Gallery
                        </h3>
                        <div class="blog-gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.2rem;">
                            <?php
                            $gallery_images = array_filter(explode(',', $post['additional_images']));
                            foreach ($gallery_images as $g_path):
                                $g_src = htmlspecialchars(trim($g_path));
                                ?>
                                <div class="blog-gallery-item" style="border-radius: var(--border-radius); overflow: hidden; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); cursor: pointer; height: 160px; transition: var(--transition);">
                                    <img src="<?php echo $g_src; ?>" class="blog-gallery-img" style="width: 100%; height: 100%; object-fit: cover; transition: var(--transition-slow);" alt="Gallery Image" loading="lazy">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Lightbox modal for Blog Details -->
                <div class="blog-lightbox" id="blog-lightbox" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(33, 26, 23, 0.95); backdrop-filter: blur(5px); z-index: 20000; display: none; align-items: center; justify-content: center; padding: 2rem;">
                    <button id="blog-lightbox-close" style="position: absolute; top: 25px; right: 25px; background-color: var(--white); border: none; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.25rem; color: var(--dark); box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: var(--transition);"><i class="fa-solid fa-xmark"></i></button>
                    
                    <button id="blog-lightbox-prev" class="blog-lightbox-nav" style="position: absolute; left: 30px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--white); font-size: 2.5rem; cursor: pointer; opacity: 0.6; transition: var(--transition); z-index: 20001;"><i class="fa-solid fa-chevron-left"></i></button>
                    
                    <img src="" id="blog-lightbox-img" style="max-width: 90%; max-height: 85vh; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 2px solid var(--white); object-fit: contain;">
                    
                    <button id="blog-lightbox-next" class="blog-lightbox-nav" style="position: absolute; right: 30px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--white); font-size: 2.5rem; cursor: pointer; opacity: 0.6; transition: var(--transition); z-index: 20001;"><i class="fa-solid fa-chevron-right"></i></button>
                </div>

                <style>
                    .blog-gallery-item:hover {
                        transform: translateY(-4px);
                        box-shadow: var(--shadow-md);
                        border-color: var(--gold);
                    }
                    .blog-gallery-item:hover .blog-gallery-img {
                        transform: scale(1.06);
                    }
                    #blog-lightbox-close:hover {
                        background-color: var(--red);
                        color: var(--white);
                    }
                    .blog-lightbox-nav:hover {
                        opacity: 1;
                        color: var(--gold);
                    }
                    
                    @media (max-width: 768px) {
                        .blog-lightbox-nav {
                            font-size: 1.8rem;
                        }
                        #blog-lightbox-prev { left: 15px; }
                        #blog-lightbox-next { right: 15px; }
                    }
                </style>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const galleryItems = document.querySelectorAll('.blog-gallery-item');
                    const lightbox = document.getElementById('blog-lightbox');
                    const lightboxImg = document.getElementById('blog-lightbox-img');
                    const lightboxClose = document.getElementById('blog-lightbox-close');
                    const lightboxPrev = document.getElementById('blog-lightbox-prev');
                    const lightboxNext = document.getElementById('blog-lightbox-next');

                    let galleryImages = [];
                    let currentGalleryIndex = 0;

                    // Initialize gallery image source list
                    const imgElements = document.querySelectorAll('.blog-gallery-img');
                    imgElements.forEach((img, idx) => {
                        galleryImages.push(img.src);
                        img.closest('.blog-gallery-item').addEventListener('click', function() {
                            currentGalleryIndex = idx;
                            openLightbox(idx);
                        });
                    });

                    function openLightbox(index) {
                        if (index < 0 || index >= galleryImages.length) return;
                        lightboxImg.src = galleryImages[index];
                        lightbox.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                    }

                    function closeLightbox() {
                        lightbox.style.display = 'none';
                        document.body.style.overflow = '';
                    }

                    function nextImage() {
                        if (galleryImages.length === 0) return;
                        currentGalleryIndex = (currentGalleryIndex + 1) % galleryImages.length;
                        openLightbox(currentGalleryIndex);
                    }

                    function prevImage() {
                        if (galleryImages.length === 0) return;
                        currentGalleryIndex = (currentGalleryIndex - 1 + galleryImages.length) % galleryImages.length;
                        openLightbox(currentGalleryIndex);
                    }

                    if (lightboxClose) {
                        lightboxClose.addEventListener('click', closeLightbox);
                    }
                    if (lightboxPrev) {
                        lightboxPrev.addEventListener('click', prevImage);
                    }
                    if (lightboxNext) {
                        lightboxNext.addEventListener('click', nextImage);
                    }

                    if (lightbox) {
                        lightbox.addEventListener('click', function(e) {
                            if (e.target === lightbox) {
                                closeLightbox();
                            }
                        });
                    }

                    document.addEventListener('keydown', function(e) {
                        if (lightbox.style.display === 'flex') {
                            if (e.key === 'Escape') closeLightbox();
                            if (e.key === 'ArrowRight') nextImage();
                            if (e.key === 'ArrowLeft') prevImage();
                        }
                    });
                });
                </script>

                <a href="blogs.php" class="btn-back-blogs">
                    <i class="fa-solid fa-arrow-left"></i> Back to All Stories
                </a>
            </div>

            <!-- Right Column: Sticky Recent Posts Sidebar -->
            <aside class="blog-details-right">
                <div class="blog-details-sidebar">
                    <div class="sidebar-card">
                        <h4 class="sidebar-card-title"><i class="fa-solid fa-book-bookmark"></i> Recent Web Posts</h4>
                        <div class="recent-post-list">
                            <?php foreach ($recent_posts as $rp): ?>
                                <div class="recent-post-item">
                                    <img src="<?php echo htmlspecialchars($rp['image']); ?>" alt="thumbnail" class="recent-post-thumb">
                                    <div class="recent-post-info">
                                        <a href="blog-details.php?id=<?php echo $rp['id']; ?>" class="recent-post-info-title">
                                            <?php echo htmlspecialchars($rp['title']); ?>
                                        </a>
                                        <span class="recent-post-info-date">
                                            <i class="fa-regular fa-calendar"></i> <?php echo htmlspecialchars($rp['date']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
