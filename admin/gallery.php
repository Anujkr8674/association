<?php
$page_title = 'Manage Gallery';
require_once __DIR__ . '/includes/sidebar.php';

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$total_rows = 0;
$total_pages = 1;

try {
    $total_rows = $pdo->query("SELECT COUNT(*) FROM `gallery`")->fetchColumn();
    $total_pages = max(1, ceil($total_rows / $limit));
    
    $stmt = $pdo->prepare("SELECT * FROM `gallery` ORDER BY `created_at` DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $list_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database query failed: " . $e->getMessage();
    $list_items = [];
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
    <div class="panel-header">
        <h2 class="panel-title">Gallery Listing</h2>
        <a href="gallery_edit.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add Gallery Item</a>
    </div>

    <div class="table-responsive">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Thumbnail</th>
                    <th>Item Title</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list_items)): ?>
                    <tr><td colspan="4" class="no-data-row">No gallery items found. Click "Add Gallery Item" to get started.</td></tr>
                <?php else: ?>
                    <?php foreach ($list_items as $item): ?>
                        <tr>
                            <td>
                                <?php
                                $img_src = htmlspecialchars($item['image']);
                                if (strpos($item['image'], 'http') !== 0) {
                                    $img_src = '../' . $img_src;
                                }
                                ?>
                                <img src="<?php echo $img_src; ?>" class="thumbnail-img" alt="thumbnail">
                            </td>
                            <td style="font-weight: 700;"><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><span class="category-badge"><?php echo htmlspecialchars($item['category']); ?></span></td>
                            <td>
                                <div class="action-cell">
                                    <a href="gallery_edit.php?id=<?php echo $item['id']; ?>" class="btn-action btn-edit" title="Edit Item"><i class="fa-solid fa-pencil"></i></a>
                                    <a href="action.php?act=delete_gallery&id=<?php echo $item['id']; ?>" class="btn-action btn-delete" title="Delete Item" onclick="return confirm('Are you sure you want to delete this gallery item?');"><i class="fa-solid fa-trash-can"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination-container" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; padding: 1.5rem 2rem; border-top: 1px solid var(--border); background-color: var(--white); flex-wrap: wrap;">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="btn-pagination" style="text-decoration: none; padding: 0.5rem 0.8rem; border: 1px solid var(--border); border-radius: 6px; color: var(--gray); font-size: 0.85rem; font-weight: 700; transition: var(--transition);"><i class="fa-solid fa-angle-left"></i> Previous</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                <?php if ($p == $page): ?>
                    <span class="btn-pagination active" style="padding: 0.5rem 0.85rem; background-color: var(--red); color: var(--white); border-radius: 6px; font-size: 0.85rem; font-weight: 700; border: 1px solid var(--red);"><?php echo $p; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $p; ?>" class="btn-pagination" style="text-decoration: none; padding: 0.5rem 0.85rem; border: 1px solid var(--border); border-radius: 6px; color: var(--gray); font-size: 0.85rem; font-weight: 700; transition: var(--transition);"><?php echo $p; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="btn-pagination" style="text-decoration: none; padding: 0.5rem 0.8rem; border: 1px solid var(--border); border-radius: 6px; color: var(--gray); font-size: 0.85rem; font-weight: 700; transition: var(--transition);">Next <i class="fa-solid fa-angle-right"></i></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Original Size Image Modal -->
<div class="image-preview-modal" id="admin-preview-modal">
    <button class="image-preview-modal-close" id="admin-preview-modal-close" aria-label="Close Preview"><i class="fa-solid fa-xmark"></i></button>
    <img src="" alt="Full Preview" class="image-preview-modal-content" id="admin-preview-modal-img">
</div>

<style>
    .dash-table .thumbnail-img {
        cursor: pointer;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    
    .dash-table .thumbnail-img:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    /* Preview Modal Styles */
    .image-preview-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background-color: rgba(33, 26, 23, 0.85);
        backdrop-filter: blur(5px);
        z-index: 15000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    .image-preview-modal.open {
        display: flex;
    }

    .image-preview-modal-content {
        max-width: 90%;
        max-height: 85vh;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        border: 2px solid var(--white);
        object-fit: contain;
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }
    
    .image-preview-modal.open .image-preview-modal-content {
        transform: scale(1);
    }

    .image-preview-modal-close {
        position: absolute;
        top: 25px;
        right: 25px;
        background-color: var(--white);
        border: none;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.25rem;
        color: var(--dark);
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        transition: var(--transition);
    }

    .image-preview-modal-close:hover {
        background-color: var(--red);
        color: var(--white);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumbnail-img');
    const modal = document.getElementById('admin-preview-modal');
    const modalImg = document.getElementById('admin-preview-modal-img');
    const modalClose = document.getElementById('admin-preview-modal-close');

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            modalImg.src = this.src;
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeModal() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (modalClose) {
        modalClose.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // Escape key support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('open')) {
            closeModal();
        }
    });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
