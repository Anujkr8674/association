<?php
// Function to manually parse .env file
function load_env($path) {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Split on first '='
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
            putenv(trim($name) . '=' . trim($value));
        }
    }
}

// Load env variables
load_env(__DIR__ . '/.env');

// Toggle to enable/disable automatic external API event synchronization
define('ENABLE_API_SYNC', true);

// Database credentials with fallbacks
$host = isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'localhost';
$db_user = isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : 'root';
$db_pass = isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : '';
$db_name = isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'association';

try {
    // 1. Establish connection to MySQL server without database first to check/create it
    $pdo = new PDO("mysql:host=$host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // 2. Connect to the association database
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper to add missing columns safely
function add_column_if_missing($pdo, $table, $column, $definition) {
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        }
    } catch (PDOException $e) {
        // Fail silently or handle migration check
    }
}

// 3. Create Admin table
$pdo->exec("CREATE TABLE IF NOT EXISTS `admin` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 4. Create Events table
$pdo->exec("CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `date` DATE NOT NULL,
    `time` VARCHAR(100) NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `image` VARCHAR(255) DEFAULT '',
    `description` TEXT NOT NULL,
    `motif` VARCHAR(50) DEFAULT 'lotus',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Safe migrations for events table
add_column_if_missing($pdo, 'events', 'slug', "VARCHAR(150) NULL AFTER `title`");
add_column_if_missing($pdo, 'events', 'short_description', "VARCHAR(255) NULL AFTER `slug`");
add_column_if_missing($pdo, 'events', 'end_date', "DATE NULL AFTER `date`");
add_column_if_missing($pdo, 'events', 'start_time', "TIME NULL AFTER `time`");
add_column_if_missing($pdo, 'events', 'end_time', "TIME NULL AFTER `start_time`");
add_column_if_missing($pdo, 'events', 'all_day', "TINYINT(1) DEFAULT 1 AFTER `end_time`");
add_column_if_missing($pdo, 'events', 'event_type', "VARCHAR(50) DEFAULT 'association' AFTER `category`");
add_column_if_missing($pdo, 'events', 'address', "VARCHAR(255) NULL AFTER `location`");
add_column_if_missing($pdo, 'events', 'city', "VARCHAR(100) NULL AFTER `address`");
add_column_if_missing($pdo, 'events', 'state', "VARCHAR(100) NULL AFTER `city`");
add_column_if_missing($pdo, 'events', 'country', "VARCHAR(100) NULL AFTER `state`");
add_column_if_missing($pdo, 'events', 'latitude', "DECIMAL(10, 8) NULL AFTER `country`");
add_column_if_missing($pdo, 'events', 'longitude', "DECIMAL(11, 8) NULL AFTER `latitude`");
add_column_if_missing($pdo, 'events', 'map_url', "VARCHAR(255) NULL AFTER `longitude`");
add_column_if_missing($pdo, 'events', 'source', "VARCHAR(50) DEFAULT 'association' AFTER `motif`");
add_column_if_missing($pdo, 'events', 'external_event_id', "VARCHAR(100) NULL AFTER `source`");
add_column_if_missing($pdo, 'events', 'source_event_key', "VARCHAR(100) NULL AFTER `external_event_id`");
add_column_if_missing($pdo, 'events', 'is_custom', "TINYINT(1) DEFAULT 1 AFTER `source_event_key`");
add_column_if_missing($pdo, 'events', 'is_external', "TINYINT(1) DEFAULT 0 AFTER `is_custom`");
add_column_if_missing($pdo, 'events', 'is_featured', "TINYINT(1) DEFAULT 0 AFTER `is_external`");
add_column_if_missing($pdo, 'events', 'is_active', "TINYINT(1) DEFAULT 1 AFTER `is_featured`");
add_column_if_missing($pdo, 'events', 'status', "VARCHAR(20) DEFAULT 'active' AFTER `is_active`");

// 5. Create Blogs table
$pdo->exec("CREATE TABLE IF NOT EXISTS `blogs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `excerpt` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT '',
    `category` VARCHAR(50) NOT NULL,
    `date` DATE NOT NULL,
    `author` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Safe migration for blogs table to store multiple photos
add_column_if_missing($pdo, 'blogs', 'additional_images', "TEXT NULL AFTER `image`");

// 6. Create Gallery table
$pdo->exec("CREATE TABLE IF NOT EXISTS `gallery` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 6.1 Create Blog Categories table
$pdo->exec("CREATE TABLE IF NOT EXISTS `blog_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) UNIQUE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 6.2 Create Gallery Categories table
$pdo->exec("CREATE TABLE IF NOT EXISTS `gallery_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) UNIQUE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 6.2.1 Create Recent Activities table
$pdo->exec("CREATE TABLE IF NOT EXISTS `recent_activities` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    `additional_images` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Safe migration to add additional_images column if not exists
try {
    $pdo->query("SELECT `additional_images` FROM `recent_activities` LIMIT 1");
} catch (PDOException $e) {
    $pdo->exec("ALTER TABLE `recent_activities` ADD COLUMN `additional_images` TEXT NULL AFTER `image`");
}

// Seed default Recent Activities
$recentActCount = $pdo->query("SELECT COUNT(*) FROM `recent_activities`")->fetchColumn();
if ($recentActCount == 0) {
    $stmt = $pdo->prepare("INSERT INTO `recent_activities` (`title`, `description`, `image`) VALUES (?, ?, ?)");
    $stmt->execute(['Morning Programme 2018', 'Our beautiful community gathering for morning rituals and prayers during Durga Puja 2018.', 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?q=80&w=600']);
    $stmt->execute(['Durga Puja Invitation Card 2021', 'The official creative design and release of our Durga Puja invitation cards for 2021.', 'https://images.unsplash.com/photo-1502134249126-9f3755a50d78?q=80&w=600']);
    $stmt->execute(['Evening Programme 2018', 'Dance dramas, classical songs and folk performances by our community members in 2018.', 'https://images.unsplash.com/photo-1536304997881-a372c179924b?q=80&w=600']);
    $stmt->execute(['Dandiya Night 2018', 'Vibrant Garba and Dandiya dance events under decorative lighting with delicious foods.', 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80&w=600']);
}

// 6.3 Seed default Blog Categories
$blogCatCount = $pdo->query("SELECT COUNT(*) FROM `blog_categories`")->fetchColumn();
if ($blogCatCount == 0) {
    $pdo->exec("INSERT INTO `blog_categories` (`name`) VALUES ('HERITAGE'), ('CULTURE'), ('FESTIVALS')");
}

// 6.4 Seed default Gallery Categories
$galleryCatCount = $pdo->query("SELECT COUNT(*) FROM `gallery_categories`")->fetchColumn();
if ($galleryCatCount == 0) {
    $pdo->exec("INSERT INTO `gallery_categories` (`name`) VALUES ('DURGA-PUJA'), ('FESTIVALS'), ('PUJA'), ('CULTURAL-EVENTS')");
}

// 6.5 Create Testimonial Videos table
$pdo->exec("CREATE TABLE IF NOT EXISTS `testimonial_videos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Seed default testimonial videos if empty
$videoCount = $pdo->query("SELECT COUNT(*) FROM `testimonial_videos`")->fetchColumn();
if ($videoCount == 0) {
    $default_videos = [
        ['title' => 'Sindur Khela on Dashami celebration', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ['title' => 'Anjali and Evening Aarti Highlights', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ['title' => 'Dhunuchi Dance Competition 2026', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ['title' => 'Bengali Cultural Drama Performance', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ['title' => 'Rabindra Sangeet & Recital Tribute', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ['title' => 'Annual Picnic & Sports Highlights', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']
    ];
    $insertVideo = $pdo->prepare("INSERT INTO `testimonial_videos` (`title`, `url`) VALUES (?, ?)");
    foreach ($default_videos as $vid) {
        $insertVideo->execute([$vid['title'], $vid['url']]);
    }
}

// 7. Create event_normalization table
$pdo->exec("CREATE TABLE IF NOT EXISTS `event_normalization` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `original_name` VARCHAR(150) UNIQUE NOT NULL,
    `normalized_name` VARCHAR(150) NOT NULL,
    `normalized_category` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 8. Create event_overrides table
$pdo->exec("CREATE TABLE IF NOT EXISTS `event_overrides` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT UNIQUE NOT NULL,
    `title` VARCHAR(150) NULL,
    `short_description` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `location` VARCHAR(150) NULL,
    `start_date` DATE NULL,
    `end_date` DATE NULL,
    `start_time` TIME NULL,
    `end_time` TIME NULL,
    `all_day` TINYINT(1) NULL,
    `image` VARCHAR(255) NULL,
    `category` VARCHAR(50) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB");

// 9. Create event_schedules table
$pdo->exec("CREATE TABLE IF NOT EXISTS `event_schedules` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `date` DATE NOT NULL,
    `time` VARCHAR(100) NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB");

// 10. Create sync_logs table
$pdo->exec("CREATE TABLE IF NOT EXISTS `sync_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `year` INT NOT NULL,
    `month` INT NOT NULL,
    `source` VARCHAR(50) NOT NULL,
    `last_synced_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `status` VARCHAR(20) NOT NULL,
    UNIQUE KEY `year_month_source` (`year`, `month`, `source`)
) ENGINE=InnoDB");

// 11. Create current_committee table
$pdo->exec("CREATE TABLE IF NOT EXISTS `current_committee` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `position` VARCHAR(255) NOT NULL,
    `bio` TEXT NULL,
    `email` VARCHAR(255) NULL,
    `phone` VARCHAR(50) NULL,
    `image` VARCHAR(255) NULL,
    `member_type` VARCHAR(50) DEFAULT 'board', -- 'board' or 'executive'
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Seed default current committee if empty
$commCount = $pdo->query("SELECT COUNT(*) FROM `current_committee`")->fetchColumn();
if ($commCount == 0) {
    $default_comm = [
        [
            'name' => 'Dr. Amitabha Ghosh',
            'position' => 'President',
            'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=300',
            'bio' => 'Senior cardiologist and cultural patron. Leading the association\'s strategic vision and heritage outreach.',
            'email' => 'president@bengalicultural.org',
            'member_type' => 'board',
            'sort_order' => 1
        ],
        [
            'name' => 'Smt. Sudeshna Sen',
            'position' => 'Vice President',
            'image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=300',
            'bio' => 'Renowned educator and social activist. Focuses on youth programs and educational workshop integrations.',
            'email' => 'vp@bengalicultural.org',
            'member_type' => 'board',
            'sort_order' => 2
        ],
        [
            'name' => 'Shri. Rajib Chatterjee',
            'position' => 'General Secretary',
            'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=300',
            'bio' => 'IT Program Manager and event enthusiast. Manages overall administrative operations and communications.',
            'email' => 'secretary@bengalicultural.org',
            'member_type' => 'board',
            'sort_order' => 3
        ],
        [
            'name' => 'Shri. Debjit Mukhopadhyay',
            'position' => 'Joint Secretary',
            'image' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=300',
            'bio' => 'Local businessman and volunteer lead. Coordinates logistics, membership coordination, and food drives.',
            'email' => 'joint.sec@bengalicultural.org',
            'member_type' => 'board',
            'sort_order' => 4
        ],
        [
            'name' => 'Smt. Madhurima Bose',
            'position' => 'Treasurer',
            'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=300',
            'bio' => 'Chartered Accountant. Overlooks financial accounts, audit compliances, and membership funding logs.',
            'email' => 'treasurer@bengalicultural.org',
            'member_type' => 'board',
            'sort_order' => 5
        ],
        [
            'name' => 'Shri. Arindam Das',
            'position' => 'Cultural Secretary',
            'image' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=300',
            'bio' => 'Classical vocalist and director. Directs music festivals, theatre workshops, and dance drama scripts.',
            'email' => 'cultural@bengalicultural.org',
            'member_type' => 'board',
            'sort_order' => 6
        ],
        [
            'name' => 'Shri. Sougata Banerjee',
            'position' => 'Executive Committee Member',
            'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=300',
            'bio' => 'Senior Developer. Manages digital portals, email setups, and tech infrastructure for programs.',
            'email' => '',
            'member_type' => 'executive',
            'sort_order' => 7
        ],
        [
            'name' => 'Smt. Tanusree Dey',
            'position' => 'Executive Committee Member',
            'image' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=300',
            'bio' => 'Traditional fine artist. Designs pandal stage decorations, floral alpana art, and exhibitions.',
            'email' => '',
            'member_type' => 'executive',
            'sort_order' => 8
        ],
        [
            'name' => 'Shri. Abhijit Roy',
            'position' => 'Executive Committee Member',
            'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=300',
            'bio' => 'Logistics Advisor. Coordinates venue bookings, power setups, and municipal coordination.',
            'email' => '',
            'member_type' => 'executive',
            'sort_order' => 9
        ]
    ];
    
    $insertComm = $pdo->prepare("INSERT INTO `current_committee` (`name`, `position`, `image`, `bio`, `email`, `phone`, `member_type`, `sort_order`) VALUES (?, ?, ?, ?, ?, NULL, ?, ?)");
    foreach ($default_comm as $c) {
        $insertComm->execute([$c['name'], $c['position'], $c['image'], $c['bio'], $c['email'], $c['member_type'], $c['sort_order']]);
    }
}

// 12. Create committee_documents table
$pdo->exec("CREATE TABLE IF NOT EXISTS `committee_documents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `year` VARCHAR(100) NOT NULL,
    `pdf_path` VARCHAR(255) NOT NULL,
    `doc_type` VARCHAR(50) NOT NULL, -- 'previous_executive', 'puja_samiti', 'process'
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Seed default documents for previous committees if empty
$docCount = $pdo->query("SELECT COUNT(*) FROM `committee_documents`")->fetchColumn();
if ($docCount == 0) {
    $default_docs = [
        [
            'title' => 'Executive Committee List 2024-25',
            'year' => '2024-2025',
            'pdf_path' => 'mock_committee_2024.pdf',
            'doc_type' => 'previous_executive'
        ],
        [
            'title' => 'Executive Committee List 2022-23',
            'year' => '2022-2023',
            'pdf_path' => 'mock_committee_2022.pdf',
            'doc_type' => 'previous_executive'
        ],
        [
            'title' => 'Sarbojonin Durga Puja Samiti 2025',
            'year' => '2025',
            'pdf_path' => 'mock_puja_samiti_2025.pdf',
            'doc_type' => 'puja_samiti'
        ],
        [
            'title' => 'Membership Registration & Election Process Bylaws',
            'year' => '2024',
            'pdf_path' => 'mock_process_bylaws.pdf',
            'doc_type' => 'process'
        ]
    ];
    $insertDoc = $pdo->prepare("INSERT INTO `committee_documents` (`title`, `year`, `pdf_path`, `doc_type`) VALUES (?, ?, ?, ?)");
    foreach ($default_docs as $d) {
        $insertDoc->execute([$d['title'], $d['year'], $d['pdf_path'], $d['doc_type']]);
    }
}

// 13. Create member_documents table
$pdo->exec("CREATE TABLE IF NOT EXISTS `member_documents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `year` VARCHAR(100) NOT NULL,
    `pdf_path` VARCHAR(255) NOT NULL,
    `doc_type` VARCHAR(50) DEFAULT 'our_members', -- 'our_members' or 'member_profile'
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 14. Create partner_documents table
$pdo->exec("CREATE TABLE IF NOT EXISTS `partner_documents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `year` VARCHAR(100) NOT NULL,
    `pdf_path` VARCHAR(255) NOT NULL,
    `doc_type` VARCHAR(50) NOT NULL, -- 'sponsor', 'patron', 'authority'
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 15. Create association_documents table
$pdo->exec("CREATE TABLE IF NOT EXISTS `association_documents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `year` VARCHAR(100) NOT NULL,
    `pdf_path` VARCHAR(255) NOT NULL,
    `doc_type` VARCHAR(50) NOT NULL, -- 'souvenir', 'competition', 'recognition'
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 16. Create key_messages table
$pdo->exec("CREATE TABLE IF NOT EXISTS `key_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `year` VARCHAR(100) NOT NULL,
    `pdf_path` VARCHAR(255) NOT NULL,
    `doc_type` VARCHAR(50) NOT NULL, -- 'president_samiti', 'secretary_samiti', 'eminent', 'president_india'
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 7. Seed Admin User if not exists
$admin_user = isset($_ENV['ADMIN_USER']) ? $_ENV['ADMIN_USER'] : 'Admin';
$admin_pass = isset($_ENV['ADMIN_PASS']) ? $_ENV['ADMIN_PASS'] : 'Admin#0000';

$stmt = $pdo->prepare("SELECT * FROM `admin` WHERE `username` = ?");
$stmt->execute([$admin_user]);
if (!$stmt->fetch()) {
    $hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);
    $insert = $pdo->prepare("INSERT INTO `admin` (`username`, `password`) VALUES (?, ?)");
    $insert->execute([$admin_user, $hashed_pass]);
}

// 8. Seed Events Table if empty
$eventCount = $pdo->query("SELECT COUNT(*) FROM `events`")->fetchColumn();
if ($eventCount == 0) {
    $default_events = [
      
        [
            'title' => 'Kali Puja & Diwali Festival',
            'date' => '2026-11-08',
            'time' => '07:00 PM - Midnight',
            'location' => 'Sector 62 B-Block Ground',
            'category' => 'Festivals',
            'image' => 'https://images.unsplash.com/photo-1605647540924-852290f6b0d5?q=80&w=600',
            'description' => 'Traditional Kali Puja performed at midnight. The entire ground is lit up with thousands of clay lamps (diyas), accompanied by cultural events and snacks stalls.',
            'motif' => 'diya'
        ]
    ];

    $insertEv = $pdo->prepare("INSERT INTO `events` (`title`, `date`, `time`, `location`, `category`, `image`, `description`, `motif`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($default_events as $ev) {
        $insertEv->execute([
            $ev['title'],
            $ev['date'],
            $ev['time'],
            $ev['location'],
            $ev['category'],
            $ev['image'],
            $ev['description'],
            $ev['motif']
        ]);
    }
}

// 9. Seed Blogs Table if empty
$blogCount = $pdo->query("SELECT COUNT(*) FROM `blogs`")->fetchColumn();
if ($blogCount == 0) {
    $default_blogs = [
        [
            'title' => 'Preserving Bengali Culture in Modern Times',
            'excerpt' => 'How diaspora communities keep traditional language, literature, and art forms alive for the next generation.',
            'content' => 'Living away from Bengal, diaspora families face the unique challenge of keeping their children connected to their linguistic and cultural roots. The Bengali Cultural Association addresses this by running weekly language schools, hosting classical music workshops, and creating platforms for children to perform on stage. In this article, we explore the methods parents use—such as celebrating festivals at home, playing Rabindra Sangeet, and preparing traditional cuisines—to foster a sense of identity and pride in their heritage.',
            'image' => 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?q=80&w=1000',
            'category' => 'Heritage',
            'date' => '2026-07-15',
            'author' => 'Executive Committee'
        ],
        
    ];

    $insertBlog = $pdo->prepare("INSERT INTO `blogs` (`title`, `excerpt`, `content`, `image`, `category`, `date`, `author`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($default_blogs as $bg) {
        $insertBlog->execute([
            $bg['title'],
            $bg['excerpt'],
            $bg['content'],
            $bg['image'],
            $bg['category'],
            $bg['date'],
            $bg['author']
        ]);
    }
}

// 10. Seed Gallery Table if empty
$galleryCount = $pdo->query("SELECT COUNT(*) FROM `gallery`")->fetchColumn();
if ($galleryCount == 0) {
    $default_gallery = [
        // ['image' => 'https://images.unsplash.com/photo-1561376399-5ef8d0859942?q=80&w=600', 'title' => 'Sindur Khela on Dashami', 'category' => 'durga-puja'],
        // ['image' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?q=80&w=600', 'title' => 'Children performing Rabindra Nritya', 'category' => 'cultural'],
        // ['image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=600', 'title' => 'Bhog Distribution to Community', 'category' => 'festivals'],
        // ['image' => 'https://images.unsplash.com/photo-1601050690597-df056fb4ce78?q=80&w=600', 'title' => 'Dhunuchi Dance Competition', 'category' => 'durga-puja'],
        // ['image' => 'https://images.unsplash.com/photo-1590073844006-33379778ae09?q=80&w=600', 'title' => 'Alpona Floor Art Workshop', 'category' => 'community'],
        // ['image' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=600', 'title' => 'Bengali Drama Performance', 'category' => 'cultural'],
        // ['image' => 'https://images.unsplash.com/photo-1561376399-5ef8d0859942?q=80&w=600', 'title' => 'Anjali Offerings on Ashtami', 'category' => 'durga-puja'],
        // ['image' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?q=80&w=600', 'title' => 'Children writing alphabet (Hatey Khori)', 'category' => 'festivals'],
        // ['image' => 'https://images.unsplash.com/photo-1505576399279-565b52d4ac71?q=80&w=600', 'title' => 'Bengali New Year Prabhat Pheri', 'category' => 'cultural'],
        // ['image' => 'https://images.unsplash.com/photo-1526218626217-dc65a29bb444?q=80&w=600', 'title' => 'Outdoor games at Annual Picnic', 'category' => 'community'],
        ['image' => 'https://images.unsplash.com/photo-1620121692029-d088224ddc74?q=80&w=600', 'title' => 'Decorated Durga Idol Close Up', 'category' => 'durga-puja'],
        ['image' => 'https://images.unsplash.com/photo-1605152276897-4f618f831968?q=80&w=600', 'title' => 'Shyama Puja Aarati', 'category' => 'festivals']
    ];

    $insertGall = $pdo->prepare("INSERT INTO `gallery` (`title`, `image`, `category`) VALUES (?, ?, ?)");
    foreach ($default_gallery as $gl) {
        $insertGall->execute([$gl['title'], $gl['image'], $gl['category']]);
    }
}
?>
