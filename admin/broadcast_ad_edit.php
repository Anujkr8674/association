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
    'description' => '',
    'images' => '[]',
    'status' => 0
];

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `broadcast_ads` WHERE `id` = ?");
        $stmt->execute([$id]);
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetched) {
            $item = $fetched;
        }
    } catch (PDOException $e) {
        die("Error fetching broadcast ad: " . $e->getMessage());
    }
}

$page_title = $id > 0 ? 'Edit Broadcast Ad' : 'Create Broadcast Ad';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="form-card">
    <div class="form-header">
        <h1 class="form-title"><?php echo $id > 0 ? 'Edit' : 'New'; ?> Broadcast Ad Popup</h1>
        <div class="form-subtitle">
            <i class="fa-solid fa-rectangle-ad"></i>
            <span>Set up event promotion or important notices to broadcast directly to your site visitors.</span>
        </div>
    </div>

    <div class="form-body">
        <form id="broadcast-ad-form" novalidate>
            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">

            <div class="form-grid">
                <!-- Title -->
                <div class="form-group full-width">
                    <label class="form-label" for="title">Broadcast Title *</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Noida BCA Durga Puja 2026 Promo" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                    <span class="form-error" id="title-error" style="color: var(--vermilion); font-size: 0.8rem; display: none;">Title is required.</span>
                </div>

                <!-- Description -->
                <div class="form-group full-width">
                    <label class="form-label" for="description">Short Description * (Max 2 lines recommended)</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Vibrant Dhunuchi dance, food stalls, and kids drawing events. Free entry for all!" style="min-height: 100px;" required><?php echo htmlspecialchars($item['description']); ?></textarea>
                    <span class="form-error" id="description-error" style="color: var(--vermilion); font-size: 0.8rem; display: none;">Description is required.</span>
                </div>

                <!-- Status (On/Off) -->
                <div class="form-group">
                    <label class="form-label" for="status">Initial Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="0" <?php echo $item['status'] == 0 ? 'selected' : ''; ?>>Off / Inactive</option>
                        <option value="1" <?php echo $item['status'] == 1 ? 'selected' : ''; ?>>On / Active (Will turn off all other active ads)</option>
                    </select>
                </div>

                <!-- Image Upload Section -->
                <div class="form-group full-width">
                    <label class="form-label">Broadcast Images (Max 3, formats: PNG, JPG, JPEG, WEBP) *</label>
                    
                    <!-- Pre-existing images -->
                    <?php 
                    $existing_images = json_decode($item['images'], true);
                    if (!is_array($existing_images)) $existing_images = [];
                    ?>
                    <div id="existing-images-container" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem;">
                        <?php foreach ($existing_images as $idx => $img_path): ?>
                            <div class="existing-img-card" id="existing-img-<?php echo $idx; ?>" style="position: relative; width: 100px; height: 100px; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden; cursor: pointer;">
                                <img src="../<?php echo htmlspecialchars($img_path); ?>" class="lightbox-trigger" style="width: 100%; height: 100%; object-fit: cover;" alt="Existing Ad Image">
                                <input type="hidden" name="kept_images[]" value="<?php echo htmlspecialchars($img_path); ?>">
                                <button type="button" onclick="removeExistingImg(<?php echo $idx; ?>)" style="position: absolute; top: 4px; right: 4px; background: rgba(200, 59, 45, 0.9); border: none; color: white; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;"><i class="fa-solid fa-times"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Drag & Drop Zone -->
                    <div id="ad-image-dropzone" style="border: 2px dashed var(--border-color); padding: 2rem 1.5rem; text-align: center; border-radius: var(--border-radius); background-color: var(--primary-bg); cursor: pointer; transition: var(--transition);">
                        <i class="fa-solid fa-images" style="font-size: 2.2rem; color: var(--red); opacity: 0.7; margin-bottom: 0.75rem;"></i>
                        <h4 style="margin-bottom: 0.35rem; color: var(--dark);">Click or Drag images here to upload</h4>
                        <p style="font-size: 0.8rem; color: var(--gray); margin-bottom: 0;">Add up to 3 beautiful promotional slides. Click on previews to view full size.</p>
                        <input type="file" id="ad-images-input" name="images[]" multiple accept="image/*" style="display: none;">
                    </div>

                    <!-- Upload Queue List -->
                    <div id="ad-images-queue" style="margin-top: 1.25rem; display: flex; gap: 1rem; flex-wrap: wrap;"></div>
                </div>

                <!-- Progress Bar -->
                <div id="progress-wrapper" class="form-group full-width" style="display: none; margin-top: 1rem;">
                    <label class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Uploading Files...</span>
                        <span id="progress-percent" style="font-weight: 700; color: var(--red);">0%</span>
                    </label>
                    <div style="width: 100%; height: 10px; background-color: var(--border-color); border-radius: 5px; overflow: hidden; margin-top: 0.4rem;">
                        <div id="progress-bar" style="width: 0%; height: 100%; background-color: var(--red); transition: width 0.1s linear;"></div>
                    </div>
                </div>

                <!-- Submit / Cancel -->
                <div class="form-group full-width" style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <a href="broadcast_ads.php" class="btn btn-secondary" style="padding: 0.8rem 2rem; border-radius: 30px; font-weight: 700; text-align: center; text-decoration: none; border: 1px solid var(--border-color); color: var(--gray); flex: 1;">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem; border-radius: 30px; font-weight: 700; background-color: var(--red); color: var(--white); border: none; cursor: pointer; flex: 2; display: flex; align-items: center; justify-content: center; gap: 0.6rem;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Broadcast Ad
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- IMAGE LIGHTBOX MODAL OVERLAY -->
<div id="image-lightbox-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(21, 26, 23, 0.85); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(5px); opacity: 0; transition: opacity 0.3s ease;">
    <div style="position: relative; max-width: 90%; max-height: 85%; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
        <img id="lightbox-full-img" src="" style="max-width: 100%; max-height: 80vh; object-fit: contain; display: block;" alt="Full Size">
        <button id="lightbox-close-btn" style="position: absolute; top: 12px; right: 12px; background: rgba(33, 26, 23, 0.8); border: none; color: white; font-size: 1.25rem; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: var(--transition);"><i class="fa-solid fa-xmark"></i></button>
    </div>
