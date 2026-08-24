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
    'category' => 'General',
    'tag' => '',
    'excerpt' => '',
    'full_text' => '',
    'date' => date('Y-m-d'),
    'attachments' => '[]'
];

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `notices` WHERE `id` = ?");
        $stmt->execute([$id]);
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetched) {
            $item = $fetched;
        }
    } catch (PDOException $e) {
        die("Error fetching notice: " . $e->getMessage());
    }
}

try {
    $categories = $pdo->query("SELECT * FROM `notice_categories` ORDER BY `name` ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

$page_title = $id > 0 ? 'Edit Notice' : 'Add Notice';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="form-card">
    <div class="form-header">
        <h1 class="form-title"><?php echo $id > 0 ? 'Edit' : 'New'; ?> Notice / Bulletin</h1>
        <div class="form-subtitle">
            <i class="fa-solid fa-bullhorn"></i>
            <span>Publish official notices, announcements, event alerts, or general news.</span>
        </div>
    </div>

    <div class="form-body">
        <form id="notice-form" novalidate>
            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">

            <div class="form-grid">
                <!-- Title -->
                <div class="form-group full-width">
                    <label class="form-label" for="title">Notice Title *</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Durga Puja Souvenir Magazine - Call for Submissions" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                    <span class="form-error" id="title-error" style="color: var(--vermilion); font-size: 0.8rem; display: none;">Notice title is required.</span>
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label class="form-label" for="category">Category *</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo strcasecmp($item['category'], $cat['name']) === 0 ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form-error" id="category-error" style="color: var(--vermilion); font-size: 0.8rem; display: none;">Please select a category.</span>
                </div>

                <!-- Tag -->
                <div class="form-group">
                    <label class="form-label" for="tag">Tag / Keyword (Optional)</label>
                    <input type="text" id="tag" name="tag" class="form-control" placeholder="e.g. Magazine, Rehearsals, AGM" value="<?php echo htmlspecialchars($item['tag']); ?>">
                </div>

                <!-- Date -->
                <div class="form-group">
                    <label class="form-label" for="date">Publish Date *</label>
                    <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($item['date']); ?>" required>
                    <span class="form-error" id="date-error" style="color: var(--vermilion); font-size: 0.8rem; display: none;">Please specify a publish date.</span>
                </div>

                <!-- Excerpt -->
                <div class="form-group full-width">
                    <label class="form-label" for="excerpt">Brief Excerpt / Summary (Optional)</label>
                    <textarea id="excerpt" name="excerpt" class="form-control" placeholder="Provide a short summary to show on the notice card listing..." style="min-height: 80px;"><?php echo htmlspecialchars($item['excerpt']); ?></textarea>
                </div>

                <!-- Full Text -->
                <div class="form-group full-width">
                    <label class="form-label" for="full_text">Full Notice / Bulletin Content *</label>
                    <textarea id="full_text" name="full_text" class="form-control" placeholder="Type the complete detailed description of the announcement here..." style="min-height: 180px;" required><?php echo htmlspecialchars($item['full_text']); ?></textarea>
                    <span class="form-error" id="full-text-error" style="color: var(--vermilion); font-size: 0.8rem; display: none;">Notice details are required.</span>
                </div>

                <!-- Attachments Section -->
                <div class="form-group full-width">
                    <label class="form-label">Attach Media & PDF Files</label>
                    
                    <!-- Pre-existing attachments (if edit mode) -->
                    <?php 
                    $existing_attachments = json_decode($item['attachments'], true);
                    if (!is_array($existing_attachments)) $existing_attachments = [];
                    ?>
                    <div id="existing-attachments-container" style="margin-bottom: 1.25rem; display: flex; flex-direction: column; gap: 0.6rem;">
                        <?php foreach ($existing_attachments as $idx => $att): ?>
                            <div class="existing-att-row" id="existing-att-<?php echo $idx; ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 6px; background-color: var(--secondary-bg);">
                                <div style="display: flex; align-items: center; gap: 0.8rem; font-size: 0.9rem; color: var(--dark); font-weight: 600;">
                                    <i class="fa-solid <?php echo $att['type'] === 'pdf' ? 'fa-file-pdf' : 'fa-file-image'; ?>" style="color: var(--red); font-size: 1.1rem;"></i>
                                    <span><?php echo htmlspecialchars($att['name']); ?></span>
                                </div>
                                <input type="hidden" name="kept_attachments[]" value='<?php echo json_encode($att); ?>'>
                                <button type="button" onclick="removeExistingAttachment(<?php echo $idx; ?>)" style="background: none; border: none; color: var(--vermilion); cursor: pointer; font-size: 0.95rem;"><i class="fa-solid fa-trash-can"></i> Remove</button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Drag & Drop / Selector zone -->
                    <div id="attachment-dropzone" style="border: 2px dashed var(--border-color); padding: 2rem 1.5rem; text-align: center; border-radius: var(--border-radius); background-color: var(--primary-bg); cursor: pointer; transition: var(--transition);">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2.2rem; color: var(--red); opacity: 0.7; margin-bottom: 0.75rem;"></i>
                        <h4 style="margin-bottom: 0.35rem; color: var(--dark);">Click or Drag files here to upload</h4>
                        <p style="font-size: 0.8rem; color: var(--gray); margin-bottom: 0;">Supports PDF documents and Image files (PNG, JPG, JPEG).</p>
                        <input type="file" id="attachments-input" name="attachments[]" multiple style="display: none;">
                    </div>

                    <!-- Selected Files Queue Previews -->
                    <div id="files-queue-container" style="margin-top: 1.25rem; display: flex; flex-direction: column; gap: 0.8rem;"></div>
                </div>

                <!-- Progress Bar Container -->
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
                    <a href="notices.php" class="btn btn-secondary" style="padding: 0.8rem 2rem; border-radius: 30px; font-weight: 700; text-align: center; text-decoration: none; border: 1px solid var(--border-color); color: var(--gray); flex: 1;">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem; border-radius: 30px; font-weight: 700; background-color: var(--red); color: var(--white); border: none; cursor: pointer; flex: 2; display: flex; align-items: center; justify-content: center; gap: 0.6rem;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Announcement Notice
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .existing-att-row:hover {
        border-color: rgba(139, 30, 30, 0.3) !important;
    }
    #attachment-dropzone:hover {
        background-color: var(--secondary-bg);
        border-color: var(--red);
    }
    .queued-file-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.8rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background-color: var(--white);
        box-shadow: var(--shadow-sm);
        position: relative;
    }
    .queued-thumbnail {
        width: 50px;
        height: 50px;
        border-radius: 4px;
        object-fit: cover;
        border: 1px solid var(--border-color);
        background-color: var(--primary-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: var(--gray);
        flex-shrink: 0;
    }
    .queued-info {
        flex-grow: 1;
        overflow: hidden;
    }
    .queued-name {
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--dark);
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        margin-bottom: 0.15rem;
    }
    .queued-size {
        font-size: 0.78rem;
        color: var(--gray);
    }
    .queued-remove-btn {
        background: none;
        border: none;
        color: var(--vermilion);
        cursor: pointer;
        font-size: 1.15rem;
        padding: 0.4rem;
        transition: var(--transition);
    }
    .queued-remove-btn:hover {
        transform: scale(1.1);
    }
