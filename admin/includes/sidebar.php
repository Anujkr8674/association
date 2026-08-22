<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
$page_title = isset($page_title) ? $page_title : 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Bengali Cultural Association</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --red: #D43F3A;
            --gold: #E5A93B;
            --dark: #211A17;
            --white: #FFFFFF;
            --cream: #FFFBF0;
            --sand: #FBF4E6;
            --gray: #7A726E;
            --border: rgba(33, 26, 23, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--cream);
            color: var(--dark);
            min-height: 100vh;
        }

        /* Layout Structure */
        .dash-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar navigation */
        .dash-sidebar {
            background-color: var(--dark);
            color: var(--white);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            border-right: 1px solid rgba(255,255,255,0.05);
            width: 240px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .brand-logo {
            font-size: 1.8rem;
            color: var(--gold);
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gold);
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .menu-link:hover,
        .menu-link.active {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--gold);
        }

        .menu-link.active i {
            color: var(--red);
        }

        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
        }

        .btn-logout {
            width: 100%;
            background-color: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.7);
            padding: 0.7rem;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-logout:hover {
            border-color: var(--red);
            background-color: var(--red);
            color: var(--white);
        }

        /* Main Workspace Content */
        .dash-main {
            margin-left: 240px;
            width: calc(100% - 240px);
            padding: 2.5rem 3rem;
            min-height: 100vh;
        }

        .dash-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .header-title-box h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--dark);
        }

        .header-title-box p {
            font-size: 0.9rem;
            color: var(--gray);
            margin-top: 0.2rem;
        }

        .user-pill {
            background-color: var(--sand);
            border: 1px solid var(--border);
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-pill i {
            color: var(--red);
        }

        /* Statistics Row widgets */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.8rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background-color: var(--white);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(33, 26, 23, 0.03);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: inherit;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(33, 26, 23, 0.08);
            border-color: var(--gold);
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .stat-num {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
        }

        .stat-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-events { background-color: #FFF2F2; color: var(--red); }
        .stat-blogs { background-color: #FFFBF0; color: var(--gold); }
        .stat-gallery { background-color: #F0F9FF; color: #0284C7; }

        /* Panel Content Area styling */
        .panel-card {
            background-color: var(--white);
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(33, 26, 23, 0.03);
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .panel-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--dark);
            text-transform: capitalize;
        }

        .btn-add {
            background-color: var(--red);
            color: var(--white);
            border: none;
            border-radius: 6px;
            padding: 0.55rem 1.2rem;
            font-size: 0.88rem;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            text-decoration: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: var(--transition);
        }

        .btn-add:hover {
            background-color: #B9302B;
            transform: translateY(-1px);
        }

        /* Success alerts */
        .alert-success {
            background-color: #DEF7EC;
            border: 1px solid #BCF0DA;
            color: #03543F;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.95rem;
            margin-bottom: 2rem;
            border-radius: 8px;
        }

        /* Table Styling */
        .table-responsive {
            overflow-x: auto;
        }

        .dash-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .dash-table th {
            background-color: var(--sand);
            padding: 1rem 2rem;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
        }

        .dash-table td {
            padding: 1.2rem 2rem;
            font-size: 0.92rem;
            color: var(--dark);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .dash-table tr:last-child td {
            border-bottom: none;
        }

        .thumbnail-img {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--border);
        }

        .category-badge {
            background-color: var(--sand);
            border: 1px solid var(--border);
            color: var(--gray);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .src-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            text-transform: uppercase;
            display: inline-block;
        }
        .src-association { background-color: #FFF2F2; border: 1px solid #FCDEDE; color: var(--red); }
        .src-vedika { background-color: #FFFBF0; border: 1px solid #FDF3D7; color: var(--gold); }
        .src-calendarific { background-color: #ECFDF5; border: 1px solid #D1FAE5; color: #059669; }
        .src-festivo { background-color: #EFF6FF; border: 1px solid #DBEAFE; color: #2563EB; }
        .src-google_calendar { background-color: #F5F3FF; border: 1px solid #EDE9FE; color: #7C3AED; }
        .src-manual { background-color: #F8FAFC; border: 1px solid #E2E8F0; color: #475569; }

        /* Actions styling */
        .action-cell {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background-color: var(--white);
            color: var(--gray);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-edit:hover {
            color: var(--gold);
            border-color: var(--gold);
            background-color: #FFFDF9;
        }

        .btn-delete:hover {
            color: var(--red);
            border-color: var(--red);
            background-color: #FFF5F5;
        }

        .btn-pagination {
            transition: var(--transition);
        }
        .btn-pagination:hover {
            border-color: var(--red) !important;
            color: var(--red) !important;
            background-color: var(--sand) !important;
        }

        .no-data-row {
            text-align: center;
            padding: 3rem 2rem !important;
            color: var(--gray);
            font-style: italic;
        }

        /* Form styling */
        .form-card {
            background-color: var(--white);
            width: 100%;
            max-width: 820px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(33, 26, 23, 0.04);
            border: 1px solid var(--border);
            overflow: hidden;
            margin: 0 auto;
        }

        .form-header {
            background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
            color: var(--white);
            padding: 2.2rem 2.5rem;
            position: relative;
        }

        .form-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: var(--gold);
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--white);
        }

        .form-subtitle {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
            margin-top: 0.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-body {
            padding: 2.5rem;
        }

        .section-divider-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: var(--red);
            font-weight: 700;
            border-bottom: 2px solid var(--sand);
            padding-bottom: 0.4rem;
            margin: 2.2rem 0 1.5rem 0;
            grid-column: span 2;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            font-family: 'Outfit', sans-serif;
            background-color: var(--sand);
            border: 1px solid transparent;
            border-radius: 8px;
            color: var(--dark);
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            background-color: var(--white);
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(229, 169, 59, 0.15);
        }

        .form-checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--gray);
            cursor: pointer;
        }

        .checkbox-input {
            width: 18px;
            height: 18px;
            accent-color: var(--red);
            cursor: pointer;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Schedules row container styles */
        .schedule-row {
            display: grid;
            grid-template-columns: 1.2fr 1fr 2fr 2fr auto;
            gap: 0.8rem;
            align-items: center;
            margin-bottom: 0.8rem;
            background: var(--sand);
            padding: 0.8rem;
            border-radius: 6px;
        }

        .btn-row {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .btn {
            padding: 0.75rem 1.6rem;
            font-size: 0.95rem;
            font-weight: 700;
            border-radius: 8px;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-submit {
            background-color: var(--red);
            color: var(--white);
            border: none;
        }

        .btn-submit:hover {
            background-color: #B9302B;
            box-shadow: 0 4px 12px rgba(212, 63, 58, 0.2);
            transform: translateY(-1px);
        }

        .btn-cancel {
            background-color: var(--white);
            color: var(--gray);
            border: 1px solid var(--border);
        }

        .btn-cancel:hover {
            background-color: var(--sand);
            color: var(--dark);
        }

        /* Overview Page Custom Styles */
        .overview-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 1100px) {
            .overview-grid {
                grid-template-columns: 1fr;
            }
        }

        .recent-card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(33, 26, 23, 0.03);
            border: 1px solid var(--border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .recent-header {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border);
            background-color: var(--sand);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .recent-title {
            font-weight: 700;
            font-size: 1rem;
            color: var(--dark);
        }

        .recent-body {
            padding: 0;
            flex-grow: 1;
        }

        .recent-list {
            list-style: none;
        }

        .recent-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .recent-item:last-child {
            border-bottom: none;
        }

        .recent-item-info {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .recent-item-title {
            font-weight: 600;
            font-size: 0.92rem;
            color: var(--dark);
        }

        .recent-item-meta {
            font-size: 0.8rem;
            color: var(--gray);
        }

        .link-view-all {
            color: var(--red);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            transition: var(--transition);
        }

        .link-view-all:hover {
            color: #B9302B;
            text-decoration: underline;
        }

        /* Sidebar Dropdown Menu Styles */
        .sidebar-menu-dropdown {
            display: none;
            list-style: none;
            padding-left: 1rem;
            margin-top: 0.25rem;
            flex-direction: column;
            gap: 0.25rem;
        }

        .sidebar-menu-dropdown.show {
            display: flex;
        }

        .menu-dropdown-toggle {
            justify-content: space-between !important;
            cursor: pointer;
        }

        .menu-dropdown-toggle i.fa-chevron-down {
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .menu-dropdown-toggle.open i.fa-chevron-down {
            transform: rotate(180deg);
        }

        /* Mobile Top Bar Navigation Styles */
        .mobile-top-bar {
            display: none;
        }
        .mobile-sidebar-overlay {
            display: none;
        }

        /* Mobile Screen Responsiveness */
        @media (max-width: 768px) {
            .dash-container {
                flex-direction: column;
            }
            
            .dash-sidebar {
                position: fixed;
                top: 0;
                left: -240px;
                width: 240px;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
                border-right: 1px solid rgba(255,255,255,0.05);
                box-shadow: 5px 0 25px rgba(0,0,0,0.15);
            }
            
            .dash-sidebar.open {
                left: 0;
            }
            
            .sidebar-brand {
                border-bottom: 1px solid rgba(255,255,255,0.1);
                padding-bottom: 1.5rem;
                width: 100%;
            }
            
            .sidebar-menu {
                width: 100%;
            }
            
            .sidebar-footer {
                width: 100%;
            }
            
            .dash-main {
                margin-left: 0;
                width: 100%;
                padding: 1.5rem 0.5rem;
                padding-top: 5.5rem; /* Allow space for mobile-top-bar */
            }

            .mobile-top-bar {
                display: flex;
                align-items: center;
                gap: 1rem;
                background-color: var(--dark);
                color: var(--white);
                padding: 0 1.5rem;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 60px;
                z-index: 999;
                border-bottom: 1px solid rgba(255,255,255,0.05);
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }

            .mobile-toggle-btn {
                background: none;
                border: none;
                color: var(--white);
                font-size: 1.4rem;
                cursor: pointer;
                padding: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: var(--transition);
            }
            
            .mobile-toggle-btn:hover {
                color: var(--gold);
            }

            .mobile-brand-title {
                font-family: 'Playfair Display', serif;
                font-weight: 700;
                font-size: 1.1rem;
                color: var(--gold);
                letter-spacing: 0.5px;
            }

            .mobile-sidebar-overlay {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(33, 26, 23, 0.4);
                backdrop-filter: blur(3px);
                z-index: 998;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .mobile-sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }
            
            .dash-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                margin-bottom: 1.5rem;
            }

            .stats-row {
                grid-template-columns: 1fr;
                gap: 1rem;
                margin-bottom: 2rem;
            }
            
            .panel-header {
                padding: 1.2rem 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .btn-add {
                width: 100%;
                justify-content: center;
            }
            
            .dash-table {
                min-width: 800px;
            }
            
            .dash-table th, 
            .dash-table td {
                padding: 0.8rem 1rem;
            }
            
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                width: 100%;
                margin-top: 0.5rem;
            }

            .form-card {
                border-radius: 12px;
            }

            .form-header {
                padding: 1.5rem 1.2rem;
            }

            .form-title {
                font-size: 1.4rem;
            }

            .form-body {
                padding: 1.2rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            #time-fields-group {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }

            .schedule-row {
                grid-template-columns: 1fr !important;
                gap: 0.6rem !important;
                padding: 1rem !important;
                border: 1px solid var(--border);
            }

            .schedule-row div:last-child {
                text-align: right;
                margin-top: 0.2rem;
            }

            .btn-row {
                flex-direction: column-reverse;
                gap: 0.8rem;
            }

            .btn-row .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Mobile Top Bar Header -->
    <div class="mobile-top-bar">
        <button type="button" class="mobile-toggle-btn" id="mobile-sidebar-toggle"><i class="fa-solid fa-bars"></i></button>
        <span class="mobile-brand-title">BCA Noida Admin</span>
    </div>

    <!-- Mobile Sidebar Overlay background -->
    <div class="mobile-sidebar-overlay" id="sidebar-overlay"></div>

    <div class="dash-container">
        <!-- Sidebar Navigation -->
        <aside class="dash-sidebar" id="admin-sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo"><i class="fa-solid fa-dharmachakra"></i></div>
                <div>
                    <h2 class="brand-title">BCA Noida</h2>
                    <span class="brand-subtitle">Admin Dashboard</span>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="dashboard.php" class="menu-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Overview</span>
                    </a>
                </li>
                <li>
                    <a href="events.php" class="menu-link <?php echo ($current_page === 'events.php' || $current_page === 'event_edit.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Manage Events</span>
                    </a>
                </li>
                <li>
                    <a href="blogs.php" class="menu-link <?php echo ($current_page === 'blogs.php' || $current_page === 'blog_edit.php' || $current_page === 'blog_categories.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-blog"></i>
                        <span>Manage Blogs</span>
                    </a>
                </li>
                <li style="margin-top: -0.25rem; margin-bottom: 0.25rem;">
                    <a href="blog_categories.php" class="menu-link <?php echo $current_page === 'blog_categories.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.8;">
                        <i class="fa-solid fa-tags" style="font-size: 0.75rem;"></i>
                        <span>Blog Categories</span>
                    </a>
                </li>
                <li>
                    <a href="gallery.php" class="menu-link <?php echo ($current_page === 'gallery.php' || $current_page === 'gallery_edit.php' || $current_page === 'gallery_categories.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-images"></i>
                        <span>Manage Gallery</span>
                    </a>
                </li>
                <li style="margin-top: -0.25rem; margin-bottom: 0.25rem;">
                    <a href="gallery_categories.php" class="menu-link <?php echo $current_page === 'gallery_categories.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.8;">
                        <i class="fa-solid fa-tags" style="font-size: 0.75rem;"></i>
                        <span>Gallery Categories</span>
                    </a>
                </li>
                <li>
                    <a href="recent_activities.php" class="menu-link <?php echo ($current_page === 'recent_activities.php' || $current_page === 'recent_activity_edit.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Recent Activities</span>
                    </a>
                </li>
                <li>
                    <a href="videos.php" class="menu-link <?php echo ($current_page === 'videos.php' || $current_page === 'video_edit.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-video"></i>
                        <span>Manage Videos</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" class="menu-link menu-dropdown-toggle <?php echo (strpos($current_page, 'committee') !== false) ? 'active open' : ''; ?>" id="committee-dropdown-toggle">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <i class="fa-solid fa-people-group"></i>
                            <span>Committee</span>
                        </div>
                        <i class="fa-solid fa-chevron-down"></i>
                    </a>
                    <ul class="sidebar-menu-dropdown <?php echo (strpos($current_page, 'committee') !== false) ? 'show' : ''; ?>" id="committee-dropdown-menu">
                        <li>
                            <a href="committee_current.php" class="menu-link <?php echo $current_page === 'committee_current.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-user-tie" style="font-size: 0.75rem;"></i>
                                <span>Current Board</span>
                            </a>
                        </li>
                        <li>
                            <a href="committee_previous.php" class="menu-link <?php echo $current_page === 'committee_previous.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-history" style="font-size: 0.75rem;"></i>
                                <span>Previous Executive</span>
                            </a>
                        </li>
                        <li>
                            <a href="committee_puja_samiti.php" class="menu-link <?php echo $current_page === 'committee_puja_samiti.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-place-of-worship" style="font-size: 0.75rem;"></i>
                                <span>Puja Samiti</span>
                            </a>
                        </li>
                        <li>
                            <a href="committee_processes.php" class="menu-link <?php echo $current_page === 'committee_processes.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-contract" style="font-size: 0.75rem;"></i>
                                <span>Processes</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:void(0)" class="menu-link menu-dropdown-toggle <?php echo (strpos($current_page, 'members_') !== false || $current_page === 'membership_requests.php') ? 'active open' : ''; ?>" id="members-dropdown-toggle">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <i class="fa-solid fa-users"></i>
                            <span>Members</span>
                        </div>
                        <i class="fa-solid fa-chevron-down"></i>
                    </a>
                    <ul class="sidebar-menu-dropdown <?php echo (strpos($current_page, 'members_') !== false || $current_page === 'membership_requests.php') ? 'show' : ''; ?>" id="members-dropdown-menu">
                        <li>
                            <a href="members_our.php" class="menu-link <?php echo $current_page === 'members_our.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 0.75rem;"></i>
                                <span>Our Members</span>
                            </a>
                        </li>
                        <li>
                            <a href="members_profile.php" class="menu-link <?php echo $current_page === 'members_profile.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-invoice" style="font-size: 0.75rem;"></i>
                                <span>Member Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="membership_requests.php" class="menu-link <?php echo $current_page === 'membership_requests.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-clipboard-list" style="font-size: 0.75rem;"></i>
                                <span>Join Requests</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:void(0)" class="menu-link menu-dropdown-toggle <?php echo (strpos($current_page, 'partners_') !== false) ? 'active open' : ''; ?>" id="partners-dropdown-toggle">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <i class="fa-solid fa-handshake"></i>
                            <span>Partners</span>
                        </div>
                        <i class="fa-solid fa-chevron-down"></i>
                    </a>
                    <ul class="sidebar-menu-dropdown <?php echo (strpos($current_page, 'partners_') !== false) ? 'show' : ''; ?>" id="partners-dropdown-menu">
                        <li>
                            <a href="partners_sponsors.php" class="menu-link <?php echo $current_page === 'partners_sponsors.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 0.75rem;"></i>
                                <span>Sponsors</span>
                            </a>
                        </li>
                        <li>
                            <a href="partners_patrons.php" class="menu-link <?php echo $current_page === 'partners_patrons.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 0.75rem;"></i>
                                <span>Patrons</span>
                            </a>
                        </li>
                        <li>
                            <a href="partners_authorities.php" class="menu-link <?php echo $current_page === 'partners_authorities.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 0.75rem;"></i>
                                <span>Authorities</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:void(0)" class="menu-link menu-dropdown-toggle <?php echo (strpos($current_page, 'documents_') !== false) ? 'active open' : ''; ?>" id="documents-dropdown-toggle">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <i class="fa-solid fa-folder-open"></i>
                            <span>Documents</span>
                        </div>
                        <i class="fa-solid fa-chevron-down"></i>
                    </a>
                    <ul class="sidebar-menu-dropdown <?php echo (strpos($current_page, 'documents_') !== false) ? 'show' : ''; ?>" id="documents-dropdown-menu">
                        <li>
                            <a href="documents_souvenir.php" class="menu-link <?php echo $current_page === 'documents_souvenir.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 0.75rem;"></i>
                                <span>Souvenir</span>
                            </a>
                        </li>
                        <li>
                            <a href="documents_competitions.php" class="menu-link <?php echo $current_page === 'documents_competitions.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 0.75rem;"></i>
                                <span>Competitions & Winners</span>
                            </a>
                        </li>
                        <li>
                            <a href="documents_recognition.php" class="menu-link <?php echo $current_page === 'documents_recognition.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 0.75rem;"></i>
                                <span>Recognition</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:void(0)" class="menu-link menu-dropdown-toggle <?php echo (strpos($current_page, 'messages_') !== false) ? 'active open' : ''; ?>" id="messages-dropdown-toggle">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <i class="fa-solid fa-message"></i>
                            <span>Key Messages</span>
                        </div>
                        <i class="fa-solid fa-chevron-down"></i>
                    </a>
                    <ul class="sidebar-menu-dropdown <?php echo (strpos($current_page, 'messages_') !== false) ? 'show' : ''; ?>" id="messages-dropdown-menu">
                        <li>
                            <a href="messages_president_samiti.php" class="menu-link <?php echo $current_page === 'messages_president_samiti.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 0.75rem;"></i>
                                <span>President (Samiti)</span>
                            </a>
                        </li>
                        <li>
                            <a href="messages_secretary_samiti.php" class="menu-link <?php echo $current_page === 'messages_secretary_samiti.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 0.75rem;"></i>
                                <span>Secretary (Samiti)</span>
                            </a>
                        </li>
                        <li>
                            <a href="messages_eminent.php" class="menu-link <?php echo $current_page === 'messages_eminent.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 0.75rem;"></i>
                                <span>Eminent Personalities</span>
                            </a>
                        </li>
                        <li>
                            <a href="messages_president_india.php" class="menu-link <?php echo $current_page === 'messages_president_india.php' ? 'active' : ''; ?>" style="padding-left: 2.2rem; font-size: 0.85rem; opacity: 0.9;">
                                <i class="fa-solid fa-file-pdf" style="font-size: 0.75rem;"></i>
                                <span>President of India</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="sidebar-footer">
                <form action="action.php?act=logout" method="POST">
                    <button type="submit" class="btn-logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="dash-main">
            <!-- Header section -->
            <header class="dash-header">
                <div class="header-title-box">
                    <h1><?php echo htmlspecialchars($page_title); ?></h1>
                    <p>Welcome back, manage your site content dynamically below.</p>
                </div>
                <div class="user-pill">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Hello, <?php echo htmlspecialchars($_SESSION['admin_user']); ?></span>
                </div>
            </header>