</div>

<style>
    #ad-image-dropzone:hover {
        background-color: var(--secondary-bg);
        border-color: var(--red);
    }
    .queued-img-card {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        cursor: pointer;
    }
    .queued-img-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .queued-img-remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(200, 59, 45, 0.9);
        border: none;
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
</style>

<script>
    let selectedFiles = [];
    const dropzone = document.getElementById('ad-image-dropzone');
    const fileInput = document.getElementById('ad-images-input');
    const queueContainer = document.getElementById('ad-images-queue');
    const existingContainer = document.getElementById('existing-images-container');

    // Lightbox modal elements
    const lightboxModal = document.getElementById('image-lightbox-modal');
    const lightboxImg = document.getElementById('lightbox-full-img');
    const lightboxClose = document.getElementById('lightbox-close-btn');

    // Trigger file dialog
    dropzone.addEventListener('click', () => fileInput.click());

    // Drag events
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = 'var(--red)';
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.style.borderColor = '';
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '';
        if (e.dataTransfer.files.length > 0) {
            handleNewFiles(e.dataTransfer.files);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleNewFiles(fileInput.files);
        }
    });

    function getKeptCount() {
        if (!existingContainer) return 0;
        return existingContainer.querySelectorAll('.existing-img-card').length;
    }

    function handleNewFiles(files) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Validate image type
            if (!file.type.startsWith('image/')) {
                alert('Only image files are supported: ' + file.name);
                continue;
            }

            // Total count check
            if (getKeptCount() + selectedFiles.length >= 3) {
                alert('You can upload a maximum of 3 images.');
                break;
            }

            if (selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                continue;
            }

            selectedFiles.push(file);
        }
        renderQueue();
    }

    function renderQueue() {
        queueContainer.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'queued-img-card';

            const img = document.createElement('img');
            img.alt = 'Preview';
            
            const reader = new FileReader();
            reader.onload = (e) => { 
                img.src = e.target.result; 
                // Add click listener to show lightbox
                card.addEventListener('click', (ev) => {
                    if (ev.target.closest('.queued-img-remove-btn')) return; // Ignore if clicked close
                    showLightbox(e.target.result);
                });
            };
            reader.readAsDataURL(file);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'queued-img-remove-btn';
            removeBtn.innerHTML = '<i class="fa-solid fa-times"></i>';
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                selectedFiles.splice(index, 1);
                renderQueue();
            });

            card.appendChild(img);
            card.appendChild(removeBtn);
            queueContainer.appendChild(card);
        });
    }

    function removeExistingImg(idx) {
        const row = document.getElementById('existing-img-' + idx);
        if (row) {
            row.remove();
        }
    }

    // Lightbox triggers for existing images
    document.querySelectorAll('.lightbox-trigger').forEach(img => {
        img.addEventListener('click', function() {
            showLightbox(this.src);
        });
    });

    function showLightbox(src) {
        lightboxImg.src = src;
        lightboxModal.style.display = 'flex';
        setTimeout(() => {
            lightboxModal.style.opacity = '1';
        }, 10);
    }

    function hideLightbox() {
        lightboxModal.style.opacity = '0';
        setTimeout(() => {
            lightboxModal.style.display = 'none';
            lightboxImg.src = '';
        }, 300);
    }

    lightboxClose.addEventListener('click', hideLightbox);
    lightboxModal.addEventListener('click', (e) => {
        if (e.target === lightboxModal) hideLightbox();
    });

    // Form submit
    const form = document.getElementById('broadcast-ad-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const title = document.getElementById('title');
        const description = document.getElementById('description');
        let isValid = true;

        if (title.value.trim() === '') {
            document.getElementById('title-error').style.display = 'block';
            title.style.borderColor = 'var(--vermilion)';
            isValid = false;
        } else {
            document.getElementById('title-error').style.display = 'none';
            title.style.borderColor = '';
        }

        if (description.value.trim() === '') {
            document.getElementById('description-error').style.display = 'block';
            description.style.borderColor = 'var(--vermilion)';
            isValid = false;
        } else {
            document.getElementById('description-error').style.display = 'none';
            description.style.borderColor = '';
        }

        if (!isValid) return;

        const formData = new FormData(form);
        formData.delete('images[]'); // Remove default empty inputs

        // Append actual files
        selectedFiles.forEach(file => {
            formData.append('images[]', file);
        });

        const xhr = new XMLHttpRequest();
        const progressWrapper = document.getElementById('progress-wrapper');
        const progressBar = document.getElementById('progress-bar');
        const progressPercent = document.getElementById('progress-percent');

        progressWrapper.style.display = 'block';

        xhr.upload.addEventListener('progress', function(event) {
            if (event.lengthComputable) {
                const percent = Math.round((event.loaded / event.total) * 100);
                progressBar.style.width = percent + '%';
                progressPercent.textContent = percent + '%';
            }
        });

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.status === 'success') {
                        progressBar.style.backgroundColor = '#25D366';
                        setTimeout(() => {
                            window.location.href = 'broadcast_ads.php?success=saved';
                        }, 500);
                    } else {
                        alert('Error: ' + res.message);
                        progressWrapper.style.display = 'none';
                    }
                } catch(e) {
                    alert('Invalid server response.');
                    progressWrapper.style.display = 'none';
                }
            } else {
                alert('HTTP Error: ' + xhr.status);
                progressWrapper.style.display = 'none';
            }
        };

        xhr.onerror = function() {
            alert('A network error occurred.');
            progressWrapper.style.display = 'none';
        };

        xhr.open('POST', 'action.php?act=save_broadcast_ad', true);
        xhr.send(formData);
    });
</script>
