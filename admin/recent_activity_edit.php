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
    'image' => ''
];

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `recent_activities` WHERE `id` = ?");
        $stmt->execute([$id]);
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetched) {
            $item = $fetched;
        }
    } catch (PDOException $e) {
        die("Error fetching activity: " . $e->getMessage());
    }
}
?>
<?php
$page_title = $id > 0 ? 'Edit Activity' : 'Add Activity';
require_once __DIR__ . '/includes/sidebar.php';
?>

    <div class="form-card">
        <div class="form-header">
            <h1 class="form-title"><?php echo $id > 0 ? 'Edit' : 'New'; ?> Recent Activity</h1>
            <div class="form-subtitle">
                <i class="fa-solid fa-file-pen"></i>
                <span>Publish community programs, events and activities for the homepage circular cards.</span>
            </div>
        </div>

        <div class="form-body">
            <form id="activity-form" action="action.php?act=save_recent_activity" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">

                <div class="form-grid">
                    <!-- Title -->
                    <div class="form-group full-width">
                        <label class="form-label" for="title">Activity Title</label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Dandiya Night 2026" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                    </div>

                    <!-- Cover Image Upload -->
                    <div class="form-group full-width">
                        <label class="form-label" style="font-size: 1.1rem; color: var(--red); border-bottom: 2px solid var(--sand); padding-bottom: 0.35rem; margin-bottom: 1rem;"><i class="fa-solid fa-image"></i> Activity Cover Image (Single)</label>
                        <?php if ($id > 0): ?>
                            <!-- Edit Mode: Show current cover and replacement option -->
                            <div class="current-image-preview" style="margin-bottom: 0.8rem;">
                                <label class="form-label">Current Cover Image</label>
                                <div style="position: relative; display: inline-block; margin-top: 0.35rem;">
                                    <?php if (!empty($item['image'])): ?>
                                        <?php
                                        $current_img = htmlspecialchars($item['image']);
                                        if (strpos($item['image'], 'http') !== 0) {
                                            $current_img = '../' . $current_img;
                                        }
                                        ?>
                                        <img src="<?php echo $current_img; ?>" alt="Current Image" style="max-height: 120px; border-radius: var(--border-radius); border: 1px solid var(--border-color); cursor: pointer;" id="current-image-thumb" loading="lazy">
                                    <?php else: ?>
                                        <div style="width: 100px; height: 70px; background-color: var(--sand); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--gray); font-size: 0.8rem;"><i class="fa-solid fa-image"></i> No Cover</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label" for="image_file">Replace Cover Image</label>
                                <input type="file" id="image_file" name="image_file" class="form-control" accept="image/*">
                                <small class="upload-hint" style="margin-top: 0.4rem; display: block;">Leave blank to keep current cover.</small>
                            </div>
                        <?php else: ?>
                            <!-- Add Mode: Drag & Drop Cover Image -->
                            <div class="upload-dropzone" id="dropzone">
                                <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                                <p style="font-weight: 700; font-size: 1rem; margin: 0;">Drag and drop COVER image here, or click to browse</p>
                                <span class="upload-hint">Supported formats: JPG, PNG, WEBP. Single file.</span>
                                <input type="file" id="images-input" name="images[]" class="file-input" accept="image/*" style="display: none;">
                            </div>
                            
                            <!-- Previews grid -->
                            <div class="previews-grid" id="previews-grid" style="display: none;"></div>
                        <?php endif; ?>
                    </div>

                    <!-- Additional Gallery Photos Upload -->
                    <div class="form-group full-width" style="margin-top: 1.5rem;">
                        <label class="form-label" style="font-size: 1.1rem; color: var(--red); border-bottom: 2px solid var(--sand); padding-bottom: 0.35rem; margin-bottom: 1rem;"><i class="fa-solid fa-images"></i> Activity Gallery / Additional Photos (Multiple)</label>
                        
                        <!-- Drag & Drop Zone for Gallery -->
                        <div class="upload-dropzone" id="gallery-dropzone">
                            <i class="fa-solid fa-images upload-icon"></i>
                            <p style="font-weight: 700; font-size: 1rem; margin: 0;">Drag and drop GALLERY images here, or click to browse</p>
                            <span class="upload-hint">Upload whatever number of images you want. Multi-file support.</span>
                            <input type="file" id="gallery-input" name="activity_gallery[]" class="file-input" accept="image/*" multiple style="display: none;">
                        </div>

                        <!-- Active Gallery Previews (including dynamic deletions in Edit Mode) -->
                        <div class="previews-grid" id="gallery-previews-grid">
                            <?php 
                            if ($id > 0 && !empty($item['additional_images'])): 
                                $gal_images = array_filter(explode(',', $item['additional_images']));
                                foreach ($gal_images as $idx => $g_img):
                                    $g_src = htmlspecialchars(trim($g_img));
                                    $g_full = (strpos($g_img, 'http') === 0) ? $g_src : '../' . $g_src;
                                    ?>
                                    <div class="preview-thumbnail-wrapper existing-gallery-item" data-path="<?php echo $g_src; ?>">
                                        <img src="<?php echo $g_full; ?>" alt="Gallery Image" class="gallery-preview-img" loading="lazy">
                                        <button type="button" class="remove-btn delete-existing-gallery-btn" title="Delete Image"><i class="fa-solid fa-trash-can"></i></button>
                                        <input type="hidden" name="retained_gallery_images[]" value="<?php echo $g_src; ?>">
                                    </div>
                                <?php 
                                endforeach;
                            endif; 
                            ?>
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="form-group full-width">
                        <div class="progress-container" id="progress-container">
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill" id="progress-bar-fill"></div>
                            </div>
                            <span class="progress-text" id="progress-text">Uploading: 0%</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group full-width">
                        <label class="form-label" for="description">Full Activity Description</label>
                        <textarea id="description" name="description" class="form-control" placeholder="Write full details about the activity..." style="min-height: 180px;" required><?php echo htmlspecialchars($item['description']); ?></textarea>
                    </div>
                </div>

                <div class="btn-row">
                    <a href="recent_activities.php" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn btn-submit" id="upload-btn">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Publish Activity</span>
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

    <style>
        .upload-dropzone {
            border: 2px dashed rgba(201, 154, 46, 0.4);
            border-radius: var(--border-radius);
            padding: 2.2rem 1.5rem;
            background-color: rgba(251, 244, 230, 0.15);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            margin-top: 0.4rem;
        }
        
        .upload-dropzone:hover, .upload-dropzone.dragover {
            border-color: var(--red);
            background-color: rgba(139, 30, 30, 0.03);
        }
        
        .upload-icon {
            font-size: 2.5rem;
            color: var(--gold);
            transition: all 0.3s ease;
        }
        
        .upload-dropzone:hover .upload-icon {
            color: var(--red);
            transform: translateY(-4px);
        }
        
        .upload-hint {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .previews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 1rem;
            margin: 1.2rem 0;
        }

        .preview-thumbnail-wrapper {
            position: relative;
            width: 100%;
            padding-top: 100%; /* 1:1 Aspect Ratio */
            border-radius: 6px;
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
            top: 4px;
            right: 4px;
            background: rgba(139, 30, 30, 0.85);
            color: var(--white);
            border: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
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
            margin: 1rem 0;
            background-color: var(--white);
            padding: 1.2rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .progress-bar-track {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, var(--red) 0%, var(--gold) 100%);
            transition: width 0.1s linear;
            border-radius: 4px;
        }

        .progress-text {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--dark);
            margin-top: 0.6rem;
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const isEditMode = <?php echo $id > 0 ? 'true' : 'false'; ?>;
        const form = document.getElementById('activity-form');
        
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

        let selectedCoverFiles = [];
        let selectedGalleryFiles = [];
        const previewsGrid = document.getElementById('previews-grid');
        const galleryPreviewsGrid = document.getElementById('gallery-previews-grid');

        // Cover Selection Logic
        if (!isEditMode) {
            const dropzone = document.getElementById('dropzone');
            const imagesInput = document.getElementById('images-input');
            
            dropzone.addEventListener('click', () => {
                imagesInput.click();
            });
            
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
                    handleCoverSelection(e.dataTransfer.files);
                }
            });
            
            imagesInput.addEventListener('change', () => {
                if (imagesInput.files.length > 0) {
                    handleCoverSelection(imagesInput.files);
                }
            });
            
            function handleCoverSelection(files) {
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        selectedCoverFiles = [file];
                    }
                }
                renderCoverPreviews();
            }
            
            function renderCoverPreviews() {
                previewsGrid.innerHTML = '';
                if (selectedCoverFiles.length === 0) {
                    previewsGrid.style.display = 'none';
                    return;
                }
                previewsGrid.style.display = 'grid';
                
                selectedCoverFiles.forEach((file, index) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'preview-thumbnail-wrapper';
                    
                    const img = document.createElement('img');
                    img.alt = file.name;
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                    
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
                        selectedCoverFiles = [];
                        renderCoverPreviews();
                    });
                    
                    wrapper.appendChild(img);
                    wrapper.appendChild(removeBtn);
                    previewsGrid.appendChild(wrapper);
                });
            }
        }

        // Additional Gallery Logic
        const galleryDropzone = document.getElementById('gallery-dropzone');
        const galleryInput = document.getElementById('gallery-input');
        
        galleryDropzone.addEventListener('click', () => {
            galleryInput.click();
        });
        
        galleryDropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            galleryDropzone.classList.add('dragover');
        });
        
        galleryDropzone.addEventListener('dragleave', () => {
            galleryDropzone.classList.remove('dragover');
        });
        
        galleryDropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            galleryDropzone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                handleGallerySelection(e.dataTransfer.files);
            }
        });
        
        galleryInput.addEventListener('change', () => {
            if (galleryInput.files.length > 0) {
                handleGallerySelection(galleryInput.files);
            }
        });
        
        function handleGallerySelection(files) {
            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    selectedGalleryFiles.push(file);
                }
            });
            renderGalleryPreviews();
        }

        // Handle existing gallery deletions
        const deleteExistingBtns = document.querySelectorAll('.delete-existing-gallery-btn');
        deleteExistingBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (confirm('Are you sure you want to delete this gallery image?')) {
                    this.closest('.existing-gallery-item').remove();
                }
            });
        });

        // Clicking on existing gallery images to zoom
        const galleryPreviewImages = document.querySelectorAll('.gallery-preview-img');
        galleryPreviewImages.forEach(img => {
            img.addEventListener('click', function() {
                openModal(this.src);
            });
        });

        function renderGalleryPreviews() {
            // Keep existing gallery items, clear only newly selected preview thumbs
            const newPreviews = galleryPreviewsGrid.querySelectorAll('.new-gallery-preview-item');
            newPreviews.forEach(el => el.remove());
            
            selectedGalleryFiles.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'preview-thumbnail-wrapper new-gallery-preview-item';
                
                const img = document.createElement('img');
                img.alt = file.name;
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-btn';
                removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                
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
                    selectedGalleryFiles.splice(index, 1);
                    renderGalleryPreviews();
                });
                
                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                galleryPreviewsGrid.appendChild(wrapper);
            });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!isEditMode && selectedCoverFiles.length === 0) {
                alert('Please select a cover image for this activity.');
                return;
            }
            
            const formData = new FormData();
            formData.append('id', form.id.value);
            formData.append('title', form.title.value);
            formData.append('description', form.description.value);
            
            if (isEditMode) {
                const fileInput = document.getElementById('image_file');
                if (fileInput && fileInput.files[0]) {
                    formData.append('image_file', fileInput.files[0]);
                }
                
                // Append retained gallery images
                const retainedInputs = document.querySelectorAll('input[name="retained_gallery_images[]"]');
                retainedInputs.forEach(input => {
                    formData.append('retained_gallery_images[]', input.value);
                });
            } else {
                formData.append('images[]', selectedCoverFiles[0]);
            }
            
            // Append newly uploaded gallery files
            selectedGalleryFiles.forEach(file => {
                formData.append('activity_gallery[]', file);
            });
            
            performAjaxUpload(formData);
        });
        
        function performAjaxUpload(formData) {
            const progressContainer = document.getElementById('progress-container');
            const progressBarFill = document.getElementById('progress-bar-fill');
            const progressText = document.getElementById('progress-text');
            
            const inputs = form.querySelectorAll('input, select, textarea, button, a.btn');
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
            xhr.open('POST', 'action.php?act=save_recent_activity&ajax=1', true);
            
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
                                window.location.href = 'recent_activities.php?success=' + (isEditMode ? 'updated' : 'created');
                            }, 800);
                        } else {
                            alert('Upload failed: ' + (res.message || 'Unknown error'));
                            resetFormState();
                        }
                    } catch(err) {
                        window.location.href = 'recent_activities.php?success=' + (isEditMode ? 'updated' : 'created');
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
