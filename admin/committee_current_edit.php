<?php
$page_title = 'Edit Committee Member';
require_once __DIR__ . '/includes/sidebar.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$member = [
    'id' => 0,
    'name' => '',
    'position' => '',
    'bio' => '',
    'email' => '',
    'phone' => '',
    'image' => '',
    'member_type' => 'board',
    'sort_order' => 0
];

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `current_committee` WHERE `id` = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            $member = $res;
            $page_title = 'Edit Committee Member: ' . htmlspecialchars($member['name']);
        }
    } catch (PDOException $e) {
        $error_msg = "Database query error: " . $e->getMessage();
    }
} else {
    $page_title = 'Add Committee Member';
}
?>

<div class="form-card">
    <div class="form-header">
        <h2 class="form-title"><?php echo $id > 0 ? 'Edit Member Details' : 'Create Committee Member'; ?></h2>
        <div class="form-subtitle">
            <i class="fa-solid fa-user-shield"></i>
            <span>Fill in details to display this member on the frontend Committee Page.</span>
        </div>
    </div>

    <div class="form-body">
        <form id="member-form" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
            
            <div class="form-grid">
                <!-- Member Name -->
                <div class="form-group">
                    <label class="form-label" for="member_name">Full Name <span style="color: var(--red);">*</span></label>
                    <input type="text" id="member_name" name="name" class="form-control" placeholder="e.g. Dr. Amitabha Ghosh" value="<?php echo htmlspecialchars($member['name']); ?>" required>
                </div>

                <!-- Position / Designation -->
                <div class="form-group">
                    <label class="form-label" for="member_position">Designation / Role <span style="color: var(--red);">*</span></label>
                    <input type="text" id="member_position" name="position" class="form-control" placeholder="e.g. President" value="<?php echo htmlspecialchars($member['position']); ?>" required>
                </div>

                <!-- Contact Email -->
                <div class="form-group">
                    <label class="form-label" for="member_email">Contact Email</label>
                    <input type="email" id="member_email" name="email" class="form-control" placeholder="e.g. president@bengalicultural.org" value="<?php echo htmlspecialchars($member['email']); ?>">
                </div>

                <!-- Contact Mobile No -->
                <div class="form-group">
                    <label class="form-label" for="member_phone">Mobile Number</label>
                    <input type="text" id="member_phone" name="phone" class="form-control" placeholder="e.g. +91 98765 43210" value="<?php echo htmlspecialchars($member['phone']); ?>">
                </div>

                <!-- Group / Type -->
                <div class="form-group">
                    <label class="form-label" for="member_type">Committee Group <span style="color: var(--red);">*</span></label>
                    <select id="member_type" name="member_type" class="form-control" required>
                        <option value="board" <?php echo $member['member_type'] === 'board' ? 'selected' : ''; ?>>Board Member (Top Grid)</option>
                        <!-- <option value="executive" <?php echo $member['member_type'] === 'executive' ? 'selected' : ''; ?>>Executive Board Member (Second Grid)</option> -->
                    </select>
                </div>

                <!-- Sort Order -->
                <div class="form-group">
                    <label class="form-label" for="sort_order">Sort Order (Weight)</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" placeholder="e.g. 1" value="<?php echo htmlspecialchars($member['sort_order']); ?>">
                    <small style="color: var(--gray); font-size: 0.75rem; display: block; margin-top: 0.2rem;">Lower numbers are listed first.</small>
                </div>

                <!-- Member Bio -->
                <div class="form-group full-width">
                    <label class="form-label" for="member_bio">Brief Biography / Info</label>
                    <textarea id="member_bio" name="bio" class="form-control" placeholder="Describe the member's professional background or community work..." rows="4"><?php echo htmlspecialchars($member['bio']); ?></textarea>
                </div>

                <!-- Photo Upload Dropzone -->
                <div class="form-group full-width">
                    <label class="form-label">Profile Photo</label>
                    
                    <?php if ($id > 0 && !empty($member['image'])): ?>
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;" id="current-image-container">
                            <img src="../<?php echo htmlspecialchars($member['image']); ?>" alt="Current Photo" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border);">
                            <div>
                                <span style="font-size: 0.85rem; font-weight: 700; display: block; color: var(--gray);">Current Profile Image</span>
                                <span style="font-size: 0.78rem; color: var(--gray); font-family: monospace;"><?php echo htmlspecialchars(basename($member['image'])); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="upload-dropzone" id="dropzone">
                        <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                        <span class="upload-title">Drag & drop member profile image here or click to browse</span>
                        <span class="upload-hint">Supported formats: JPG, JPEG, PNG, WEBP. Single file.</span>
                        <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/jpg,image/png,image/webp" style="display: none;">
                    </div>
                    
                    <!-- File upload status/preview box -->
                    <div id="file-preview-box" class="file-preview-wrapper" style="display: none; align-items: center; justify-content: space-between; margin-top: 1rem; padding: 0.8rem 1.2rem; background-color: var(--sand); border: 1px solid var(--border); border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <img id="image-preview" src="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border); display: none;">
                            <div>
                                <span id="file-name" style="font-size: 0.9rem; font-weight: 700; color: var(--dark); display: block;"></span>
                                <span id="file-size" style="font-size: 0.78rem; color: var(--gray);"></span>
                            </div>
                        </div>
                        <button type="button" id="remove-file-btn" style="background: none; border: none; color: var(--red); cursor: pointer; font-size: 1.15rem; transition: var(--transition);"><i class="fa-solid fa-circle-xmark"></i></button>
                    </div>
                </div>
            </div>

            <!-- Upload progress bar -->
            <div id="progress-container" style="display: none; margin-top: 2rem;">
                <div class="progress-bar-bg" style="width: 100%; height: 8px; background-color: rgba(33, 26, 23, 0.08); border-radius: 4px; overflow: hidden; position: relative;">
                    <div id="progress-bar-fill" style="width: 0%; height: 100%; background-color: var(--red); border-radius: 4px; transition: width 0.1s ease;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.82rem; font-weight: 700; color: var(--gray); margin-top: 0.4rem;">
                    <span id="progress-text">Uploading: 0%</span>
                </div>
            </div>

            <!-- Buttons -->
            <div class="btn-row">
                <a href="committee_current.php" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-submit" id="submit-btn">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span><?php echo $id > 0 ? 'Save Updates' : 'Add Member'; ?></span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .upload-dropzone {
        border: 2px dashed var(--border);
        background-color: var(--cream);
        border-radius: 10px;
        padding: 2.2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
    }

    .upload-dropzone:hover,
    .upload-dropzone.dragover {
        border-color: var(--red);
        background-color: var(--sand);
    }

    .upload-icon {
        font-size: 2.8rem;
        color: var(--red);
        margin-bottom: 0.8rem;
        transition: var(--transition);
    }

    .upload-dropzone:hover .upload-icon {
        transform: translateY(-3px);
    }

    .upload-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.3rem;
    }

    .upload-hint {
        font-size: 0.78rem;
        color: var(--gray);
    }

    .file-preview-wrapper button:hover {
        transform: scale(1.15);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('image_file');
    const previewBox = document.getElementById('file-preview-box');
    const imgPreview = document.getElementById('image-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const removeBtn = document.getElementById('remove-file-btn');
    const form = document.getElementById('member-form');
    
    const isEditMode = <?php echo $id > 0 ? 'true' : 'false'; ?>;

    // Trigger file selection click
    dropzone.addEventListener('click', () => fileInput.click());

    // Drag-over styling
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('dragover');
    });

    // Drop files
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            handleFileSelect(e.dataTransfer.files[0]);
        }
    });

    // Native file picker change
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFileSelect(fileInput.files[0]);
        }
    });

    function handleFileSelect(file) {
        if (!file.type.match('image.*')) {
            alert('Please select a valid image file (JPG, PNG, WEBP).');
            return;
        }

        fileName.innerText = file.name;
        fileSize.innerText = Math.round(file.size / 1024) + ' KB';

        // Render preview image
        const reader = new FileReader();
        reader.onload = (e) => {
            imgPreview.src = e.target.result;
            imgPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);

        previewBox.style.display = 'flex';
        dropzone.style.display = 'none';

        // Set the files property of fileInput
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;
    }

    // Remove selected file
    removeBtn.addEventListener('click', () => {
        fileInput.value = '';
        previewBox.style.display = 'none';
        dropzone.style.display = 'flex';
    });

    // Form submit with progressive progress bar AJAX uploader
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const nameVal = document.getElementById('member_name').value.trim();
        const posVal = document.getElementById('member_position').value.trim();

        if (!nameVal || !posVal) {
            alert('Please fill out all required fields.');
            return;
        }

        // Prepare FormData
        const formData = new FormData(form);

        // Upload AJAX
        const progressContainer = document.getElementById('progress-container');
        const progressBarFill = document.getElementById('progress-bar-fill');
        const progressText = document.getElementById('progress-text');
        const submitBtn = document.getElementById('submit-btn');

        // Disable elements during submission
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
        xhr.open('POST', 'action.php?act=save_committee_member&ajax=1', true);

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
                            window.location.href = 'committee_current.php?success=' + (isEditMode ? 'updated' : 'created');
                        }, 800);
                    } else {
                        alert('Upload failed: ' + (res.message || 'Unknown error'));
                        resetFormState();
                    }
                } catch(err) {
                    window.location.href = 'committee_current.php?success=' + (isEditMode ? 'updated' : 'created');
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
            progressContainer.style.display = 'none';
            inputs.forEach(el => {
                if (el.tagName === 'A') {
                    el.style.pointerEvents = '';
                    el.style.opacity = '';
                } else {
                    el.disabled = false;
                }
            });
        }
    });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
