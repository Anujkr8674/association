<?php
$page_title = 'Manage Contact Messages';
require_once __DIR__ . '/includes/sidebar.php';

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$total_rows = 0;
$total_pages = 1;

try {
    $total_rows = $pdo->query("SELECT COUNT(*) FROM `contact_messages`")->fetchColumn();
    $total_pages = max(1, ceil($total_rows / $limit));
    
    $stmt = $pdo->prepare("SELECT * FROM `contact_messages` ORDER BY `created_at` DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database query failed: " . $e->getMessage();
    $messages = [];
}

$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<!-- Success notices -->
<?php if (!empty($success)): ?>
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span><?php 
            if ($success === 'deleted') echo 'Contact message deleted successfully!';
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
        <h2 class="panel-title">Contact Messages Log</h2>
    </div>

    <div class="table-responsive">
        <table class="dash-table">
            <thead>
                <tr>
                    <th style="padding: 1rem 1.5rem;">Sender Details</th>
                    <th style="padding: 1rem 1.5rem;">Subject</th>
                    <th style="padding: 1rem 1.5rem;">Message</th>
                    <th style="width: 150px; padding: 1rem 1.5rem; text-align: center;">Submitted At</th>
                    <th style="width: 100px; padding: 1rem 1.5rem; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                    <tr>
                        <td colspan="5" class="no-data-row" style="padding: 2.5rem 1.5rem !important;">No contact messages found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($messages as $msg_item): ?>
                        <tr>
                            <td style="padding: 1rem 1.5rem; vertical-align: top;">
                                <div style="font-weight: 700; color: var(--dark); font-size: 0.95rem; margin-bottom: 0.25rem;">
                                    <?php echo htmlspecialchars($msg_item['full_name']); ?>
                                </div>
                                <div style="font-size: 0.8rem; color: var(--gray); margin-bottom: 0.15rem;">
                                    <i class="fa-solid fa-envelope" style="width: 16px;"></i> <?php echo htmlspecialchars($msg_item['email']); ?>
                                </div>
                                <div style="font-size: 0.8rem; color: var(--gray);">
                                    <i class="fa-solid fa-phone" style="width: 16px;"></i> <?php echo htmlspecialchars($msg_item['phone']); ?>
                                </div>
                            </td>
                            <td style="padding: 1rem 1.5rem; vertical-align: top; font-size: 0.88rem; font-weight: 700; color: var(--dark); max-width: 200px; word-wrap: break-word; white-space: normal;">
                                <span class="category-badge" style="font-size: 0.75rem; text-transform: uppercase; padding: 0.2rem 0.5rem;"><?php echo htmlspecialchars($msg_item['subject']); ?></span>
                            </td>
                            <td style="padding: 1rem 1.5rem; vertical-align: top; font-size: 0.88rem; line-height: 1.5; color: var(--gray); max-width: 300px; word-wrap: break-word; white-space: normal;">
                                <?php 
                                $message_text = $msg_item['message'];
                                $max_len = 80;
                                if (strlen($message_text) > $max_len) {
                                    $truncated = substr($message_text, 0, $max_len);
                                    $last_space = strrpos($truncated, ' ');
                                    if ($last_space !== false) {
                                        $truncated = substr($truncated, 0, $last_space);
                                    }
                                    echo htmlspecialchars($truncated) . '... ';
                                    echo '<button type="button" class="btn-read-more" data-full-message="' . htmlspecialchars($message_text) . '" style="background: none; border: none; color: var(--red); font-weight: 700; cursor: pointer; font-size: 0.82rem; padding: 0; text-decoration: underline; display: inline;">Read More</button>';
                                } else {
                                    echo nl2br(htmlspecialchars($message_text));
                                }
                                ?>
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: center; vertical-align: top; font-size: 0.82rem; font-weight: 600; color: var(--gray);">
                                <?php echo date('M d, Y', strtotime($msg_item['created_at'])); ?><br>
                                <span style="font-size: 0.75rem; font-weight: normal; opacity: 0.8;"><?php echo date('h:i A', strtotime($msg_item['created_at'])); ?></span>
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: center; vertical-align: top;">
                                <a href="action.php?act=delete_contact_message&id=<?php echo $msg_item['id']; ?>" class="btn-action btn-delete" title="Delete Message" onclick="return confirm('Are you sure you want to delete this message? This action cannot be undone.');" style="display: inline-flex;">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
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

<!-- Read More Modal -->
<div class="modal-overlay" id="readmore-modal-overlay">
    <div class="success-modal" style="position: relative; max-width: 550px; text-align: left; padding: 2.5rem;">
        <button type="button" id="readmore-modal-x" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; opacity: 0.6; transition: var(--transition); outline: none;"><i class="fa-solid fa-xmark"></i></button>
        
        <h3 class="success-title" style="color: var(--red); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem; font-family: var(--font-headings);"><i class="fa-solid fa-envelope-open"></i> Full Message</h3>
        <div id="readmore-modal-content" style="font-size: 0.95rem; line-height: 1.6; color: var(--dark); max-height: 300px; overflow-y: auto; margin-bottom: 2rem; padding-right: 0.5rem; white-space: pre-wrap; word-wrap: break-word;"></div>
        
        <div style="display: flex; justify-content: flex-end;">
            <button class="btn btn-primary" id="readmore-modal-close" style="padding: 0.6rem 2rem; border-radius: 30px; font-weight: 700; background-color: var(--red); color: var(--white); border: none; cursor: pointer;">Close</button>
        </div>
    </div>
</div>

<style>
    /* Modal Overlay */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: var(--transition-slow);
    }

    .modal-overlay.open {
        opacity: 1;
        pointer-events: auto;
    }

    /* Success/Request Modal Styling */
    .success-modal {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        width: 100%;
        box-shadow: var(--shadow-lg);
        transform: scale(0.9);
        transition: var(--transition-slow);
    }

    .modal-overlay.open .success-modal {
        transform: scale(1);
    }
    
    .btn-read-more:hover {
        opacity: 0.8;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalOverlay = document.getElementById('readmore-modal-overlay');
    const modalContent = document.getElementById('readmore-modal-content');
    const modalCloseBtn = document.getElementById('readmore-modal-close');
    const modalXBtn = document.getElementById('readmore-modal-x');

    // Attach click listeners to all read more buttons
    document.querySelectorAll('.btn-read-more').forEach(btn => {
        btn.addEventListener('click', function() {
            const fullMsg = this.getAttribute('data-full-message');
            modalContent.textContent = fullMsg;
            modalOverlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeModal() {
        modalOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    modalCloseBtn.addEventListener('click', closeModal);
    modalXBtn.addEventListener('click', closeModal);
    
    // Close on clicking overlay outside the card
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
