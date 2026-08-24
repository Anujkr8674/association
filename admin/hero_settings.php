<?php
$page_title = 'Hero Sections';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch all slides from the database
try {
    $stmt = $pdo->query("SELECT * FROM `hero_slides` ORDER BY `page` ASC, `sort_order` ASC");
    $all_slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database query failed: " . $e->getMessage();
    $all_slides = [];
}

// Group slides by page
$home_slides = [];
$durgapuja_slides = [];
foreach ($all_slides as $slide) {
    if ($slide['page'] === 'home') {
        $home_slides[] = $slide;
    } elseif ($slide['page'] === 'durga-puja') {
        $durgapuja_slides[] = $slide;
    }
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

<div class="panel-card">
    <div class="panel-header" style="flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 1rem; align-items: center;">
            <h2 class="panel-title" style="margin-right: 1.5rem;">Hero Slides Management</h2>
            <!-- Tab switches -->
            <div class="tab-buttons" style="display: flex; gap: 0.5rem; background-color: var(--sand); padding: 0.25rem; border-radius: 8px; border: 1px solid var(--border);">
                <button class="tab-btn active" data-target="home-panel" style="padding: 0.4rem 1rem; border: none; background: none; border-radius: 6px; font-family: 'Outfit', sans-serif; font-size: 0.88rem; font-weight: 700; color: var(--gray); cursor: pointer; transition: var(--transition);">Homepage Hero</button>
                <button class="tab-btn" data-target="durga-puja-panel" style="padding: 0.4rem 1rem; border: none; background: none; border-radius: 6px; font-family: 'Outfit', sans-serif; font-size: 0.88rem; font-weight: 700; color: var(--gray); cursor: pointer; transition: var(--transition);">Durga Puja Hero</button>
            </div>
        </div>
        <a href="hero_slide_edit.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add New Slide</a>
    </div>

    <!-- Homepage Panel -->
    <div id="home-panel" class="tab-panel active">
        <div class="table-responsive">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th style="width: 100px; text-align: center;">Order</th>
                        <th style="width: 120px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($home_slides)): ?>
                        <tr><td colspan="5" class="no-data-row">No homepage slides found. Click "Add New Slide" to get started.</td></tr>
                    <?php else: ?>
                        <?php foreach ($home_slides as $slide): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $img_src = (strpos($slide['image_path'], 'http') === 0) ? $slide['image_path'] : '../' . $slide['image_path'];
                                    ?>
                                    <img src="<?php echo htmlspecialchars($img_src); ?>" class="thumbnail-preview" alt="Slide Thumbnail" style="width: 80px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border); cursor: pointer; transition: var(--transition);" onclick="openLightbox('<?php echo htmlspecialchars($img_src); ?>')">
                                </td>
                                <td style="font-weight: 700;"><?php echo htmlspecialchars($slide['title']); ?></td>
                                <td style="color: var(--gray); font-size: 0.88rem;"><?php echo htmlspecialchars($slide['subtitle']); ?></td>
                                <td style="text-align: center; font-weight: 700;"><?php echo $slide['sort_order']; ?></td>
                                <td>
                                    <div class="action-cell" style="justify-content: center;">
                                        <a href="hero_slide_edit.php?id=<?php echo $slide['id']; ?>" class="btn-action btn-edit" title="Edit Slide"><i class="fa-solid fa-pencil"></i></a>
                                        <a href="action.php?act=delete_hero_slide&id=<?php echo $slide['id']; ?>" class="btn-action btn-delete" title="Delete Slide" onclick="return confirm('Are you sure you want to delete this hero slide?');"><i class="fa-solid fa-trash-can"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Durga Puja Panel -->
    <div id="durga-puja-panel" class="tab-panel" style="display: none;">
        <div class="table-responsive">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th style="width: 100px; text-align: center;">Order</th>
                        <th style="width: 120px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($durgapuja_slides)): ?>
                        <tr><td colspan="5" class="no-data-row">No Durga Puja slides found. Click "Add New Slide" to get started.</td></tr>
                    <?php else: ?>
                        <?php foreach ($durgapuja_slides as $slide): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $img_src = (strpos($slide['image_path'], 'http') === 0) ? $slide['image_path'] : '../' . $slide['image_path'];
                                    ?>
                                    <img src="<?php echo htmlspecialchars($img_src); ?>" class="thumbnail-preview" alt="Slide Thumbnail" style="width: 80px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border); cursor: pointer; transition: var(--transition);" onclick="openLightbox('<?php echo htmlspecialchars($img_src); ?>')">
                                </td>
                                <td style="font-weight: 700;"><?php echo htmlspecialchars($slide['title']); ?></td>
                                <td style="color: var(--gray); font-size: 0.88rem;"><?php echo htmlspecialchars($slide['subtitle']); ?></td>
                                <td style="text-align: center; font-weight: 700;"><?php echo $slide['sort_order']; ?></td>
                                <td>
                                    <div class="action-cell" style="justify-content: center;">
                                        <a href="hero_slide_edit.php?id=<?php echo $slide['id']; ?>" class="btn-action btn-edit" title="Edit Slide"><i class="fa-solid fa-pencil"></i></a>
                                        <a href="action.php?act=delete_hero_slide&id=<?php echo $slide['id']; ?>" class="btn-action btn-delete" title="Delete Slide" onclick="return confirm('Are you sure you want to delete this hero slide?');"><i class="fa-solid fa-trash-can"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Original Size Image Lightbox Modal -->
<div id="lightbox-modal" class="lightbox-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background-color: rgba(33, 26, 23, 0.9); backdrop-filter: blur(8px); z-index: 20000; align-items: center; justify-content: center; padding: 2rem;">
    <button class="lightbox-close" style="position: absolute; top: 25px; right: 25px; background-color: var(--white); border: none; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.25rem; color: var(--dark); box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: var(--transition);" onclick="closeLightbox()"><i class="fa-solid fa-xmark"></i></button>
    <div class="lightbox-content" style="max-width: 90%; max-height: 85%; border-radius: 8px; border: 2px solid var(--white); background: #000; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <img id="lightbox-img" src="" alt="Full size view" style="display: block; width: auto; height: auto; max-width: 100%; max-height: 80vh; object-fit: contain; margin: 0 auto;">
    </div>
</div>

<style>
    .tab-btn.active {
        background-color: var(--white) !important;
        color: var(--red) !important;
        box-shadow: 0 2px 6px rgba(33, 26, 23, 0.05);
    }
    
    .thumbnail-preview:hover {
        transform: scale(1.06);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .lightbox-close:hover {
        background-color: var(--red) !important;
        color: var(--white) !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching logic
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            
            // Remove active classes
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.style.display = 'none');
            
            // Add active classes
            this.classList.add('active');
            document.getElementById(targetId).style.display = 'block';
        });
    });
});

// Lightbox controller
function openLightbox(src) {
    const modal = document.getElementById('lightbox-modal');
    const img = document.getElementById('lightbox-img');
    if (modal && img) {
        img.src = src;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeLightbox() {
    const modal = document.getElementById('lightbox-modal');
    const img = document.getElementById('lightbox-img');
    if (modal && img) {
        modal.style.display = 'none';
        img.src = '';
        document.body.style.overflow = '';
    }
}

// Close lightbox on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLightbox();
    }
});

// Close lightbox when clicking outside the content
const lightboxModal = document.getElementById('lightbox-modal');
if (lightboxModal) {
    lightboxModal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
