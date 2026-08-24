<?php
require_once 'config.php';

// Fetch notice categories
try {
    $categories = $pdo->query("SELECT * FROM `notice_categories` ORDER BY `name` ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

// Active Filter
$active_filter = isset($_GET['category']) ? trim($_GET['category']) : 'all';

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$total_rows = 0;
$total_pages = 1;

try {
    if ($active_filter === 'all') {
        $total_rows = $pdo->query("SELECT COUNT(*) FROM `notices`")->fetchColumn();
        $total_pages = max(1, ceil($total_rows / $limit));
        
        $stmt = $pdo->prepare("SELECT * FROM `notices` ORDER BY `date` DESC LIMIT :limit OFFSET :offset");
    } else {
        $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM `notices` WHERE `category` = ?");
        $stmt_count->execute([$active_filter]);
        $total_rows = $stmt_count->fetchColumn();
        $total_pages = max(1, ceil($total_rows / $limit));
        
        $stmt = $pdo->prepare("SELECT * FROM `notices` WHERE `category` = :category ORDER BY `date` DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':category', $active_filter, PDO::PARAM_STR);
    }
    
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $announcements = [];
}

// Include the shared header
include 'includes/header.php';
?>

<style>
    /* ==========================================================================
       ANNOUNCEMENTS PAGE SPECIFIC STYLES
       ========================================================================== */
    .ann-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .ann-banner::before {
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

    .ann-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .ann-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .ann-sec {
        padding: 6.5rem 0;
        background-color: var(--primary-bg);
    }

    /* Filter Toolbar */
    .ann-filters {
        display: flex;
        justify-content: center;
        gap: 0.8rem;
        margin-bottom: 4rem;
        flex-wrap: wrap;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 1.5rem;
    }

    .ann-filter-btn {
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

    .ann-filter-btn:hover {
        background-color: var(--secondary-bg);
        border-color: var(--red);
        color: var(--red);
    }

    .ann-filter-btn.active {
        background-color: var(--red);
        border-color: var(--red);
        color: var(--white);
        box-shadow: 0 4px 10px rgba(139, 30, 30, 0.15);
    }

    /* Announcements List Stack */
    .ann-list {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
        max-width: 900px;
        margin: 0 auto;
    }

    .ann-card {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        display: flex;
        overflow: hidden;
        transition: var(--transition-slow);
    }

    .ann-card.hide {
        display: none;
    }

    .ann-card:hover {
        transform: translateX(5px);
        box-shadow: var(--shadow-md);
        border-color: rgba(201, 154, 46, 0.4);
    }

    /* Left Calendar-style block */
    .ann-date-block {
        width: 140px;
        background-color: var(--secondary-bg);
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        flex-shrink: 0;
        text-align: center;
        transition: var(--transition);
    }

    .ann-card:hover .ann-date-block {
        background-color: var(--red);
    }

    .ann-date-day {
        font-family: var(--font-headings);
        font-size: 3rem;
        font-weight: 700;
        color: var(--red);
        line-height: 1;
        margin-bottom: 0.2rem;
        display: block;
        transition: var(--transition);
    }

    .ann-card:hover .ann-date-day {
        color: var(--white);
    }

    .ann-date-month {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--dark);
        letter-spacing: 1.5px;
        display: block;
        margin-bottom: 0.25rem;
        transition: var(--transition);
    }

    .ann-card:hover .ann-date-month {
        color: var(--gold);
    }

    .ann-date-year {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 500;
        transition: var(--transition);
    }

    .ann-card:hover .ann-date-year {
        color: rgba(255, 255, 255, 0.85);
    }

    /* Right content block */
    .ann-content-block {
        padding: 2.25rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .ann-header-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }

    .ann-cat-badge {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        letter-spacing: 0.5px;
        background-color: var(--secondary-bg);
        color: var(--red);
        border: 1px solid rgba(139, 30, 30, 0.1);
    }

    .ann-tag-text {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .ann-title {
        font-size: 1.4rem;
        margin-bottom: 1rem;
        line-height: 1.35;
        color: var(--dark);
    }

    .ann-excerpt {
        font-size: 0.95rem;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* Smooth expanding text block */
    .ann-full-text {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s cubic-bezier(0, 1, 0, 1); /* Quick expanding transition */
    }

    .ann-full-text-inner {
        padding-top: 1.5rem;
        margin-top: 1.5rem;
        border-top: 1px dashed var(--border-color);
        font-size: 0.95rem;
        line-height: 1.7;
        color: var(--text-muted);
    }

    .ann-more-btn {
        background: none;
        border: none;
        color: var(--red);
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 1.5rem;
        align-self: flex-start;
        transition: var(--transition);
        padding: 0.2rem 0;
    }

    .ann-more-btn:hover {
        color: var(--vermilion);
    }

    .ann-more-btn i {
        font-size: 0.75rem;
        transition: var(--transition);
    }

    .ann-card.expanded .ann-more-btn i {
        transform: rotate(180deg);
    }

    /* No items found placeholder */
    .ann-no-items {
        display: none;
        text-align: center;
        padding: 4rem 2rem;
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        width: 100%;
    }

    .ann-no-items i {
        font-size: 3rem;
        color: var(--gold);
        margin-bottom: 1.5rem;
    }

    .ann-no-items h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .ann-no-items p {
        color: var(--text-muted);
        margin-bottom: 0;
    }

    /* Bottom Newsletter CTA */
    .ann-news-sec {
        background-color: var(--secondary-bg);
        padding: 6rem 0;
        text-align: center;
        border-top: 1px solid var(--border-color);
    }

    .ann-news-card {
        max-width: 600px;
        margin: 0 auto;
    }

    .ann-news-card h3 {
        font-size: 1.8rem;
        color: var(--red);
        margin-bottom: 0.8rem;
    }

    .ann-news-card p {
        font-size: 0.95rem;
        margin-bottom: 2rem;
    }

    .newsletter-form {
        display: flex;
        gap: 0.75rem;
        max-width: 500px;
        margin: 0 auto;
    }

    .news-input {
        flex-grow: 1;
        padding: 0.8rem 1.4rem;
        border: 2px solid var(--border-color);
        border-radius: 30px;
        outline: none;
        font-family: var(--font-body);
        font-size: 0.92rem;
    }

    .news-input:focus {
        border-color: var(--red);
    }

    .news-submit {
        padding: 0.8rem 1.8rem;
        border-radius: 30px;
        background-color: var(--red);
        color: var(--white);
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: var(--transition);
    }

    .news-submit:hover {
        background-color: var(--vermilion);
    }

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS
       ========================================================================== */
    @media (max-width: 768px) {
        .ann-card {
            flex-direction: column;
        }
        .ann-date-block {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid var(--border-color);
            flex-direction: row;
            justify-content: flex-start;
            gap: 1rem;
            padding: 1rem 2.25rem;
        }
        .ann-date-day {
            font-size: 2.2rem;
        }
        .ann-date-month {
            margin-bottom: 0;
        }
        .ann-content-block {
            padding: 2rem;
        }
        .newsletter-form {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }
    }

    /* Responsive Notice Attachments */
    .notice-attachments-container {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.8rem;
        width: 100%;
    }

    .notice-file-badge {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        background-color: var(--secondary-bg);
        border: 1px solid var(--border-color);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        width: 100%;
        max-width: 320px;
        gap: 0.5rem;
        transition: var(--transition);
    }

    .notice-file-badge:hover {
        background-color: var(--white);
        box-shadow: var(--shadow-sm);
        border-color: var(--gold);
    }

    @media (max-width: 576px) {
        .notice-attachments-container {
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
        }
        .notice-file-badge {
            max-width: 100%;
        }
    }
</style>

<!-- Banner Header -->
<section class="ann-banner">
    <div class="container">
        <h1 class="ann-banner-title">Official Notices</h1>
        <span class="ann-banner-subtitle">Stay Informed with the Notice Board</span>
    </div>
</section>

<!-- Main Listing Section -->
<section class="ann-sec">
    <div class="container">
        
        <!-- Category Filters -->
        <div class="ann-filters">
            <a href="notice.php?category=all" class="ann-filter-btn <?php echo $active_filter === 'all' ? 'active' : ''; ?>" style="text-decoration: none; display: inline-block; text-align: center;">All Bulletins</a>
            <?php foreach ($categories as $cat): ?>
                <a href="notice.php?category=<?php echo urlencode($cat['name']); ?>" class="ann-filter-btn <?php echo $active_filter === $cat['name'] ? 'active' : ''; ?>" style="text-decoration: none; display: inline-block; text-align: center;"><?php echo htmlspecialchars($cat['name']); ?></a>
            <?php endforeach; ?>
        </div>

        <!-- Announcements Stack -->
        <div class="ann-list">
            <?php if (empty($announcements)): ?>
                <div class="ann-no-items" style="display: block;">
                    <i class="fa-regular fa-folder-open"></i>
                    <h3>No Announcements</h3>
                    <p>There are no active notices or announcements under this category. Please check again later.</p>
                </div>
            <?php else: ?>
                <?php foreach ($announcements as $item): ?>
                    <?php 
                    $time = strtotime($item['date']);
                    $date_day = date('d', $time);
                    $date_month = strtoupper(date('M', $time));
                    $date_year = date('Y', $time);
                    $attachments = !empty($item['attachments']) ? json_decode($item['attachments'], true) : [];
                    ?>
                    <div class="ann-card" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                        <!-- Date Column -->
                        <div class="ann-date-block">
                            <span class="ann-date-day"><?php echo $date_day; ?></span>
                            <div>
                                <span class="ann-date-month"><?php echo $date_month; ?></span>
                                <span class="ann-date-year"><?php echo $date_year; ?></span>
                            </div>
                        </div>

                        <!-- Content Column -->
                        <div class="ann-content-block">
                            <div class="ann-header-meta">
                                <span class="ann-cat-badge"><?php echo htmlspecialchars($item['category']); ?></span>
                                <?php if (!empty($item['tag'])): ?>
                                    <span class="ann-tag-text"><i class="fa-solid fa-hashtag" style="color: var(--gold); font-size: 0.75rem;"></i> <?php echo htmlspecialchars($item['tag']); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="ann-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <?php if (!empty($item['excerpt'])): ?>
                                <p class="ann-excerpt"><?php echo htmlspecialchars($item['excerpt']); ?></p>
                            <?php endif; ?>

                            <!-- Expandable full text -->
                            <div class="ann-full-text">
                                <div class="ann-full-text-inner">
                                    <p style="white-space: pre-line;"><?php echo htmlspecialchars($item['full_text']); ?></p>
                                    
                                    <!-- Attachments download list -->
                                    <?php if (!empty($attachments)): ?>
                                        <div class="notice-attachments-sec" style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px dashed var(--border-color);">
                                            <h5 style="margin-bottom: 0.8rem; font-size: 0.92rem; color: var(--dark); display: flex; align-items: center; gap: 0.5rem;"><i class="fa-solid fa-paperclip" style="color: var(--red);"></i> Attached Documents & Media</h5>
                                            <div class="notice-attachments-container">
                                                <?php foreach ($attachments as $file): ?>
                                                    <div class="notice-file-badge">
                                                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                                                            <i class="fa-solid <?php echo $file['type'] === 'pdf' ? 'fa-file-pdf' : 'fa-file-image'; ?>" style="color: var(--red); font-size: 1.1rem; flex-shrink: 0;"></i>
                                                            <span style="font-weight: 600; color: var(--dark); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex-grow: 1;"><?php echo htmlspecialchars($file['name']); ?></span>
                                                        </div>
                                                        <div style="display: flex; gap: 1rem; padding-left: 1.7rem; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 0.5rem; margin-top: 0.1rem;">
                                                            <a href="<?php echo htmlspecialchars($file['path']); ?>" target="_blank" style="color: var(--red); text-decoration: none; font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; gap: 0.25rem;" title="View File"><i class="fa-regular fa-eye"></i> View</a>
                                                            <a href="<?php echo htmlspecialchars($file['path']); ?>" download style="color: var(--red); text-decoration: none; font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; gap: 0.25rem;" title="Download File"><i class="fa-solid fa-download"></i> Download</a>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Read More toggle button -->
                            <button class="ann-more-btn">
                                <span class="btn-text">Read Full Notice</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination Controls -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-container" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; padding: 2rem 0; margin-top: 2.5rem; flex-wrap: wrap;">
                <?php if ($page > 1): ?>
                    <a href="?category=<?php echo urlencode($active_filter); ?>&page=<?php echo $page - 1; ?>" class="ann-filter-btn" style="text-decoration: none; padding: 0.5rem 1.2rem; border-radius: 30px;"><i class="fa-solid fa-angle-left"></i> Previous</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <?php if ($p == $page): ?>
                        <span class="ann-filter-btn active" style="padding: 0.5rem 1.2rem; border-radius: 30px;"><?php echo $p; ?></span>
                    <?php else: ?>
                        <a href="?category=<?php echo urlencode($active_filter); ?>&page=<?php echo $p; ?>" class="ann-filter-btn" style="text-decoration: none; padding: 0.5rem 1.2rem; border-radius: 30px;"><?php echo $p; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?category=<?php echo urlencode($active_filter); ?>&page=<?php echo $page + 1; ?>" class="ann-filter-btn" style="text-decoration: none; padding: 0.5rem 1.2rem; border-radius: 30px;">Next <i class="fa-solid fa-angle-right"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Vanilla JS accordion functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButtons = document.querySelectorAll('.ann-more-btn');

        function toggleAccordion(card) {
            const content = card.querySelector('.ann-full-text');
            const btnText = card.querySelector('.btn-text');
            
            if (card.classList.contains('expanded')) {
                content.style.maxHeight = null;
                card.classList.remove('expanded');
                btnText.innerText = 'Read Full Notice';
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                card.classList.add('expanded');
                btnText.innerText = 'Collapse Notice';
            }
        }

        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const card = this.closest('.ann-card');
                toggleAccordion(card);
            });
        });
    });
</script>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
