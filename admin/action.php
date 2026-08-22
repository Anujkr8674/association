<?php
session_start();
require_once __DIR__ . '/../config.php';

// Check if logged in (except logout act which is fine)
$act = isset($_GET['act']) ? $_GET['act'] : '';

if ($act !== 'logout') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

switch ($act) {
    case 'logout':
        // Log out admin
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: login.php');
        exit;

    case 'delete_event':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM `events` WHERE `id` = ?");
                $stmt->execute([$id]);
                header('Location: events.php?success=deleted');
                exit;
            } catch (PDOException $e) {
                die("Error deleting event: " . $e->getMessage());
            }
        }
        break;

    case 'delete_blog':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                // Fetch image paths to delete physical files
                $stmt = $pdo->prepare("SELECT `image`, `additional_images` FROM `blogs` WHERE `id` = ?");
                $stmt->execute([$id]);
                $blog_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($blog_data) {
                    // Delete cover image
                    $old_img = $blog_data['image'];
                    if ($old_img && strpos($old_img, 'uploads/blogs/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_img;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }
                    
                    // Delete additional images
                    if (!empty($blog_data['additional_images'])) {
                        $add_imgs = explode(',', $blog_data['additional_images']);
                        foreach ($add_imgs as $img_path) {
                            $img_path = trim($img_path);
                            if ($img_path && strpos($img_path, 'uploads/blogs/') === 0) {
                                $add_file_path = __DIR__ . '/../' . $img_path;
                                if (file_exists($add_file_path)) {
                                    @unlink($add_file_path);
                                }
                            }
                        }
                    }
                }

                $stmt = $pdo->prepare("DELETE FROM `blogs` WHERE `id` = ?");
                $stmt->execute([$id]);
                header('Location: blogs.php?success=deleted');
                exit;
            } catch (PDOException $e) {
                die("Error deleting blog: " . $e->getMessage());
            }
        }
        break;

    case 'delete_gallery':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                // Fetch image path to delete physical file if local
                $stmt = $pdo->prepare("SELECT `image` FROM `gallery` WHERE `id` = ?");
                $stmt->execute([$id]);
                $old_img = $stmt->fetchColumn();
                if ($old_img && strpos($old_img, 'uploads/gallery/') === 0) {
                    $old_file_path = __DIR__ . '/../' . $old_img;
                    if (file_exists($old_file_path)) {
                        @unlink($old_file_path);
                    }
                }

                $stmt = $pdo->prepare("DELETE FROM `gallery` WHERE `id` = ?");
                $stmt->execute([$id]);
                header('Location: gallery.php?success=deleted');
                exit;
            } catch (PDOException $e) {
                die("Error deleting gallery item: " . $e->getMessage());
            }
        }
        break;

    case 'save_event':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $short_description = isset($_POST['short_description']) ? trim($_POST['short_description']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            
            $date = isset($_POST['date']) ? trim($_POST['date']) : '';
            $end_date = !empty($_POST['end_date']) ? trim($_POST['end_date']) : null;
            
            $time = isset($_POST['time']) ? trim($_POST['time']) : '';
            $start_time = !empty($_POST['start_time']) ? trim($_POST['start_time']) : null;
            $end_time = !empty($_POST['end_time']) ? trim($_POST['end_time']) : null;
            $all_day = isset($_POST['all_day']) ? intval($_POST['all_day']) : 1;
            
            $location = isset($_POST['location']) ? trim($_POST['location']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $city = isset($_POST['city']) ? trim($_POST['city']) : '';
            $state = isset($_POST['state']) ? trim($_POST['state']) : '';
            $country = isset($_POST['country']) ? trim($_POST['country']) : '';
            $latitude = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
            $longitude = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;
            $map_url = isset($_POST['map_url']) ? trim($_POST['map_url']) : '';
            
            $category = isset($_POST['category']) ? trim($_POST['category']) : '';
            $image = isset($_POST['image']) ? trim($_POST['image']) : '';
            $motif = isset($_POST['motif']) ? trim($_POST['motif']) : 'lotus';
            
            // Check if is custom or external
            $is_custom = 1;
            if ($id > 0) {
                try {
                    $stmt = $pdo->prepare("SELECT `is_custom` FROM `events` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    $is_custom = $stmt->fetchColumn();
                } catch (PDOException $e) {
                    $is_custom = 1;
                }
            }

            // Sub-schedules saving helper
            function save_schedules_helper($pdo, $event_id, $post_data) {
                try {
                    $stmtDel = $pdo->prepare("DELETE FROM `event_schedules` WHERE `event_id` = ?");
                    $stmtDel->execute([$event_id]);
                    
                    if (isset($post_data['sch_date']) && is_array($post_data['sch_date'])) {
                        $stmtIns = $pdo->prepare("INSERT INTO `event_schedules` (`event_id`, `date`, `time`, `title`, `description`) VALUES (?, ?, ?, ?, ?)");
                        for ($i = 0; $i < count($post_data['sch_date']); $i++) {
                            $s_date = trim($post_data['sch_date'][$i]);
                            $s_time = trim($post_data['sch_time'][$i]);
                            $s_title = trim($post_data['sch_title'][$i]);
                            $s_desc = isset($post_data['sch_desc'][$i]) ? trim($post_data['sch_desc'][$i]) : '';
                            
                            if (!empty($s_date) && !empty($s_title)) {
                                $stmtIns->execute([$event_id, $s_date, $s_time, $s_title, $s_desc]);
                            }
                        }
                    }
                } catch (PDOException $e) {
                    error_log("Failed saving schedules: " . $e->getMessage());
                }
            }

            if ($is_custom == 0 && $id > 0) {
                // Synced external event override
                try {
                    $stmtCheck = $pdo->prepare("SELECT `id` FROM `event_overrides` WHERE `event_id` = ?");
                    $stmtCheck->execute([$id]);
                    $override_id = $stmtCheck->fetchColumn();

                    if ($override_id) {
                        $stmtUp = $pdo->prepare("UPDATE `event_overrides` SET 
                            `title` = ?, `short_description` = ?, `description` = ?, `location` = ?, 
                            `category` = ?, `start_date` = ?, `end_date` = ?, `start_time` = ?, 
                            `end_time` = ?, `all_day` = ?, `image` = ? 
                            WHERE `id` = ?");
                        $stmtUp->execute([
                            $title, $short_description, $description, $location, 
                            $category, $date, $end_date, $start_time, 
                            $end_time, $all_day, $image, $override_id
                        ]);
                    } else {
                        $stmtIns = $pdo->prepare("INSERT INTO `event_overrides` 
                            (`event_id`, `title`, `short_description`, `description`, `location`, 
                             `category`, `start_date`, `end_date`, `start_time`, `end_time`, `all_day`, `image`) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmtIns->execute([
                            $id, $title, $short_description, $description, $location, 
                            $category, $date, $end_date, $start_time, $end_time, $all_day, $image
                        ]);
                    }

                    // Save schedules
                    save_schedules_helper($pdo, $id, $_POST);

                    header('Location: events.php?success=overridden');
                    exit;
                } catch (PDOException $e) {
                    die("Error saving external event override: " . $e->getMessage());
                }
            } else {
                // Custom association event
                $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $title));
                
                if ($id > 0) {
                    // Update events table directly
                    try {
                        $stmt = $pdo->prepare("UPDATE `events` SET 
                            `title` = ?, `slug` = ?, `short_description` = ?, `date` = ?, `end_date` = ?, 
                            `time` = ?, `start_time` = ?, `end_time` = ?, `all_day` = ?, 
                            `location` = ?, `address` = ?, `city` = ?, `state` = ?, `country` = ?, 
                            `latitude` = ?, `longitude` = ?, `map_url` = ?, `category` = ?, 
                            `image` = ?, `motif` = ? 
                            WHERE `id` = ?");
                        $stmt->execute([
                            $title, $slug, $short_description, $date, $end_date,
                            $time, $start_time, $end_time, $all_day,
                            $location, $address, $city, $state, $country,
                            $latitude, $longitude, $map_url, $category,
                            $image, $motif, $id
                        ]);

                        // Save schedules
                        save_schedules_helper($pdo, $id, $_POST);

                        header('Location: events.php?success=updated');
                        exit;
                    } catch (PDOException $e) {
                        die("Error updating event: " . $e->getMessage());
                    }
                } else {
                    // Insert new event
                    try {
                        $stmt = $pdo->prepare("INSERT INTO `events` 
                            (`title`, `slug`, `short_description`, `date`, `end_date`, 
                             `time`, `start_time`, `end_time`, `all_day`, 
                             `location`, `address`, `city`, `state`, `country`, 
                             `latitude`, `longitude`, `map_url`, `category`, 
                             `image`, `motif`, `source`, `is_custom`, `is_external`, `is_active`) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'association', 1, 0, 1)");
                        $stmt->execute([
                            $title, $slug, $short_description, $date, $end_date,
                            $time, $start_time, $end_time, $all_day,
                            $location, $address, $city, $state, $country,
                            $latitude, $longitude, $map_url, $category,
                            $image, $motif
                        ]);
                        $new_id = $pdo->lastInsertId();

                        // Save schedules
                        save_schedules_helper($pdo, $new_id, $_POST);

                        header('Location: events.php?success=created');
                        exit;
                    } catch (PDOException $e) {
                        die("Error inserting event: " . $e->getMessage());
                    }
                }
            }
        }
        break;

    case 'save_blog':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $excerpt = isset($_POST['excerpt']) ? trim($_POST['excerpt']) : '';
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';
            $category = isset($_POST['category']) ? trim($_POST['category']) : '';
            $date = isset($_POST['date']) ? trim($_POST['date']) : '';
            $author = isset($_POST['author']) ? trim($_POST['author']) : '';
            
            // Set up local upload directories
            $upload_dir = __DIR__ . '/../uploads/blogs/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $db_path = '';

            // Handle single cover file upload:
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['image_file']['tmp_name'];
                $orig_name = basename($_FILES['image_file']['name']);
                $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                $new_filename = uniqid('blog_cover_', true) . '.' . $ext;
                $dest = $upload_dir . $new_filename;
                
                if (move_uploaded_file($tmp_name, $dest)) {
                    $db_path = 'uploads/blogs/' . $new_filename;
                }
            } elseif (isset($_FILES['images']) && is_array($_FILES['images']['name']) && isset($_FILES['images']['name'][0])) {
                // If they upload via Add Mode dropzone
                if ($_FILES['images']['error'][0] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['images']['tmp_name'][0];
                    $orig_name = basename($_FILES['images']['name'][0]);
                    $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                    $new_filename = uniqid('blog_cover_', true) . '.' . $ext;
                    $dest = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($tmp_name, $dest)) {
                        $db_path = 'uploads/blogs/' . $new_filename;
                    }
                }
            }

            // Handle additional gallery images:
            $existing_add_imgs = isset($_POST['existing_additional_images']) ? trim($_POST['existing_additional_images']) : '';
            $add_imgs_array = [];
            if (!empty($existing_add_imgs)) {
                $add_imgs_array = array_filter(explode(',', $existing_add_imgs));
            }

            $new_additional_files = [];
            if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['name'])) {
                $total_add_files = count($_FILES['additional_images']['name']);
                for ($i = 0; $i < $total_add_files; $i++) {
                    if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['additional_images']['tmp_name'][$i];
                        $orig_name = basename($_FILES['additional_images']['name'][$i]);
                        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                        $new_filename = uniqid('blog_gal_', true) . '.' . $ext;
                        $dest = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($tmp_name, $dest)) {
                            $new_additional_files[] = 'uploads/blogs/' . $new_filename;
                        }
                    }
                }
            }

            $consolidated_additional_images = implode(',', array_merge($add_imgs_array, $new_additional_files));

            if ($id > 0) {
                // Update
                try {
                    if (!empty($db_path)) {
                        // Delete old cover image if it exists
                        $stmt = $pdo->prepare("SELECT `image` FROM `blogs` WHERE `id` = ?");
                        $stmt->execute([$id]);
                        $old_img = $stmt->fetchColumn();
                        if ($old_img && strpos($old_img, 'uploads/blogs/') === 0) {
                            $old_file_path = __DIR__ . '/../' . $old_img;
                            if (file_exists($old_file_path)) {
                                @unlink($old_file_path);
                            }
                        }

                        $stmt = $pdo->prepare("UPDATE `blogs` SET `title` = ?, `excerpt` = ?, `content` = ?, `image` = ?, `additional_images` = ?, `category` = ?, `date` = ?, `author` = ? WHERE `id` = ?");
                        $stmt->execute([$title, $excerpt, $content, $db_path, $consolidated_additional_images, $category, $date, $author, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE `blogs` SET `title` = ?, `excerpt` = ?, `content` = ?, `additional_images` = ?, `category` = ?, `date` = ?, `author` = ? WHERE `id` = ?");
                        $stmt->execute([$title, $excerpt, $content, $consolidated_additional_images, $category, $date, $author, $id]);
                    }

                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'success', 'message' => 'Blog post updated successfully.']);
                        exit;
                    } else {
                        header('Location: blogs.php?success=updated');
                        exit;
                    }
                } catch (PDOException $e) {
                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                        exit;
                    } else {
                        die("Error updating blog: " . $e->getMessage());
                    }
                }
            } else {
                // Insert
                try {
                    $stmt = $pdo->prepare("INSERT INTO `blogs` (`title`, `excerpt`, `content`, `image`, `additional_images`, `category`, `date`, `author`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $excerpt, $content, $db_path, $consolidated_additional_images, $category, $date, $author]);

                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'success', 'message' => 'Blog post created successfully.']);
                        exit;
                    } else {
                        header('Location: blogs.php?success=created');
                        exit;
                    }
                } catch (PDOException $e) {
                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                        exit;
                    } else {
                        die("Error inserting blog: " . $e->getMessage());
                    }
                }
            }
        }
        break;

    case 'save_gallery':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $category = isset($_POST['category']) ? trim($_POST['category']) : '';
            
            // Set up local upload directories
            $upload_dir = __DIR__ . '/../uploads/gallery/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if ($id > 0) {
                // Edit Mode: Replace single image or just update text fields
                try {
                    $db_path = '';
                    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['image_file']['tmp_name'];
                        $orig_name = basename($_FILES['image_file']['name']);
                        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                        
                        // Sanitize filename
                        $new_filename = uniqid('gallery_', true) . '.' . $ext;
                        $dest = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($tmp_name, $dest)) {
                            $db_path = 'uploads/gallery/' . $new_filename;
                            
                            // Delete old local file if it exists
                            $stmt = $pdo->prepare("SELECT `image` FROM `gallery` WHERE `id` = ?");
                            $stmt->execute([$id]);
                            $old_img = $stmt->fetchColumn();
                            if ($old_img && strpos($old_img, 'uploads/gallery/') === 0) {
                                $old_file_path = __DIR__ . '/../' . $old_img;
                                if (file_exists($old_file_path)) {
                                    @unlink($old_file_path);
                                }
                            }
                        }
                    }

                    if (!empty($db_path)) {
                        $stmt = $pdo->prepare("UPDATE `gallery` SET `title` = ?, `image` = ?, `category` = ? WHERE `id` = ?");
                        $stmt->execute([$title, $db_path, $category, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE `gallery` SET `title` = ?, `category` = ? WHERE `id` = ?");
                        $stmt->execute([$title, $category, $id]);
                    }

                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'success', 'message' => 'Gallery item updated successfully.']);
                        exit;
                    } else {
                        header('Location: gallery.php?success=updated');
                        exit;
                    }
                } catch (PDOException $e) {
                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                        exit;
                    } else {
                        die("Error updating gallery item: " . $e->getMessage());
                    }
                }
            } else {
                // Add Mode: Handle multiple uploads
                try {
                    $uploaded_count = 0;
                    if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                        $total_files = count($_FILES['images']['name']);
                        for ($i = 0; $i < $total_files; $i++) {
                            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                                $tmp_name = $_FILES['images']['tmp_name'][$i];
                                $orig_name = basename($_FILES['images']['name'][$i]);
                                $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                                
                                // Unique filename
                                $new_filename = uniqid('gallery_', true) . '.' . $ext;
                                $dest = $upload_dir . $new_filename;
                                
                                if (move_uploaded_file($tmp_name, $dest)) {
                                    $db_path = 'uploads/gallery/' . $new_filename;
                                    
                                    // Title suffixing for multiple files
                                    $img_title = $title;
                                    if ($total_files > 1) {
                                        $img_title .= ' - ' . ($i + 1);
                                    }
                                    
                                    $stmt = $pdo->prepare("INSERT INTO `gallery` (`title`, `image`, `category`) VALUES (?, ?, ?)");
                                    $stmt->execute([$img_title, $db_path, $category]);
                                    $uploaded_count++;
                                }
                            }
                        }
                    }

                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'success', 'message' => "$uploaded_count images uploaded successfully."]);
                        exit;
                    } else {
                        header('Location: gallery.php?success=created');
                        exit;
                    }
                } catch (PDOException $e) {
                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                        exit;
                    } else {
                        die("Error inserting gallery item: " . $e->getMessage());
                    }
                }
            }
        }
        break;

    case 'save_blog_category':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = isset($_POST['name']) ? strtoupper(trim($_POST['name'])) : '';
            if (!empty($name)) {
                try {
                    $stmt = $pdo->prepare("INSERT IGNORE INTO `blog_categories` (`name`) VALUES (?)");
                    $stmt->execute([$name]);
                    header('Location: blog_categories.php?success=category_added');
                    exit;
                } catch (PDOException $e) {
                    die("Error saving blog category: " . $e->getMessage());
                }
            }
        }
        header('Location: blog_categories.php');
        exit;

    case 'delete_blog_category':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM `blog_categories` WHERE `id` = ?");
                $stmt->execute([$id]);
                header('Location: blog_categories.php?success=category_deleted');
                exit;
            } catch (PDOException $e) {
                die("Error deleting blog category: " . $e->getMessage());
            }
        }
        header('Location: blog_categories.php');
        exit;

    case 'save_gallery_category':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = isset($_POST['name']) ? strtoupper(trim($_POST['name'])) : '';
            if (!empty($name)) {
                try {
                    $stmt = $pdo->prepare("INSERT IGNORE INTO `gallery_categories` (`name`) VALUES (?)");
                    $stmt->execute([$name]);
                    header('Location: gallery_categories.php?success=category_added');
                    exit;
                } catch (PDOException $e) {
                    die("Error saving gallery category: " . $e->getMessage());
                }
            }
        }
        header('Location: gallery_categories.php');
        exit;

    case 'delete_gallery_category':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM `gallery_categories` WHERE `id` = ?");
                $stmt->execute([$id]);
                header('Location: gallery_categories.php?success=category_deleted');
                exit;
            } catch (PDOException $e) {
                die("Error deleting gallery category: " . $e->getMessage());
            }
        }
        header('Location: gallery_categories.php');
        exit;

    case 'update_blog_category':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $name = isset($_POST['name']) ? strtoupper(trim($_POST['name'])) : '';
            if ($id > 0 && !empty($name)) {
                try {
                    $stmt = $pdo->prepare("UPDATE `blog_categories` SET `name` = ? WHERE `id` = ?");
                    $stmt->execute([$name, $id]);
                    header('Location: blog_categories.php?success=category_updated');
                    exit;
                } catch (PDOException $e) {
                    die("Error updating blog category: " . $e->getMessage());
                }
            }
        }
        header('Location: blog_categories.php');
        exit;

    case 'update_gallery_category':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $name = isset($_POST['name']) ? strtoupper(trim($_POST['name'])) : '';
            if ($id > 0 && !empty($name)) {
                try {
                    $stmt = $pdo->prepare("UPDATE `gallery_categories` SET `name` = ? WHERE `id` = ?");
                    $stmt->execute([$name, $id]);
                    header('Location: gallery_categories.php?success=category_updated');
                    exit;
                } catch (PDOException $e) {
                    die("Error updating gallery category: " . $e->getMessage());
                }
            }
        }
        header('Location: gallery_categories.php');
        exit;

    case 'save_recent_activity':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            
            // Set up local upload directories
            $upload_dir = __DIR__ . '/../uploads/recent-activity/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $db_path = '';

            // Handle cover image file upload:
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['image_file']['tmp_name'];
                $orig_name = basename($_FILES['image_file']['name']);
                $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                $new_filename = uniqid('activity_', true) . '.' . $ext;
                $dest = $upload_dir . $new_filename;
                
                if (move_uploaded_file($tmp_name, $dest)) {
                    $db_path = 'uploads/recent-activity/' . $new_filename;
                }
            } elseif (isset($_FILES['images']) && is_array($_FILES['images']['name']) && isset($_FILES['images']['name'][0])) {
                // If they upload via Add Mode cover dropzone
                if ($_FILES['images']['error'][0] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['images']['tmp_name'][0];
                    $orig_name = basename($_FILES['images']['name'][0]);
                    $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                    $new_filename = uniqid('activity_', true) . '.' . $ext;
                    $dest = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($tmp_name, $dest)) {
                        $db_path = 'uploads/recent-activity/' . $new_filename;
                    }
                }
            }

            // Handle additional gallery images:
            $additional_img_paths = [];
            if (isset($_FILES['activity_gallery']) && is_array($_FILES['activity_gallery']['name'])) {
                $total_gallery_files = count($_FILES['activity_gallery']['name']);
                for ($i = 0; $i < $total_gallery_files; $i++) {
                    if ($_FILES['activity_gallery']['error'][$i] === UPLOAD_ERR_OK) {
                        $g_tmp_name = $_FILES['activity_gallery']['tmp_name'][$i];
                        $g_orig_name = basename($_FILES['activity_gallery']['name'][$i]);
                        $g_ext = strtolower(pathinfo($g_orig_name, PATHINFO_EXTENSION));
                        $g_new_filename = uniqid('act_gal_', true) . '.' . $g_ext;
                        $g_dest = $upload_dir . $g_new_filename;
                        
                        if (move_uploaded_file($g_tmp_name, $g_dest)) {
                            $additional_img_paths[] = 'uploads/recent-activity/' . $g_new_filename;
                        }
                    }
                }
            }

            if ($id > 0) {
                // Update Mode
                try {
                    // Fetch existing gallery string to support retention/deletions
                    $stmt_g = $pdo->prepare("SELECT `additional_images` FROM `recent_activities` WHERE `id` = ?");
                    $stmt_g->execute([$id]);
                    $existing_gallery_str = $stmt_g->fetchColumn();
                    $existing_gallery = $existing_gallery_str ? array_filter(explode(',', $existing_gallery_str)) : [];

                    // Retained images
                    $retained_gallery = isset($_POST['retained_gallery_images']) ? $_POST['retained_gallery_images'] : [];
                    
                    // Unlink deleted gallery images
                    foreach ($existing_gallery as $old_g_img) {
                        if (!in_array($old_g_img, $retained_gallery)) {
                            $old_g_path = __DIR__ . '/../' . trim($old_g_img);
                            if (file_exists($old_g_path)) {
                                @unlink($old_g_path);
                            }
                        }
                    }

                    // Merge retained and new
                    $final_gallery = array_merge($retained_gallery, $additional_img_paths);
                    $final_gallery_str = implode(',', $final_gallery);

                    if (!empty($db_path)) {
                        // Delete old cover image
                        $stmt = $pdo->prepare("SELECT `image` FROM `recent_activities` WHERE `id` = ?");
                        $stmt->execute([$id]);
                        $old_img = $stmt->fetchColumn();
                        if ($old_img && strpos($old_img, 'uploads/recent-activity/') === 0) {
                            $old_file_path = __DIR__ . '/../' . $old_img;
                            if (file_exists($old_file_path)) {
                                @unlink($old_file_path);
                            }
                        }

                        $stmt = $pdo->prepare("UPDATE `recent_activities` SET `title` = ?, `description` = ?, `image` = ?, `additional_images` = ? WHERE `id` = ?");
                        $stmt->execute([$title, $description, $db_path, $final_gallery_str, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE `recent_activities` SET `title` = ?, `description` = ?, `additional_images` = ? WHERE `id` = ?");
                        $stmt->execute([$title, $description, $final_gallery_str, $id]);
                    }

                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'success', 'message' => 'Activity updated successfully.']);
                        exit;
                    } else {
                        header('Location: recent_activities.php?success=updated');
                        exit;
                    }
                } catch (PDOException $e) {
                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                        exit;
                    } else {
                        die("Error updating activity: " . $e->getMessage());
                    }
                }
            } else {
                // Insert Mode
                try {
                    $final_gallery_str = implode(',', $additional_img_paths);

                    $stmt = $pdo->prepare("INSERT INTO `recent_activities` (`title`, `description`, `image`, `additional_images`) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$title, $description, $db_path, $final_gallery_str]);

                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'success', 'message' => 'Activity created successfully.']);
                        exit;
                    } else {
                        header('Location: recent_activities.php?success=created');
                        exit;
                    }
                } catch (PDOException $e) {
                    if (isset($_GET['ajax'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                        exit;
                    } else {
                        die("Error inserting activity: " . $e->getMessage());
                    }
                }
            }
        }
        break;

    case 'delete_recent_activity':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                // Delete physical image files
                $stmt = $pdo->prepare("SELECT `image`, `additional_images` FROM `recent_activities` WHERE `id` = ?");
                $stmt->execute([$id]);
                $act_data = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($act_data) {
                    // Unlink cover
                    $old_img = $act_data['image'];
                    if ($old_img && strpos($old_img, 'uploads/recent-activity/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_img;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }

                    // Unlink additional gallery images
                    if (!empty($act_data['additional_images'])) {
                        $gal_imgs = array_filter(explode(',', $act_data['additional_images']));
                        foreach ($gal_imgs as $g_img) {
                            $g_img_path = __DIR__ . '/../' . trim($g_img);
                            if (file_exists($g_img_path)) {
                                @unlink($g_img_path);
                            }
                        }
                    }
                }
                
                $stmt = $pdo->prepare("DELETE FROM `recent_activities` WHERE `id` = ?");
                $stmt->execute([$id]);
                header('Location: recent_activities.php?success=deleted');
                exit;
            } catch (PDOException $e) {
                die("Error deleting activity: " . $e->getMessage());
            }
        }
        break;

    case 'save_video':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $url = isset($_POST['url']) ? trim($_POST['url']) : '';

            if (empty($title) || empty($url)) {
                die("Error: Video Title and URL are required.");
            }

            if ($id > 0) {
                // Update
                try {
                    $stmt = $pdo->prepare("UPDATE `testimonial_videos` SET `title` = ?, `url` = ? WHERE `id` = ?");
                    $stmt->execute([$title, $url, $id]);
                    header('Location: videos.php?success=updated');
                    exit;
                } catch (PDOException $e) {
                    die("Error updating video: " . $e->getMessage());
                }
            } else {
                // Insert
                try {
                    $stmt = $pdo->prepare("INSERT INTO `testimonial_videos` (`title`, `url`) VALUES (?, ?)");
                    $stmt->execute([$title, $url]);
                    header('Location: videos.php?success=created');
                    exit;
                } catch (PDOException $e) {
                    die("Error saving video: " . $e->getMessage());
                }
            }
        }
        break;

    case 'save_committee_member':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $position = isset($_POST['position']) ? trim($_POST['position']) : '';
        $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $member_type = isset($_POST['member_type']) ? trim($_POST['member_type']) : 'board';
        $sort_order = isset($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
        
        $upload_dir = __DIR__ . '/../uploads/committe/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $db_path = '';
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image_file']['tmp_name'];
            $orig_name = basename($_FILES['image_file']['name']);
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            $new_filename = uniqid('member_', true) . '.' . $ext;
            $dest = $upload_dir . $new_filename;
            if (move_uploaded_file($tmp_name, $dest)) {
                $db_path = 'uploads/committe/' . $new_filename;
                
                // Delete old image
                if ($id > 0) {
                    $stmt = $pdo->prepare("SELECT `image` FROM `current_committee` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    $old_img = $stmt->fetchColumn();
                    if ($old_img && strpos($old_img, 'uploads/committe/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_img;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }
                }
            }
        }

        try {
            if ($id > 0) {
                if (!empty($db_path)) {
                    $stmt = $pdo->prepare("UPDATE `current_committee` SET `name` = ?, `position` = ?, `bio` = ?, `email` = ?, `phone` = ?, `image` = ?, `member_type` = ?, `sort_order` = ? WHERE `id` = ?");
                    $stmt->execute([$name, $position, $bio, $email, $phone, $db_path, $member_type, $sort_order, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE `current_committee` SET `name` = ?, `position` = ?, `bio` = ?, `email` = ?, `phone` = ?, `member_type` = ?, `sort_order` = ? WHERE `id` = ?");
                    $stmt->execute([$name, $position, $bio, $email, $phone, $member_type, $sort_order, $id]);
                }
                $success_type = 'updated';
            } else {
                $stmt = $pdo->prepare("INSERT INTO `current_committee` (`name`, `position`, `bio`, `email`, `phone`, `image`, `member_type`, `sort_order`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $position, $bio, $email, $phone, $db_path, $member_type, $sort_order]);
                $success_type = 'created';
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'success', 'message' => 'Member saved successfully']);
                exit;
            } else {
                header('Location: committee_current.php?success=' . $success_type);
                exit;
            }
        } catch (PDOException $e) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                exit;
            } else {
                die("Error saving member: " . $e->getMessage());
            }
        }
        break;

    case 'delete_committee_member':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                // Delete image file first
                $stmt = $pdo->prepare("SELECT `image` FROM `current_committee` WHERE `id` = ?");
                $stmt->execute([$id]);
                $old_img = $stmt->fetchColumn();
                if ($old_img && strpos($old_img, 'uploads/committe/') === 0) {
                    $old_file_path = __DIR__ . '/../' . $old_img;
                    if (file_exists($old_file_path)) {
                        @unlink($old_file_path);
                    }
                }

                $stmt = $pdo->prepare("DELETE FROM `current_committee` WHERE `id` = ?");
                $stmt->execute([$id]);
                header('Location: committee_current.php?success=deleted');
                exit;
            } catch (PDOException $e) {
                die("Error deleting member: " . $e->getMessage());
            }
        }
        break;

    case 'save_committee_document':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $year = isset($_POST['year']) ? trim($_POST['year']) : '';
        $doc_type = isset($_POST['doc_type']) ? trim($_POST['doc_type']) : 'previous_executive';
        
        $upload_dir = __DIR__ . '/../uploads/committe/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $db_path = '';
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['pdf_file']['tmp_name'];
            $orig_name = basename($_FILES['pdf_file']['name']);
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            $new_filename = uniqid('doc_', true) . '.' . $ext;
            $dest = $upload_dir . $new_filename;
            if (move_uploaded_file($tmp_name, $dest)) {
                $db_path = 'uploads/committe/' . $new_filename;
                
                // Delete old PDF
                if ($id > 0) {
                    $stmt = $pdo->prepare("SELECT `pdf_path` FROM `committee_documents` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    $old_pdf = $stmt->fetchColumn();
                    if ($old_pdf && strpos($old_pdf, 'uploads/committe/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_pdf;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }
                }
            }
        }

        try {
            if ($id > 0) {
                if (!empty($db_path)) {
                    $stmt = $pdo->prepare("UPDATE `committee_documents` SET `title` = ?, `year` = ?, `pdf_path` = ?, `doc_type` = ? WHERE `id` = ?");
                    $stmt->execute([$title, $year, $db_path, $doc_type, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE `committee_documents` SET `title` = ?, `year` = ?, `doc_type` = ? WHERE `id` = ?");
                    $stmt->execute([$title, $year, $doc_type, $id]);
                }
                $success_type = 'updated';
            } else {
                $stmt = $pdo->prepare("INSERT INTO `committee_documents` (`title`, `year`, `pdf_path`, `doc_type`) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $year, $db_path, $doc_type]);
                $success_type = 'created';
            }

            // Determine redirection page based on doc_type
            $redirect_page = 'committee_previous.php';
            if ($doc_type === 'puja_samiti') {
                $redirect_page = 'committee_puja_samiti.php';
            } elseif ($doc_type === 'process') {
                $redirect_page = 'committee_processes.php';
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'success', 'message' => 'Document saved successfully', 'redirect' => $redirect_page]);
                exit;
            } else {
                header('Location: ' . $redirect_page . '?success=' . $success_type);
                exit;
            }
        } catch (PDOException $e) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                exit;
            } else {
                die("Error saving document: " . $e->getMessage());
            }
        }
        break;

    case 'delete_committee_document':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                // Delete PDF file first
                $stmt = $pdo->prepare("SELECT `pdf_path`, `doc_type` FROM `committee_documents` WHERE `id` = ?");
                $stmt->execute([$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row) {
                    $old_pdf = $row['pdf_path'];
                    $doc_type = $row['doc_type'];
                    
                    if ($old_pdf && strpos($old_pdf, 'uploads/committe/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_pdf;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }

                    $stmt = $pdo->prepare("DELETE FROM `committee_documents` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    
                    // Determine redirection page based on doc_type
                    $redirect_page = 'committee_previous.php';
                    if ($doc_type === 'puja_samiti') {
                        $redirect_page = 'committee_puja_samiti.php';
                    } elseif ($doc_type === 'process') {
                        $redirect_page = 'committee_processes.php';
                    }

                    header('Location: ' . $redirect_page . '?success=deleted');
                    exit;
                }
            } catch (PDOException $e) {
                die("Error deleting document: " . $e->getMessage());
            }
        }
        break;

    case 'save_member_document':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $year = isset($_POST['year']) ? trim($_POST['year']) : '';
        $doc_type = isset($_POST['doc_type']) ? trim($_POST['doc_type']) : 'our_members';
        
        $upload_dir = __DIR__ . '/../uploads/members/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $db_path = '';
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['pdf_file']['tmp_name'];
            $orig_name = basename($_FILES['pdf_file']['name']);
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            $new_filename = uniqid('member_doc_', true) . '.' . $ext;
            $dest = $upload_dir . $new_filename;
            if (move_uploaded_file($tmp_name, $dest)) {
                $db_path = 'uploads/members/' . $new_filename;
                
                // Delete old PDF
                if ($id > 0) {
                    $stmt = $pdo->prepare("SELECT `pdf_path` FROM `member_documents` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    $old_pdf = $stmt->fetchColumn();
                    if ($old_pdf && strpos($old_pdf, 'uploads/members/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_pdf;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }
                }
            }
        }

        try {
            if ($id > 0) {
                if (!empty($db_path)) {
                    $stmt = $pdo->prepare("UPDATE `member_documents` SET `title` = ?, `year` = ?, `pdf_path` = ?, `doc_type` = ? WHERE `id` = ?");
                    $stmt->execute([$title, $year, $db_path, $doc_type, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE `member_documents` SET `title` = ?, `year` = ?, `doc_type` = ? WHERE `id` = ?");
                    $stmt->execute([$title, $year, $doc_type, $id]);
                }
                $success_type = 'updated';
            } else {
                $stmt = $pdo->prepare("INSERT INTO `member_documents` (`title`, `year`, `pdf_path`, `doc_type`) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $year, $db_path, $doc_type]);
                $success_type = 'created';
            }

            // Determine redirection page based on doc_type
            $redirect_page = 'members_our.php';
            if ($doc_type === 'member_profile') {
                $redirect_page = 'members_profile.php';
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'success', 'message' => 'Document saved successfully', 'redirect' => $redirect_page]);
                exit;
            } else {
                header('Location: ' . $redirect_page . '?success=' . $success_type);
                exit;
            }
        } catch (PDOException $e) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                exit;
            } else {
                die("Error saving document: " . $e->getMessage());
            }
        }
        break;

    case 'delete_member_document':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                // Delete PDF file first
                $stmt = $pdo->prepare("SELECT `pdf_path`, `doc_type` FROM `member_documents` WHERE `id` = ?");
                $stmt->execute([$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row) {
                    $old_pdf = $row['pdf_path'];
                    $doc_type = $row['doc_type'];
                    
                    if ($old_pdf && strpos($old_pdf, 'uploads/members/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_pdf;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }

                    $stmt = $pdo->prepare("DELETE FROM `member_documents` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    
                    // Determine redirection page based on doc_type
                    $redirect_page = 'members_our.php';
                    if ($doc_type === 'member_profile') {
                        $redirect_page = 'members_profile.php';
                    }

                    header('Location: ' . $redirect_page . '?success=deleted');
                    exit;
                }
            } catch (PDOException $e) {
                die("Error deleting document: " . $e->getMessage());
            }
        }
        break;

    case 'save_partner_document':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $year = isset($_POST['year']) ? trim($_POST['year']) : '';
        $doc_type = isset($_POST['doc_type']) ? trim($_POST['doc_type']) : 'sponsor';
        
        $upload_dir = __DIR__ . '/../uploads/partners/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $db_path = '';
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['pdf_file']['tmp_name'];
            $orig_name = basename($_FILES['pdf_file']['name']);
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            $new_filename = uniqid('partner_doc_', true) . '.' . $ext;
            $dest = $upload_dir . $new_filename;
            if (move_uploaded_file($tmp_name, $dest)) {
                $db_path = 'uploads/partners/' . $new_filename;
                
                // Delete old PDF
                if ($id > 0) {
                    $stmt = $pdo->prepare("SELECT `pdf_path` FROM `partner_documents` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    $old_pdf = $stmt->fetchColumn();
                    if ($old_pdf && strpos($old_pdf, 'uploads/partners/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_pdf;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }
                }
            }
        }

        try {
            if ($id > 0) {
                if (!empty($db_path)) {
                    $stmt = $pdo->prepare("UPDATE `partner_documents` SET `title` = ?, `year` = ?, `pdf_path` = ?, `doc_type` = ? WHERE `id` = ?");
                    $stmt->execute([$title, $year, $db_path, $doc_type, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE `partner_documents` SET `title` = ?, `year` = ?, `doc_type` = ? WHERE `id` = ?");
                    $stmt->execute([$title, $year, $doc_type, $id]);
                }
                $success_type = 'updated';
            } else {
                $stmt = $pdo->prepare("INSERT INTO `partner_documents` (`title`, `year`, `pdf_path`, `doc_type`) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $year, $db_path, $doc_type]);
                $success_type = 'created';
            }

            // Determine redirection page based on doc_type
            $redirect_page = 'partners_sponsors.php';
            if ($doc_type === 'patron') {
                $redirect_page = 'partners_patrons.php';
            } elseif ($doc_type === 'authority') {
                $redirect_page = 'partners_authorities.php';
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'success', 'message' => 'Document saved successfully', 'redirect' => $redirect_page]);
                exit;
            } else {
                header('Location: ' . $redirect_page . '?success=' . $success_type);
                exit;
            }
        } catch (PDOException $e) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                exit;
            } else {
                die("Error saving document: " . $e->getMessage());
            }
        }
        break;

    case 'delete_partner_document':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                // Delete PDF file first
                $stmt = $pdo->prepare("SELECT `pdf_path`, `doc_type` FROM `partner_documents` WHERE `id` = ?");
                $stmt->execute([$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row) {
                    $old_pdf = $row['pdf_path'];
                    $doc_type = $row['doc_type'];
                    
                    if ($old_pdf && strpos($old_pdf, 'uploads/partners/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_pdf;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }

                    $stmt = $pdo->prepare("DELETE FROM `partner_documents` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    
                    // Determine redirection page based on doc_type
                    $redirect_page = 'partners_sponsors.php';
                    if ($doc_type === 'patron') {
                        $redirect_page = 'partners_patrons.php';
                    } elseif ($doc_type === 'authority') {
                        $redirect_page = 'partners_authorities.php';
                    }

                    header('Location: ' . $redirect_page . '?success=deleted');
                    exit;
                }
            } catch (PDOException $e) {
                die("Error deleting document: " . $e->getMessage());
            }
        }
        break;

    case 'save_association_document':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $year = isset($_POST['year']) ? trim($_POST['year']) : '';
        $doc_type = isset($_POST['doc_type']) ? trim($_POST['doc_type']) : 'souvenir';
        
        $upload_dir = __DIR__ . '/../uploads/documents/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $db_path = '';
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['pdf_file']['tmp_name'];
            $orig_name = basename($_FILES['pdf_file']['name']);
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            $new_filename = uniqid('association_doc_', true) . '.' . $ext;
            $dest = $upload_dir . $new_filename;
            if (move_uploaded_file($tmp_name, $dest)) {
                $db_path = 'uploads/documents/' . $new_filename;
                
                // Delete old PDF
                if ($id > 0) {
                    $stmt = $pdo->prepare("SELECT `pdf_path` FROM `association_documents` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    $old_pdf = $stmt->fetchColumn();
                    if ($old_pdf && strpos($old_pdf, 'uploads/documents/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_pdf;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }
                }
            }
        }

        try {
            if ($id > 0) {
                if (!empty($db_path)) {
                    $stmt = $pdo->prepare("UPDATE `association_documents` SET `title` = ?, `year` = ?, `pdf_path` = ?, `doc_type` = ? WHERE `id` = ?");
                    $stmt->execute([$title, $year, $db_path, $doc_type, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE `association_documents` SET `title` = ?, `year` = ?, `doc_type` = ? WHERE `id` = ?");
                    $stmt->execute([$title, $year, $doc_type, $id]);
                }
                $success_type = 'updated';
            } else {
                $stmt = $pdo->prepare("INSERT INTO `association_documents` (`title`, `year`, `pdf_path`, `doc_type`) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $year, $db_path, $doc_type]);
                $success_type = 'created';
            }

            // Determine redirection page based on doc_type
            $redirect_page = 'documents_souvenir.php';
            if ($doc_type === 'competition') {
                $redirect_page = 'documents_competitions.php';
            } elseif ($doc_type === 'recognition') {
                $redirect_page = 'documents_recognition.php';
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'success', 'message' => 'Document saved successfully', 'redirect' => $redirect_page]);
                exit;
            } else {
                header('Location: ' . $redirect_page . '?success=' . $success_type);
                exit;
            }
        } catch (PDOException $e) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                exit;
            } else {
                die("Error saving document: " . $e->getMessage());
            }
        }
        break;

    case 'delete_association_document':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                // Delete PDF file first
                $stmt = $pdo->prepare("SELECT `pdf_path`, `doc_type` FROM `association_documents` WHERE `id` = ?");
                $stmt->execute([$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row) {
                    $old_pdf = $row['pdf_path'];
                    $doc_type = $row['doc_type'];
                    
                    if ($old_pdf && strpos($old_pdf, 'uploads/documents/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_pdf;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }

                    $stmt = $pdo->prepare("DELETE FROM `association_documents` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    
                    // Determine redirection page based on doc_type
                    $redirect_page = 'documents_souvenir.php';
                    if ($doc_type === 'competition') {
                        $redirect_page = 'documents_competitions.php';
                    } elseif ($doc_type === 'recognition') {
                        $redirect_page = 'documents_recognition.php';
                    }

                    header('Location: ' . $redirect_page . '?success=deleted');
                    exit;
                }
            } catch (PDOException $e) {
                die("Error deleting document: " . $e->getMessage());
            }
        }
        break;

    case 'save_key_message':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $year = isset($_POST['year']) ? trim($_POST['year']) : '';
        $doc_type = isset($_POST['doc_type']) ? trim($_POST['doc_type']) : 'president_samiti';
        
        $upload_dir = __DIR__ . '/../uploads/key-message/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $db_path = '';
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['pdf_file']['tmp_name'];
            $orig_name = basename($_FILES['pdf_file']['name']);
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            $new_filename = uniqid('key_msg_', true) . '.' . $ext;
            $dest = $upload_dir . $new_filename;
            if (move_uploaded_file($tmp_name, $dest)) {
                $db_path = 'uploads/key-message/' . $new_filename;
                
                // Delete old PDF
                if ($id > 0) {
                    $stmt = $pdo->prepare("SELECT `pdf_path` FROM `key_messages` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    $old_pdf = $stmt->fetchColumn();
                    if ($old_pdf && strpos($old_pdf, 'uploads/key-message/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_pdf;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }
                }
            }
        }

        try {
            if ($id > 0) {
                if (!empty($db_path)) {
                    $stmt = $pdo->prepare("UPDATE `key_messages` SET `title` = ?, `year` = ?, `pdf_path` = ?, `doc_type` = ? WHERE `id` = ?");
                    $stmt->execute([$title, $year, $db_path, $doc_type, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE `key_messages` SET `title` = ?, `year` = ?, `doc_type` = ? WHERE `id` = ?");
                    $stmt->execute([$title, $year, $doc_type, $id]);
                }
                $success_type = 'updated';
            } else {
                $stmt = $pdo->prepare("INSERT INTO `key_messages` (`title`, `year`, `pdf_path`, `doc_type`) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $year, $db_path, $doc_type]);
                $success_type = 'created';
            }

            // Determine redirection page based on doc_type
            $redirect_page = 'messages_president_samiti.php';
            if ($doc_type === 'secretary_samiti') {
                $redirect_page = 'messages_secretary_samiti.php';
            } elseif ($doc_type === 'eminent') {
                $redirect_page = 'messages_eminent.php';
            } elseif ($doc_type === 'president_india') {
                $redirect_page = 'messages_president_india.php';
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'success', 'message' => 'Document saved successfully', 'redirect' => $redirect_page]);
                exit;
            } else {
                header('Location: ' . $redirect_page . '?success=' . $success_type);
                exit;
            }
        } catch (PDOException $e) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                exit;
            } else {
                die("Error saving document: " . $e->getMessage());
            }
        }
        break;

    case 'delete_key_message':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                // Delete PDF file first
                $stmt = $pdo->prepare("SELECT `pdf_path`, `doc_type` FROM `key_messages` WHERE `id` = ?");
                $stmt->execute([$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row) {
                    $old_pdf = $row['pdf_path'];
                    $doc_type = $row['doc_type'];
                    
                    if ($old_pdf && strpos($old_pdf, 'uploads/key-message/') === 0) {
                        $old_file_path = __DIR__ . '/../' . $old_pdf;
                        if (file_exists($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }

                    $stmt = $pdo->prepare("DELETE FROM `key_messages` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    
                    // Determine redirection page based on doc_type
                    $redirect_page = 'messages_president_samiti.php';
                    if ($doc_type === 'secretary_samiti') {
                        $redirect_page = 'messages_secretary_samiti.php';
                    } elseif ($doc_type === 'eminent') {
                        $redirect_page = 'messages_eminent.php';
                    } elseif ($doc_type === 'president_india') {
                        $redirect_page = 'messages_president_india.php';
                    }

                    header('Location: ' . $redirect_page . '?success=deleted');
                    exit;
                }
            } catch (PDOException $e) {
                die("Error deleting document: " . $e->getMessage());
            }
        }
        break;

    case 'delete_membership_request':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM `membership_requests` WHERE `id` = ?");
                $stmt->execute([$id]);
                header('Location: membership_requests.php?success=deleted');
                exit;
            } catch (PDOException $e) {
                die("Error deleting membership request: " . $e->getMessage());
            }
        }
        break;
    case 'delete_contact_message':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM `contact_messages` WHERE `id` = ?");
                $stmt->execute([$id]);
                header('Location: contact_messages.php?success=deleted');
                exit;
            } catch (PDOException $e) {
                die("Error deleting contact message: " . $e->getMessage());
            }
        }
        break;
}

header('Location: dashboard.php');
exit;
?>
