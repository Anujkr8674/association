<?php
$page_title = 'Overview Dashboard';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch quick stats & recent items
try {
    $events_count = $pdo->query("SELECT COUNT(*) FROM `events`")->fetchColumn();
    $blogs_count = $pdo->query("SELECT COUNT(*) FROM `blogs`")->fetchColumn();
    $gallery_count = $pdo->query("SELECT COUNT(*) FROM `gallery`")->fetchColumn();
    
    // Fetch latest 3 events
    $recent_events = $pdo->query("SELECT * FROM `events` ORDER BY `date` DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch latest 3 blogs
    $recent_blogs = $pdo->query("SELECT * FROM `blogs` ORDER BY `date` DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch latest 3 gallery items
    $recent_gallery = $pdo->query("SELECT * FROM `gallery` ORDER BY `created_at` DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database query failed: " . $e->getMessage();
    $events_count = $blogs_count = $gallery_count = 0;
    $recent_events = $recent_blogs = $recent_gallery = [];
}

$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<!-- Success notices -->
<?php if (!empty($success)): ?>
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>Operation completed successfully: <?php echo htmlspecialchars($success); ?></span>
    </div>
<?php endif; ?>

<?php if (isset($error_msg)): ?>
    <div style="background-color: #FDF2F2; border: 1px solid #FDE8E8; color: #9B1C1C; padding: 1rem 2rem; margin-bottom: 2rem; border-radius: 8px; font-size: 0.95rem;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span><?php echo htmlspecialchars($error_msg); ?></span>
    </div>
<?php endif; ?>

<!-- Quick Stats Cards Row -->
<div class="stats-row">
    <a href="events.php" class="stat-card">
        <div class="stat-info">
            <span class="stat-num"><?php echo $events_count; ?></span>
            <span class="stat-label">Total Events</span>
        </div>
        <div class="stat-icon stat-events"><i class="fa-solid fa-calendar-days"></i></div>
    </a>
    <a href="blogs.php" class="stat-card">
        <div class="stat-info">
            <span class="stat-num"><?php echo $blogs_count; ?></span>
            <span class="stat-label">Total Blogs</span>
        </div>
        <div class="stat-icon stat-blogs"><i class="fa-solid fa-blog"></i></div>
    </a>
    <a href="gallery.php" class="stat-card">
        <div class="stat-info">
            <span class="stat-num"><?php echo $gallery_count; ?></span>
            <span class="stat-label">Gallery Items</span>
        </div>
        <div class="stat-icon stat-gallery"><i class="fa-solid fa-images"></i></div>
    </a>
</div>

<!-- Overview Grid -->
<div class="overview-grid">
    <!-- Recent Events Card -->
    <div class="recent-card">
        <div class="recent-header">
            <span class="recent-title"><i class="fa-solid fa-calendar-days" style="color: var(--red); margin-right: 0.5rem;"></i> Recent Events</span>
            <a href="events.php" class="link-view-all">View All</a>
        </div>
        <div class="recent-body">
            <ul class="recent-list">
                <?php if (empty($recent_events)): ?>
                    <li class="recent-item no-data-row">No events found.</li>
                <?php else: ?>
                    <?php foreach ($recent_events as $ev): ?>
                        <li class="recent-item">
                            <div class="recent-item-info">
                                <span class="recent-item-title"><?php echo htmlspecialchars($ev['title']); ?></span>
                                <span class="recent-item-meta">
                                    <i class="fa-regular fa-calendar"></i> <?php echo date('d M Y', strtotime($ev['date'])); ?> 
                                    &bull; <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($ev['location']); ?>
                                </span>
                            </div>
                            <a href="event_edit.php?id=<?php echo $ev['id']; ?>" class="btn-action btn-edit" title="Edit Event"><i class="fa-solid fa-pencil"></i></a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border); background-color: #FAFAFA; margin-top: auto;">
            <a href="event_edit.php" class="btn btn-submit" style="width: 100%; font-size: 0.85rem; padding: 0.6rem;"><i class="fa-solid fa-plus"></i> Add Event</a>
        </div>
    </div>

    <!-- Recent Blogs Card -->
    <div class="recent-card">
        <div class="recent-header">
            <span class="recent-title"><i class="fa-solid fa-blog" style="color: var(--gold); margin-right: 0.5rem;"></i> Recent Blogs</span>
            <a href="blogs.php" class="link-view-all">View All</a>
        </div>
        <div class="recent-body">
            <ul class="recent-list">
                <?php if (empty($recent_blogs)): ?>
                    <li class="recent-item no-data-row">No blog posts found.</li>
                <?php else: ?>
                    <?php foreach ($recent_blogs as $bg): ?>
                        <li class="recent-item">
                            <div class="recent-item-info">
                                <span class="recent-item-title"><?php echo htmlspecialchars($bg['title']); ?></span>
                                <span class="recent-item-meta">
                                    <i class="fa-regular fa-calendar"></i> <?php echo date('d M Y', strtotime($bg['date'])); ?> 
                                    &bull; <i class="fa-regular fa-user"></i> <?php echo htmlspecialchars($bg['author']); ?>
                                </span>
                            </div>
                            <a href="blog_edit.php?id=<?php echo $bg['id']; ?>" class="btn-action btn-edit" title="Edit Blog"><i class="fa-solid fa-pencil"></i></a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border); background-color: #FAFAFA; margin-top: auto;">
            <a href="blog_edit.php" class="btn btn-submit" style="width: 100%; font-size: 0.85rem; padding: 0.6rem; background-color: var(--gold);"><i class="fa-solid fa-plus"></i> Add Blog Post</a>
        </div>
    </div>

    <!-- Recent Gallery Card -->
    <div class="recent-card" style="grid-column: span 2; margin-top: 1rem;">
        <div class="recent-header">
            <span class="recent-title"><i class="fa-solid fa-images" style="color: #0284C7; margin-right: 0.5rem;"></i> Recent Gallery Items</span>
            <a href="gallery.php" class="link-view-all">View All</a>
        </div>
        <div class="recent-body" style="padding: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
                <?php if (empty($recent_gallery)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; color: var(--gray); font-style: italic; padding: 1.5rem;">No gallery items found.</div>
                <?php else: ?>
                    <?php foreach ($recent_gallery as $gl): ?>
                        <div style="border: 1px solid var(--border); border-radius: 8px; overflow: hidden; background-color: var(--sand); display: flex; flex-direction: column;">
                            <img src="<?php echo htmlspecialchars($gl['image']); ?>" style="width: 100%; height: 120px; object-fit: cover;" alt="thumbnail">
                            <div style="padding: 0.8rem; flex-grow: 1; display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; min-height: 52px;">
                                <div style="display: flex; flex-direction: column; gap: 0.15rem; min-width: 0; flex-grow: 1;">
                                    <span style="font-weight: 700; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--dark);"><?php echo htmlspecialchars($gl['title']); ?></span>
                                    <span class="category-badge" style="font-size: 0.65rem; width: fit-content;"><?php echo htmlspecialchars($gl['category']); ?></span>
                                </div>
                                <a href="gallery_edit.php?id=<?php echo $gl['id']; ?>" class="btn-action btn-edit" title="Edit Gallery Item" style="flex-shrink: 0;"><i class="fa-solid fa-pencil"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border); background-color: #FAFAFA;">
            <a href="gallery_edit.php" class="btn btn-submit" style="width: 100%; font-size: 0.85rem; padding: 0.6rem; background-color: #0284C7;"><i class="fa-solid fa-plus"></i> Add Gallery Item</a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