</style>

<script>
    // Global selected files array to maintain queue
    let selectedFiles = [];

    const dropzone = document.getElementById('attachment-dropzone');
    const fileInput = document.getElementById('attachments-input');
    const queueContainer = document.getElementById('files-queue-container');

    // Trigger file dialog
    dropzone.addEventListener('click', () => fileInput.click());

    // Drag events
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = 'var(--red)';
        dropzone.style.backgroundColor = 'var(--secondary-bg)';
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.style.borderColor = '';
        dropzone.style.backgroundColor = '';
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '';
        dropzone.style.backgroundColor = '';
        if (e.dataTransfer.files.length > 0) {
            handleNewFiles(e.dataTransfer.files);
        }
    });

    // File input changes
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleNewFiles(fileInput.files);
        }
    });

    function handleNewFiles(files) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Check file type: only PDF and Images allowed
            const fileType = file.type;
            const isImage = fileType.startsWith('image/');
            const isPDF = fileType === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');

            if (!isImage && !isPDF) {
                alert('Only PDF and Image files are supported: ' + file.name);
                continue;
            }

            // Avoid adding duplicates
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
            card.className = 'queued-file-card';

            const thumbnail = document.createElement('div');
            thumbnail.className = 'queued-thumbnail';

            const isImage = file.type.startsWith('image/');
            if (isImage) {
                const img = document.createElement('img');
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '3px';
                
                const reader = new FileReader();
                reader.onload = (e) => { img.src = e.target.result; };
                reader.readAsDataURL(file);
                
                thumbnail.appendChild(img);
            } else {
                thumbnail.innerHTML = '<i class="fa-solid fa-file-pdf" style="color: var(--red);"></i>';
            }

            const info = document.createElement('div');
            info.className = 'queued-info';

            const name = document.createElement('div');
            name.className = 'queued-name';
            name.textContent = file.name;

            const size = document.createElement('div');
            size.className = 'queued-size';
            size.textContent = formatBytes(file.size);

            info.appendChild(name);
            info.appendChild(size);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'queued-remove-btn';
            removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            removeBtn.addEventListener('click', () => {
                selectedFiles.splice(index, 1);
                renderQueue();
            });

            card.appendChild(thumbnail);
            card.appendChild(info);
            card.appendChild(removeBtn);

            queueContainer.appendChild(card);
        });
    }

    function removeExistingAttachment(idx) {
        const row = document.getElementById('existing-att-' + idx);
        if (row) {
            row.remove();
        }
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = 2;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // Submit handler
    const form = document.getElementById('notice-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validations
        const title = document.getElementById('title');
        const category = document.getElementById('category');
        const date = document.getElementById('date');
        const fullText = document.getElementById('full_text');

        let isValid = true;

        if (title.value.trim() === '') {
            document.getElementById('title-error').style.display = 'block';
            title.style.borderColor = 'var(--vermilion)';
            isValid = false;
        } else {
            document.getElementById('title-error').style.display = 'none';
            title.style.borderColor = '';
        }

        if (category.value === '') {
            document.getElementById('category-error').style.display = 'block';
            category.style.borderColor = 'var(--vermilion)';
            isValid = false;
        } else {
            document.getElementById('category-error').style.display = 'none';
            category.style.borderColor = '';
        }

        if (date.value === '') {
            document.getElementById('date-error').style.display = 'block';
            date.style.borderColor = 'var(--vermilion)';
            isValid = false;
        } else {
            document.getElementById('date-error').style.display = 'none';
            date.style.borderColor = '';
        }

        if (fullText.value.trim() === '') {
            document.getElementById('full-text-error').style.display = 'block';
            fullText.style.borderColor = 'var(--vermilion)';
            isValid = false;
        } else {
            document.getElementById('full-text-error').style.display = 'none';
            fullText.style.borderColor = '';
        }

        if (!isValid) return;

        // Prep FormData
        const formData = new FormData(form);
        
        // Remove empty attachments file selector values first to avoid empty upload objects
        formData.delete('attachments[]');

        // Append files from selectedFiles array queue
        selectedFiles.forEach(file => {
            formData.append('attachments[]', file);
        });

        // Setup XHR request with progress tracking
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
                        progressBar.style.backgroundColor = '#25D366'; // Green success bar
                        setTimeout(() => {
                            window.location.href = 'notices.php?success=saved';
                        }, 600);
                    } else {
                        alert('Error: ' + res.message);
                        progressWrapper.style.display = 'none';
                    }
                } catch(e) {
                    alert('Invalid JSON response from server.');
                    progressWrapper.style.display = 'none';
                }
            } else {
                alert('An HTTP error occurred: ' + xhr.status);
                progressWrapper.style.display = 'none';
            }
        };

        xhr.onerror = function() {
            alert('A network error occurred during notice submission.');
            progressWrapper.style.display = 'none';
        };

        xhr.open('POST', 'action.php?act=save_notice', true);
        xhr.send(formData);
    });
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
