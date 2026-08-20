<?php
// Include the shared header
include 'includes/header.php';
require_once 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$activity = null;
$recent_activities = [];

if ($id > 0) {
    try {
        if (isset($pdo)) {
            // Fetch current activity details
            $stmt = $pdo->prepare("SELECT * FROM `recent_activities` WHERE `id` = ?");
            $stmt->execute([$id]);
            $activity = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch recent activities for sidebar
            $stmt_rec = $pdo->prepare("SELECT * FROM `recent_activities` WHERE `id` != ? ORDER BY `created_at` DESC LIMIT 5");
            $stmt_rec->execute([$id]);
            $recent_activities = $stmt_rec->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        // Fail silently
    }
}

// Fallback logic if database is empty/offline
if (!$activity) {
    $fallback_data = [
        1 => ['id' => 1, 'title' => 'Morning Programme 2018', 'description' => 'Our beautiful community gathering for morning rituals and prayers during Durga Puja 2018.', 'image' => 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?q=80&w=600'],
        2 => ['id' => 2, 'title' => 'Durga Puja Invitation Card 2021', 'description' => 'The official creative design and release of our Durga Puja invitation cards for 2021.', 'image' => 'https://images.unsplash.com/photo-1502134249126-9f3755a50d78?q=80&w=600'],
        3 => ['id' => 3, 'title' => 'Evening Programme 2018', 'description' => 'Dance dramas, classical songs and folk performances by our community members in 2018.', 'image' => 'https://images.unsplash.com/photo-1536304997881-a372c179924b?q=80&w=600'],
        4 => ['id' => 4, 'title' => 'Dandiya Night 2018', 'description' => 'Vibrant Garba and Dandiya dance events under decorative lighting with delicious foods.', 'image' => 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80&w=600']
    ];
    $activity = isset($fallback_data[$id]) ? $fallback_data[$id] : $fallback_data[1];
    
    foreach ($fallback_data as $fid => $fitem) {
        if ($fid != $activity['id']) {
            $recent_activities[] = $fitem;
        }
    }
}

$act_img = htmlspecialchars($activity['image']);
if (strpos($activity['image'], 'http') !== 0) {
    $act_img = $act_img; // absolute path to file in project root
}
?>

<style>
    .details-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        position: relative;
        overflow: hidden;
    }

    .details-banner::before {
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

    .details-banner-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
    }

    .details-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        line-height: 1.25;
    }

    .details-meta {
        font-size: 0.9rem;
        color: var(--gold);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .details-sec {
        padding: 5.5rem 0;
        background-color: var(--primary-bg);
    }

    .details-container {
        display: grid;
        grid-template-columns: 8fr 4fr;
        gap: 3.5rem;
        align-items: start;
    }

    .main-details-content {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        padding: 2.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .details-cover-box {
        width: 100%;
        max-height: 480px;
        border-radius: var(--border-radius);
        overflow: hidden;
        margin-bottom: 2.2rem;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }

    .details-cover-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .details-body-text {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
    }

    .details-body-text p {
        margin-bottom: 1.5rem;
    }

    .btn-back-activities {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--red);
        font-family: var(--font-headings);
        font-weight: 700;
        text-decoration: none;
        margin-top: 2rem;
        transition: var(--transition);
        font-size: 0.95rem;
        border: 1px solid var(--border-color);
        padding: 0.6rem 1.2rem;
        border-radius: var(--border-radius);
    }

    .btn-back-activities:hover {
        background-color: rgba(139, 30, 30, 0.03);
        color: var(--gold);
        border-color: var(--gold);
        transform: translateX(-4px);
    }

    /* Sticky Sidebar Styles */
    .details-sidebar {
        position: sticky;
        top: 100px;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .sidebar-widget {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        padding: 1.75rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .widget-title {
        font-family: var(--font-headings);
        font-size: 1.25rem;
        color: var(--dark);
        margin-bottom: 1.25rem;
        border-bottom: 2px solid var(--sand);
        padding-bottom: 0.5rem;
    }

    .sidebar-activity-list {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    .sidebar-activity-item {
        display: flex;
        gap: 0.85rem;
        text-decoration: none;
        align-items: center;
        transition: var(--transition);
    }

    .sidebar-activity-thumb {
        width: 65px;
        height: 65px;
        border-radius: 50%; /* circular thumbnails in sidebar too! */
        overflow: hidden;
        border: 2px solid var(--white);
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        flex-shrink: 0;
        background-color: var(--secondary-bg);
    }

    .sidebar-activity-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition-slow);
    }

    .sidebar-activity-info {
        flex-grow: 1;
    }

    .sidebar-activity-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1.3;
        margin-bottom: 0.2rem;
        transition: var(--transition);
    }

    .sidebar-activity-date {
        font-size: 0.78rem;
        color: var(--text-muted);
    }

    .sidebar-activity-item:hover .sidebar-activity-title {
        color: var(--red);
    }

    .sidebar-activity-item:hover .sidebar-activity-thumb img {
        transform: scale(1.08);
    }

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .details-container {
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }
        .details-sidebar {
            position: static;
        }
    }
</style>

<!-- Banner Header -->
<section class="details-banner">
    <div class="container">
        <div class="details-banner-content">
            <span class="details-meta">Recent Activity / Program Detail</span>
            <h1 class="details-title"><?php echo htmlspecialchars($activity['title']); ?></h1>
        </div>
    </div>
</section>

<!-- Content Details Section -->
<section class="details-sec">
    <div class="container">
        <div class="details-container">
            <!-- Left Column: Cover and Description -->
            <div class="main-details-content">
                <div class="details-cover-box">
                    <img src="<?php echo $act_img; ?>" alt="<?php echo htmlspecialchars($activity['title']); ?>" class="details-cover-img">
                </div>
                
                <div class="details-body-text">
                    <?php 
                    $paragraphs = explode("\n", $activity['description']);
                    foreach ($paragraphs as $para) {
                        $trimmed = trim($para);
                        if (!empty($trimmed)) {
                            echo '<p>' . htmlspecialchars($trimmed) . '</p>';
                        }
                    }
                    ?>
                </div>

                <?php if (!empty($activity['additional_images'])): ?>
                    <div class="activity-gallery-section" style="margin-top: 3.5rem;">
                        <h3 style="font-family: var(--font-headings); font-size: 1.6rem; color: var(--dark); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
                            <i class="fa-solid fa-images" style="color: var(--red);"></i> Story Gallery
                        </h3>
                        <div class="activity-gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.2rem;">
                            <?php
                            $gallery_images = array_filter(explode(',', $activity['additional_images']));
                            foreach ($gallery_images as $g_path):
                                $g_src = htmlspecialchars(trim($g_path));
                                ?>
                                <div class="activity-gallery-item" style="border-radius: var(--border-radius); overflow: hidden; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); cursor: pointer; height: 160px; transition: var(--transition);">
                                    <img src="<?php echo $g_src; ?>" class="activity-gallery-img" style="width: 100%; height: 100%; object-fit: cover; transition: var(--transition-slow);" alt="Gallery Image" loading="lazy">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Lightbox modal for Activity Details -->
                <div class="activity-lightbox" id="activity-lightbox" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(33, 26, 23, 0.95); backdrop-filter: blur(5px); z-index: 20000; display: none; align-items: center; justify-content: center; padding: 2rem;">
                    <button id="activity-lightbox-close" style="position: absolute; top: 25px; right: 25px; background-color: var(--white); border: none; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.25rem; color: var(--dark); box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: var(--transition);"><i class="fa-solid fa-xmark"></i></button>
                    
                    <button id="activity-lightbox-prev" class="activity-lightbox-nav" style="position: absolute; left: 30px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--white); font-size: 2.5rem; cursor: pointer; opacity: 0.6; transition: var(--transition); z-index: 20001;"><i class="fa-solid fa-chevron-left"></i></button>
                    
                    <img src="" id="activity-lightbox-img" style="max-width: 90%; max-height: 85vh; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 2px solid var(--white); object-fit: contain;">
                    
                    <button id="activity-lightbox-next" class="activity-lightbox-nav" style="position: absolute; right: 30px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--white); font-size: 2.5rem; cursor: pointer; opacity: 0.6; transition: var(--transition); z-index: 20001;"><i class="fa-solid fa-chevron-right"></i></button>
                </div>

                <style>
                    .activity-gallery-item:hover {
                        transform: translateY(-4px);
                        box-shadow: var(--shadow-md);
                        border-color: var(--gold);
                    }
                    .activity-gallery-item:hover .activity-gallery-img {
                        transform: scale(1.06);
                    }
                    #activity-lightbox-close:hover {
                        background-color: var(--red);
                        color: var(--white);
                    }
                    .activity-lightbox-nav:hover {
                        opacity: 1;
                        color: var(--gold);
                    }
                    
                    @media (max-width: 768px) {
                        .activity-lightbox-nav {
                            font-size: 1.8rem;
                        }
                        #activity-lightbox-prev { left: 15px; }
                        #activity-lightbox-next { right: 15px; }
                    }
                </style>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const galleryItems = document.querySelectorAll('.activity-gallery-item');
                    const lightbox = document.getElementById('activity-lightbox');
                    const lightboxImg = document.getElementById('activity-lightbox-img');
                    const lightboxClose = document.getElementById('activity-lightbox-close');
                    const lightboxPrev = document.getElementById('activity-lightbox-prev');
                    const lightboxNext = document.getElementById('activity-lightbox-next');

                    let galleryImages = [];
                    let currentGalleryIndex = 0;

                    // Initialize gallery image source list
                    const imgElements = document.querySelectorAll('.activity-gallery-img');
                    imgElements.forEach((img, idx) => {
                        galleryImages.push(img.src);
                        img.closest('.activity-gallery-item').addEventListener('click', function() {
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

                <a href="activities.php" class="btn-back-activities">
                    <i class="fa-solid fa-arrow-left"></i> Back to All Activities
                </a>
            </div>

            <!-- Right Column: Sticky Sidebar -->
            <div class="details-sidebar">
                <div class="sidebar-widget">
                    <h3 class="widget-title">Other Activities</h3>
                    <div class="sidebar-activity-list">
                        <?php if (empty($recent_activities)): ?>
                            <p style="font-size: 0.9rem; color: var(--text-muted); font-style: italic;">No other activities found.</p>
                        <?php else: ?>
                            <?php foreach ($recent_activities as $rec): ?>
                                <?php 
                                $rec_img = htmlspecialchars($rec['image']);
                                if (strpos($rec['image'], 'http') !== 0) {
                                    $rec_img = $rec_img;
                                }
                                ?>
                                <a href="activity-details.php?id=<?php echo $rec['id']; ?>" class="sidebar-activity-item">
                                    <div class="sidebar-activity-thumb">
                                        <img src="<?php echo $rec_img; ?>" alt="<?php echo htmlspecialchars($rec['title']); ?>" loading="lazy">
                                    </div>
                                    <div class="sidebar-activity-info">
                                        <h4 class="sidebar-activity-title"><?php echo htmlspecialchars($rec['title']); ?></h4>
                                        <span class="sidebar-activity-date"><i class="fa-regular fa-calendar" style="color: var(--gold); margin-right: 0.2rem;"></i> <?php echo date('d M Y', strtotime($rec['created_at'])); ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
