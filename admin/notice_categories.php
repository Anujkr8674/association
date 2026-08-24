<?php
$page_title = 'Manage Notice Categories';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch all categories
try {
    $categories = $pdo->query("SELECT * FROM `notice_categories` ORDER BY `name` ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database query failed: " . $e->getMessage();
    $categories = [];
}

$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$edit_name = '';
if ($edit_id > 0) {
    foreach ($categories as $cat) {
        if (intval($cat['id']) === $edit_id) {
            $edit_name = $cat['name'];
            break;
        }
    }
}

$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<!-- Success notices -->
<?php if (!empty($success)): ?>
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span><?php 
            if ($success === 'category_added') echo 'Category added successfully!';
            elseif ($success === 'category_deleted') echo 'Category deleted successfully!';
            elseif ($success === 'category_updated') echo 'Category updated successfully!';
            else echo 'Operation completed successfully!';
        ?></span>
    </div>
<?php endif; ?>

<?php if (isset($error_msg)): ?>
    <div style="background-color: #FDF2F2; border: 1px solid #FDE8E8; color: #9B1C1C; padding: 1rem 2rem; margin-bottom: 2rem; border-radius: 8px; font-size: 0.95rem;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span><?php echo htmlspecialchars($error_msg); ?></span>
    </div>
<?php endif; ?>

<div class="overview-grid">
    <!-- Left Column: Add/Edit Category Form -->
    <div class="recent-card">
        <div class="recent-header">
            <h3 class="recent-title">
                <i class="fa-solid <?php echo $edit_id > 0 ? 'fa-pen-to-square' : 'fa-folder-plus'; ?>"></i> 
                <?php echo $edit_id > 0 ? 'Edit Category' : 'Add New Category'; ?>
            </h3>
        </div>
        <div style="padding: 2rem;">
            <form action="action.php?act=<?php echo $edit_id > 0 ? 'update_notice_category' : 'save_notice_category'; ?>" method="POST">
                <?php if ($edit_id > 0): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                <?php endif; ?>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="category_name">Category Name</label>
                    <input type="text" id="category_name" name="name" class="form-control" placeholder="e.g. EVENTS" value="<?php echo htmlspecialchars($edit_name); ?>" required>
                    <small style="display: block; margin-top: 0.4rem; color: var(--gray); font-size: 0.78rem;">Enter a clear name for filtering notices.</small>
                </div>
                
                <div style="display: flex; gap: 0.6rem;">
                    <?php if ($edit_id > 0): ?>
                        <a href="notice_categories.php" class="btn btn-cancel" style="flex: 1;">Cancel</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-submit" style="flex: 1;">
                        <i class="fa-solid <?php echo $edit_id > 0 ? 'fa-floppy-disk' : 'fa-plus'; ?>"></i> 
                        <?php echo $edit_id > 0 ? 'Update' : 'Create'; ?> Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Categories List -->
    <div class="recent-card">
        <div class="recent-header">
            <h3 class="recent-title"><i class="fa-solid fa-tags"></i> Notice Categories</h3>
        </div>
        <div class="table-responsive">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th style="width: 80px; padding: 1rem 1.5rem;">ID</th>
                        <th style="padding: 1rem 1.5rem;">Category Name</th>
                        <th style="width: 120px; text-align: center; padding: 1rem 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="3" class="no-data-row" style="padding: 2.5rem 1.5rem !important;">No categories defined. Add one on the left.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td style="padding: 1rem 1.5rem;"><?php echo $cat['id']; ?></td>
                                <td style="font-weight: 700; padding: 1rem 1.5rem; color: var(--dark);">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </td>
                                <td style="text-align: center; padding: 1rem 1.5rem;">
                                    <div class="action-cell" style="justify-content: center;">
                                        <a href="notice_categories.php?edit=<?php echo $cat['id']; ?>" class="btn-action btn-edit" title="Edit Category"><i class="fa-solid fa-pencil"></i></a>
                                        <a href="action.php?act=delete_notice_category&id=<?php echo $cat['id']; ?>" class="btn-action btn-delete" title="Delete Category" onclick="return confirm('Are you sure you want to delete this notice category? All notices under this category will remain, but you may need to update them.');"><i class="fa-solid fa-trash-can"></i></a>
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

<?php
require_once __DIR__ . '/includes/footer.php';
?>
