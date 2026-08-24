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
    'page' => 'home',
    'title' => '',
    'subtitle' => '',
    'image_path' => '',
    'sort_order' => 0
];

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `hero_slides` WHERE `id` = ?");
        $stmt->execute([$id]);
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetched) {
            $item = $fetched;
        }
    } catch (PDOException $e) {
        die("Error fetching hero slide: " . $e->getMessage());
    }
}

$page_title = $id > 0 ? 'Edit Hero Slide' : 'Add Hero Slide';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="form-card">
    <div class="form-header">
        <h2 class="form-title"><?php echo $id > 0 ? 'Edit Hero Slide' : 'Add Hero Slide'; ?></h2>
        <p class="form-subtitle"><i class="fa-solid fa-circle-info"></i> Enter slide details and upload background image.</p>
    </div>

    <!-- Error Alert Banner -->
    <div id="error-alert" style="display: none; background-color: #FDF2F2; border: 1px solid #FDE8E8; color: #9B1C1C; padding: 1rem 2rem; margin: 1.5rem 2.5rem 0 2.5rem; border-radius: 8px; font-size: 0.95rem; align-items: center; gap: 0.6rem;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span id="error-alert-text">An error occurred.</span>
    </div>

    <form id="hero-slide-form" class="form-body" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
        
        <div class="form-grid">
            <div class="form-group">
                <label for="page" class="form-label">Target Page</label>
                <select name="page" id="page" class="form-control" style="background-color: var(--sand); border: 1px solid transparent; border-radius: 8px; padding: 0.75rem 1rem; color: var(--dark);" required>
                    <option value="home" <?php echo $item['page'] === 'home' ? 'selected' : ''; ?>>Homepage Cover Slider</option>
                    <option value="durga-puja" <?php echo $item['page'] === 'durga-puja' ? 'selected' : ''; ?>>Durga Puja Page Cover Slider</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sort_order" class="form-label">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control" placeholder="e.g. 1" value="<?php echo htmlspecialchars($item['sort_order']); ?>" required min="0">
            </div>

            <div class="form-group full-width">
                <label for="title" class="form-label">Slide Title</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="e.g. Durga Puja Celebrations" value="<?php echo htmlspecialchars($item['title']); ?>" required>
            </div>

            <div class="form-group full-width">
                <label for="subtitle" class="form-label">Slide Subtitle / Text Description</label>
                <textarea name="subtitle" id="subtitle" class="form-control" placeholder="e.g. Celebrating Culture, Preserving Tradition, Connecting Community" required><?php echo htmlspecialchars($item['subtitle']); ?></textarea>
            </div>

            <div class="form-group full-width">
                <label for="image_file" class="form-label">Cover Background Image</label>
                <input type="file" name="image_file" id="image_file" class="form-control" accept="image/*" <?php echo $id > 0 ? '' : 'required'; ?>>
                <small style="color: var(--gray); font-size: 0.8rem; margin-top: 0.4rem; display: block;">Supports JPG, JPEG, PNG, WEBP, and GIF images. Recommended resolution: 1920x1080.</small>
                
                <!-- Image Previews Container -->
                <div id="image-previews-container" style="display: flex; gap: 2rem; margin-top: 1.5rem; flex-wrap: wrap;">
                    <!-- Current Image Preview -->
                    <?php if ($id > 0 && !empty($item['image_path'])): ?>
                        <?php 
                        $curr_src = (strpos($item['image_path'], 'http') === 0) ? $item['image_path'] : '../' . $item['image_path'];
                        ?>
                        <div id="current-preview-box">
                            <span style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--gray); margin-bottom: 0.4rem; text-transform: uppercase;">Current Image (Click to zoom)</span>
                            <img id="current-preview-img" src="<?php echo htmlspecialchars($curr_src); ?>" alt="Current Preview" style="width: 180px; height: 110px; object-fit: cover; border-radius: 8px; border: 2px solid var(--border); cursor: pointer; transition: var(--transition);" onclick="openLightbox(this.src)">
                        </div>
                    <?php endif; ?>

                    <!-- New Image Preview (Client-side FileReader) -->
                    <div id="new-preview-box" style="display: none;">
                        <span style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--gray); margin-bottom: 0.4rem; text-transform: uppercase;">New Image Preview (Click to zoom)</span>
                        <img id="new-preview-img" src="" alt="New Preview" style="width: 180px; height: 110px; object-fit: cover; border-radius: 8px; border: 2px solid var(--gold); cursor: pointer; transition: var(--transition);" onclick="openLightbox(this.src)">
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Progress Bar Container -->
        <div id="progress-container" style="display: none; margin-top: 2rem; background-color: var(--sand); border: 1px solid var(--border); padding: 1.5rem; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
                <span id="progress-status" style="font-size: 0.9rem; font-weight: 700; color: var(--gray); text-transform: uppercase;"><i class="fa-solid fa-spinner fa-spin"></i> Uploading Image...</span>
                <span id="progress-percent" style="font-size: 0.95rem; font-weight: 800; color: var(--red);">0%</span>
            </div>
            <div style="width: 100%; height: 8px; background-color: rgba(33, 26, 23, 0.08); border-radius: 10px; overflow: hidden;">
                <div id="progress-bar" style="width: 0%; height: 100%; background: linear-gradient(90deg, var(--gold) 0%, var(--red) 100%); transition: width 0.1s ease; border-radius: 10px;"></div>
            </div>
        </div>

        <div class="btn-row">
            <a href="hero_settings.php" class="btn btn-cancel">Cancel</a>
            <button type="submit" class="btn btn-submit">Save Hero Slide <i class="fa-solid fa-floppy-disk"></i></button>
        </div>
    </form>
