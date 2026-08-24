<?php
session_start();
require_once __DIR__ . '/../config.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$page_title = 'Manage Broadcast Ads';
require_once __DIR__ . '/includes/sidebar.php';

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$total_rows = 0;
$total_pages = 1;

try {
    $total_rows = $pdo->query("SELECT COUNT(*) FROM `broadcast_ads`")->fetchColumn();
    $total_pages = max(1, ceil($total_rows / $limit));
    
    $stmt = $pdo->prepare("SELECT * FROM `broadcast_ads` ORDER BY `created_at` DESC LIMIT :limit OFFSET :offset");
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

<!-- Style overrides for custom toggle switches -->
<style>
    /* Toggle switch styles */
    .switch-label {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }
    .switch-label input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: var(--red);
    }
    input:checked + .slider:before {
        transform: translateX(24px);
    }
    .thumbs-row {
        display: flex;
        gap: 0.35rem;
        justify-content: center;
    }
    .thumb-indicator {
        width: 45px;
        height: 35px;
        object-fit: cover;
        border: 1px solid var(--border);
        border-radius: 4px;
    }
</style>

<!-- Success notices -->
<?php if (!empty($success)): ?>
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>Operation completed successfully: <?php 
            if ($success === 'deleted') echo 'Broadcast ad deleted successfully!';
            elseif ($success === 'saved') echo 'Broadcast ad saved successfully!';
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
        <h2 class="panel-title">Broadcast Advertisements & Offers</h2>
        <a href="broadcast_ad_edit.php" class="btn-add"><i class="fa-solid fa-plus"></i> Create Broadcast Ad</a>
    </div>

    <div class="table-responsive">
        <table class="dash-table">
            <thead>
                <tr>
                    <th style="padding: 1rem 1.5rem;">Broadcast Title</th>
                    <th style="padding: 1rem 1.5rem;">Description Preview</th>
                    <th style="width: 180px; padding: 1rem 1.5rem; text-align: center;">Images Preview</th>
                    <th style="width: 110px; padding: 1rem 1.5rem; text-align: center;">Status (On/Off)</th>
                    <th style="width: 120px; text-align: center; padding: 1rem 1.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list_items)): ?>
                    <tr><td colspan="5" class="no-data-row" style="padding: 2.5rem 1.5rem !important;">No broadcast announcements found. Click "Create Broadcast Ad" to set up your first popup advertisement.</td></tr>
                <?php else: ?>
                    <?php foreach ($list_items as $item): ?>
                        <tr>
                            <td style="font-weight: 700; padding: 1rem 1.5rem; color: var(--dark); max-width: 250px; word-wrap: break-word; white-space: normal;">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </td>
                            <td style="padding: 1rem 1.5rem; font-size: 0.88rem; color: var(--gray); max-width: 320px; word-wrap: break-word; white-space: normal;">
                                <?php echo htmlspecialchars(mb_strimwidth($item['description'], 0, 95, '...')); ?>
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: center;">
                                <div class="thumbs-row">
                                    <?php 
                                    $images = !empty($item['images']) ? json_decode($item['images'], true) : [];
                                    if (is_array($images) && count($images) > 0) {
                                        foreach ($images as $img) {
                                            $img_url = htmlspecialchars($img);
                                            echo "<img src='../{$img_url}' class='thumb-indicator' alt='Thumb'>";
                                        }
                                    } else {
                                        echo "<span style='color: #bbb; font-style: italic; font-size: 0.85rem;'>No images</span>";
                                    }
                                    ?>
                                </div>
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: center;">
                                <label class="switch-label">
                                    <input type="checkbox" class="status-toggle" data-id="<?php echo $item['id']; ?>" <?php echo $item['status'] == 1 ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: center;">
                                <div class="action-cell" style="justify-content: center;">
                                    <a href="broadcast_ad_edit.php?id=<?php echo $item['id']; ?>" class="btn-action btn-edit" title="Edit Broadcast"><i class="fa-solid fa-pencil"></i></a>
                                    <a href="action.php?act=delete_broadcast_ad&id=<?php echo $item['id']; ?>" class="btn-action btn-delete" title="Delete Broadcast" onclick="return confirm('Are you sure you want to delete this broadcast advertisement? This action cannot be undone.');"><i class="fa-solid fa-trash-can"></i></a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.status-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const status = this.checked ? 1 : 0;
            
            const formData = new FormData();
            formData.append('id', id);
            formData.append('status', status);

            fetch('action.php?act=toggle_broadcast_ad_status', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Reload page to reflect that all other ads are set to off
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    this.checked = !this.checked; // Revert change
                }
            })
            .catch(err => {
                alert('A network error occurred.');
                this.checked = !this.checked; // Revert change
            });
        });
    });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
