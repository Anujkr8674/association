<?php
$page_title = 'Member Profile Documents';
require_once __DIR__ . '/includes/sidebar.php';

$doc_type = 'member_profile';

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$total_rows = 0;
$total_pages = 1;

try {
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM `member_documents` WHERE `doc_type` = ?");
    $stmt_count->execute([$doc_type]);
    $total_rows = $stmt_count->fetchColumn();
    $total_pages = max(1, ceil($total_rows / $limit));
    
    $stmt = $pdo->prepare("SELECT * FROM `member_documents` WHERE `doc_type` = :doc_type ORDER BY `year` DESC, `created_at` DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':doc_type', $doc_type, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database query failed: " . $e->getMessage();
    $documents = [];
}

// Edit Mode Check
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$edit_title = '';
$edit_year = '';
if ($edit_id > 0) {
    try {
        $stmt_edit = $pdo->prepare("SELECT * FROM `member_documents` WHERE `id` = ? AND `doc_type` = ?");
        $stmt_edit->execute([$edit_id, $doc_type]);
        $edit_doc = $stmt_edit->fetch(PDO::FETCH_ASSOC);
        if ($edit_doc) {
            $edit_title = $edit_doc['title'];
            $edit_year = $edit_doc['year'];
        } else {
            $edit_id = 0;
        }
    } catch (PDOException $e) {
        $edit_id = 0;
    }
}

$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<!-- Success notices -->
<?php if (!empty($success)): ?>
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span><?php 
            if ($success === 'created') echo 'PDF Document uploaded successfully!';
            elseif ($success === 'updated') echo 'PDF Document updated successfully!';
            elseif ($success === 'deleted') echo 'PDF Document deleted successfully!';
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

<div class="overview-grid">
    <!-- Left Column: Add/Edit Document Form -->
    <div class="recent-card">
        <div class="recent-header">
            <h3 class="recent-title">
                <i class="fa-solid <?php echo $edit_id > 0 ? 'fa-pen-to-square' : 'fa-file-arrow-up'; ?>"></i> 
                <?php echo $edit_id > 0 ? 'Edit Document details' : 'Upload Member Profile PDF'; ?>
            </h3>
        </div>
        <div style="padding: 2rem;">
            <form id="document-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="doc_type" value="<?php echo $doc_type; ?>">
                <?php if ($edit_id > 0): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                <?php endif; ?>
                
                <!-- Document Title -->
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="doc_title">Document Title <span style="color: var(--red);">*</span></label>
                    <input type="text" id="doc_title" name="title" class="form-control" placeholder="e.g. Members Profile Sheet" value="<?php echo htmlspecialchars($edit_title); ?>" required>
                </div>
                
                <!-- Target Year -->
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="doc_year">Year <span style="color: var(--red);">*</span></label>
                    <input type="text" id="doc_year" name="year" class="form-control" placeholder="e.g. 2026" value="<?php echo htmlspecialchars($edit_year); ?>" required>
                </div>

                <!-- PDF File Upload Dropzone -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Upload PDF Document <?php echo $edit_id > 0 ? '<small style="color: var(--gray); font-weight: normal;">(Leave empty to keep current PDF)</small>' : '<span style="color: var(--red);">*</span>'; ?></label>
                    <div class="upload-dropzone" id="dropzone">
                        <i class="fa-solid fa-file-pdf upload-icon"></i>
                        <span class="upload-title">Drag & drop PDF here or click to browse</span>
                        <span class="upload-hint">Supported format: PDF only. Max size: 20MB.</span>
                        <input type="file" id="pdf_file" name="pdf_file" accept="application/pdf" style="display: none;" <?php echo $edit_id > 0 ? '' : 'required'; ?>>
                    </div>
                    
                    <!-- File upload status/preview box -->
                    <div id="file-preview-box" class="file-preview-wrapper" style="display: none; align-items: center; justify-content: space-between; margin-top: 1rem; padding: 0.8rem 1.2rem; background-color: var(--sand); border: 1px solid var(--border); border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <i class="fa-solid fa-file-pdf" style="color: var(--red); font-size: 2.2rem;"></i>
                            <div>
                                <span id="file-name" style="font-size: 0.9rem; font-weight: 700; color: var(--dark); display: block; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></span>
                                <span id="file-size" style="font-size: 0.78rem; color: var(--gray);"></span>
                            </div>
                        </div>
                        <button type="button" id="remove-file-btn" style="background: none; border: none; color: var(--red); cursor: pointer; font-size: 1.15rem; transition: var(--transition);"><i class="fa-solid fa-circle-xmark"></i></button>
                    </div>
                </div>

                <!-- Progress container -->
                <div id="progress-container" style="display: none; margin-bottom: 1.5rem;">
                    <div class="progress-bar-bg" style="width: 100%; height: 8px; background-color: rgba(33, 26, 23, 0.08); border-radius: 4px; overflow: hidden; position: relative;">
                        <div id="progress-bar-fill" style="width: 0%; height: 100%; background-color: var(--red); border-radius: 4px; transition: width 0.1s ease;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.82rem; font-weight: 700; color: var(--gray); margin-top: 0.4rem;">
                        <span id="progress-text">Uploading: 0%</span>
                    </div>
                </div>

                <!-- Action Button -->
                <div style="display: flex; gap: 0.6rem;">
                    <?php if ($edit_id > 0): ?>
                        <a href="members_profile.php" class="btn btn-cancel" style="flex: 1; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; height: 42px; border-radius: 6px;">Cancel</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-submit" id="submit-btn" style="flex: 1;">
                        <i class="fa-solid <?php echo $edit_id > 0 ? 'fa-floppy-disk' : 'fa-plus'; ?>"></i> 
                        <?php echo $edit_id > 0 ? 'Save Updates' : 'Upload PDF Document'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Documents List -->
    <div class="recent-card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="recent-header">
                <h3 class="recent-title"><i class="fa-solid fa-file-pdf"></i> Uploaded Documents</h3>
            </div>
            <div class="table-responsive">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th style="padding: 1rem 1.5rem;">Title / Year</th>
                            <th style="width: 120px; text-align: center; padding: 1rem 1.5rem;">Download</th>
                            <th style="width: 120px; text-align: center; padding: 1rem 1.5rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="3" class="no-data-row" style="padding: 2.5rem 1.5rem !important;">No documents uploaded. Add one on the left.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td style="padding: 1rem 1.5rem;">
                                        <div style="font-weight: 700; color: var(--dark); font-size: 0.95rem; margin-bottom: 0.2rem;"><?php echo htmlspecialchars($doc['title']); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--gray); font-weight: 600;">Year / Tenure: <span class="category-badge" style="font-size: 0.72rem; padding: 0.15rem 0.4rem;"><?php echo htmlspecialchars($doc['year']); ?></span></div>
                                    </td>
                                    <td style="text-align: center; padding: 1rem 1.5rem;">
                                        <a href="../<?php echo htmlspecialchars($doc['pdf_path']); ?>" target="_blank" class="btn-action btn-edit" title="View / Open PDF" style="width: auto; height: auto; padding: 0.4rem 0.8rem; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; border-radius: 20px; border-color: var(--red); color: var(--red); background-color: rgba(212, 63, 58, 0.03);">
                                            <i class="fa-solid fa-file-pdf" style="font-size: 0.9rem;"></i> View PDF
                                        </a>
                                    </td>
                                    <td style="text-align: center; padding: 1rem 1.5rem;">
                                        <div style="display: inline-flex; gap: 0.4rem;">
                                            <a href="members_profile.php?edit=<?php echo $doc['id']; ?>&page=<?php echo $page; ?>" class="btn-action btn-edit" title="Edit Document"><i class="fa-solid fa-pencil"></i></a>
                                            <a href="action.php?act=delete_member_document&id=<?php echo $doc['id']; ?>" class="btn-action btn-delete" title="Delete Document" onclick="return confirm('Are you sure you want to delete this document? The file will be permanently deleted from the server.');" style="display: inline-flex;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Controls -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-container" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; padding: 1.5rem 2rem; border-top: 1px solid var(--border); background-color: var(--white); flex-wrap: wrap;">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $edit_id > 0 ? '&edit=' . $edit_id : ''; ?>" class="btn-pagination" style="text-decoration: none; padding: 0.5rem 0.8rem; border: 1px solid var(--border); border-radius: 6px; color: var(--gray); font-size: 0.85rem; font-weight: 700; transition: var(--transition);"><i class="fa-solid fa-angle-left"></i> Previous</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <?php if ($p == $page): ?>
                        <span class="btn-pagination active" style="padding: 0.5rem 0.85rem; background-color: var(--red); color: var(--white); border-radius: 6px; font-size: 0.85rem; font-weight: 700; border: 1px solid var(--red);"><?php echo $p; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $p; ?><?php echo $edit_id > 0 ? '&edit=' . $edit_id : ''; ?>" class="btn-pagination" style="text-decoration: none; padding: 0.5rem 0.85rem; border: 1px solid var(--border); border-radius: 6px; color: var(--gray); font-size: 0.85rem; font-weight: 700; transition: var(--transition);"><?php echo $p; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $edit_id > 0 ? '&edit=' . $edit_id : ''; ?>" class="btn-pagination" style="text-decoration: none; padding: 0.5rem 0.8rem; border: 1px solid var(--border); border-radius: 6px; color: var(--gray); font-size: 0.85rem; font-weight: 700; transition: var(--transition);">Next <i class="fa-solid fa-angle-right"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .upload-dropzone {
        border: 2px dashed var(--border);
        background-color: var(--cream);
        border-radius: 8px;
        padding: 1.8rem 1rem;
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
        font-size: 2.2rem;
        color: var(--red);
        margin-bottom: 0.5rem;
        transition: var(--transition);
    }

    .upload-dropzone:hover .upload-icon {
        transform: translateY(-2px);
    }

    .upload-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.2rem;
    }

    .upload-hint {
        font-size: 0.72rem;
        color: var(--gray);
    }

    .file-preview-wrapper button:hover {
        transform: scale(1.15);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('pdf_file');
    const previewBox = document.getElementById('file-preview-box');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const removeBtn = document.getElementById('remove-file-btn');
    const form = document.getElementById('document-form');

    const isEditMode = <?php echo $edit_id > 0 ? 'true' : 'false'; ?>;

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
        if (file.type !== 'application/pdf') {
            alert('Please select a valid PDF document.');
            return;
        }

        fileName.innerText = file.name;
        fileSize.innerText = Math.round(file.size / 1024) + ' KB';

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

        const titleVal = document.getElementById('doc_title').value.trim();
        const yearVal = document.getElementById('doc_year').value.trim();

        if (!titleVal || !yearVal) {
            alert('Please fill out all required fields.');
            return;
        }

        if (!isEditMode && fileInput.files.length === 0) {
            alert('Please upload a PDF document.');
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
        xhr.open('POST', 'action.php?act=save_member_document&ajax=1', true);

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
                            window.location.href = res.redirect + '?success=' + (isEditMode ? 'updated' : 'created');
                        }, 800);
                    } else {
                        alert('Upload failed: ' + (res.message || 'Unknown error'));
                        resetFormState();
                    }
                } catch(err) {
                    window.location.href = 'members_profile.php?success=' + (isEditMode ? 'updated' : 'created');
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
