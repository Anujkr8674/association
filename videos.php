<?php
// Include the shared header
include 'includes/header.php';
require_once 'config.php';

// Helper function to extract YouTube ID
function get_youtube_video_id($url) {
    $video_id = '';
    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
        $video_id = $match[1];
    }
    return $video_id;
}

// Fallback static list of videos
$videos_fallback = [
    ['title' => 'Sindur Khela on Dashami celebration', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
    ['title' => 'Anjali and Evening Aarti Highlights', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
    ['title' => 'Dhunuchi Dance Competition 2026', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
    ['title' => 'Bengali Cultural Drama Performance', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
    ['title' => 'Rabindra Sangeet & Recital Tribute', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
    ['title' => 'Annual Picnic & Sports Highlights', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']
];

// Fetch all video items from database with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 12; // 3 rows of 4 columns
$offset = ($page - 1) * $limit;
$total_rows = 0;
$total_pages = 1;
$video_items = [];

try {
    if (isset($pdo)) {
        $total_rows = $pdo->query("SELECT COUNT(*) FROM `testimonial_videos`")->fetchColumn();
        $total_pages = max(1, ceil($total_rows / $limit));

        $stmt = $pdo->prepare("SELECT * FROM `testimonial_videos` ORDER BY `created_at` DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $video_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Fail silently
}

if (empty($video_items) && $page == 1) {
    $video_items = $videos_fallback;
    $total_pages = 1;
}
?>

<style>
    /* ==========================================================================
       VIDEOS PAGE SPECIFIC STYLES
       ========================================================================== */
    .vid-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .vid-banner::before {
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

    .vid-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .vid-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .vid-sec {
        padding: 6.5rem 0;
        background-color: var(--primary-bg);
    }

    .vid-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-bottom: 3.5rem;
    }

    @media (max-width: 991px) {
        .vid-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .vid-grid {
            grid-template-columns: 1fr;
            max-width: 380px;
            margin: 0 auto 3.5rem auto;
        }
    }

    .vid-card {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        cursor: pointer;
        transition: var(--transition-slow);
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }

    .vid-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(201, 154, 46, 0.3);
    }

    .vid-img-wrapper {
        position: relative;
        height: 200px;
        background-color: #000;
        overflow: hidden;
    }

    .vid-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.85;
        transition: var(--transition-slow);
    }

    .vid-card:hover .vid-img {
        transform: scale(1.06);
        opacity: 0.95;
    }

    .vid-play-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(33, 26, 23, 0.15);
        transition: var(--transition);
    }

    .vid-play-btn-circle {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background-color: var(--red);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: var(--shadow-md);
        transition: var(--transition-slow);
        padding-left: 4px;
    }

    .vid-card:hover .vid-play-btn-circle {
        transform: scale(1.18);
        background-color: var(--gold);
        color: var(--dark);
        box-shadow: 0 6px 20px rgba(229, 169, 59, 0.4);
    }

    .vid-card-body {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .vid-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1.4;
        margin: 0;
        font-family: var(--font-headings);
    }

    /* Lightbox Modal */
    .vid-lightbox-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background-color: rgba(33, 26, 23, 0.9);
        backdrop-filter: blur(5px);
        z-index: 15000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .vid-lightbox-overlay.open {
        display: flex;
    }

    .vid-lightbox-card {
        width: 100%;
        max-width: 850px;
        height: 480px;
        background-color: #000;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.6);
        border: 2px solid var(--white);
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        position: relative;
    }

    .vid-lightbox-overlay.open .vid-lightbox-card {
        transform: scale(1);
    }

    .vid-lightbox-close {
        position: absolute;
        top: -55px;
        right: 0;
        background-color: var(--white);
        border: none;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.15rem;
        color: var(--dark);
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        transition: var(--transition);
        z-index: 15001;
    }

    .vid-lightbox-close:hover {
        background-color: var(--red);
        color: var(--white);
    }

    @media (max-width: 768px) {
        .vid-lightbox-card {
            height: 300px;
        }
        .vid-lightbox-close {
            top: -50px;
            right: 10px;
        }
    }

    /* Pagination controls */
    .vid-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 3.5rem;
    }

    .vid-pagination-btn {
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

    .vid-pagination-btn:hover {
        border-color: var(--red);
        color: var(--red);
        background-color: rgba(139, 30, 30, 0.02);
    }

    .vid-pagination-btn.active {
        background-color: var(--red);
        border-color: var(--red);
        color: var(--white);
        box-shadow: 0 4px 10px rgba(139, 30, 30, 0.15);
    }

    .vid-pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
</style>

<!-- Banner Header -->
<section class="vid-banner">
    <div class="container">
        <h1 class="vid-banner-title">Program Videos</h1>
        <span class="vid-banner-subtitle">Watch Our Shared Memories In Motion</span>
    </div>
</section>

<!-- Main Grid Section -->
<section class="vid-sec">
    <div class="container">
        <div class="vid-grid">
            <?php foreach ($video_items as $item): ?>
                <?php
                $v_id = get_youtube_video_id($item['url']);
                $img_url = $v_id ? "https://img.youtube.com/vi/{$v_id}/mqdefault.jpg" : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=600';
                ?>
                <div class="vid-card" data-video-id="<?php echo htmlspecialchars($v_id); ?>">
                    <div class="vid-img-wrapper">
                        <img src="<?php echo htmlspecialchars($img_url); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="vid-img" loading="lazy">
                        <div class="vid-play-overlay">
                            <div class="vid-play-btn-circle">
                                <i class="fa-solid fa-play"></i>
                            </div>
                        </div>
                    </div>
                    <div class="vid-card-body">
                        <h3 class="vid-card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination Controls -->
        <?php if ($total_pages > 1): ?>
            <div class="vid-pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="vid-pagination-btn"><i class="fa-solid fa-angle-left"></i> Prev</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <a href="?page=<?php echo $p; ?>" class="vid-pagination-btn <?php echo $p === $page ? 'active' : ''; ?>"><?php echo $p; ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="vid-pagination-btn">Next <i class="fa-solid fa-angle-right"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox Modal container -->
<div class="vid-lightbox-overlay" id="vid-lightbox">
    <div class="vid-lightbox-card">
        <button class="vid-lightbox-close" id="vid-lightbox-close" aria-label="Close Playback"><i class="fa-solid fa-xmark"></i></button>
        <iframe id="vid-lightbox-iframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="width: 100%; height: 100%; border-radius: 6px;"></iframe>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const vidCards = document.querySelectorAll('.vid-card');
    const lightbox = document.getElementById('vid-lightbox');
    const iframe = document.getElementById('vid-lightbox-iframe');
    const lightboxClose = document.getElementById('vid-lightbox-close');

    vidCards.forEach(card => {
        card.addEventListener('click', function() {
            const vidId = this.getAttribute('data-video-id');
            if (vidId) {
                iframe.src = `https://www.youtube.com/embed/${vidId}?autoplay=1`;
                lightbox.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        });
    });

    function closeLightbox() {
        lightbox.classList.remove('open');
        iframe.src = '';
        document.body.style.overflow = '';
    }

    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }

    if (lightbox) {
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (lightbox.classList.contains('open') && e.key === 'Escape') {
            closeLightbox();
        }
    });
});
</script>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
