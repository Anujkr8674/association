<?php
$page_title = 'Manage Events';
require_once __DIR__ . '/includes/sidebar.php';

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$total_rows = 0;
$total_pages = 1;

try {
    $total_rows = $pdo->query("SELECT COUNT(*) FROM `events`")->fetchColumn();
    $total_pages = max(1, ceil($total_rows / $limit));
    
    $stmt = $pdo->prepare("SELECT 
        e.*, 
        COALESCE(eo.title, e.title) as display_title,
        eo.id as is_overridden
        FROM `events` e 
        LEFT JOIN `event_overrides` eo ON e.id = eo.event_id 
        ORDER BY e.`date` DESC 
        LIMIT :limit OFFSET :offset");
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
        <h2 class="panel-title">Events Listing</h2>
        <a href="event_edit.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add Event</a>
    </div>

    <div class="table-responsive">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Event Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Category</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list_items)): ?>
                    <tr><td colspan="8" class="no-data-row">No events found. Click "Add Event" to get started.</td></tr>
                <?php else: ?>
                    <?php foreach ($list_items as $item): ?>
                        <tr style="<?php echo $item['is_active'] ? '' : 'opacity: 0.55; background-color: #fafafa;'; ?>">
                            <td style="font-weight: 700;">
                                <?php echo htmlspecialchars($item['display_title']); ?>
                                <?php if ($item['is_overridden']): ?>
                                    <span style="font-size: 0.65rem; color: #FFF; background: var(--gold); padding: 2px 4px; border-radius: 3px; font-weight: bold; margin-left: 5px;">Customized</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    if (!empty($item['end_date'])) {
                                        echo date('d M Y', strtotime($item['date'])) . ' - ' . date('d M Y', strtotime($item['end_date']));
                                    } else {
                                        echo date('d M Y', strtotime($item['date']));
                                    }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['time']); ?></td>
                            <td><?php echo htmlspecialchars($item['location']); ?></td>
                            <td><span class="category-badge"><?php echo htmlspecialchars($item['category']); ?></span></td>
                            <td><span class="src-badge src-<?php echo htmlspecialchars($item['source']); ?>"><?php echo htmlspecialchars($item['source']); ?></span></td>
                            <td>
                                <?php if ($item['is_active']): ?>
                                    <span style="color: #059669; font-weight: 700; font-size: 0.85rem;"><i class="fa-solid fa-circle-check"></i> Active</span>
                                <?php else: ?>
                                    <span style="color: var(--gray); font-style: italic; font-size: 0.85rem;" title="Duplicate entry filtered out in favor of higher priority source"><i class="fa-solid fa-ban"></i> Duplicate</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-cell">
                                    <a href="event_edit.php?id=<?php echo $item['id']; ?>" class="btn-action btn-edit" title="<?php echo $item['is_custom'] ? 'Edit Event' : 'Customize Synced Event'; ?>"><i class="fa-solid fa-pencil"></i></a>
                                    <?php if ($item['is_custom']): ?>
                                        <a href="action.php?act=delete_event&id=<?php echo $item['id']; ?>" class="btn-action btn-delete" title="Delete Custom Event" onclick="return confirm('Are you sure you want to delete this custom event?');"><i class="fa-solid fa-trash-can"></i></a>
                                    <?php endif; ?>
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
