<?php
$page_title = 'Overview Dashboard';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch quick stats & recent items
try {
    $events_count = $pdo->query("SELECT COUNT(*) FROM `events`")->fetchColumn();
    $blogs_count = $pdo->query("SELECT COUNT(*) FROM `blogs`")->fetchColumn();
    $gallery_count = $pdo->query("SELECT COUNT(*) FROM `gallery`")->fetchColumn();
    $activities_count = $pdo->query("SELECT COUNT(*) FROM `recent_activities`")->fetchColumn();
    $videos_count = $pdo->query("SELECT COUNT(*) FROM `testimonial_videos`")->fetchColumn();
    $hero_count = $pdo->query("SELECT COUNT(*) FROM `hero_slides`")->fetchColumn();
    $messages_count = $pdo->query("SELECT COUNT(*) FROM `contact_messages`")->fetchColumn();
    $ads_count = $pdo->query("SELECT COUNT(*) FROM `broadcast_ads`")->fetchColumn();
    $notices_count = $pdo->query("SELECT COUNT(*) FROM `notices`")->fetchColumn();
    $committee_count = $pdo->query("SELECT COUNT(*) FROM `current_committee`")->fetchColumn();
    $members_count = $pdo->query("SELECT COUNT(*) FROM `membership_requests`")->fetchColumn();
    $partners_count = $pdo->query("SELECT COUNT(*) FROM `partner_documents`")->fetchColumn();
    $documents_count = $pdo->query("SELECT COUNT(*) FROM `association_documents`")->fetchColumn();
    $key_messages_count = $pdo->query("SELECT COUNT(*) FROM `key_messages`")->fetchColumn();

    // Submenu counts
    $blog_categories_count = $pdo->query("SELECT COUNT(*) FROM `blog_categories`")->fetchColumn();
    $gallery_categories_count = $pdo->query("SELECT COUNT(*) FROM `gallery_categories`")->fetchColumn();
    $notice_categories_count = $pdo->query("SELECT COUNT(*) FROM `notice_categories`")->fetchColumn();
    
    $committee_prev_count = $pdo->query("SELECT COUNT(*) FROM `committee_documents` WHERE `doc_type` = 'previous_executive'")->fetchColumn();
    $committee_puja_count = $pdo->query("SELECT COUNT(*) FROM `committee_documents` WHERE `doc_type` = 'puja_samiti'")->fetchColumn();
    $committee_proc_count = $pdo->query("SELECT COUNT(*) FROM `committee_documents` WHERE `doc_type` = 'process'")->fetchColumn();
    
    $members_our_count = $pdo->query("SELECT COUNT(*) FROM `member_documents` WHERE `doc_type` = 'our_members'")->fetchColumn();
    $members_profile_count = $pdo->query("SELECT COUNT(*) FROM `member_documents` WHERE `doc_type` = 'member_profile'")->fetchColumn();
    
    $partners_sponsor_count = $pdo->query("SELECT COUNT(*) FROM `partner_documents` WHERE `doc_type` = 'sponsor'")->fetchColumn();
    $partners_patron_count = $pdo->query("SELECT COUNT(*) FROM `partner_documents` WHERE `doc_type` = 'patron'")->fetchColumn();
    $partners_authority_count = $pdo->query("SELECT COUNT(*) FROM `partner_documents` WHERE `doc_type` = 'authority'")->fetchColumn();
    
    $documents_souvenir_count = $pdo->query("SELECT COUNT(*) FROM `association_documents` WHERE `doc_type` = 'souvenir'")->fetchColumn();
    $documents_competition_count = $pdo->query("SELECT COUNT(*) FROM `association_documents` WHERE `doc_type` = 'competition'")->fetchColumn();
    $documents_recognition_count = $pdo->query("SELECT COUNT(*) FROM `association_documents` WHERE `doc_type` = 'recognition'")->fetchColumn();
    
    $messages_pres_samiti_count = $pdo->query("SELECT COUNT(*) FROM `key_messages` WHERE `doc_type` = 'president_samiti'")->fetchColumn();
    $messages_sec_samiti_count = $pdo->query("SELECT COUNT(*) FROM `key_messages` WHERE `doc_type` = 'secretary_samiti'")->fetchColumn();
    $messages_eminent_count = $pdo->query("SELECT COUNT(*) FROM `key_messages` WHERE `doc_type` = 'eminent'")->fetchColumn();
    $messages_pres_india_count = $pdo->query("SELECT COUNT(*) FROM `key_messages` WHERE `doc_type` = 'president_india'")->fetchColumn();
    
    // Fetch latest 3 events
    $recent_events = $pdo->query("SELECT * FROM `events` ORDER BY `date` DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch latest 3 blogs
    $recent_blogs = $pdo->query("SELECT * FROM `blogs` ORDER BY `date` DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch latest 3 gallery items
    $recent_gallery = $pdo->query("SELECT * FROM `gallery` ORDER BY `created_at` DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database query failed: " . $e->getMessage();
    $events_count = $blogs_count = $gallery_count = $activities_count = $videos_count = $hero_count = $messages_count = $ads_count = $notices_count = 0;
    $committee_count = $members_count = $partners_count = $documents_count = $key_messages_count = 0;
    $blog_categories_count = $gallery_categories_count = $notice_categories_count = 0;
    $committee_prev_count = $committee_puja_count = $committee_proc_count = 0;
    $members_our_count = $members_profile_count = 0;
    $partners_sponsor_count = $partners_patron_count = $partners_authority_count = 0;
    $documents_souvenir_count = $documents_competition_count = $documents_recognition_count = 0;
    $messages_pres_samiti_count = $messages_sec_samiti_count = $messages_eminent_count = $messages_pres_india_count = 0;
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
    <!-- Events Card -->
    <div class="stat-card">
        <a href="events.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $events_count; ?></span>
                <span class="stat-label">Total Events</span>
            </div>
            <div class="stat-icon stat-events"><i class="fa-solid fa-calendar-days"></i></div>
        </a>
    </div>

    <!-- Blogs Card -->
    <div class="stat-card">
        <a href="blogs.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $blogs_count; ?></span>
                <span class="stat-label">Total Blogs</span>
            </div>
            <div class="stat-icon stat-blogs"><i class="fa-solid fa-blog"></i></div>
        </a>
        <div class="stat-card-badges">
            <a href="blog_categories.php" class="stat-badge">Blog Categories (<?php echo $blog_categories_count; ?>)</a>
        </div>
    </div>

    <!-- Gallery Card -->
    <div class="stat-card">
        <a href="gallery.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $gallery_count; ?></span>
                <span class="stat-label">Gallery Items</span>
            </div>
            <div class="stat-icon stat-gallery"><i class="fa-solid fa-images"></i></div>
        </a>
        <div class="stat-card-badges">
            <a href="gallery_categories.php" class="stat-badge">Gallery Categories (<?php echo $gallery_categories_count; ?>)</a>
        </div>
    </div>

    <!-- Recent Activities Card -->
    <div class="stat-card">
        <a href="recent_activities.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $activities_count; ?></span>
                <span class="stat-label">Recent Activities</span>
            </div>
            <div class="stat-icon stat-activities"><i class="fa-solid fa-person-running"></i></div>
        </a>
    </div>

    <!-- Videos Card -->
    <div class="stat-card">
        <a href="videos.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $videos_count; ?></span>
                <span class="stat-label">Manage Videos</span>
            </div>
            <div class="stat-icon stat-videos"><i class="fa-solid fa-video"></i></div>
        </a>
    </div>

    <!-- Hero Sections Card -->
    <div class="stat-card">
        <a href="hero_settings.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $hero_count; ?></span>
                <span class="stat-label">Hero Sections</span>
            </div>
            <div class="stat-icon stat-hero"><i class="fa-solid fa-window-maximize"></i></div>
        </a>
    </div>

    <!-- Contact Messages Card -->
    <div class="stat-card">
        <a href="contact_messages.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $messages_count; ?></span>
                <span class="stat-label">Contact Messages</span>
            </div>
            <div class="stat-icon stat-messages"><i class="fa-solid fa-envelope"></i></div>
        </a>
    </div>

    <!-- Broadcast Ads Card -->
    <div class="stat-card">
        <a href="broadcast_ads.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $ads_count; ?></span>
                <span class="stat-label">Broadcast Ads</span>
            </div>
            <div class="stat-icon stat-ads"><i class="fa-solid fa-rectangle-ad"></i></div>
        </a>
    </div>

    <!-- Notices / Bulletins Card -->
    <div class="stat-card">
        <a href="notices.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $notices_count; ?></span>
                <span class="stat-label">Notices / Bulletins</span>
            </div>
            <div class="stat-icon stat-notices"><i class="fa-solid fa-bullhorn"></i></div>
        </a>
        <div class="stat-card-badges">
            <a href="notice_categories.php" class="stat-badge">Notice Categories (<?php echo $notice_categories_count; ?>)</a>
        </div>
    </div>

    <!-- Committee Card -->
    <div class="stat-card">
        <a href="committee_current.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $committee_count; ?></span>
                <span class="stat-label">Committee</span>
            </div>
            <div class="stat-icon stat-committee"><i class="fa-solid fa-users-gear"></i></div>
        </a>
        <div class="stat-card-badges">
            <a href="committee_current.php" class="stat-badge">Current (<?php echo $committee_count; ?>)</a>
            <a href="committee_previous.php" class="stat-badge">Previous (<?php echo $committee_prev_count; ?>)</a>
            <a href="committee_puja_samiti.php" class="stat-badge">Puja Samiti (<?php echo $committee_puja_count; ?>)</a>
            <a href="committee_processes.php" class="stat-badge">Processes (<?php echo $committee_proc_count; ?>)</a>
        </div>
    </div>

    <!-- Members Card -->
    <div class="stat-card">
        <a href="membership_requests.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $members_count; ?></span>
                <span class="stat-label">Members</span>
            </div>
            <div class="stat-icon stat-members"><i class="fa-solid fa-users"></i></div>
        </a>
        <div class="stat-card-badges">
            <a href="members_our.php" class="stat-badge">Our Members (<?php echo $members_our_count; ?>)</a>
            <a href="members_profile.php" class="stat-badge">Profile Docs (<?php echo $members_profile_count; ?>)</a>
            <a href="membership_requests.php" class="stat-badge">Requests (<?php echo $members_count; ?>)</a>
        </div>
    </div>

    <!-- Partners Card -->
    <div class="stat-card">
        <a href="partners_sponsors.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $partners_count; ?></span>
                <span class="stat-label">Partners</span>
            </div>
            <div class="stat-icon stat-partners"><i class="fa-solid fa-handshake"></i></div>
        </a>
        <div class="stat-card-badges">
            <a href="partners_sponsors.php" class="stat-badge">Sponsors (<?php echo $partners_sponsor_count; ?>)</a>
            <a href="partners_patrons.php" class="stat-badge">Patrons (<?php echo $partners_patron_count; ?>)</a>
            <a href="partners_authorities.php" class="stat-badge">Authorities (<?php echo $partners_authority_count; ?>)</a>
        </div>
    </div>

    <!-- Documents Card -->
    <div class="stat-card">
        <a href="documents_souvenir.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $documents_count; ?></span>
                <span class="stat-label">Documents</span>
            </div>
            <div class="stat-icon stat-documents"><i class="fa-solid fa-folder-open"></i></div>
        </a>
        <div class="stat-card-badges">
            <a href="documents_souvenir.php" class="stat-badge">Souvenirs (<?php echo $documents_souvenir_count; ?>)</a>
            <a href="documents_competitions.php" class="stat-badge">Competitions (<?php echo $documents_competition_count; ?>)</a>
            <a href="documents_recognition.php" class="stat-badge">Recognitions (<?php echo $documents_recognition_count; ?>)</a>
        </div>
    </div>

    <!-- Key Messages Card -->
    <div class="stat-card">
        <a href="messages_president_samiti.php" class="stat-card-main">
            <div class="stat-info">
                <span class="stat-num"><?php echo $key_messages_count; ?></span>
                <span class="stat-label">Key Messages</span>
            </div>
            <div class="stat-icon stat-key-messages"><i class="fa-solid fa-message"></i></div>
        </a>
        <div class="stat-card-badges">
            <a href="messages_president_samiti.php" class="stat-badge">President (<?php echo $messages_pres_samiti_count; ?>)</a>
            <a href="messages_secretary_samiti.php" class="stat-badge">Secretary (<?php echo $messages_sec_samiti_count; ?>)</a>
            <a href="messages_eminent.php" class="stat-badge">Eminent (<?php echo $messages_eminent_count; ?>)</a>
            <a href="messages_president_india.php" class="stat-badge">President of India (<?php echo $messages_pres_india_count; ?>)</a>
        </div>
    </div>
</div>

<!-- Overview Grid -->
<div class="overview-grid">
    <!-- Recent Events Card -->
    <!-- <div class="recent-card">
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
    </div> -->

    <!-- Recent Blogs Card -->
    <!-- <div class="recent-card">
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
    </div> -->

    <!-- Recent Gallery Card -->
    <!-- <div class="recent-card" style="grid-column: span 2; margin-top: 1rem;">
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
    </div> -->
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
