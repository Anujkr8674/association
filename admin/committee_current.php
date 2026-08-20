<?php
$page_title = 'Manage Current Committee';
require_once __DIR__ . '/includes/sidebar.php';

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$total_rows = 0;
$total_pages = 1;

try {
    $total_rows = $pdo->query("SELECT COUNT(*) FROM `current_committee`")->fetchColumn();
    $total_pages = max(1, ceil($total_rows / $limit));
    
    $stmt = $pdo->prepare("SELECT * FROM `current_committee` ORDER BY `sort_order` ASC, `created_at` DESC LIMIT :limit OFFSET :offset");
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
        <span>Operation completed successfully: <?php 
            if ($success === 'created') echo 'Committee member added successfully!';
            elseif ($success === 'updated') echo 'Committee member details updated successfully!';
            elseif ($success === 'deleted') echo 'Committee member deleted successfully!';
            else echo htmlspecialchars($success);
        ?></span>
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
        <h2 class="panel-title">Current Committee Members</h2>
        <a href="committee_current_edit.php" class="btn-add"><i class="fa-solid fa-user-plus"></i> Add Committee Member</a>
    </div>

    <div class="table-responsive">
        <table class="dash-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Photo</th>
                    <th>Name</th>
                    <th>Designation (Position)</th>
                    <th>Contact Info (Email / Phone)</th>
                    <th>Group / Type</th>
                    <th style="width: 100px; text-align: center;">Sort Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list_items)): ?>
                    <tr><td colspan="7" class="no-data-row">No committee members found. Click "Add Committee Member" to get started.</td></tr>
                <?php else: ?>
                    <?php foreach ($list_items as $item): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($item['image']); ?>" class="thumbnail-img zoom-trigger" alt="<?php echo htmlspecialchars($item['name']); ?>" style="cursor: pointer; transition: transform 0.2s; border-radius: 6px; width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="thumbnail-img" style="width: 50px; height: 50px; background-color: var(--red); color: var(--white); font-weight: 700; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; border-radius: 6px; cursor: default;">
                                        <?php 
                                        $words = explode(' ', $item['name']);
                                        $initials = '';
                                        foreach ($words as $w) {
                                            $initials .= strtoupper(substr($w, 0, 1));
                                        }
                                        echo htmlspecialchars(substr($initials, 0, 2));
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 700;"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><span style="font-weight: 600; color: var(--gray);"><?php echo htmlspecialchars($item['position']); ?></span></td>
                            <td>
                                <?php if (!empty($item['email'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($item['email']); ?>" style="color: var(--red); text-decoration: none; font-weight: 600; display: block; margin-bottom: 0.25rem;">
                                        <i class="fa-regular fa-envelope"></i> <?php echo htmlspecialchars($item['email']); ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--gray); font-style: italic; display: block; margin-bottom: 0.25rem;">No Email</span>
                                <?php endif; ?>

                                <?php if (!empty($item['phone'])): ?>
                                    <span style="color: var(--dark); font-weight: 600; display: block;">
                                        <i class="fa-solid fa-phone" style="font-size: 0.82rem; color: var(--gray);"></i> <?php echo htmlspecialchars($item['phone']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="category-badge" style="font-size: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; background-color: <?php echo $item['member_type'] === 'board' ? '#FFFBF0' : '#EFF6FF'; ?>; color: <?php echo $item['member_type'] === 'board' ? 'var(--gold)' : '#2563EB'; ?>; border: 1px solid <?php echo $item['member_type'] === 'board' ? '#FDF3D7' : '#DBEAFE'; ?>;">
                                    <?php echo htmlspecialchars($item['member_type'] === 'board' ? 'BOARD MEMBER' : 'EXECUTIVE MEMBER'); ?>
                                </span>
                            </td>
                            <td style="text-align: center; font-weight: bold; color: var(--gray);"><?php echo htmlspecialchars($item['sort_order']); ?></td>
                            <td>
                                <div class="action-cell">
                                    <a href="committee_current_edit.php?id=<?php echo $item['id']; ?>" class="btn-action btn-edit" title="Edit Member"><i class="fa-solid fa-pencil"></i></a>
                                    <a href="action.php?act=delete_committee_member&id=<?php echo $item['id']; ?>" class="btn-action btn-delete" title="Delete Member" onclick="return confirm('Are you sure you want to delete this committee member?');"><i class="fa-solid fa-trash-can"></i></a>
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

<!-- Image Zoom Lightbox Modal -->
<div id="image-zoom-modal" class="zoom-modal">
    <span class="zoom-close" id="zoom-close-btn">&times;</span>
    <img class="zoom-content" id="zoomed-image-el">
</div>

<style>
    .zoom-trigger:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    /* Zoom Modal Layout */
    .zoom-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        padding-top: 5rem;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(33, 26, 23, 0.9);
        backdrop-filter: blur(5px);
    }

    .zoom-content {
        margin: auto;
        display: block;
        max-width: 90%;
        max-height: 80vh;
        border-radius: 8px;
        border: 3px solid var(--white);
        box-shadow: var(--shadow-lg);
        animation: zoomAnim 0.3s ease-out;
    }

    @keyframes zoomAnim {
        from { transform: scale(0.85); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .zoom-close {
        position: absolute;
        top: 30px;
        right: 35px;
        color: var(--white);
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        cursor: pointer;
    }

    .zoom-close:hover,
    .zoom-close:focus {
        color: var(--red);
        text-decoration: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('image-zoom-modal');
    const modalImg = document.getElementById('zoomed-image-el');
    const closeBtn = document.getElementById('zoom-close-btn');

    document.querySelectorAll('.zoom-trigger').forEach(img => {
        img.addEventListener('click', function() {
            modal.style.display = 'block';
            modalImg.src = this.src;
            document.body.style.overflow = 'hidden';
        });
    });

    function closeModal() {
        modal.style.display = 'none';
        modalImg.src = '';
        document.body.style.overflow = '';
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
