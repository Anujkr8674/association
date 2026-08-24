<?php
// Identify the current page for active states and home-page transparent header styling
$current_page = basename($_SERVER['PHP_SELF']);
$is_home = ($current_page == 'index.php' || $current_page == '' || $current_page == 'durga-puja.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bengali Cultural Association</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="images/logo_new.png">
    <link rel="apple-touch-icon" href="images/logo_new.png">
    
    <!-- Google Fonts: Playfair Display (Serif) & Inter (Sans-serif) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* ==========================================================================
           GLOBAL VARIABLES & STYLES (Design System)
           ========================================================================== */
        :root {
            /* Colors */
            --primary-bg: #FFFBF0;      /* Warm Ivory Cream */
            --secondary-bg: #FBF4E6;    /* Soft Sand/Alabaster */
            --red: #8B1E1E;             /* Deep Bengali Red */
            --vermilion: #C83B2D;       /* Bright Puja Red */
            --gold: #C99A2E;            /* Marigold Gold */
            --dark: #211A17;            /* Soft Matte Black */
            --white: #FFFFFF;
            --text-muted: #6E5C55;      /* Secondary dark text */
            --border-color: rgba(33, 26, 23, 0.08);
            
            /* Fonts */
            --font-headings: 'Playfair Display', Georgia, serif;
            --font-body: 'Inter', system-ui, -apple-system, sans-serif;
            
            /* Transitions & Effects */
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 8px 30px rgba(33, 26, 23, 0.08);
            --shadow-lg: 0 16px 40px rgba(139, 30, 30, 0.12);
            --border-radius: 12px;
            --border-radius-lg: 20px;
        }

        /* Reset & Base Elements */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* scroll bar clr */
        html {
            scroll-behavior: smooth;
            font-size: 16px;
            scrollbar-width: thin;
            scrollbar-color: #a51c30 var(--primary-bg);
        }

        /* Custom scrollbar styling for Webkit browsers */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        ::-webkit-scrollbar-track {
            background: var(--primary-bg);
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #a51c30, #d9a441);
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #d9a441, #a51c30);
        }

        /* scroolbar clr end  */
        body {
            background-color: var(--primary-bg);
            color: var(--dark);
            font-family: var(--font-body);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-headings);
            font-weight: 700;
            color: var(--dark);
            line-height: 1.25;
        }

        p {
            margin-bottom: 1rem;
            color: var(--text-muted);
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: var(--transition);
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        ul {
            list-style: none;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Common Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            transition: var(--transition);
            border: 2px solid transparent;
            cursor: pointer;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-primary {
            background-color: var(--red);
            color: var(--white);
            box-shadow: 0 4px 15px rgba(139, 30, 30, 0.25);
        }

        .btn-primary:hover {
            background-color: var(--vermilion);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(200, 59, 45, 0.35);
            color: var(--white);
        }

        .btn-secondary {
            background-color: transparent;
            color: var(--red);
            border-color: var(--red);
        }

        .btn-secondary:hover {
            background-color: var(--red);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(139, 30, 30, 0.15);
        }

        .btn-gold {
            background-color: var(--gold);
            color: var(--white);
            box-shadow: 0 4px 15px rgba(201, 154, 46, 0.25);
        }

        .btn-gold:hover {
            background-color: #b58623;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(201, 154, 46, 0.35);
            color: var(--white);
        }

        .btn-white {
            background-color: var(--white);
            color: var(--red);
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .btn-white:hover {
            background-color: var(--secondary-bg);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        /* Subtle Bengali Alpona Borders and Accents */
        .alpona-divider {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 2.5rem 0;
            position: relative;
        }

        .alpona-divider::before,
        .alpona-divider::after {
            content: '';
            height: 1px;
            width: 100px;
            background: radial-gradient(circle, var(--gold) 0%, transparent 100%);
            margin: 0 1rem;
        }

        .alpona-divider svg {
            width: 38px;
            height: 38px;
            fill: var(--gold);
            animation: pulse-slow 3s infinite ease-in-out;
        }

        @keyframes pulse-slow {
            0%, 100% { transform: scale(1); opacity: 0.85; }
            50% { transform: scale(1.08); opacity: 1; }
        }

        /* Repeating background pattern */
        .pattern-bg {
            background-image: radial-gradient(var(--border-color) 0.5px, transparent 0.5px), radial-gradient(var(--border-color) 0.5px, #FFFBF0 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 4rem auto;
            position: relative;
            z-index: 2;
        }

        .section-header h2 {
            font-size: clamp(2rem, 4.5vw, 3rem);
            margin-bottom: 0.8rem;
            color: var(--red);
            position: relative;
            display: inline-block;
        }

        .section-header p {
            font-size: 1.1rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ==========================================================================
           HEADER / NAVIGATION STYLING
           ========================================================================== */
        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            transition: var(--transition-slow);
            padding: 1rem 0; /* Reduced vertical padding */
        }

        /* Scrolled state with glassmorphism */
        .site-header.scrolled {
            background-color: rgba(255, 255, 255, 0.9);
            /* background-color: var(--red); */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: var(--shadow-md);
            padding: 0.5rem 0; /* Reduced scrolled padding */
            border-bottom: 1px solid rgba(33, 26, 23, 0.04);
        }

        .header-solid {
            background-color: var(--white);
            box-shadow: var(--shadow-sm);
            padding: 0.6rem 0; /* Reduced solid padding */
            border-bottom: 1px solid rgba(33, 26, 23, 0.04);
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Logo Branding */
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 1010;
        }

        .logo-img {
            height: 85px;
            width: auto;
            object-fit: contain;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .logo:hover .logo-img {
            transform: scale(1.08) translateY(-2px);
            filter: drop-shadow(0 5px 12px rgba(139, 30, 30, 0.2));
        }

        .site-header.header-transparent:not(.scrolled) .logo-symbol {
            fill: var(--gold);
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-title {
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--red);
            font-family: var(--font-headings);
            transition: var(--transition);
        }

        .logo-subtitle {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            color: var(--gold);
            transition: var(--transition);
        }

        .site-header.header-transparent:not(.scrolled) .logo-title {
            color: var(--white);
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .site-header.header-transparent:not(.scrolled) .logo-subtitle {
            color: var(--gold);
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        /* Desktop Navigation Menu */
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 1.3rem; /* Reduced gap between desktop nav links */
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.5rem 0.25rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.35rem;
            position: relative;
        }

        .site-header.header-transparent:not(.scrolled) .nav-link {
            color: var(--white);
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--red);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
        }

        .site-header.header-transparent:not(.scrolled) .nav-link::after {
            background-color: var(--gold);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--red);
        }
        
        .site-header.header-transparent:not(.scrolled) .nav-link:hover,
        .site-header.header-transparent:not(.scrolled) .nav-link.active {
            color: var(--gold);
        }

        .nav-link i {
            font-size: 0.7rem;
            transition: var(--transition);
        }

        .nav-item:hover .nav-link i {
            transform: rotate(180deg);
        }

        /* Dropdown Menus */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(15px);
            background-color: var(--white);
            box-shadow: var(--shadow-lg);
            border-radius: var(--border-radius);
            min-width: 230px;
            padding: 0.8rem 0;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            border-top: 3px solid var(--red);
            z-index: 99;
        }

        .dropdown-menu::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 8px;
            border-style: solid;
            border-color: transparent transparent var(--red) transparent;
        }

        .nav-item:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(5px);
        }

        .dropdown-link {
            display: block;
            padding: 0.7rem 1.6rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--dark);
            transition: var(--transition);
        }

        .dropdown-link:hover {
            background-color: var(--secondary-bg);
            color: var(--red);
            padding-left: 1.85rem;
        }

        .dropdown-link.active {
            color: var(--red);
            font-weight: 600;
            background-color: var(--secondary-bg);
        }

        /* Header Actions (Join Us button) */
        .header-cta {
            margin-left: 1rem;
        }

        .header-cta .btn {
            padding: 0.55rem 1.25rem;
            font-size: 0.9rem;
            white-space: nowrap; /* Prevent "Join Us" wrapping */
        }

        /* Hamburger Toggle Button */
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            z-index: 1010;
        }

        .hamburger {
            display: block;
            width: 24px;
            height: 2px;
            background-color: var(--dark);
            position: relative;
            transition: var(--transition);
        }

        .site-header.header-transparent:not(.scrolled) .hamburger {
            background-color: var(--white);
        }

        .hamburger::before,
        .hamburger::after {
            content: '';
            position: absolute;
            width: 24px;
            height: 2px;
            background-color: inherit;
            left: 0;
            transition: var(--transition);
        }

        .hamburger::before { top: -6px; }
        .hamburger::after { bottom: -6px; }

        /* Hamburger Open State */
        .nav-toggle.open .hamburger {
            background-color: transparent;
        }

        .nav-toggle.open .hamburger::before {
            transform: rotate(45deg);
            top: 0;
            background-color: var(--dark);
        }

        .nav-toggle.open .hamburger::after {
            transform: rotate(-45deg);
            bottom: 0;
            background-color: var(--dark);
        }

        /* ==========================================================================
           MOBILE NAVIGATION OVERLAY
           ========================================================================== */
        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: rgba(33, 26, 23, 0.45);
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            backdrop-filter: blur(5px);
        }

        .mobile-menu-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .mobile-nav {
            position: fixed;
            top: 0;
            right: -320px;
            width: 320px;
            height: 100vh;
            background-color: var(--white);
            z-index: 1002;
            box-shadow: -5px 0 30px rgba(0,0,0,0.15);
            padding: 1.5rem 2.0rem 2rem 2.0rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            overflow-y: auto;
            transition: cubic-bezier(0.4, 0, 0.2, 1) 0.4s;
        }

        .mobile-nav.open {
            right: 0;
        }

        /* Mobile Drawer Header logo & close */
        .mobile-drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 0.5rem;
            flex-shrink: 0;
        }

        .mobile-drawer-logo .mobile-logo-img {
            height: 48px;
            width: auto;
            object-fit: contain;
        }

        .mobile-drawer-close {
            background: none;
            border: none;
            color: var(--dark);
            font-size: 1.5rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
            outline: none;
        }

        .mobile-drawer-close:hover {
            background-color: var(--secondary-bg);
            color: var(--red);
        }

        .mobile-menu {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .mobile-item {
            border-bottom: 1px solid var(--secondary-bg);
            padding-bottom: 0.75rem;
        }

        .mobile-link {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-link:hover,
        .mobile-link.active {
            color: var(--red);
        }

        .mobile-dropdown-toggle i {
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .mobile-dropdown-toggle.active i {
            transform: rotate(180deg);
        }

        .mobile-dropdown-menu {
            display: none;
            flex-direction: column;
            gap: 0.75rem;
            padding: 0.75rem 1rem 0 1rem;
        }

        .mobile-dropdown-link {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .mobile-dropdown-link:hover,
        .mobile-dropdown-link.active {
            color: var(--red);
        }

        .mobile-cta {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* ==========================================================================
           RESPONSIVE BREAKPOINTS (Header & Core grid limits)
           ========================================================================== */
        @media (max-width: 1024px) {
            .nav-menu {
                gap: 1.2rem;
            }
            .nav-link {
                font-size: 0.88rem;
            }
            .header-cta .btn {
                padding: 0.5rem 1.2rem;
                font-size: 0.85rem;
            }
            .logo-title {
                font-size: 1.15rem;
            }
        }

        @media (max-width: 991px) {
            .site-header {
                padding: 1.2rem 0;
            }
            .nav-menu,
            .header-cta {
                display: none;
            }
            .nav-toggle {
                display: block;
            }
        }

        @media (max-width: 480px) {
            .logo-subtitle {
                display: none;
            }
            .logo-title {
                font-size: 1.1rem;
            }
            .mobile-nav {
                width: 100%;
                right: -100%;
            }
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <header class="site-header <?php echo $is_home ? 'header-transparent' : 'header-solid'; ?>">
        <div class="container header-container">
            <a href="index.php" class="logo">
                <img src="images/logo_new.png" class="logo-img" alt="BCA Logo">
                <!-- <div class="logo-text">
                    <span class="logo-title">BENGALI CULTURAL</span>
                    <span class="logo-subtitle">Association</span>
                </div> -->
            </a>

            <!-- Desktop Nav Menu -->
            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                </div>
                
                 <div class="nav-item">
                    <a href="durga-puja.php" class="nav-link <?php echo ($current_page == 'durga-puja.php') ? 'active' : ''; ?>">Durga Puja</a>
                </div>

                <div class="nav-item">
                    <a href="about.php" class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About <i class="fa-solid fa-chevron-down"></i></a>
                    <div class="dropdown-menu">
                        <a href="about.php#association" class="dropdown-link">About Association</a>
                        <a href="about.php#vision-mission" class="dropdown-link">Vision & Mission</a>
                    </div>
                </div>

                <div class="nav-item">
                    <a href="committee.php" class="nav-link <?php echo ($current_page == 'committee.php') ? 'active' : ''; ?>">Committee <i class="fa-solid fa-chevron-down"></i></a>
                    <div class="dropdown-menu">
                        <a href="committee.php#current" class="dropdown-link">Current Committee</a>
                        <a href="committee.php#previous" class="dropdown-link">Previous Committee</a>
                    </div>
                </div>

                <div class="nav-item">
                    <a href="members.php" class="nav-link <?php echo ($current_page == 'members.php') ? 'active' : ''; ?>">Member <i class="fa-solid fa-chevron-down"></i></a>
                    <div class="dropdown-menu">
                       
                        <a href="members.php" class="dropdown-link">Current Members</a>
                         <a href="enrollment.php" class="dropdown-link">Enrollment</a>
                         <!-- <a href="feedback.php" class="dropdown-link">Feedback</a> -->
                         <!-- <a href="registration_procedure.php" class="dropdown-link">Registration & Authorization</a> -->
                    </div>
                </div>

                <!-- <div class="nav-item">
                    <a href="index.php#events-calendar" class="nav-link">Events</a>
                </div> -->

                <div class="nav-item">
                    <a href="notice.php" class="nav-link <?php echo ($current_page == 'notice.php') ? 'active' : ''; ?>">Notice</a>
                </div>

                <div class="nav-item">
                    <a href="#" class="nav-link <?php echo in_array($current_page, ['partners.php', 'gallery.php', 'blogs.php', 'documents.php', 'keymessages.php']) ? 'active' : ''; ?>">More <i class="fa-solid fa-chevron-down"></i></a>
                    <div class="dropdown-menu">
                        <a href="partners.php" class="dropdown-link">Partners</a>
                        <a href="activities.php" class="dropdown-link">Activities</a>
                        <a href="gallery.php" class="dropdown-link">Gallery</a>
                        <a href="blogs.php" class="dropdown-link">Blogs</a>
                        <a href="documents.php" class="dropdown-link">Documents</a>
                        <a href="keymessages.php" class="dropdown-link">Key Messages</a>
                    </div>
                </div>

                <div class="nav-item">
                    <a href="contact.php" class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a>
                </div>

                <!-- CTA Button -->
                <div class="header-cta">
                    <a href="join-us.php" class="btn btn-primary">Join Us</a>
                </div>
            </nav>

            <!-- Mobile Hamburger Toggle -->
            <button class="nav-toggle" id="mobile-toggle" aria-label="Toggle Navigation">
                <span class="hamburger"></span>
            </button>
        </div>
    </header>

    <!-- Mobile Drawer Overlay -->
    <div class="mobile-menu-overlay" id="mobile-overlay"></div>

    <!-- Mobile Navigation Drawer -->
    <nav class="mobile-nav" id="mobile-drawer">
        <!-- Mobile Drawer Header -->
        <div class="mobile-drawer-header">
            <div class="mobile-drawer-logo">
                <img src="images/logo_new.png" alt="BCA Logo" class="mobile-logo-img">
            </div>
            <button class="mobile-drawer-close" id="mobile-close" aria-label="Close Menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <ul class="mobile-menu">
            <li class="mobile-item">
                <a href="index.php" class="mobile-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
            </li>
            
            <li class="mobile-item">
                <a href="durga-puja.php" class="mobile-link <?php echo ($current_page == 'durga-puja.php') ? 'active' : ''; ?>">Durga Puja</a>
            </li>
            
            <li class="mobile-item">
                <div class="mobile-link mobile-dropdown-toggle">
                    <span>About</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <ul class="mobile-dropdown-menu">
                    <li><a href="about.php#association" class="mobile-dropdown-link">About Association</a></li>
                    <li><a href="about.php#vision-mission" class="mobile-dropdown-link">Vision & Mission</a></li>
                </ul>
            </li>

            <li class="mobile-item">
                <div class="mobile-link mobile-dropdown-toggle">
                    <span>Committee</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <ul class="mobile-dropdown-menu">
                    <li><a href="committee.php#current" class="mobile-dropdown-link">Current Committee</a></li>
                    <li><a href="committee.php#previous" class="mobile-dropdown-link">Previous Committee</a></li>
                </ul>
            </li>

            <li class="mobile-item">
                <div class="mobile-link mobile-dropdown-toggle">
                    <span>Member</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <ul class="mobile-dropdown-menu">
                    <li><a href="join-us.php" class="mobile-dropdown-link">Join Us</a></li>
                    <li><a href="members.php" class="mobile-dropdown-link">Current Members</a></li>
                </ul>
            </li>

            <li class="mobile-item">
                <a href="index.php#events-calendar" class="mobile-link">Events</a>
            </li>

            <li class="mobile-item">
                <a href="notice.php" class="mobile-link <?php echo ($current_page == 'notice.php') ? 'active' : ''; ?>">Notice</a>
            </li>

            <li class="mobile-item">
                <div class="mobile-link mobile-dropdown-toggle">
                    <span>More</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <ul class="mobile-dropdown-menu">
                    <li><a href="partners.php" class="mobile-dropdown-link">Partners</a></li>
                    <li><a href="gallery.php" class="mobile-dropdown-link">Gallery</a></li>
                    <li><a href="blogs.php" class="mobile-dropdown-link">Blogs</a></li>
                    <li><a href="documents.php" class="mobile-dropdown-link">Documents</a></li>
                    <li><a href="keymessages.php" class="mobile-dropdown-link">Key Messages</a></li>
                </ul>
            </li>

            <li class="mobile-item">
                <a href="contact.php" class="mobile-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a>
            </li>
        </ul>

        <div class="mobile-cta">
            <a href="join-us.php" class="btn btn-primary">Join Us</a>
        </div>
    </nav>

    <script>
        // ==========================================================================
        // HEADER SCROLL TRIGGER (TRANSITION TO GLASS SOLID BACKGROUND)
        // ==========================================================================
        document.addEventListener('DOMContentLoaded', function () {
            const header = document.querySelector('.site-header');
            
            function handleScroll() {
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }
            
            // Check scroll position on page load and on scroll event
            window.addEventListener('scroll', handleScroll);
            handleScroll();
            
            // ==========================================================================
            // MOBILE NAVIGATION MENU DRAWER CONTROLS
            // ==========================================================================
            const mobileToggle = document.getElementById('mobile-toggle');
            const mobileOverlay = document.getElementById('mobile-overlay');
            const mobileDrawer = document.getElementById('mobile-drawer');
            const mobileClose = document.getElementById('mobile-close');
            
            function toggleMenu() {
                mobileToggle.classList.toggle('open');
                mobileOverlay.classList.toggle('open');
                mobileDrawer.classList.toggle('open');
                
                // Toggle body scroll locking
                if (mobileDrawer.classList.contains('open')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
            
            mobileToggle.addEventListener('click', toggleMenu);
            mobileOverlay.addEventListener('click', toggleMenu);
            if (mobileClose) {
                mobileClose.addEventListener('click', toggleMenu);
            }
            
            // Close mobile menu when a direct link is clicked
            const mobileLinks = document.querySelectorAll('.mobile-link:not(.mobile-dropdown-toggle), .mobile-dropdown-link');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (mobileDrawer.classList.contains('open')) {
                        toggleMenu();
                    }
                });
            });

            // Mobile menu dropdowns toggle (accordion action)
            const mobileDropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');
            mobileDropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdownMenu = this.nextElementSibling;
                    
                    // Close other open mobile dropdowns
                    mobileDropdownToggles.forEach(otherToggle => {
                        if (otherToggle !== this) {
                            otherToggle.classList.remove('active');
                            otherToggle.nextElementSibling.style.display = 'none';
                        }
                    });
                    
                    // Toggle this dropdown
                    this.classList.toggle('active');
                    if (dropdownMenu.style.display === 'flex' || dropdownMenu.style.display === 'block') {
                        dropdownMenu.style.display = 'none';
                    } else {
                        dropdownMenu.style.display = 'flex';
                    }
                });
            });
        });
    </script>
