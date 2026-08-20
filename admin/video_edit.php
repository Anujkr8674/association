<?php
session_start();
require_once __DIR__ . '/../config.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$item = [
    'id' => 0,
    'title' => '',
    'url' => ''
];

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `testimonial_videos` WHERE `id` = ?");
        $stmt->execute([$id]);
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetched) {
            $item = $fetched;
        }
    } catch (PDOException $e) {
        die("Error fetching video item: " . $e->getMessage());
    }
}

$page_title = $id > 0 ? 'Edit YouTube Video' : 'Add YouTube Video';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="form-card">
    <div class="form-header">
        <h2 class="form-title"><?php echo $id > 0 ? 'Edit Testimonial Video' : 'Add Testimonial Video'; ?></h2>
        <p class="form-subtitle"><i class="fa-solid fa-circle-info"></i> Enter the video details below to sync with the public page.</p>
    </div>

    <form action="action.php?act=save_video" method="POST" class="form-body" id="video-form">
        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
        
        <div class="form-grid">
            <div class="form-group full-width">
                <label for="title" class="form-label">Video Title</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="e.g. Durga Puja Cultural Night Highlights" value="<?php echo htmlspecialchars($item['title']); ?>" required>
            </div>

            <div class="form-group full-width">
                <label for="url" class="form-label">YouTube Video URL</label>
                <input type="url" name="url" id="url" class="form-control" placeholder="e.g. https://www.youtube.com/watch?v=dQw4w9WgXcQ" value="<?php echo htmlspecialchars($item['url']); ?>" required>
                <small style="color: var(--gray); font-size: 0.8rem; margin-top: 0.4rem; display: block;">Supports standard YouTube links, embed URLs, or mobile short links (youtu.be).</small>
            </div>
        </div>

        <!-- Preview Section -->
        <div id="video-preview-box" style="margin-top: 2rem; display: none; background-color: var(--sand); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border);">
            <h4 style="font-size: 0.9rem; color: var(--gray); text-transform: uppercase; font-weight: 700; margin-bottom: 0.8rem;"><i class="fa-solid fa-play"></i> Video Preview</h4>
            <div style="position: relative; padding-top: 56.25%; width: 100%; border-radius: 6px; overflow: hidden; background-color: #000;">
                <iframe id="video-preview-iframe" src="" frameborder="0" allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
            </div>
        </div>

        <div class="btn-row">
            <a href="videos.php" class="btn btn-cancel">Cancel</a>
            <button type="submit" class="btn btn-submit">Save Video Link <i class="fa-solid fa-floppy-disk"></i></button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('url');
    const previewBox = document.getElementById('video-preview-box');
    const previewIframe = document.getElementById('video-preview-iframe');

    function getYoutubeId(url) {
        let video_id = '';
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        if (match && match[2].length === 11) {
            video_id = match[2];
        }
        return video_id;
    }

    function updatePreview() {
        const url = urlInput.value.trim();
        const ytId = getYoutubeId(url);
        if (ytId) {
            previewIframe.src = `https://www.youtube.com/embed/${ytId}`;
            previewBox.style.display = 'block';
        } else {
            previewBox.style.display = 'none';
            previewIframe.src = '';
        }
    }

    urlInput.addEventListener('input', updatePreview);
    
    // Initial call in Edit Mode
    if (urlInput.value) {
        updatePreview();
    }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
