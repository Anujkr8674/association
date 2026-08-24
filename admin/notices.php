<?php
$page_title = 'Manage Notices';
require_once __DIR__ . '/includes/sidebar.php';

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$total_rows = 0;
$total_pages = 1;

try {
    $total_rows = $pdo->query("SELECT COUNT(*) FROM `notices`")->fetchColumn();
    $total_pages = max(1, ceil($total_rows / $limit));
    
    $stmt = $pdo->prepare("SELECT * FROM `notices` ORDER BY `date` DESC LIMIT :limit OFFSET :offset");
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
            if ($success === 'deleted') echo 'Notice deleted successfully!';
            elseif ($success === 'saved') echo 'Notice saved successfully!';
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
        <h2 class="panel-title">Notices & Bulletins</h2>
        <a href="notice_edit.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add New Notice</a>
    </div>

    <div class="table-responsive">
        <table class="dash-table">
            <thead>
                <tr>
                    <th style="width: 80px; padding: 1rem 1.5rem; text-align: center;">Date</th>
                    <th style="padding: 1rem 1.5rem;">Notice Title</th>
                    <th style="padding: 1rem 1.5rem;">Category</th>
                    <th style="padding: 1rem 1.5rem;">Tag / Topic</th>
                    <th style="width: 150px; padding: 1rem 1.5rem; text-align: center;">Attachments</th>
                    <th style="width: 120px; text-align: center; padding: 1rem 1.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list_items)): ?>
                    <tr><td colspan="6" class="no-data-row" style="padding: 2.5rem 1.5rem !important;">No notices or bulletins found. Click "Add New Notice" to get started.</td></tr>
                <?php else: ?>
                    <?php foreach ($list_items as $item): ?>
                        <tr>
                            <td style="padding: 1rem 1.5rem; text-align: center; font-weight: 700; color: var(--red);">
                                <?php echo date('M d, Y', strtotime($item['date'])); ?>
                            </td>
                            <td style="font-weight: 700; padding: 1rem 1.5rem; color: var(--dark); max-width: 320px; word-wrap: break-word; white-space: normal;">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </td>
                            <td style="padding: 1rem 1.5rem;">
                                <span class="category-badge" style="font-size: 0.75rem; text-transform: uppercase; padding: 0.2rem 0.5rem;"><?php echo htmlspecialchars($item['category']); ?></span>
                            </td>
                            <td style="padding: 1rem 1.5rem; font-size: 0.88rem; color: var(--gray);">
                                <?php echo !empty($item['tag']) ? '#' . htmlspecialchars($item['tag']) : '-'; ?>
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: center; font-size: 0.88rem; color: var(--dark);">
                                <?php 
                                $attachments = !empty($item['attachments']) ? json_decode($item['attachments'], true) : [];
                                $count = is_array($attachments) ? count($attachments) : 0;
                                if ($count > 0) {
                                    echo '<span class="status-badge status-published" style="padding: 0.25rem 0.6rem; font-size: 0.8rem;"><i class="fa-solid fa-paperclip"></i> ' . $count . ' File(s)</span>';
                                } else {
                                    echo '<span style="color: #bbb; font-style: italic;">None</span>';
                                }
                                ?>
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: center;">
                                <div class="action-cell" style="justify-content: center;">
                                    <a href="notice_edit.php?id=<?php echo $item['id']; ?>" class="btn-action btn-edit" title="Edit Notice"><i class="fa-solid fa-pencil"></i></a>
                                    <a href="action.php?act=delete_notice&id=<?php echo $item['id']; ?>" class="btn-action btn-delete" title="Delete Notice" onclick="return confirm('Are you sure you want to delete this notice? This action cannot be undone.');"><i class="fa-solid fa-trash-can"></i></a>
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

<?php
require_once __DIR__ . '/includes/footer.php';
?>