</div>

<!-- Original Size Image Lightbox Modal -->
<div id="lightbox-modal" class="lightbox-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background-color: rgba(33, 26, 23, 0.9); backdrop-filter: blur(8px); z-index: 20000; align-items: center; justify-content: center; padding: 2rem;">
    <button type="button" class="lightbox-close" style="position: absolute; top: 25px; right: 25px; background-color: var(--white); border: none; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.25rem; color: var(--dark); box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: var(--transition);" onclick="closeLightbox()"><i class="fa-solid fa-xmark"></i></button>
    <div class="lightbox-content" style="max-width: 90%; max-height: 85%; border-radius: 8px; border: 2px solid var(--white); background: #000; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <img id="lightbox-img" src="" alt="Full size view" style="display: block; width: auto; height: auto; max-width: 100%; max-height: 80vh; object-fit: contain; margin: 0 auto;">
    </div>
</div>

<style>
    .thumbnail-preview:hover,
    #current-preview-img:hover,
    #new-preview-img:hover {
        transform: scale(1.04);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }
    
    .lightbox-close:hover {
        background-color: var(--red) !important;
        color: var(--white) !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('image_file');
    const newPreviewBox = document.getElementById('new-preview-box');
    const newPreviewImg = document.getElementById('new-preview-img');
    const form = document.getElementById('hero-slide-form');
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const progressPercent = document.getElementById('progress-percent');
    const progressStatus = document.getElementById('progress-status');
    const errorAlert = document.getElementById('error-alert');
    const errorAlertText = document.getElementById('error-alert-text');

    // Show preview of selected image
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                newPreviewImg.src = e.target.result;
                newPreviewBox.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            newPreviewBox.style.display = 'none';
            newPreviewImg.src = '';
        }
    });

    // Handle AJAX Form Submission with Progress Bar
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Hide error alert if any
        errorAlert.style.display = 'none';
        
        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();
        
        // Show progress bar
        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';
        progressPercent.textContent = '0%';
        progressStatus.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Uploading Image...';

        // Track upload progress
        xhr.upload.addEventListener('progress', function(event) {
            if (event.lengthComputable) {
                const percent = Math.round((event.loaded / event.total) * 100);
                progressBar.style.width = percent + '%';
                progressPercent.textContent = percent + '%';
                if (percent === 100) {
                    progressStatus.innerHTML = '<i class="fa-solid fa-circle-check"></i> Saving Slide Details...';
                }
            }
        });

        // Track response
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        // Redirect on success
                        window.location.href = 'hero_settings.php?success=' + encodeURIComponent(response.message);
                    } else {
                        // Show error
                        showError(response.message);
                    }
                } catch (err) {
                    showError('Server returned an invalid response.');
                }
            } else {
                showError('Server error occurred during upload.');
            }
        };

        xhr.onerror = function() {
            showError('Network error occurred.');
        };

        // Send request
        xhr.open('POST', 'action.php?act=save_hero_slide', true);
        xhr.send(formData);
    });

    function showError(msg) {
        errorAlertText.textContent = msg;
        errorAlert.style.display = 'flex';
        progressContainer.style.display = 'none';
    }
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
