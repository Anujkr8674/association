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
    'image' => '',
    'category' => 'durga-puja'
];

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `gallery` WHERE `id` = ?");
        $stmt->execute([$id]);
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetched) {
            $item = $fetched;
        }
    } catch (PDOException $e) {
        die("Error fetching gallery item: " . $e->getMessage());
    }
}

try {
    $gallery_categories = $pdo->query("SELECT * FROM `gallery_categories` ORDER BY `name` ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $gallery_categories = [];
}
?>
<?php
$page_title = $id > 0 ? 'Edit Gallery Item' : 'Add Gallery Item';
require_once __DIR__ . '/includes/sidebar.php';
?>

    <style>
        .upload-dropzone {
            border: 2px dashed rgba(201, 154, 46, 0.4);
            border-radius: var(--border-radius);
            padding: 3rem 2rem;
            background-color: rgba(251, 244, 230, 0.15);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            margin-top: 0.5rem;
        }
        
        .upload-dropzone:hover, .upload-dropzone.dragover {
            border-color: var(--red);
            background-color: rgba(139, 30, 30, 0.03);
        }
        
        .upload-icon {
            font-size: 3rem;
            color: var(--gold);
            transition: all 0.3s ease;
        }
        
        .upload-dropzone:hover .upload-icon {
            color: var(--red);
            transform: translateY(-5px);
        }
        
        .upload-hint {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .previews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 1.25rem;
            margin: 1.5rem 0;
        }

        .preview-thumbnail-wrapper {
            position: relative;
            width: 100%;
            padding-top: 100%; /* 1:1 Aspect Ratio */
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            background-color: #f8f9fa;
        }

        .preview-thumbnail-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .preview-thumbnail-wrapper:hover img {
            transform: scale(1.08);
        }

        .preview-thumbnail-wrapper .remove-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            background: rgba(139, 30, 30, 0.85);
            color: var(--white);
            border: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            cursor: pointer;
            z-index: 10;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .preview-thumbnail-wrapper .remove-btn:hover {
            background: var(--red);
            transform: scale(1.1);
        }

        /* Progressive Progress Bar */
        .progress-container {
            display: none;
            margin: 2rem 0 1rem 0;
            background-color: var(--white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .progress-bar-track {
            height: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, var(--red) 0%, var(--gold) 100%);
            transition: width 0.1s linear;
            border-radius: 5px;
        }

        .progress-text {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--dark);
            margin-top: 0.8rem;
            display: block;
            text-align: center;
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

    <div class="form-card">
        <div class="form-header">
            <h1 class="form-title"><?php echo $id > 0 ? 'Modify' : 'New'; ?> Gallery Item</h1>
            <div class="form-subtitle">
                <i class="fa-solid fa-image-portrait"></i>
                <span>Add photos of community gatherings, Durga Puja events, and cultural programs.</span>
            </div>
        </div>

        <div class="form-body">
            <form id="gallery-form" action="action.php?act=save_gallery" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">

                <!-- Title -->
                <div class="form-group">
                    <label class="form-label" for="title">Item Title / Caption</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Dhunuchi Dance on Dashami" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label class="form-label" for="category">Category Filter</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($gallery_categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo strcasecmp($item['category'], $cat['name']) === 0 ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($id > 0): ?>
                    <!-- Edit Mode: Show current image and replace option -->
                    <div class="current-image-preview" style="margin: 1.5rem 0;">
                        <label class="form-label">Current Image</label>
                        <div style="position: relative; display: inline-block; margin-top: 0.5rem;">
                            <img src="../<?php echo htmlspecialchars($item['image']); ?>" alt="Current Image" style="max-height: 180px; border-radius: var(--border-radius); border: 1px solid var(--border-color); cursor: pointer;" id="current-image-thumb" loading="lazy">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="image_file">Replace Image</label>
                        <input type="file" id="image_file" name="image_file" class="form-control" accept="image/*">
                        <small class="upload-hint" style="margin-top: 0.5rem; display: block;">Leave blank to keep the current image.</small>
                    </div>
                <?php else: ?>
                    <!-- Add Mode: Drag & Drop Multi-file uploader -->
                    <div class="form-group">
                        <label class="form-label">Upload Images</label>
                        <div class="upload-dropzone" id="dropzone">
                            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                            <p style="font-weight: 700; font-size: 1.05rem; margin: 0;">Drag and drop your images here, or click to browse</p>
                            <span class="upload-hint">Supported formats: JPG, PNG, WEBP. You can select multiple images.</span>
                            <input type="file" id="images-input" name="images[]" class="file-input" multiple accept="image/*" style="display: none;">
                        </div>
                    </div>

                    <!-- Selected previews grid -->
                    <div class="previews-grid" id="previews-grid" style="display: none;"></div>
                <?php endif; ?>

                <!-- Progress container -->
                <div class="progress-container" id="progress-container">
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" id="progress-bar-fill"></div>
                    </div>
                    <span class="progress-text" id="progress-text">Uploading: 0%</span>
                </div>

                <div class="btn-row" style="margin-top: 2rem;">
                    <a href="gallery.php" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn btn-submit" id="upload-btn">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Save Gallery</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Original Size Image Modal -->
    <div class="image-preview-modal" id="preview-modal">
        <button class="image-preview-modal-close" id="preview-modal-close" aria-label="Close Preview"><i class="fa-solid fa-xmark"></i></button>
        <img src="" alt="Full Preview" class="image-preview-modal-content" id="preview-modal-img">
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const isEditMode = <?php echo $id > 0 ? 'true' : 'false'; ?>;
        const form = document.getElementById('gallery-form');
        const uploadBtn = document.getElementById('upload-btn');
        
        // Modal Preview Elements
        const previewModal = document.getElementById('preview-modal');
        const previewModalImg = document.getElementById('preview-modal-img');
        const previewModalClose = document.getElementById('preview-modal-close');
        
        function openModal(src) {
            previewModalImg.src = src;
            previewModal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            previewModal.classList.remove('open');
            document.body.style.overflow = '';
        }
        
        if (previewModalClose) {
            previewModalClose.addEventListener('click', closeModal);
        }
        if (previewModal) {
            previewModal.addEventListener('click', function(e) {
                if (e.target === previewModal) {
                    closeModal();
                }
            });
        }
        
        // Click on current image thumb to preview (Edit Mode)
        const currentImgThumb = document.getElementById('current-image-thumb');
        if (currentImgThumb) {
            currentImgThumb.addEventListener('click', function() {
                openModal(this.src);
            });
        }

        // Add Mode uploader features
        if (!isEditMode) {
            const dropzone = document.getElementById('dropzone');
            const imagesInput = document.getElementById('images-input');
            const previewsGrid = document.getElementById('previews-grid');
            let selectedFiles = [];
            
            // Trigger select file click
            dropzone.addEventListener('click', () => {
                imagesInput.click();
            });
            
            // Drag over effects
            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });
            
            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('dragover');
            });
            
            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
                if (e.dataTransfer.files.length > 0) {
                    handleFileSelection(e.dataTransfer.files);
                }
            });
            
            imagesInput.addEventListener('change', () => {
                if (imagesInput.files.length > 0) {
                    handleFileSelection(imagesInput.files);
                }
            });
            
            function handleFileSelection(files) {
                Array.from(files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        // Check if already selected
                        const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                        if (!exists) {
                            selectedFiles.push(file);
                        }
                    }
                });
                renderPreviews();
            }
            
            function renderPreviews() {
                previewsGrid.innerHTML = '';
                if (selectedFiles.length === 0) {
                    previewsGrid.style.display = 'none';
                    return;
                }
                
                previewsGrid.style.display = 'grid';
                
                selectedFiles.forEach((file, index) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'preview-thumbnail-wrapper';
                    
                    const img = document.createElement('img');
                    img.alt = file.name;
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                    
                    // Read and preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        img.addEventListener('click', () => {
                            openModal(e.target.result);
                        });
                    };
                    reader.readAsDataURL(file);
                    
                    removeBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        selectedFiles.splice(index, 1);
                        renderPreviews();
                    });
                    
                    wrapper.appendChild(img);
                    wrapper.appendChild(removeBtn);
                    previewsGrid.appendChild(wrapper);
                });
            }
            
            // Form submit override (Ajax progressive upload)
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (selectedFiles.length === 0) {
                    alert('Please select at least one image file to upload.');
                    return;
                }
                
                const formData = new FormData();
                formData.append('id', form.id.value);
                formData.append('title', form.title.value);
                formData.append('category', form.category.value);
                
                selectedFiles.forEach(file => {
                    formData.append('images[]', file);
                });
                
                performAjaxUpload(formData);
            });
            
        } else {
            // Edit Mode Form submit override
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('id', form.id.value);
                formData.append('title', form.title.value);
                formData.append('category', form.category.value);
                
                const fileInput = document.getElementById('image_file');
                if (fileInput && fileInput.files[0]) {
                    formData.append('image_file', fileInput.files[0]);
                }
                
                performAjaxUpload(formData);
            });
        }
        
        function performAjaxUpload(formData) {
            const progressContainer = document.getElementById('progress-container');
            const progressBarFill = document.getElementById('progress-bar-fill');
            const progressText = document.getElementById('progress-text');
            
            // Disable form inputs
            const inputs = form.querySelectorAll('input, select, button, a.btn');
            inputs.forEach(el => {
                if (el.tagName === 'A') {
                    el.style.pointerEvents = 'none';
                    el.style.opacity = '0.6';
                } else {
                    el.disabled = true;
                }
            });
            
            progressContainer.style.display = 'block';
            progressBarFill.style.width = '0%';
            progressText.innerText = 'Initializing upload...';
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'action.php?act=save_gallery&ajax=1', true);
            
            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBarFill.style.width = percent + '%';
                    progressText.innerText = `Uploading: ${percent}% (${Math.round(e.loaded/1024)}KB / ${Math.round(e.total/1024)}KB)`;
                }
            };
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.status === 'success') {
                            progressBarFill.style.width = '100%';
                            progressText.innerText = 'Upload complete! Redirecting...';
                            setTimeout(() => {
                                window.location.href = 'gallery.php?success=' + (isEditMode ? 'updated' : 'created');
                            }, 800);
                        } else {
                            alert('Upload failed: ' + (res.message || 'Unknown error'));
                            resetFormState();
                        }
                    } catch(err) {
                        // Fallback redirect on standard HTML response or unparseable JSON
                        window.location.href = 'gallery.php?success=' + (isEditMode ? 'updated' : 'created');
                    }
                } else {
                    alert('Server returned error status: ' + xhr.status);
                    resetFormState();
                }
            };
            
            xhr.onerror = function() {
                alert('A network error occurred during upload.');
                resetFormState();
            };
            
            xhr.send(formData);
            
            function resetFormState() {
                inputs.forEach(el => {
                    if (el.tagName === 'A') {
                        el.style.pointerEvents = '';
                        el.style.opacity = '';
                    } else {
                        el.disabled = false;
                    }
                });
                progressContainer.style.display = 'none';
            }
        }
    });
    </script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

