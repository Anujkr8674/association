<?php
// Include the shared header
include 'includes/header.php';
require_once 'config.php';

// Gallery fallback static list in case DB is empty/offline
$gallery_items_fallback = [
    ['image' => 'https://images.unsplash.com/photo-1561376399-5ef8d0859942?q=80&w=600', 'title' => 'Sindur Khela on Dashami', 'category' => 'durga-puja'],
    ['image' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?q=80&w=600', 'title' => 'Children performing Rabindra Nritya', 'category' => 'cultural'],
    ['image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=600', 'title' => 'Bhog Distribution to Community', 'category' => 'festivals'],
    ['image' => 'https://images.unsplash.com/photo-1601050690597-df056fb4ce78?q=80&w=600', 'title' => 'Dhunuchi Dance Competition', 'category' => 'durga-puja'],
    ['image' => 'https://images.unsplash.com/photo-1590073844006-33379778ae09?q=80&w=600', 'title' => 'Alpona Floor Art Workshop', 'category' => 'community'],
    ['image' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=600', 'title' => 'Bengali Drama Performance', 'category' => 'cultural'],
    ['image' => 'https://images.unsplash.com/photo-1561376399-5ef8d0859942?q=80&w=600', 'title' => 'Anjali Offerings on Ashtami', 'category' => 'durga-puja'],
    ['image' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?q=80&w=600', 'title' => 'Children writing alphabet (Hatey Khori)', 'category' => 'festivals'],
    ['image' => 'https://images.unsplash.com/photo-1505576399279-565b52d4ac71?q=80&w=600', 'title' => 'Bengali New Year Prabhat Pheri', 'category' => 'cultural'],
    ['image' => 'https://images.unsplash.com/photo-1526218626217-dc65a29bb444?q=80&w=600', 'title' => 'Outdoor games at Annual Picnic', 'category' => 'community'],
    ['image' => 'https://images.unsplash.com/photo-1620121692029-d088224ddc74?q=80&w=600', 'title' => 'Decorated Durga Idol Close Up', 'category' => 'durga-puja'],
    ['image' => 'https://images.unsplash.com/photo-1605152276897-4f618f831968?q=80&w=600', 'title' => 'Shyama Puja Aarati', 'category' => 'festivals']
];

// Fetch all gallery items and categories from database
$gallery_items = [];
$gallery_categories = [];

try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT * FROM `gallery` ORDER BY `id` DESC");
        $gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $gallery_categories = $pdo->query("SELECT * FROM `gallery_categories` ORDER BY `name` ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Fail silently, falls back to static arrays below
}

// Fallback logic if database is empty
if (empty($gallery_items)) {
    $gallery_items = $gallery_items_fallback;
}

if (empty($gallery_categories)) {
    $gallery_categories = [
        ['name' => 'durga-puja'],
        ['name' => 'cultural'],
        ['name' => 'festivals'],
        ['name' => 'community']
    ];
}
?>

<style>
    /* ==========================================================================
       GALLERY PAGE SPECIFIC STYLES
       ========================================================================== */
    .gall-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .gall-banner::before {
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

    .gall-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .gall-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .gall-sec {
        padding: 6.5rem 0;
        background-color: var(--primary-bg);
    }

    /* Filter tabs */
    .gall-filters {
        display: flex;
        justify-content: center;
        gap: 0.8rem;
        margin-bottom: 4rem;
        flex-wrap: wrap;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 1.5rem;
    }

    .gall-filter-btn {
        background-color: var(--white);
        border: 2px solid var(--border-color);
        padding: 0.6rem 1.6rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--dark);
        border-radius: 30px;
        cursor: pointer;
        transition: var(--transition);
    }

    .gall-filter-btn:hover {
        background-color: var(--secondary-bg);
        border-color: var(--red);
        color: var(--red);
    }

    .gall-filter-btn.active {
        background-color: var(--red);
        border-color: var(--red);
        color: var(--white);
        box-shadow: 0 4px 10px rgba(139, 30, 30, 0.15);
    }

    /* Grid layout */
    .gall-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .gall-card {
        position: relative;
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        height: 260px;
        cursor: pointer;
        background-color: var(--secondary-bg);
        border: 1px solid var(--border-color);
        transition: var(--transition-slow);
    }

    .gall-card.hide {
        display: none;
    }

    .gall-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--gold);
    }

    .gall-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition-slow);
    }

    .gall-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(0deg, rgba(33, 26, 23, 0.85) 0%, rgba(33, 26, 23, 0.1) 85%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 1.5rem;
        opacity: 0;
        transition: var(--transition);
    }

    .gall-card-title {
        color: var(--white);
        font-family: var(--font-headings);
        font-size: 1.15rem;
        margin-bottom: 0.25rem;
        transform: translateY(15px);
        transition: var(--transition-slow);
    }

    .gall-card-cat {
        color: var(--gold);
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        transform: translateY(15px);
        transition: var(--transition-slow);
    }

    .gall-card:hover .gall-img {
        transform: scale(1.08);
    }

    .gall-card:hover .gall-card-overlay {
        opacity: 1;
    }

    .gall-card:hover .gall-card-title,
    .gall-card:hover .gall-card-cat {
        transform: translateY(0);
    }

    /* Share Photos bottom block */
    .gall-share-sec {
        background-color: var(--secondary-bg);
        padding: 6.5rem 0;
        text-align: center;
        border-top: 1px solid var(--border-color);
    }

    .gall-share-card {
        max-width: 750px;
        margin: 0 auto;
    }

    .gall-share-card h3 {
        font-size: 2.2rem;
        color: var(--red);
        margin-bottom: 1rem;
    }

    .gall-share-card p {
        font-size: 1.05rem;
        margin-bottom: 2rem;
        line-height: 1.65;
    }

    /* Lightbox Styles */
    .gall-lightbox-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(33, 26, 23, 0.95);
        backdrop-filter: blur(5px);
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .gall-lightbox-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    .gall-lightbox-card {
        background-color: transparent;
        border-radius: 4px;
        width: 100%;
        max-width: 900px;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }

    .gall-lightbox-img {
        max-height: 75vh;
        max-width: 100%;
        object-fit: contain;
        border-radius: 4px;
        box-shadow: var(--shadow-lg);
        border: 4px solid var(--white);
    }

    .gall-lightbox-caption {
        color: var(--white);
        margin-top: 1.25rem;
        font-family: var(--font-headings);
        font-size: 1.3rem;
        text-align: center;
    }

    .gall-lightbox-index {
        color: var(--gold);
        font-size: 0.8rem;
        margin-top: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    .gall-lightbox-close {
        position: absolute;
        top: -50px;
        right: 0;
        background-color: var(--white);
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10002;
        color: var(--dark);
        font-size: 1rem;
        transition: var(--transition);
    }

    .gall-lightbox-close:hover {
        background-color: var(--red);
        color: var(--white);
    }

    .gall-lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--white);
        font-size: 2.5rem;
        cursor: pointer;
        z-index: 10001;
        transition: var(--transition);
        opacity: 0.6;
    }

    .gall-lightbox-nav:hover {
        opacity: 1;
        color: var(--gold);
    }

    .gall-lightbox-prev { left: 30px; }
    .gall-lightbox-next { right: 30px; }

    /* Empty state */
    .gall-empty {
        display: none;
        text-align: center;
        padding: 4rem 2rem;
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        width: 100%;
        grid-column: span 4;
    }

    .gall-empty i {
        font-size: 3rem;
        color: var(--gold);
        margin-bottom: 1.5rem;
    }

    .gall-empty h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .gall-empty p {
        color: var(--text-muted);
        margin-bottom: 0;
    }

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS
       ========================================================================== */
    @media (max-width: 1200px) {
        .gall-grid {
            grid-template-columns: repeat(3, 1fr);
        }
        .gall-empty {
            grid-column: span 3;
        }
    }

    @media (max-width: 991px) {
        .gall-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .gall-empty {
            grid-column: span 2;
        }
        .gall-lightbox-prev { left: 10px; }
        .gall-lightbox-next { right: 10px; }
        .gall-lightbox-close { right: 15px; top: -50px; }
    }

    /* Client-side Pagination Styles */
    .gall-pagination-btn {
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

    .gall-pagination-btn:hover {
        border-color: var(--red);
        color: var(--red);
        background-color: rgba(139, 30, 30, 0.02);
    }

    .gall-pagination-btn.active {
        background-color: var(--red);
        border-color: var(--red);
        color: var(--white);
        box-shadow: 0 4px 10px rgba(139, 30, 30, 0.15);
    }

    .gall-pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    @media (max-width: 576px) {
        .gall-grid {
            grid-template-columns: 1fr;
            max-width: 380px;
            margin: 0 auto;
        }
        .gall-empty {
            grid-column: span 1;
        }
        .gall-lightbox-nav {
            font-size: 1.8rem;
        }
    }
</style>

<!-- Banner Header -->
<section class="gall-banner">
    <div class="container">
        <h1 class="gall-banner-title">Media Gallery</h1>
        <span class="gall-banner-subtitle">Capturing Our Shared Memories</span>
    </div>
</section>

<!-- Main Gallery Section -->
<section class="gall-sec">
    <div class="container">
        
        <!-- Category Filters -->
        <div class="gall-filters">
            <button class="gall-filter-btn active" data-filter="all">All Photos</button>
            <?php foreach ($gallery_categories as $cat): ?>
                <button class="gall-filter-btn" data-filter="<?php echo htmlspecialchars($cat['name']); ?>">
                    <?php echo htmlspecialchars(ucwords(strtolower(str_replace('-', ' ', $cat['name'])))); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Gallery Grid -->
        <div class="gall-grid">
            <?php foreach ($gallery_items as $index => $item): ?>
                <div class="gall-card" data-category="<?php echo $item['category']; ?>" data-index="<?php echo $index; ?>">
                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" class="gall-img" loading="lazy">
                    <div class="gall-card-overlay">
                        <h3 class="gall-card-title"><?php echo $item['title']; ?></h3>
                        <span class="gall-card-cat"><?php echo str_replace('-', ' ', $item['category']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Fallback Empty State Card -->
            <div class="gall-empty" id="gall-empty-card">
                <i class="fa-regular fa-image"></i>
                <h3>No Photos Found</h3>
                <p>We do not have any photos uploaded under this category yet. Please check back soon.</p>
            </div>
        </div>

        <!-- Client-side Pagination Controls -->
        <div class="gall-pagination" id="gallery-pagination" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 3.5rem;"></div>
    </div>
</section>

<!-- Bottom Photo Submit Panel -->
<section class="gall-share-sec">
    <div class="container">
        <div class="gall-share-card">
            <h3>Have Program Photos to Share?</h3>
            <p>If you took high-resolution photos during any of our recent festivals, performances, or community initiatives, feel free to send them directly to our media wing.</p>
            <a href="contact.php" class="btn btn-primary">Submit Photos via Email <i class="fa-solid fa-cloud-arrow-up"></i></a>
        </div>
    </div>
</section>

<!-- Lightbox Modal -->
<div class="gall-lightbox-overlay" id="gall-lightbox">
    <button class="gall-lightbox-close" id="gall-lightbox-close" aria-label="Close Lightbox"><i class="fa-solid fa-xmark"></i></button>
    
    <button class="gall-lightbox-nav gall-lightbox-prev" id="gall-lightbox-prev" aria-label="Previous Image"><i class="fa-solid fa-chevron-left"></i></button>
    
    <div class="gall-lightbox-card">
        <img src="" alt="Full Screen View" class="gall-lightbox-img" id="gall-lightbox-img">
        <div class="gall-lightbox-caption" id="gall-lightbox-caption">Caption Text</div>
        <div class="gall-lightbox-index" id="gall-lightbox-index">Image 1 of 12</div>
    </div>
    
    <button class="gall-lightbox-nav gall-lightbox-next" id="gall-lightbox-next" aria-label="Next Image"><i class="fa-solid fa-chevron-right"></i></button>
</div>

<!-- Vanilla JS Filters & Lightbox -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. FILTERING & PAGINATION
        const filterBtns = document.querySelectorAll('.gall-filter-btn');
        const gallCards = document.querySelectorAll('.gall-card');
        const emptyCard = document.getElementById('gall-empty-card');
        const paginationContainer = document.getElementById('gallery-pagination');

        let activeFilter = 'all';
        
        // Check if a category query parameter is passed (e.g. ?category=durga-puja)
        const urlParams = new URLSearchParams(window.location.search);
        const catParam = urlParams.get('category');
        if (catParam) {
            const matchedBtn = Array.from(filterBtns).find(btn => btn.getAttribute('data-filter').toLowerCase() === catParam.toLowerCase());
            if (matchedBtn) {
                filterBtns.forEach(b => b.classList.remove('active'));
                matchedBtn.classList.add('active');
                activeFilter = matchedBtn.getAttribute('data-filter');
            }
        }

        let currentPage = 1;
        const limitPerPage = 20; // 5 rows of 4 columns on desktop

        function paginateGallery() {
            // Get all matching cards for current category filter
            const filteredCards = [];
            gallCards.forEach(card => {
                const cat = card.getAttribute('data-category');
                if (activeFilter === 'all' || (cat && cat.toLowerCase() === activeFilter.toLowerCase())) {
                    filteredCards.push(card);
                } else {
                    card.style.display = 'none';
                    card.classList.add('hide'); // for lightbox check
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
                    card.style.display = 'block';
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
            prevBtn.className = 'gall-pagination-btn';
            prevBtn.innerHTML = '<i class="fa-solid fa-angle-left"></i> Prev';
            prevBtn.disabled = currentPage === 1;
            prevBtn.addEventListener('click', () => {
                currentPage--;
                paginateGallery();
                document.querySelector('.gall-filters').scrollIntoView({ behavior: 'smooth' });
            });
            paginationContainer.appendChild(prevBtn);

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = 'gall-pagination-btn';
                if (i === currentPage) {
                    pageBtn.classList.add('active');
                }
                pageBtn.innerText = i;
                pageBtn.addEventListener('click', () => {
                    currentPage = i;
                    paginateGallery();
                    document.querySelector('.gall-filters').scrollIntoView({ behavior: 'smooth' });
                });
                paginationContainer.appendChild(pageBtn);
            }

            // Next Button
            const nextBtn = document.createElement('button');
            nextBtn.className = 'gall-pagination-btn';
            nextBtn.innerHTML = 'Next <i class="fa-solid fa-angle-right"></i>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.addEventListener('click', () => {
                currentPage++;
                paginateGallery();
                document.querySelector('.gall-filters').scrollIntoView({ behavior: 'smooth' });
            });
            paginationContainer.appendChild(nextBtn);
        }

        // Initialize pagination
        paginateGallery();

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                // Change active styles
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                activeFilter = this.getAttribute('data-filter');
                currentPage = 1; // Reset to page 1 on category switch
                paginateGallery();
            });
        });

        // 2. DYNAMIC LIGHTBOX FOR ACTIVE (VISIBLE) IMAGES ONLY
        const lightbox = document.getElementById('gall-lightbox');
        const lightboxImg = document.getElementById('gall-lightbox-img');
        const lightboxCaption = document.getElementById('gall-lightbox-caption');
        const lightboxIndexText = document.getElementById('gall-lightbox-index');

        const lightboxClose = document.getElementById('gall-lightbox-close');
        const lightboxPrev = document.getElementById('gall-lightbox-prev');
        const lightboxNext = document.getElementById('gall-lightbox-next');

        let visibleImages = []; // Stores image data of only currently filtered (visible) cards
        let currentIdx = 0; // Index relative to visibleImages

        function updateVisibleImagesList() {
            visibleImages = [];
            gallCards.forEach(card => {
                // Include all cards matching category filter (even if on other pages)
                const cat = card.getAttribute('data-category');
                if (activeFilter === 'all' || (cat && cat.toLowerCase() === activeFilter.toLowerCase())) {
                    const img = card.querySelector('.gall-img');
                    const title = card.querySelector('.gall-card-title').innerText;
                    visibleImages.push({
                        src: img.src,
                        caption: title
                    });
                }
            });
        }

        // Open Lightbox
        gallCards.forEach(card => {
            card.addEventListener('click', function () {
                updateVisibleImagesList();

                const imgSrc = this.querySelector('.gall-img').src;
                // Find matching index in our visible list
                currentIdx = visibleImages.findIndex(img => img.src === imgSrc);

                openLightbox(currentIdx);
            });
        });

        function openLightbox(index) {
            if (index < 0 || index >= visibleImages.length) return;
            
            lightboxImg.src = visibleImages[index].src;
            lightboxCaption.innerText = visibleImages[index].caption;
            lightboxIndexText.innerText = `Image ${index + 1} of ${visibleImages.length}`;
            
            lightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightbox.classList.remove('open');
            document.body.style.overflow = '';
        }

        function prevImage() {
            currentIdx = (currentIdx - 1 + visibleImages.length) % visibleImages.length;
            openLightbox(currentIdx);
        }

        function nextImage() {
            currentIdx = (currentIdx + 1) % visibleImages.length;
            openLightbox(currentIdx);
        }

        lightboxClose.addEventListener('click', closeLightbox);
        lightboxPrev.addEventListener('click', prevImage);
        lightboxNext.addEventListener('click', nextImage);

        // Backdrop click close
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });

        // Keyboard Controls
        document.addEventListener('keydown', function (e) {
            if (lightbox.classList.contains('open')) {
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') prevImage();
                if (e.key === 'ArrowRight') nextImage();
            }
        });
    });
</script>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
