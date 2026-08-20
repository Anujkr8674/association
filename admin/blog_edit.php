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
    'excerpt' => '',
    'content' => '',
    'image' => '',
    'category' => 'Heritage',
    'date' => date('Y-m-d'),
    'author' => 'Executive Committee'
];

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `blogs` WHERE `id` = ?");
        $stmt->execute([$id]);
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetched) {
            $item = $fetched;
        }
    } catch (PDOException $e) {
        die("Error fetching blog post: " . $e->getMessage());
    }
}

try {
    $blog_categories = $pdo->query("SELECT * FROM `blog_categories` ORDER BY `name` ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $blog_categories = [];
}
?>
<?php
$page_title = $id > 0 ? 'Edit Blog Post' : 'Add Blog Post';
require_once __DIR__ . '/includes/sidebar.php';
?>

    <div class="form-card">
        <div class="form-header">
            <h1 class="form-title"><?php echo $id > 0 ? 'Edit' : 'New'; ?> Blog Post</h1>
            <div class="form-subtitle">
                <i class="fa-solid fa-file-pen"></i>
                <span>Publish articles, cultural essays, or announcements for the blog section.</span>
            </div>
        </div>

        <div class="form-body">
            <form id="blog-form" action="action.php?act=save_blog" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">

                <div class="form-grid">
                    <!-- Title -->
                    <div class="form-group full-width">
                        <label class="form-label" for="title">Blog Title</label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Preserving Bengali Traditions in NCR" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                    </div>

                    <!-- Category -->
                    <div class="form-group">
                        <label class="form-label" for="category">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php foreach ($blog_categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo strcasecmp($item['category'], $cat['name']) === 0 ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Author -->
                    <div class="form-group">
                        <label class="form-label" for="author">Author Name</label>
                        <input type="text" id="author" name="author" class="form-control" placeholder="e.g. Cultural Secretary" value="<?php echo htmlspecialchars($item['author']); ?>" required>
                    </div>

                    <!-- Publish Date -->
                    <div class="form-group">
                        <label class="form-label" for="date">Publish Date</label>
                        <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($item['date']); ?>" required>
                    </div>

                    <!-- Cover Image Upload -->
                    <div class="form-group">
                        <?php if ($id > 0): ?>
                            <!-- Edit Mode: Show current cover and replacement option -->
                            <div class="current-image-preview" style="margin-bottom: 0.8rem;">
                                <label class="form-label">Current Cover Image</label>
                                <div style="position: relative; display: inline-block; margin-top: 0.35rem;">
                                    <?php if (!empty($item['image'])): ?>
                                        <?php
                                        $current_cover = htmlspecialchars($item['image']);
                                        if (strpos($item['image'], 'http') !== 0) {
                                            $current_cover = '../' . $current_cover;
                                        }
                                        ?>
                                        <img src="<?php echo $current_cover; ?>" alt="Current Cover Image" style="max-height: 120px; border-radius: var(--border-radius); border: 1px solid var(--border-color); cursor: pointer;" id="current-image-thumb" loading="lazy">
                                    <?php else: ?>
                                        <div style="width: 100px; height: 70px; background-color: var(--sand); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--gray); font-size: 0.8rem;"><i class="fa-solid fa-image"></i> No Cover</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label" for="image_file">Replace Cover Image</label>
                                <input type="file" id="image_file" name="image_file" class="form-control" accept="image/*">
                                <small class="upload-hint" style="margin-top: 0.4rem; display: block;">Leave blank to keep current cover image.</small>
                            </div>
                        <?php else: ?>
                            <!-- Add Mode: Drag & Drop Cover Image -->
                            <label class="form-label">Upload Cover Image</label>
                            <div class="upload-dropzone" id="dropzone">
                                <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                                <p style="font-weight: 700; font-size: 1rem; margin: 0;">Drag and drop cover here, or click to browse</p>
                                <span class="upload-hint">Supported formats: JPG, PNG, WEBP. Single file.</span>
                                <input type="file" id="images-input" name="images[]" class="file-input" accept="image/*" style="display: none;">
                            </div>
                            
                            <!-- Previews grid -->
                            <div class="previews-grid" id="previews-grid" style="display: none;"></div>
                        <?php endif; ?>
                    </div>

                    <!-- Blog Gallery / Additional Photos Upload -->
                    <div class="form-group">
                        <label class="form-label">Blog Gallery / Additional Photos</label>
                        
                        <?php if ($id > 0 && !empty($item['additional_images'])): ?>
                            <!-- Edit Mode: Show current additional images with individual delete buttons -->
                            <div class="current-additional-images-grid" style="display: flex; gap: 0.8rem; flex-wrap: wrap; margin-bottom: 0.8rem; margin-top: 0.35rem;">
                                <?php
                                $add_imgs = array_filter(explode(',', $item['additional_images']));
                                foreach ($add_imgs as $img_path):
                                    $img_src = htmlspecialchars(trim($img_path));
                                    if (strpos($img_src, 'http') !== 0) {
                                        $img_src = '../' . $img_src;
                                    }
                                    ?>
                                    <div class="current-additional-img-wrapper" style="position: relative; width: 80px; height: 80px; border-radius: 6px; border: 1px solid var(--border-color); overflow: hidden;" data-path="<?php echo htmlspecialchars(trim($img_path)); ?>">
                                        <img src="<?php echo $img_src; ?>" style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;" class="current-add-img-thumb" loading="lazy">
                                        <button type="button" class="delete-additional-img-btn" style="position: absolute; top: 4px; right: 4px; background: rgba(139, 30, 30, 0.85); color: var(--white); border: none; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; cursor: pointer; transition: var(--transition);"><i class="fa-solid fa-trash-can"></i></button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Hidden input to track remaining images -->
                            <input type="hidden" name="existing_additional_images" id="existing_additional_images" value="<?php echo htmlspecialchars($item['additional_images']); ?>">
                        <?php else: ?>
                            <input type="hidden" name="existing_additional_images" id="existing_additional_images" value="">
                        <?php endif; ?>

                        <!-- Dropzone for new additional images -->
                        <div class="upload-dropzone" id="additional-dropzone">
                            <i class="fa-solid fa-images upload-icon"></i>
                            <p style="font-weight: 700; font-size: 1rem; margin: 0;">Drag and drop gallery photos here, or click to browse</p>
                            <span class="upload-hint">Supported formats: JPG, PNG, WEBP. Select multiple images.</span>
                            <input type="file" id="additional-images-input" name="additional_images[]" class="file-input" multiple accept="image/*" style="display: none;">
                        </div>
                        
                        <!-- Previews grid for newly selected additional images -->
                        <div class="previews-grid" id="additional-previews-grid" style="display: none;"></div>
                    </div>

                    <!-- Progress bar (full-width) -->
                    <div class="form-group full-width" style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                        <div class="progress-container" id="progress-container">
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill" id="progress-bar-fill"></div>
                            </div>
                            <span class="progress-text" id="progress-text">Uploading: 0%</span>
                        </div>
                    </div>

                    <!-- Excerpt -->
                    <div class="form-group full-width">
                        <label class="form-label" for="excerpt">Brief Excerpt (Short Preview)</label>
                        <input type="text" id="excerpt" name="excerpt" class="form-control" placeholder="Provide a one-sentence summary that appears on the card list..." value="<?php echo htmlspecialchars($item['excerpt']); ?>" required>
                    </div>

                    <!-- Full Content -->
                    <div class="form-group full-width">
                        <label class="form-label" for="content">Full Article Content</label>
                        <textarea id="content" name="content" class="form-control" placeholder="Write the main article paragraphs here..." style="min-height: 250px;" required><?php echo htmlspecialchars($item['content']); ?></textarea>
                    </div>
                </div>

                <div class="btn-row">
                    <a href="blogs.php" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn btn-submit" id="upload-btn">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Publish Post</span>
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
        const form = document.getElementById('blog-form');
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

        // Edit Mode: Handle deleting existing gallery images
        const deleteAdditionalBtns = document.querySelectorAll('.delete-additional-img-btn');
        const existingAdditionalInput = document.getElementById('existing_additional_images');
        
        deleteAdditionalBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const wrapper = this.closest('.current-additional-img-wrapper');
                const pathToDelete = wrapper.getAttribute('data-path');
                
                let currentPaths = existingAdditionalInput.value.split(',').filter(p => p.trim() !== '');
                currentPaths = currentPaths.filter(p => p !== pathToDelete);
                existingAdditionalInput.value = currentPaths.join(',');
                
                wrapper.remove();
            });
        });

        const currentAddThumbs = document.querySelectorAll('.current-add-img-thumb');
        currentAddThumbs.forEach(thumb => {
            thumb.addEventListener('click', function() {
                openModal(this.src);
            });
        });

        // 1. Cover Image Dropzone (Only in Add Mode)
        let selectedCoverFiles = [];
        if (!isEditMode) {
            const dropzone = document.getElementById('dropzone');
            const imagesInput = document.getElementById('images-input');
            const previewsGrid = document.getElementById('previews-grid');
            
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

        // 2. Additional Gallery Images Dropzone (Both Add and Edit Mode)
        const additionalDropzone = document.getElementById('additional-dropzone');
        const additionalImagesInput = document.getElementById('additional-images-input');
        const additionalPreviewsGrid = document.getElementById('additional-previews-grid');
        let selectedAdditionalFiles = [];

        if (additionalDropzone) {
            additionalDropzone.addEventListener('click', () => {
                additionalImagesInput.click();
            });
            
            additionalDropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                additionalDropzone.classList.add('dragover');
            });
            
            additionalDropzone.addEventListener('dragleave', () => {
                additionalDropzone.classList.remove('dragover');
            });
            
            additionalDropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                additionalDropzone.classList.remove('dragover');
                if (e.dataTransfer.files.length > 0) {
                    handleAdditionalSelection(e.dataTransfer.files);
                }
            });
            
            additionalImagesInput.addEventListener('change', () => {
                if (additionalImagesInput.files.length > 0) {
                    handleAdditionalSelection(additionalImagesInput.files);
                }
            });
        }

        function handleAdditionalSelection(files) {
            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const exists = selectedAdditionalFiles.some(f => f.name === file.name && f.size === file.size);
                    if (!exists) {
                        selectedAdditionalFiles.push(file);
                    }
                }
            });
            renderAdditionalPreviews();
        }

        function renderAdditionalPreviews() {
            additionalPreviewsGrid.innerHTML = '';
            if (selectedAdditionalFiles.length === 0) {
                additionalPreviewsGrid.style.display = 'none';
                return;
            }
            additionalPreviewsGrid.style.display = 'grid';
            
            selectedAdditionalFiles.forEach((file, index) => {
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
                    selectedAdditionalFiles.splice(index, 1);
                    renderAdditionalPreviews();
                });
                
                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                additionalPreviewsGrid.appendChild(wrapper);
            });
        }

        // 3. Form Submission override
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!isEditMode && selectedCoverFiles.length === 0) {
                alert('Please select a cover image for the blog post.');
                return;
            }
            
            const formData = new FormData();
            formData.append('id', form.id.value);
            formData.append('title', form.title.value);
            formData.append('category', form.category.value);
            formData.append('author', form.author.value);
            formData.append('date', form.date.value);
            formData.append('excerpt', form.excerpt.value);
            formData.append('content', form.content.value);
            formData.append('existing_additional_images', existingAdditionalInput.value);
            
            // Append cover file
            if (isEditMode) {
                const fileInput = document.getElementById('image_file');
                if (fileInput && fileInput.files[0]) {
                    formData.append('image_file', fileInput.files[0]);
                }
            } else {
                formData.append('images[]', selectedCoverFiles[0]);
            }
            
            // Append additional gallery images
            selectedAdditionalFiles.forEach(file => {
                formData.append('additional_images[]', file);
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
            xhr.open('POST', 'action.php?act=save_blog&ajax=1', true);
            
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
                                window.location.href = 'blogs.php?success=' + (isEditMode ? 'updated' : 'created');
                            }, 800);
                        } else {
                            alert('Upload failed: ' + (res.message || 'Unknown error'));
                            resetFormState();
                        }
                    } catch(err) {
                        window.location.href = 'blogs.php?success=' + (isEditMode ? 'updated' : 'created');
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

