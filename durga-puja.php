<?php
// Include the shared header and db configuration
include 'includes/header.php';
require_once 'config.php';

// Fetch dynamic recent activities from database
$recent_activities = [];
try {
    if (isset($pdo)) {
        $stmt_act = $pdo->query("SELECT * FROM `recent_activities` ORDER BY `created_at` DESC LIMIT 6");
        $recent_activities = $stmt_act->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Fail silently
}
if (empty($recent_activities)) {
    $recent_activities = [
        ['id' => 1, 'title' => 'Morning Programme 2018', 'description' => 'Our beautiful community gathering for morning rituals and prayers during Durga Puja 2018.', 'image' => 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?q=80&w=600'],
        ['id' => 2, 'title' => 'Durga Puja Invitation Card 2021', 'description' => 'The official creative design and release of our Durga Puja invitation cards for 2021.', 'image' => 'https://images.unsplash.com/photo-1502134249126-9f3755a50d78?q=80&w=600'],
        ['id' => 3, 'title' => 'Evening Programme 2018', 'description' => 'Dance dramas, classical songs and folk performances by our community members in 2018.', 'image' => 'https://images.unsplash.com/photo-1536304997881-a372c179924b?q=80&w=600'],
        ['id' => 4, 'title' => 'Dandiya Night 2018', 'description' => 'Vibrant Garba and Dandiya dance events under decorative lighting with delicious foods.', 'image' => 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80&w=600']
    ];
}

// Fallback images specifically for Durga Puja category (6 items)
$gallery_images_fallback = [
    ['image' => 'https://images.unsplash.com/photo-1561376399-5ef8d0859942?q=80&w=600', 'title' => 'Sindur Khela on Dashami', 'category' => 'Durga Puja'],
    ['image' => 'https://images.unsplash.com/photo-1601050690597-df056fb4ce78?q=80&w=600', 'title' => 'Dhunuchi Dance Competition', 'category' => 'Durga Puja'],
    ['image' => 'https://images.unsplash.com/photo-1620121692029-d088224ddc74?q=80&w=600', 'title' => 'Decorated Durga Idol Close Up', 'category' => 'Durga Puja'],
    ['image' => 'https://images.unsplash.com/photo-1590073844006-33379778ae09?q=80&w=600', 'title' => 'Puja Pandal Entrance Decor', 'category' => 'Durga Puja'],
    ['image' => 'https://images.unsplash.com/photo-1561376399-5ef8d0859942?q=80&w=600', 'title' => 'Anjali Offerings on Ashtami', 'category' => 'Durga Puja'],
    ['image' => 'https://images.unsplash.com/photo-1605152276897-4f618f831968?q=80&w=600', 'title' => 'Shyama Puja Aarati', 'category' => 'Durga Puja']
];

// Fetch dynamic gallery images from database for durga-puja category (limit 6)
$durga_puja_gallery = [];
try {
    if (isset($pdo)) {
        // Query category using either 'durga-puja' or 'durga puja' slug/name
        $stmt_gal = $pdo->prepare("SELECT * FROM `gallery` WHERE `category` = 'durga-puja' OR `category` = 'durga puja' ORDER BY `id` DESC LIMIT 6");
        $stmt_gal->execute();
        $db_gallery = $stmt_gal->fetchAll(PDO::FETCH_ASSOC);
        foreach ($db_gallery as $g_item) {
            $durga_puja_gallery[] = [
                'image' => $g_item['image'],
                'title' => $g_item['title'],
                'category' => 'Durga Puja'
            ];
        }
    }
} catch (PDOException $e) {
    // Fail silently
}
if (empty($durga_puja_gallery)) {
    $durga_puja_gallery = $gallery_images_fallback;
}
// Trim to exactly 6 in case database query fetched more or was filtered differently
$durga_puja_gallery = array_slice($durga_puja_gallery, 0, 6);
?>

<style>
    /* ==========================================================================
       DURGA PUJA PAGE SPECIFIC STYLES
       ========================================================================== */
    
    /* 1. HERO CAROUSEL SECTION */
    .hero-carousel {
        height: 100vh;
        width: 100%;
        position: relative;
        overflow: hidden;
        background-color: var(--dark);
    }

    .carousel-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1s ease-in-out;
        z-index: 1;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .carousel-slide.active {
        opacity: 1;
        z-index: 2;
    }

    .carousel-slide::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.3) 100%);
        z-index: 3;
    }

    .hero-content {
        position: absolute;
        top: 55%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: 800px;
        text-align: center;
        z-index: 10;
        color: var(--white);
        background-color: rgba(21, 15, 13, 0.25);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: clamp(2rem, 5vw, 3rem) clamp(1.5rem, 5vw, 2.5rem);
        border-radius: var(--border-radius-lg);
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
    }

    .hero-title {
        font-size: clamp(2.2rem, 5vw, 4rem);
        font-family: var(--font-headings);
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--white);
        text-shadow: 0 4px 15px rgba(0,0,0,0.5);
    }

    .hero-subtitle {
        /* font-size: clamp(1rem, 2vw, 1.35rem); */
        font-size: clamp(0.95rem, 1.8vw, 1.2rem);
        margin-bottom: 1.5rem;
        font-weight: 700;
        color: var(--gold);
        letter-spacing: 1.5px;
        text-transform: uppercase;
        display: block;
        text-shadow: 0 2px 10px rgba(0,0,0,0.5);
    }

    .hero-desc {
        font-size: clamp(0.95rem, 1.8vw, 1.1rem);
        margin-bottom: 2.2rem;
        line-height: 1.6;
        color: var(--secondary-bg);
        text-shadow: 0 2px 8px rgba(0,0,0,0.4);
    }

    .hero-buttons {
        display: flex;
        justify-content: center;
        gap: 1.25rem;
        flex-wrap: wrap;
    }

    .btn-hero-secondary {
        background-color: transparent;
        color: var(--white);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .btn-hero-secondary:hover {
        background-color: var(--white);
        color: var(--red);
        border-color: var(--white);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 255, 255, 0.15);
    }

    /* Carousel Nav Arrows */
    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(255, 255, 255, 0.15);
        color: var(--white);
        border: none;
        width: 54px;
        height: 54px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 15;
        transition: var(--transition);
        backdrop-filter: blur(5px);
        font-size: 1.1rem;
    }

    .carousel-btn:hover {
        background-color: var(--red);
        transform: translateY(-50%) scale(1.05);
    }

    .carousel-btn-prev { left: 30px; }
    .carousel-btn-next { right: 30px; }

    /* Carousel Dots */
    .carousel-dots {
        position: absolute;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 0.8rem;
        z-index: 15;
    }

    .carousel-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.4);
        border: none;
        cursor: pointer;
        transition: var(--transition);
    }

    .carousel-dot.active {
        background-color: var(--gold);
        width: 26px;
        border-radius: 10px;
    }

    /* 2. JOURNEY OF PUJO (TIMELINE) SECTION */
    .journey-section {
        background-color: var(--secondary-bg);
        padding: 8rem 0;
        position: relative;
        border-bottom: 1px solid var(--border-color);
        overflow: hidden;
    }
    
    /* Decorative Alpana backdrop */
    .journey-section::before {
        content: '';
        position: absolute;
        bottom: -50px;
        right: -50px;
        width: 250px;
        height: 250px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" stroke="rgba(201, 154, 46, 0.05)" stroke-width="1.5" fill="none"/><circle cx="50" cy="50" r="30" stroke="rgba(201, 154, 46, 0.04)" stroke-dasharray="2,2" stroke-width="1" fill="none"/><path d="M50 10 L50 90 M10 50 L90 50" stroke="rgba(201, 154, 46, 0.03)" stroke-width="1"/></svg>');
        background-size: contain;
        background-repeat: no-repeat;
        pointer-events: none;
    }

    .journey-section::after {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        width: 250px;
        height: 250px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" stroke="rgba(201, 154, 46, 0.05)" stroke-width="1.5" fill="none"/><circle cx="50" cy="50" r="30" stroke="rgba(201, 154, 46, 0.04)" stroke-dasharray="2,2" stroke-width="1" fill="none"/><path d="M50 10 L50 90 M10 50 L90 50" stroke="rgba(201, 154, 46, 0.03)" stroke-width="1"/></svg>');
        background-size: contain;
        background-repeat: no-repeat;
        pointer-events: none;
    }

    .timeline-container {
        position: relative;
        max-width: 1100px;
        margin: 4.5rem auto 1rem auto;
        padding: 0 1.5rem;
    }

    /* Desktop horizontal timeline line */
    .timeline-track {
        position: absolute;
        top: 24px;
        left: 10%;
        width: 80%;
        height: 3px;
        background-color: rgba(201, 154, 46, 0.25);
        z-index: 1;
        border-radius: 2px;
    }

    .timeline-progress {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 50%; /* JS will calculate this based on active index */
        background: linear-gradient(90deg, var(--red), var(--gold));
        transition: width 0.6s cubic-bezier(0.25, 1, 0.5, 1);
        border-radius: 2px;
    }

    .timeline-nodes {
        display: flex;
        justify-content: space-between;
        position: relative;
        z-index: 2;
    }

    .timeline-node {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        cursor: pointer;
        padding: 0 1rem;
        transition: var(--transition);
        position: relative;
    }

    /* The timeline circular point */
    .timeline-dot-wrapper {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .timeline-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background-color: var(--white);
        border: 3.5px solid var(--gold);
        box-shadow: 0 0 0 4px rgba(251, 244, 230, 0.9);
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        position: relative;
        z-index: 2;
    }

    .timeline-node:hover .timeline-dot {
        transform: scale(1.25);
        border-color: var(--red);
        background-color: var(--gold);
        box-shadow: 0 0 10px rgba(201, 154, 46, 0.4);
    }

    /* Active circular point is slightly larger */
    .timeline-node.active .timeline-dot {
        width: 24px;
        height: 24px;
        border-color: var(--red);
        background-color: var(--white);
        box-shadow: 0 0 0 6px rgba(139, 30, 30, 0.15), 0 0 15px rgba(139, 30, 30, 0.2);
    }

    .timeline-dot::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: var(--red);
        transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .timeline-node.active .timeline-dot::after {
        transform: translate(-50%, -50%) scale(1);
    }

    /* Node content labels */
    .timeline-day {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--gold);
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 0.6rem;
        transition: var(--transition);
    }

    .timeline-node:hover .timeline-day,
    .timeline-node.active .timeline-day {
        color: var(--red);
        letter-spacing: 2px;
    }

    .timeline-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.8rem;
        font-family: var(--font-headings);
        transition: var(--transition);
    }

    .timeline-node:hover .timeline-title,
    .timeline-node.active .timeline-title {
        color: var(--red);
        transform: translateY(-2px);
    }

    .timeline-desc {
        font-size: 0.88rem;
        line-height: 1.6;
        color: var(--text-muted);
        transition: var(--transition);
        padding: 0 0.5rem;
        opacity: 0.85;
    }

    .timeline-node:hover .timeline-desc,
    .timeline-node.active .timeline-desc {
        color: var(--dark);
        opacity: 1;
    }

    /* Fade-up entry scroll animation wrapper */
    .fade-up-element {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.8s ease, transform 0.8s ease;
    }

    .fade-up-element.visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* Responsive Mobile vertical timeline */
    @media (max-width: 768px) {
        .timeline-track {
            display: none;
        }

        .timeline-nodes {
            flex-direction: column;
            gap: 2rem;
            position: relative;
            padding-left: 2rem;
        }

        /* Mobile vertical line helper */
        .timeline-nodes::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 9px;
            bottom: 10px;
            width: 2px;
            background-color: rgba(201, 154, 46, 0.25);
        }

        .timeline-node {
            flex-direction: row;
            align-items: flex-start;
            text-align: left;
            padding: 0;
            gap: 1.5rem;
        }

        .timeline-dot-wrapper {
            width: 20px;
            height: 20px;
            margin-bottom: 0;
            margin-top: 0.2rem;
            flex-shrink: 0;
            position: relative;
            left: -31px;
            z-index: 2;
        }

        .timeline-dot {
            box-shadow: 0 0 0 4px var(--secondary-bg) !important;
        }

        .timeline-node.active .timeline-dot {
            width: 20px;
            height: 20px;
        }

        .timeline-node.active .timeline-dot::after {
            width: 6px;
            height: 6px;
        }

        .timeline-content {
            display: flex;
            flex-direction: column;
        }

        .timeline-day {
            margin-bottom: 0.3rem;
        }

        .timeline-title {
            font-size: 1.1rem;
            margin-bottom: 0.4rem;
        }

        .timeline-desc {
            padding: 0;
        }
    }

    /* 3. RECENT ACTIVITY */
    .recent-activity-section {
        padding: 6.5rem 0;
        background-color: var(--sand);
    }
    .activity-card:hover .activity-circle-wrapper {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(139, 30, 30, 0.2);
        border-color: var(--gold);
    }
    .activity-card:hover img {
        transform: scale(1.08);
    }
    .activity-card:hover .activity-title {
        color: var(--red) !important;
    }
    @media (max-width: 991px) {
        .activity-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 2.5rem 1.5rem !important;
        }
    }
    @media (max-width: 575px) {
        .activity-grid {
            grid-template-columns: 1fr !important;
            gap: 2.5rem 1.5rem !important;
        }
        .activity-circle-wrapper {
            width: 160px !important;
            height: 160px !important;
        }
    }

    /* 4. DURGA PUJA INFORMATION */
    .puja-info-section {
        background-color: var(--secondary-bg);
        padding: 8rem 0;
        position: relative;
        border-bottom: 1px solid var(--border-color);
    }

    .puja-info-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 4.5rem;
        align-items: center;
    }

    .welcome-img-wrapper {
        position: relative;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        border: 5px solid var(--white);
    }

    .welcome-img {
        border-radius: var(--border-radius-lg);
        width: 100%;
        object-fit: cover;
        height: 480px;
        transition: var(--transition-slow);
    }

    .welcome-img-wrapper:hover .welcome-img {
        transform: scale(1.04);
    }

    .welcome-subtitle {
        color: var(--vermilion);
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        font-size: 0.9rem;
        margin-bottom: 0.8rem;
        display: block;
    }

    .welcome-title {
        font-size: clamp(2.2rem, 4.5vw, 3rem);
        margin-bottom: 1.8rem;
        color: var(--red);
    }

    .welcome-text {
        font-size: 0.98rem;
        line-height: 1.8;
        margin-bottom: 1.2rem;
    }

    .welcome-text-container {
        height: 310px;
        overflow: hidden;
        position: relative;
        margin-bottom: 1.5rem;
    }

    .welcome-text-container::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 50px;
        background: linear-gradient(to top, var(--secondary-bg) 0%, rgba(251, 244, 230, 0) 100%);
        pointer-events: none;
        transition: var(--transition);
        opacity: 1;
    }

    .welcome-text-container.expanded::after {
        opacity: 0;
    }

    .welcome-text-container::-webkit-scrollbar {
        width: 6px;
    }
    .welcome-text-container::-webkit-scrollbar-track {
        background: rgba(33, 26, 23, 0.05);
        border-radius: 3px;
    }
    .welcome-text-container::-webkit-scrollbar-thumb {
        background: var(--gold);
        border-radius: 3px;
    }
    .welcome-text-container::-webkit-scrollbar-thumb:hover {
        background: var(--red);
    }

    /* 5. PUJA STALLS SECTION */
    .stalls-section {
        background-color: var(--primary-bg);
        padding: 8rem 0;
        position: relative;
        border-bottom: 1px solid var(--border-color);
    }

    .stalls-tabs-container {
        display: flex;
        align-items: center;
        gap: 1rem;
        width: 100%;
        max-width: 1100px;
        margin: 0 auto 3.5rem auto;
        position: relative;
    }
    
    .stalls-tabs-row {
        display: flex;
        gap: 1.2rem;
        flex: 1;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 0.5rem 0.2rem;
        /* Hide scrollbar */
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .stalls-tabs-row::-webkit-scrollbar {
        display: none;
    }

    .tabs-nav-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid var(--border-color);
        background-color: var(--white);
        color: var(--dark);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
        flex-shrink: 0;
        font-size: 0.95rem;
    }

    .tabs-nav-btn:hover {
        background-color: var(--red);
        color: var(--white);
        border-color: var(--red);
        box-shadow: var(--shadow-md);
    }
    
    .stall-tab-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.6rem;
        padding: 1.2rem 2rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
        background-color: var(--white);
        min-width: 150px;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
        color: var(--dark);
        font-weight: 700;
        font-size: 0.95rem;
    }

    .tab-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: var(--transition);
    }
    
    .tab-accent-line {
        width: 35px;
        height: 3px;
        border-radius: 2px;
        margin-top: 0.2rem;
        transition: var(--transition);
    }

    /* Tab colors and states */
    .stall-tab-btn[data-tab="food"] .tab-icon-box { background-color: rgba(201, 154, 46, 0.1); color: var(--gold); }
    .stall-tab-btn[data-tab="food"] .tab-accent-line { background-color: var(--gold); }
    
    .stall-tab-btn[data-tab="crafts"] .tab-icon-box { background-color: rgba(139, 30, 30, 0.1); color: var(--red); }
    .stall-tab-btn[data-tab="crafts"] .tab-accent-line { background-color: var(--red); }
    
    .stall-tab-btn[data-tab="toys"] .tab-icon-box { background-color: rgba(200, 59, 45, 0.1); color: var(--vermilion); }
    .stall-tab-btn[data-tab="toys"] .tab-accent-line { background-color: var(--vermilion); }
    
    .stall-tab-btn[data-tab="fashion"] .tab-icon-box { background-color: rgba(33, 26, 23, 0.15); color: var(--dark); }
    .stall-tab-btn[data-tab="fashion"] .tab-accent-line { background-color: var(--dark); }

    .stall-tab-btn[data-tab="pooja"] .tab-icon-box { background-color: rgba(201, 154, 46, 0.1); color: var(--gold); }
    .stall-tab-btn[data-tab="pooja"] .tab-accent-line { background-color: var(--gold); }
    
    .stall-tab-btn[data-tab="brands"] .tab-icon-box { background-color: rgba(200, 59, 45, 0.1); color: var(--vermilion); }
    .stall-tab-btn[data-tab="brands"] .tab-accent-line { background-color: var(--vermilion); }

    /* Active Tab state */
    .stall-tab-btn.active {
        background-color: var(--red);
        color: var(--white);
        border-color: var(--red);
        box-shadow: var(--shadow-md);
    }
    
    .stall-tab-btn.active .tab-icon-box {
        background-color: rgba(255, 255, 255, 0.2) !important;
        color: var(--white) !important;
    }
    
    .stall-tab-btn.active .tab-accent-line {
        background-color: var(--white) !important;
    }

    /* Stalls Details Card layout */
    .stalls-details-card {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 4.5rem;
        align-items: center;
        background-color: var(--secondary-bg);
        padding: 3.5rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        transition: var(--transition);
    }
    
    .stalls-details-card.fade-in {
        animation: stallFadeIn 0.5s ease;
    }
    
    @keyframes stallFadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .stalls-bullets-list {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        margin: 1.8rem 0;
    }

    .stall-bullet-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.9rem 1.2rem;
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        font-weight: 600;
        color: var(--dark);
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
    }
    
    .stall-bullet-item i {
        color: var(--gold);
        font-size: 1.15rem;
    }
    
    .stalls-actions {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
    }

    /* Scrollable Text container for Stalls details */
    .stall-text-container {
        height: 110px;
        overflow: hidden;
        position: relative;
        margin-bottom: 1.5rem;
        transition: var(--transition);
    }
    
    .stall-text-container.expanded {
        height: 240px;
    }

    .stalls-details-card .welcome-img-wrapper {
        height: 100%;
        align-self: stretch;
        display: flex;
    }

    .stalls-details-card .welcome-img {
        height: 100%;
        min-height: 480px;
        width: 100%;
        object-fit: cover;
        transition: var(--transition-slow);
    }

    .stall-text-container::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 40px;
        background: linear-gradient(to top, var(--secondary-bg) 0%, rgba(251, 244, 230, 0) 100%);
        pointer-events: none;
        transition: var(--transition);
        opacity: 1;
    }

    .stall-text-container.expanded::after {
        opacity: 0;
    }

    .stall-text-container::-webkit-scrollbar {
        width: 6px;
    }
    .stall-text-container::-webkit-scrollbar-track {
        background: rgba(33, 26, 23, 0.05);
        border-radius: 3px;
    }
    .stall-text-container::-webkit-scrollbar-thumb {
        background: var(--gold);
        border-radius: 3px;
    }
    .stall-text-container::-webkit-scrollbar-thumb:hover {
        background: var(--red);
    }

    /* 6. FAQ ACCORDION SECTION */
    .faq-section {
        background-color: var(--primary-bg);
        padding: 8rem 0;
        position: relative;
    }

    .faq-max-width {
        max-width: 800px;
        margin: 0 auto;
    }

    .faq-accordion {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .faq-item {
        background-color: var(--white);
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: var(--transition);
    }

    .faq-item.active {
        box-shadow: var(--shadow-sm);
        border-color: rgba(201, 154, 46, 0.3);
    }

    .faq-question {
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--dark);
        transition: var(--transition);
    }

    .faq-question:hover {
        color: var(--red);
    }

    .faq-item.active .faq-question {
        color: var(--red);
        border-bottom: 1px solid var(--border-color);
    }

    .faq-icon {
        color: var(--gold);
        font-size: 0.95rem;
        transition: var(--transition);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out;
    }

    .faq-answer-inner {
        padding: 1.5rem 2rem;
        font-size: 0.95rem;
        line-height: 1.7;
        color: var(--text-muted);
    }

    /* ==========================================================================
       6B. JOIN OUR ASSOCIATION CTA STYLES
       ========================================================================== */
    .join-cta-section {
        position: relative;
        padding: 9rem 0;
        background-color: var(--dark);
        color: var(--white);
        text-align: center;
        overflow: hidden;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
    }

    .join-cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(180deg, rgba(33, 26, 23, 0.65) 0%, rgba(33, 26, 23, 0.85) 100%);
        z-index: 1;
    }

    .join-cta-container {
        position: relative;
        z-index: 5;
        max-width: 800px;
        margin: 0 auto;
    }

    .join-cta-title {
        font-size: clamp(2.2rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1.5rem;
    }

    .join-cta-text {
        font-size: 1.15rem;
        color: rgba(255, 255, 255, 0.85);
        line-height: 1.8;
        margin-bottom: 3.2rem;
        font-weight: 500;
    }

    .join-cta-buttons {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .join-cta-buttons .btn {
        padding: 0.95rem 2.4rem;
        font-size: 1rem;
    }

    /* 7. DIALOG / MODAL STYLES */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background-color: rgba(33, 26, 23, 0.7);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .modal-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    .modal-card {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        width: 100%;
        max-width: 600px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        position: relative;
        transform: scale(0.9);
        transition: var(--transition-slow);
        border: 1px solid var(--border-color);
    }

    .modal-overlay.open .modal-card {
        transform: scale(1);
    }

    .modal-close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        color: var(--dark);
        font-size: 1rem;
    }

    .modal-close-btn:hover {
        background-color: var(--red);
        color: var(--white);
    }

    .modal-body {
        padding: 2.8rem;
    }

    .modal-title {
        font-size: 1.8rem;
        color: var(--red);
        font-family: var(--font-headings);
        margin-bottom: 1rem;
    }

    .modal-desc {
        font-size: 0.98rem;
        line-height: 1.8;
        color: var(--text-muted);
    }

    /* GALLERY PREVIEW SECTION STYLES */
    .gallery-preview-section {
        background-color: var(--secondary-bg);
        padding: 8rem 0;
        overflow: hidden;
    }

    .gallery-carousel-wrapper {
        position: relative;
        margin-bottom: 3.5rem;
    }

    .gallery-carousel-track-container {
        overflow: hidden;
        padding: 10px 0;
    }

    .gallery-carousel-track {
        display: flex;
        transition: transform 0.5s ease-in-out;
        gap: 1.5rem;
    }

    .gallery-item {
        flex: 0 0 calc((100% - (3 * 1.5rem)) / 4); /* Exactly 4 visible items in the row on desktop */
        position: relative;
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        height: 260px;
        cursor: pointer;
        background-color: var(--secondary-bg);
        border: 1px solid var(--border-color);
    }

    .gallery-item-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition-slow);
    }

    .gallery-item-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(0deg, rgba(33, 26, 23, 0.85) 0%, rgba(33, 26, 23, 0.1) 80%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 1.5rem;
        opacity: 0;
        transition: var(--transition);
    }

    .gallery-item-title {
        color: var(--white);
        font-family: var(--font-headings);
        font-size: 1.15rem;
        margin-bottom: 0.25rem;
        transform: translateY(10px);
        transition: var(--transition-slow);
    }

    .gallery-item-cat {
        color: var(--gold);
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        transform: translateY(10px);
        transition: var(--transition-slow);
    }

    .gallery-item:hover .gallery-item-img {
        transform: scale(1.08);
    }

    .gallery-item:hover .gallery-item-overlay {
        opacity: 1;
    }

    .gallery-item:hover .gallery-item-title,
    .gallery-item:hover .gallery-item-cat {
        transform: translateY(0);
    }

    /* Controls */
    .gallery-ctrl-btn {
        background-color: var(--white);
        color: var(--red);
        box-shadow: var(--shadow-md);
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        cursor: pointer;
        transition: var(--transition);
        border: 1px solid var(--border-color);
    }

    .gallery-ctrl-btn:hover {
        background-color: var(--red);
        color: var(--white);
        box-shadow: 0 4px 15px rgba(139, 30, 30, 0.3);
    }

    .gallery-ctrl-prev { left: -25px; }
    .gallery-ctrl-next { right: -25px; }

    .gallery-ctrl-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .gallery-footer {
        text-align: center;
    }

    /* Lightbox modal styles */
    .lightbox-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--white);
        font-size: 2.5rem;
        cursor: pointer;
        opacity: 0.7;
        transition: var(--transition);
        z-index: 10001;
    }
    .lightbox-nav-btn:hover {
        opacity: 1;
        color: var(--gold);
    }
    .lightbox-prev { left: 30px; }
    .lightbox-next { right: 30px; }
    .lightbox-card {
        background-color: transparent !important;
        border: none !important;
        max-width: 80% !important;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .lightbox-img {
        max-height: 80vh;
        max-width: 100%;
        border-radius: 8px;
        border: 2px solid var(--white);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .lightbox-caption {
        color: var(--white);
        margin-top: 1rem;
        font-size: 1.2rem;
        font-family: var(--font-headings);
        text-align: center;
    }

    /* Responsive breakpoints */
    @media (max-width: 1200px) {
        .gallery-item {
            flex: 0 0 calc((100% - (2 * 1.5rem)) / 3); /* 3 items visible */
        }
    }
    @media (max-width: 768px) {
        .gallery-item {
            flex: 0 0 calc((100% - (1 * 1.5rem)) / 2); /* 2 items visible */
        }
        .gallery-ctrl-prev { left: -10px; }
        .gallery-ctrl-next { right: -10px; }
    }
    @media (max-width: 480px) {
        .gallery-item {
            flex: 0 0 100%; /* 1 item visible */
        }
    }

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS
       ========================================================================== */
    @media (max-width: 991px) {
        .puja-info-grid {
            grid-template-columns: 1fr;
            gap: 3rem;
        }
        .stalls-grid {
            grid-template-columns: 1fr;
            gap: 3rem;
        }
        .stalls-grid .welcome-img-wrapper {
            order: -1;
        }
        .welcome-img {
            height: 380px;
        }
        .stalls-details-card {
            grid-template-columns: 1fr;
            gap: 3rem;
            padding: 2.2rem;
        }
        .stalls-details-card .welcome-img-wrapper {
            order: -1;
            height: auto;
        }
        .stalls-details-card .welcome-img {
            height: 320px;
            min-height: auto;
        }
    }

    @media (max-width: 768px) {
        .stalls-list {
            grid-template-columns: 1fr;
        }
        .carousel-btn {
            width: 44px;
            height: 44px;
            font-size: 1rem;
        }
        .carousel-btn-prev { left: 15px; }
        .carousel-btn-next { right: 15px; }
        .join-cta-section {
            padding: 5.5rem 0;
        }
    }

    @media (max-width: 576px) {
        .join-cta-buttons {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
            padding: 0 1.5rem;
        }
    }
</style>

<!-- 1. DURGA PUJA HERO SECTION -->
<section class="hero-carousel" id="hero">
    <!-- Slide 1 -->
    <div class="carousel-slide active" style="background-image: url('https://images.unsplash.com/photo-1561376399-5ef8d0859942?q=80&w=1600');">
        <div class="hero-content">
            <h1 class="hero-title">Durga Puja</h1>
            <span class="hero-subtitle">Celebrating Faith, Culture, Tradition & Togetherness</span>
            <!-- <p class="hero-desc">Experience the spirit of Bengal through devotion, culture, celebration and community.</p> -->
            <div class="hero-buttons">
                <a href="#puja-info" class="btn btn-white">Explore Puja</a>
                <a href="join-us.php" class="btn btn-hero-secondary">Join Our Celebration</a>
            </div>
        </div>
    </div>
    <!-- Slide 2 -->
    <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1605152276897-4f618f831968?q=80&w=1600');">
        <div class="hero-content">
            <h1 class="hero-title">Durga Puja</h1>
            <span class="hero-subtitle">Where Faith Meets Culture, Tradition Meets Celebration</span>
            <!-- <p class="hero-desc">Experience the spirit of Bengal through devotion, culture, celebration and community.</p> -->
            <div class="hero-buttons">
                <a href="#puja-info" class="btn btn-white">Explore Puja</a>
                <a href="join-us.php" class="btn btn-hero-secondary">Join Our Celebration</a>
            </div>
        </div>
    </div>
    <!-- Slide 3 -->
    <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?q=80&w=1600');">
        <div class="hero-content">
            <h1 class="hero-title">Durga Puja</h1>
            <span class="hero-subtitle">Celebrating Devotion, Embracing Tradition, Together as One</span>
            <!-- <p class="hero-desc">Experience the spirit of Bengal through devotion, culture, celebration and community.</p> -->
            <div class="hero-buttons">
                <a href="#puja-info" class="btn btn-white">Explore Puja</a>
                <a href="join-us.php" class="btn btn-hero-secondary">Join Our Celebration</a>
            </div>
        </div>
    </div>

    <!-- Navigation buttons -->
    <button class="carousel-btn carousel-btn-prev" aria-label="Previous Slide"><i class="fa-solid fa-chevron-left"></i></button>
    <button class="carousel-btn carousel-btn-next" aria-label="Next Slide"><i class="fa-solid fa-chevron-right"></i></button>

    <!-- Pagination dots -->
    <div class="carousel-dots">
        <button class="carousel-dot active" data-slide="0" aria-label="Go to slide 1"></button>
        <button class="carousel-dot" data-slide="1" aria-label="Go to slide 2"></button>
        <button class="carousel-dot" data-slide="2" aria-label="Go to slide 3"></button>
    </div>
</section>

<!-- 2. THE JOURNEY OF PUJO SECTION (TIMELINE) -->
<section class="journey-section fade-up-element" id="journey">
    <div class="container">
        <div class="section-header">
            <span class="welcome-subtitle">THE JOURNEY OF PUJO</span>
            <h2>Five Days, One Emotion</h2>
            <p class="section-subtitle">From Maa's arrival on Shashthi to the emotional farewell on Dashami, every day of Durga Puja carries its own story, tradition and emotion.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <!-- Timeline Container -->
        <div class="timeline-container">
            <!-- Desktop horizontal timeline track -->
            <div class="timeline-track">
                <div class="timeline-progress"></div>
            </div>

            <!-- Timeline Nodes -->
            <div class="timeline-nodes">
                <!-- Day 1: Shashthi -->
                <div class="timeline-node" data-day="shashthi">
                    <div class="timeline-dot-wrapper">
                        <div class="timeline-dot"></div>
                    </div>
                    <div class="timeline-content">
                        <span class="timeline-day">Shashthi</span>
                        <h3 class="timeline-title">Maa's Arrival</h3>
                        <p class="timeline-desc">The beginning of the celebration as devotees welcome Maa Durga with devotion, rituals and joy.</p>
                    </div>
                </div>

                <!-- Day 2: Saptami -->
                <div class="timeline-node" data-day="saptami">
                    <div class="timeline-dot-wrapper">
                        <div class="timeline-dot"></div>
                    </div>
                    <div class="timeline-content">
                        <span class="timeline-day">Saptami</span>
                        <h3 class="timeline-title">The Celebration Begins</h3>
                        <p class="timeline-desc">Pushpanjali, rituals and the festive spirit fill the Puja grounds as the celebration comes alive.</p>
                    </div>
                </div>

                <!-- Day 3: Ashtami -->
                <div class="timeline-node active" data-day="ashtami">
                    <div class="timeline-dot-wrapper">
                        <div class="timeline-dot"></div>
                    </div>
                    <div class="timeline-content">
                        <span class="timeline-day">Ashtami</span>
                        <h3 class="timeline-title">The Heart of Pujo</h3>
                        <p class="timeline-desc">A day of deep devotion, Pushpanjali, Sandhi Puja and moments shared with family and the community.</p>
                    </div>
                </div>

                <!-- Day 4: Navami -->
                <div class="timeline-node" data-day="navami">
                    <div class="timeline-dot-wrapper">
                        <div class="timeline-dot"></div>
                    </div>
                    <div class="timeline-content">
                        <span class="timeline-day">Navami</span>
                        <h3 class="timeline-title">Joy at Its Peak</h3>
                        <p class="timeline-desc">Cultural programs, Dhunuchi dance, music and celebrations bring everyone together.</p>
                    </div>
                </div>

                <!-- Day 5: Dashami -->
                <div class="timeline-node" data-day="dashami">
                    <div class="timeline-dot-wrapper">
                        <div class="timeline-dot"></div>
                    </div>
                    <div class="timeline-content">
                        <span class="timeline-day">Dashami</span>
                        <h3 class="timeline-title">Bidai with Emotion</h3>
                        <p class="timeline-desc">Sindoor Khela, Visarjan and an emotional farewell to Maa, carrying her blessings in our hearts.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- RECENT ACTIVITY SECTION -->
<section class="recent-activity-section" style="padding: 6.5rem 0; background-color: var(--sand);">
    <div class="container">
        <div class="section-header">
            <h2>Recent Activity</h2>
            <p class="section-subtitle">Catch up on our community's latest programs, performances, and memories.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="activity-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 3.5rem 2rem; justify-items: center; margin-top: 3rem;">
            <?php foreach ($recent_activities as $act): ?>
                <?php 
                $act_img = htmlspecialchars($act['image']);
                if (strpos($act['image'], 'http') !== 0) {
                    $act_img = $act_img; // absolute path to file in project root
                }
                ?>
                <a href="activity-details.php?id=<?php echo $act['id']; ?>" class="activity-card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; text-align: center; width: 100%; max-width: 220px; transition: var(--transition);">
                    <div class="activity-circle-wrapper" style="width: 180px; height: 180px; border-radius: 50%; overflow: hidden; border: 4px solid var(--white); box-shadow: 0 8px 24px rgba(33,26,23,0.12); transition: var(--transition); background-color: var(--secondary-bg);">
                        <img src="<?php echo $act_img; ?>" alt="<?php echo htmlspecialchars($act['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: var(--transition-slow);">
                    </div>
                    <h3 class="activity-title" style="margin-top: 1.25rem; font-family: var(--font-headings); font-size: 1.15rem; color: var(--dark); font-weight: 700; transition: var(--transition);"><?php echo htmlspecialchars($act['title']); ?></h3>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="activity-footer" style="text-align: center; margin-top: 4rem;">
            <a href="activities.php" class="btn btn-secondary">View All Activities <i class="fa-solid fa-list-check"></i></a>
        </div>
    </div>
</section>

<!-- 4. DURGA PUJA INFORMATION SECTION -->
<section class="puja-info-section" id="puja-info">
    <div class="container">
        <div class="puja-info-grid">
            <!-- Left: Durga Puja Pandal Image -->
            <div class="welcome-img-wrapper">
                <img src="https://images.unsplash.com/photo-1590073844006-33379778ae09?q=80&w=1000" alt="Maa Durga Puja Celebration Pandal" class="welcome-img" loading="lazy">
            </div>
            <!-- Right: Durga Puja description with expandable inline read more -->
            <div>
                <span class="welcome-subtitle">THE GRAND FESTIVAL</span>
                <h2 class="welcome-title">Durga Puja</h2>
                
                <div class="welcome-text-container" id="puja-text-container">
                    <p class="welcome-text">
                        Durga Puja, also known as Durgotsava, is the grandest festival of Bengal, symbolizing the victory of Goddess Durga over the shape-shifting demon Mahishasura. It is not just a religious ritual; it is a five-day carnival of art, music, street food, and community bonding. For Bengalis worldwide, it represents the emotional homecoming of Maa Durga with her children (Lakshmi, Saraswati, Ganesha, and Kartikeya).
                    </p>
                    <p class="welcome-text">
                        The celebration begins on Sasthi with the welcoming rituals and continues through Saptami, Ashtami, Navami, and Vijaya Dashami. Each day has its specific significance. Ashtami afternoon is famous for the grand Pushpanjali and Sandhi Puja, while Navami brings the competitive and rhythmic Dhunuchi dance. The festival is a celebration of craftsmanship, shown in the clay idols sculpted by traditional artisans and the artistic structural pandals.
                    </p>
                    <p class="welcome-text">
                        On the final day of Vijaya Dashami, married women participate in Sindoor Khela, bidding a tearful farewell to the goddess by playing with vermilion before the grand idol immersion (Visarjan). Durga Puja serves as a platform to unite people of all backgrounds, spreading messages of peace, equality, and happiness.
                    </p>
                </div>
                
                <button type="button" class="btn btn-secondary" id="puja-read-more-btn">Read More <i class="fa-solid fa-chevron-down"></i></button>
            </div>
        </div>
    </div>
</section>

<!-- 5. PUJA STALLS / COMMUNITY MARKET SECTION -->
<section class="stalls-section" id="stalls">
    <div class="container">
        <div class="section-header">
            <span class="welcome-subtitle">COMMUNITY MARKETPLACE</span>
            <h2>Puja Stalls</h2>
            <p class="section-subtitle">A vibrant space for local businesses, food vendors and artisans to be part of our Durga Puja celebration.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <!-- Stalls Tabs Container with Navigation Buttons -->
        <div class="stalls-tabs-container">
            <button class="tabs-nav-btn prev" id="tabs-prev-btn" aria-label="Previous Stalls Tab">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <div class="stalls-tabs-row" id="stalls-tabs-row">
                <button class="stall-tab-btn active" data-tab="food" aria-label="Select Food Stalls">
                    <div class="tab-icon-box"><i class="fa-solid fa-bowl-food"></i></div>
                    <span>Food Stalls</span>
                    <div class="tab-accent-line"></div>
                </button>
                <button class="stall-tab-btn" data-tab="crafts" aria-label="Select Handicrafts Stalls">
                    <div class="tab-icon-box"><i class="fa-solid fa-gifts"></i></div>
                    <span>Handicrafts</span>
                    <div class="tab-accent-line"></div>
                </button>
                <button class="stall-tab-btn" data-tab="toys" aria-label="Select Toys Stalls">
                    <div class="tab-icon-box"><i class="fa-solid fa-puzzle-piece"></i></div>
                    <span>Toys & Games</span>
                    <div class="tab-accent-line"></div>
                </button>
                <button class="stall-tab-btn" data-tab="fashion" aria-label="Select Fashion Stalls">
                    <div class="tab-icon-box"><i class="fa-solid fa-tags"></i></div>
                    <span>Clothing</span>
                    <div class="tab-accent-line"></div>
                </button>
                <button class="stall-tab-btn" data-tab="pooja" aria-label="Select Pooja Samagri Stalls">
                    <div class="tab-icon-box"><i class="fa-solid fa-om"></i></div>
                    <span>Pooja Items</span>
                    <div class="tab-accent-line"></div>
                </button>
                <button class="stall-tab-btn" data-tab="brands" aria-label="Select Brand Promoters Stalls">
                    <div class="tab-icon-box"><i class="fa-solid fa-bullhorn"></i></div>
                    <span>Promoters</span>
                    <div class="tab-accent-line"></div>
                </button>
            </div>
            <button class="tabs-nav-btn next" id="tabs-next-btn" aria-label="Next Stalls Tab">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>

        <!-- Service Details Card (Interactive) -->
        <div class="stalls-details-card fade-in" id="stalls-details-panel">
            <!-- Left Column: Stall Image -->
            <div class="welcome-img-wrapper">
                <img src="https://images.unsplash.com/photo-1505576399279-565b52d4ac71?q=80&w=1000" id="stall-detail-image" alt="Festive Puja Stalls and Market" class="welcome-img" loading="lazy">
            </div>

            <!-- Right Column: Stall Info details -->
            <div>
                <span class="welcome-subtitle">STALL DETAILS</span>
                <h2 class="welcome-title" id="stall-detail-title" style="font-size: 2.2rem; margin-bottom: 1.2rem;">Food & Snacks Stalls</h2>
                <p class="welcome-text" id="stall-detail-short" style="font-size: 1.05rem; font-weight: 500; color: var(--text-muted);">
                    Serve traditional Bengali delicacies, hot savories, and traditional sweets to thousands of visitors.
                </p>
                
                <!-- 3 Key points -->
                <div class="stalls-bullets-list" id="stall-detail-bullets">
                    <div class="stall-bullet-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>High footfall area near the main dining zone</span>
                    </div>
                    <div class="stall-bullet-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Basic electricity and waste disposal included</span>
                    </div>
                    <div class="stall-bullet-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Dedicated dining seating tables for visitors</span>
                    </div>
                </div>

                <!-- Detailed Description (Expandable and Scrollable internally) -->
                <div class="stall-text-container" id="stall-text-container">
                    <p class="welcome-text" id="stall-detail-long">
                        Food stalls are the busiest section of our Durga Puja festival. Vendors can offer iconic snacks like Phuchka, Kathi Rolls, Mughlai Paratha, fish cutlets, as well as sweets like Roshogolla and Mishti Doi. Booking a food stall ensures direct visibility to over 5,000 daily visitors who attend the cultural and prayer sessions. Additionally, food safety inspection protocols are maintained during the five days to ensure clean operations. Each food stall is situated in a high-density dining zone with access to dedicated cleaning staff, garbage disposal systems, and shared dining tables. This is Noida's premier culinary event, attracting families and food lovers from all across the NCR region for five nights.
                    </p>
                </div>

                <div class="stalls-actions">
                    <button type="button" class="btn btn-secondary" id="stall-read-more-btn">Read More <i class="fa-solid fa-chevron-down"></i></button>
                    <a href="contact.php" class="btn btn-primary" style="background-color: var(--vermilion); border-color: var(--vermilion); box-shadow: 0 4px 15px rgba(200, 59, 45, 0.25);">Enquire About Stalls <i class="fa-regular fa-envelope"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GALLERY PREVIEW SECTION (DURGA PUJA ONLY) -->
<section class="gallery-preview-section">
    <div class="container">
        <div class="section-header">
            <h2>Moments of Durga Puja</h2>
            <p class="section-subtitle">A beautiful collection of Maa Durga’s divine moments, vibrant pandal decorations, cultural celebrations, and cherished memories shared together.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="gallery-carousel-wrapper">
            <button class="gallery-ctrl-btn gallery-ctrl-prev" id="gallery-prev" aria-label="Previous Slide"><i class="fa-solid fa-chevron-left"></i></button>
            
            <div class="gallery-carousel-track-container">
                <div class="gallery-carousel-track" id="gallery-track">
                    <?php foreach ($durga_puja_gallery as $idx => $g_img): ?>
                        <div class="gallery-item" data-index="<?php echo $idx; ?>">
                            <img src="<?php echo $g_img['image']; ?>" alt="<?php echo $g_img['title']; ?>" class="gallery-item-img" loading="lazy">
                            <div class="gallery-item-overlay">
                                <h4 class="gallery-item-title"><?php echo $g_img['title']; ?></h4>
                                <span class="gallery-item-cat"><?php echo $g_img['category']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <button class="gallery-ctrl-btn gallery-ctrl-next" id="gallery-next" aria-label="Next Slide"><i class="fa-solid fa-chevron-right"></i></button>
        </div>

        <div class="gallery-footer">
            <a href="gallery.php?category=durga-puja" class="btn btn-secondary">View Full Gallery <i class="fa-solid fa-image"></i></a>
        </div>
    </div>
</section>

<!-- 6. DURGA PUJA FAQ SECTION -->
<section class="faq-section" id="puja-faq">
    <div class="container faq-max-width">
        <div class="section-header">
            <span class="welcome-subtitle">COMMON QUERIES</span>
            <h2>Durga Puja FAQ</h2>
            <p class="section-subtitle">Find key information about attending, volunteering, and participating in our Durga Puja celebration.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="faq-accordion">
            <!-- FAQ 1 -->
            <div class="faq-item">
                <div class="faq-question">
                    <span>When is Durga Puja celebrated?</span>
                    <div class="faq-icon"><i class="fa-solid fa-plus"></i></div>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        <p>Durga Puja is celebrated in the Hindu month of Ashwin (typically falling in September or October). The dates vary each year based on the traditional lunar calendar. The festival spans five days: Sasthi, Saptami, Ashtami, Navami, and Dashami.</p>
                    </div>
                </div>
            </div>
            <!-- FAQ 2 -->
            <div class="faq-item">
                <div class="faq-question">
                    <span>What programs are organized during Durga Puja?</span>
                    <div class="faq-icon"><i class="fa-solid fa-plus"></i></div>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        <p>Our celebration features daily prayer rituals, pushpanjali, evening sandhya aarati with dhak beats, distribution of bhog prasad, cultural events (dramas, choreographies, choirs), traditional dhunuchi dance competition, Sindoor Khela on Dashami, and the final idol immersion (Visarjan).</p>
                    </div>
                </div>
            </div>
            <!-- FAQ 3 -->
            <div class="faq-item">
                <div class="faq-question">
                    <span>Is everyone welcome to attend the Durga Puja celebration?</span>
                    <div class="faq-icon"><i class="fa-solid fa-plus"></i></div>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        <p>Yes, absolutely! Durga Puja is open to everyone, regardless of background, community, or region. We welcome all families, residents, and visitors to join the prayers, rituals, dinners, and cultural evenings.</p>
                    </div>
                </div>
            </div>
            <!-- FAQ 4 -->
            <div class="faq-item">
                <div class="faq-question">
                    <span>Is Bhog available during the Puja?</span>
                    <div class="faq-icon"><i class="fa-solid fa-plus"></i></div>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        <p>Yes. Consecrated hot Bhog distribution (typically consisting of khichuri, mixed vegetable labra, sweet chutney, papad, and payesh) is served to all visitors during the afternoons of Saptami, Ashtami, and Navami at our designated distribution counters.</p>
                    </div>
                </div>
            </div>
            <!-- FAQ 5 -->
            <div class="faq-item">
                <div class="faq-question">
                    <span>Are cultural programs organized during the Puja?</span>
                    <div class="faq-icon"><i class="fa-solid fa-plus"></i></div>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        <p>Yes, cultural programs are held every evening from Sasthi to Navami. These feature performances by our in-house children and members, as well as guest recitals by renowned singers, classical musicians, and dance troupes from other regions.</p>
                    </div>
                </div>
            </div>
            <!-- FAQ 6 -->
            <div class="faq-item">
                <div class="faq-question">
                    <span>Are stalls available during Durga Puja?</span>
                    <div class="faq-icon"><i class="fa-solid fa-plus"></i></div>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        <p>Yes, we offer commercial space for setting up temporary stalls during the five days of celebration. This is an excellent opportunity for food vendors, clothing merchants, toy shops, and local artisans. Stalls must be booked in advance by submitting an inquiry on our Contact page.</p>
                    </div>
                </div>
            </div>
            <!-- FAQ 7 -->
            <div class="faq-item">
                <div class="faq-question">
                    <span>How can I participate in the Durga Puja activities?</span>
                    <div class="faq-icon"><i class="fa-solid fa-plus"></i></div>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        <p>Members can sign up for cultural rehearsals or register as volunteers for coordinating safety, food counters, decorations, and welcoming guests. You can fill out a volunteer form or contact any executive committee member to register.</p>
                    </div>
                </div>
            </div>
            <!-- FAQ 8 -->
            <div class="faq-item">
                <div class="faq-question">
                    <span>Where is the Durga Puja celebration held?</span>
                    <div class="faq-icon"><i class="fa-solid fa-plus"></i></div>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        <p>The Durga Puja celebration takes place at our permanent festival grounds located in B-Block Ground (behind Fortis Hospital), Sector 62, Noida. Secure parking and facility arrangements are set up for all attendees.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 6B. JOIN OUR ASSOCIATION CTA -->
<section class="join-cta-section" style="background-image: url('https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=1600');">
    <div class="join-cta-container">
        <h2 class="join-cta-title">Join Our Association</h2>
        <p class="join-cta-text">Be a part of our warm community and help us celebrate, preserve and promote Bengali culture and traditions. Expand your circle, support charity, and give your kids a platform to learn their heritage.</p>
        <div class="join-cta-buttons">
            <a href="tel:+919876543210" class="btn btn-gold"><i class="fa-solid fa-phone"></i> Call Us Directly</a>
            <a href="https://wa.me/919876543210" target="_blank" class="btn btn-primary" style="background-color: #25D366; border-color: #25D366; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.25);"><i class="fa-brands fa-whatsapp"></i> WhatsApp Us</a>
        </div>
    </div>
</section>



<!-- Vanilla JS Page Script behaviors -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // ==========================================================================
        // A. HERO CAROUSEL INTERACTION
        // ==========================================================================
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.carousel-dot');
        const prevBtn = document.querySelector('.carousel-btn-prev');
        const nextBtn = document.querySelector('.carousel-btn-next');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            currentSlide = (index + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        function startSlideShow() {
            slideInterval = setInterval(nextSlide, 6000);
        }

        function stopSlideShow() {
            clearInterval(slideInterval);
        }

        if (slides.length > 0) {
            prevBtn.addEventListener('click', () => {
                prevSlide();
                stopSlideShow();
                startSlideShow();
            });

            nextBtn.addEventListener('click', () => {
                nextSlide();
                stopSlideShow();
                startSlideShow();
            });

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                    stopSlideShow();
                    startSlideShow();
                });
            });

            startSlideShow();
        }

        // ==========================================================================
        // B. JOURNEY OF PUJO: TIMELINE INTERACTION
        // ==========================================================================
        const timelineNodes = document.querySelectorAll('.timeline-node');
        const progressLine = document.querySelector('.timeline-progress');

        function updateTimelineProgress(activeIndex) {
            if (progressLine && window.innerWidth > 768) {
                // There are 5 nodes, index 0 to 4.
                // The progress percentage should span from 0% (at node 0) to 100% (at node 4).
                const percentage = (activeIndex / (timelineNodes.length - 1)) * 100;
                progressLine.style.width = `${percentage}%`;
            }
        }

        timelineNodes.forEach((node, index) => {
            node.addEventListener('click', function() {
                timelineNodes.forEach(n => n.classList.remove('active'));
                this.classList.add('active');
                updateTimelineProgress(index);
            });
        });

        // Initialize progress line width
        const initialActiveNode = document.querySelector('.timeline-node.active');
        if (initialActiveNode) {
            const initialIndex = Array.from(timelineNodes).indexOf(initialActiveNode);
            updateTimelineProgress(initialIndex);
        }

        // Handle resize window to reset progress line logic on mobile
        window.addEventListener('resize', function() {
            const activeNode = document.querySelector('.timeline-node.active');
            if (activeNode) {
                const activeIndex = Array.from(timelineNodes).indexOf(activeNode);
                updateTimelineProgress(activeIndex);
            }
        });

        // ==========================================================================
        // B2. FADE-UP INTERSECTION OBSERVER
        // ==========================================================================
        const fadeUpElements = document.querySelectorAll('.fade-up-element');
        const fadeObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        fadeUpElements.forEach(el => {
            fadeObserver.observe(el);
        });

        // ==========================================================================
        // D. DURGA PUJA INFORMATION: INLINE READ MORE EXPANDER
        // ==========================================================================
        const pujaBtn = document.getElementById('puja-read-more-btn');
        const pujaTextContainer = document.getElementById('puja-text-container');

        if (pujaBtn && pujaTextContainer) {
            pujaBtn.addEventListener('click', function() {
                const isExpanded = pujaTextContainer.classList.contains('expanded');

                if (isExpanded) {
                    pujaTextContainer.classList.remove('expanded');
                    pujaTextContainer.style.overflowY = 'hidden';
                    pujaTextContainer.scrollTop = 0;
                    pujaBtn.innerHTML = 'Read More <i class="fa-solid fa-chevron-down"></i>';
                } else {
                    pujaTextContainer.classList.add('expanded');
                    pujaTextContainer.style.overflowY = 'auto';
                    pujaBtn.innerHTML = 'Read Less <i class="fa-solid fa-chevron-up"></i>';

                    // Programmatically scroll down by 140px (approx 5 lines of text) and stay there
                    setTimeout(() => {
                        pujaTextContainer.scrollTo({
                            top: 140,
                            behavior: 'smooth'
                        });
                    }, 300);
                }
            });
        }

        // ==========================================================================
        // D2. PUJA STALLS: INTERACTIVE TABS & SWITCHING
        // ==========================================================================
        const stallData = {
            "food": {
                title: "Food & Snacks Stalls",
                image: "https://images.unsplash.com/photo-1505576399279-565b52d4ac71?q=80&w=800",
                shortDesc: "Serve traditional Bengali delicacies, hot savories, and traditional sweets to thousands of visitors.",
                bullets: [
                    "High footfall area near the main dining zone",
                    "Basic electricity and waste disposal included",
                    "Dedicated dining seating tables for visitors"
                ],
                detailedDesc: "Food stalls are the busiest section of our Durga Puja festival. Vendors can offer iconic snacks like Phuchka, Kathi Rolls, Mughlai Paratha, fish cutlets, as well as sweets like Roshogolla and Mishti Doi. Booking a food stall ensures direct visibility to over 5,000 daily visitors who attend the cultural and prayer sessions. Additionally, food safety inspection protocols are maintained during the five days to ensure clean operations. Each food stall is situated in a high-density dining zone with access to dedicated cleaning staff, garbage disposal systems, and shared dining tables. This is Noida's premier culinary event, attracting families and food lovers from all across the NCR region for five nights."
            },
            "crafts": {
                title: "Artisan & Handicrafts Stalls",
                image: "https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=800",
                shortDesc: "Showcase hand-carved artifacts, traditional pottery, sholapith artwork, and ethnic home decor.",
                bullets: [
                    "Prime corner spots inside the main exhibition hangar",
                    "Includes lighting fixtures and standard product racks",
                    "Direct exposure to cultural art enthusiasts"
                ],
                detailedDesc: "Handicraft stalls provide local artisans and creative brands a platform to exhibit handmade artifacts, jute products, clay dolls, hand-woven carpets, and traditional paintings. Visitors love shopping for authentic cultural items during the festive shopping window, making this a highly profitable spot for craft vendors. Our dedicated volunteers offer setup guidance and basic storage help to vendors during the festival nights. The exhibition hangar is fully covered, weatherproofed, and secured overnight by local security personnel, ensuring that your valuable merchandise remains safe throughout the festival week."
            },
            "toys": {
                title: "Kids & Family Entertainment Stalls",
                image: "https://images.unsplash.com/photo-1566577134770-3d85bb3a9cc4?q=80&w=800",
                shortDesc: "Bring joy to children with balloon shooting, traditional wooden toys, books, and interactive games.",
                bullets: [
                    "Spacious setups situated close to the family play area",
                    "Flexible space for interactive game booths",
                    "High-traffic zone for parents and young kids"
                ],
                detailedDesc: "The kids stall section is filled with fun activities, interactive games, balloon popping, face painting, and a wide variety of toys. It is located near the playground, drawing a continuous stream of families throughout the day. Perfect for toy vendors, children book publishers, and puzzle game coordinators. The close proximity to kids workshops, ice cream stands, and cultural quiz stages guarantees a steady flow of young families. Stall operators are encouraged to run live demonstrations and games, which historically yield high visitor engagement and sales conversion."
            },
            "fashion": {
                title: "Festive Clothing & Ethnic Jewelry",
                image: "https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=800",
                shortDesc: "Offer designer sarees, kurtas, handloom materials, and handmade jewelry for festive wear.",
                bullets: [
                    "Premium stalls with lockable counters and hangers",
                    "Dedicated trial room access for customers",
                    "Pre-festival advertising options available"
                ],
                detailedDesc: "Durga Puja shopping is a cherished tradition. Our ethnic wear and jewelry stalls feature designers selling Jamdani sarees, Tangail handlooms, Punjabi kurtas, and traditional ornaments. Visitors look to purchase new outfits for Sasthi, Ashtami, and Sindoor Khela, creating high sales volumes during the festive week. The fashion stalls are built with solid, partition panels and have access to power points for lighting and iron fixtures. BCA Sector 62 Durga Puja is celebrated as a prime fashion hub in Noida, where residents wear their finest attire and shop for upcoming Diwali and wedding seasons."
            },
            "pooja": {
                title: "Pooja Samagri & Festive Decor",
                image: "https://images.unsplash.com/photo-1545128485-c400e7702796?q=80&w=800",
                shortDesc: "Offer premium prayer accessories, pure sandalwood, copper utensils, and traditional decorative items.",
                bullets: [
                    "Located close to the main prayer pandal entrance",
                    "Pre-built counter shelves for exhibiting items",
                    "High engagement during pushpanjali hours"
                ],
                detailedDesc: "Pooja Samagri stalls are essential for devotees attending the daily ceremonies. Vendors can offer pure ghee, organic incense sticks, brass diyas, alpana stencils, and fresh flowers. Being located right near the main entrance ensures that every devotee passing by will notice these essential prayer kits, leading to continuous sales. These stalls see peak demand in the early morning hours before Pushpanjali and during evening Aarati sessions. Vendors are also provided with dedicated shelving units to display sacred texts, prayer beads, and floral garlands cleanly."
            },
            "brands": {
                title: "Corporate Brand Promoters & Services",
                image: "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=800",
                shortDesc: "Premium promotional spaces for banks, insurance brands, real estate developers, and local businesses.",
                bullets: [
                    "Large high-visibility spaces for display booths",
                    "Includes high-speed Wi-Fi and power connections",
                    "Ideal for lead generation and brand awareness"
                ],
                detailedDesc: "Corporate and brand promotion stalls provide companies with prime real estate to interact directly with the local community. It is a perfect spot for real estate firms, banking partners, and insurance companies to run interactive contests, distribute brochures, collect customer leads, and showcase their services to Noida's most affluent resident demographic. BCA Durga Puja attracts high-profile guests, professionals, and corporate leaders, providing a high-impact branding opportunity. BCA Noida offers sponsorship banners and promotional stage announcements for premium corporate partners."
            }
        };

        const stallTabBtns = document.querySelectorAll('.stall-tab-btn');
        const stallsPanel = document.getElementById('stalls-details-panel');

        function selectStallTab(tabId) {
            // Update buttons state
            stallTabBtns.forEach(btn => {
                if (btn.getAttribute('data-tab') === tabId) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Trigger fade animation
            stallsPanel.classList.remove('fade-in');
            void stallsPanel.offsetWidth; // Trigger reflow
            stallsPanel.classList.add('fade-in');

            // Render dynamic content
            const data = stallData[tabId];
            document.getElementById('stall-detail-image').src = data.image;
            document.getElementById('stall-detail-title').innerText = data.title;
            document.getElementById('stall-detail-short').innerText = data.shortDesc;
            document.getElementById('stall-detail-long').innerText = data.detailedDesc;

            // Render bullets
            const bulletsList = document.getElementById('stall-detail-bullets');
            bulletsList.innerHTML = '';
            data.bullets.forEach(bullet => {
                const item = document.createElement('div');
                item.className = 'stall-bullet-item';
                item.innerHTML = `<i class="fa-solid fa-circle-check"></i><span>${bullet}</span>`;
                bulletsList.appendChild(item);
            });

            // Collapse scrollable desc box
            const textContainer = document.getElementById('stall-text-container');
            textContainer.classList.remove('expanded');
            textContainer.style.overflowY = 'hidden';
            textContainer.scrollTop = 0;
            document.getElementById('stall-read-more-btn').innerHTML = 'Read More <i class="fa-solid fa-chevron-down"></i>';
        }

        function scrollToActiveTab(tabId) {
            const activeBtn = document.querySelector(`.stall-tab-btn[data-tab="${tabId}"]`);
            const tabsRow = document.getElementById('stalls-tabs-row');
            if (activeBtn && tabsRow) {
                const rowWidth = tabsRow.offsetWidth;
                const btnLeft = activeBtn.offsetLeft;
                const btnWidth = activeBtn.offsetWidth;
                tabsRow.scrollTo({
                    left: btnLeft - (rowWidth / 2) + (btnWidth / 2),
                    behavior: 'smooth'
                });
            }
        }

        stallTabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                selectStallTab(tabId);
                scrollToActiveTab(tabId);
            });
        });

        // Stalls Tab Navigation buttons click triggers next/prev tab
        const prevTabBtn = document.getElementById('tabs-prev-btn');
        const nextTabBtn = document.getElementById('tabs-next-btn');
        const tabOrder = ['food', 'crafts', 'toys', 'fashion', 'pooja', 'brands'];

        function getActiveTabIndex() {
            const activeBtn = document.querySelector('.stall-tab-btn.active');
            if (activeBtn) {
                return tabOrder.indexOf(activeBtn.getAttribute('data-tab'));
            }
            return 0;
        }

        if (prevTabBtn) {
            prevTabBtn.addEventListener('click', function() {
                let currentIndex = getActiveTabIndex();
                let nextIndex = (currentIndex - 1 + tabOrder.length) % tabOrder.length;
                const nextTabId = tabOrder[nextIndex];
                selectStallTab(nextTabId);
                scrollToActiveTab(nextTabId);
            });
        }

        if (nextTabBtn) {
            nextTabBtn.addEventListener('click', function() {
                let currentIndex = getActiveTabIndex();
                let nextIndex = (currentIndex + 1) % tabOrder.length;
                const nextTabId = tabOrder[nextIndex];
                selectStallTab(nextTabId);
                scrollToActiveTab(nextTabId);
            });
        }

        // Stall details Read More toggle
        const stallReadBtn = document.getElementById('stall-read-more-btn');
        const stallTextContainer = document.getElementById('stall-text-container');

        if (stallReadBtn && stallTextContainer) {
            stallReadBtn.addEventListener('click', function() {
                const isExpanded = stallTextContainer.classList.contains('expanded');

                if (isExpanded) {
                    stallTextContainer.classList.remove('expanded');
                    stallTextContainer.style.overflowY = 'hidden';
                    stallTextContainer.scrollTop = 0;
                    stallReadBtn.innerHTML = 'Read More <i class="fa-solid fa-chevron-down"></i>';
                } else {
                    stallTextContainer.classList.add('expanded');
                    stallTextContainer.style.overflowY = 'auto';
                    stallReadBtn.innerHTML = 'Read Less <i class="fa-solid fa-chevron-up"></i>';

                    // Scroll down slightly
                    setTimeout(() => {
                        stallTextContainer.scrollTo({
                            top: 100,
                            behavior: 'smooth'
                        });
                    }, 300);
                }
            });
        }

        // ==========================================================================
        // E. FAQ ACCORDION: SINGLE ACTIVE SELECTION
        // ==========================================================================
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');
            
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                
                // Close all other accordion items
                faqItems.forEach(otherItem => {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.faq-answer').style.maxHeight = null;
                    otherItem.querySelector('.faq-icon i').className = 'fa-solid fa-plus';
                });
                
                if (!isActive) {
                    item.classList.add('active');
                    answer.style.maxHeight = answer.scrollHeight + "px";
                    item.querySelector('.faq-icon i').className = 'fa-solid fa-minus';
                }
            });
        });

        // ==========================================================================
        // E. GALLERY CAROUSEL & LIGHTBOX
        // ==========================================================================
        const track = document.getElementById('gallery-track');
        const galleryItems = document.querySelectorAll('.gallery-item');
        const galleryPrev = document.getElementById('gallery-prev');
        const galleryNext = document.getElementById('gallery-next');
        let galleryIndex = 0;

        function getItemsPerSlide() {
            if (window.innerWidth <= 576) return 1;
            if (window.innerWidth <= 991) return 2;
            if (window.innerWidth <= 1200) return 3;
            return 4;
        }

        function updateGalleryPosition() {
            if (galleryItems.length === 0) return;
            const itemWidth = galleryItems[0].getBoundingClientRect().width;
            const gap = 24; // 1.5rem
            const itemsPerSlide = getItemsPerSlide();
            const maxIndex = galleryItems.length - itemsPerSlide;
            
            if (galleryIndex > maxIndex) galleryIndex = maxIndex;
            if (galleryIndex < 0) galleryIndex = 0;

            // Toggle buttons active/disabled
            galleryPrev.disabled = (galleryIndex === 0);
            galleryNext.disabled = (galleryIndex === maxIndex);

            const amountToMove = galleryIndex * (itemWidth + gap);
            track.style.transform = `translateX(-${amountToMove}px)`;
        }

        if (galleryItems.length > 0) {
            galleryNext.addEventListener('click', () => {
                const itemsPerSlide = getItemsPerSlide();
                if (galleryIndex < galleryItems.length - itemsPerSlide) {
                    galleryIndex++;
                    updateGalleryPosition();
                }
            });

            galleryPrev.addEventListener('click', () => {
                if (galleryIndex > 0) {
                    galleryIndex--;
                    updateGalleryPosition();
                }
            });

            window.addEventListener('resize', updateGalleryPosition);
            updateGalleryPosition();
        }

        // Lightbox Functionality
        const lightbox = document.getElementById('lightbox-overlay');
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxCaption = document.getElementById('lightbox-caption');
        const lightboxClose = document.getElementById('lightbox-close');
        const lightboxPrevBtn = document.getElementById('lightbox-prev');
        const lightboxNextBtn = document.getElementById('lightbox-next');
        let currentLightboxIdx = 0;

        // Build lists of images from the rendered gallery elements
        const imagesData = [];
        galleryItems.forEach((item, idx) => {
            const img = item.querySelector('.gallery-item-img');
            const title = item.querySelector('.gallery-item-title').innerText;
            imagesData.push({ src: img.src, caption: title });

            item.addEventListener('click', () => {
                openLightbox(idx);
            });
        });

        function openLightbox(index) {
            if (index < 0 || index >= imagesData.length) return;
            currentLightboxIdx = index;
            lightboxImg.src = imagesData[index].src;
            lightboxCaption.innerText = imagesData[index].caption;
            lightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightbox.classList.remove('open');
            document.body.style.overflow = '';
        }

        function prevLightbox() {
            if (imagesData.length === 0) return;
            currentLightboxIdx = (currentLightboxIdx - 1 + imagesData.length) % imagesData.length;
            openLightbox(currentLightboxIdx);
        }

        function nextLightbox() {
            if (imagesData.length === 0) return;
            currentLightboxIdx = (currentLightboxIdx + 1) % imagesData.length;
            openLightbox(currentLightboxIdx);
        }

        if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
        if (lightboxPrevBtn) lightboxPrevBtn.addEventListener('click', prevLightbox);
        if (lightboxNextBtn) lightboxNextBtn.addEventListener('click', nextLightbox);
        
        if (lightbox) {
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox) {
                    closeLightbox();
                }
            });
        }

        // Keyboard controls for lightbox
        document.addEventListener('keydown', function(e) {
            if (lightbox && lightbox.classList.contains('open')) {
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') prevLightbox();
                if (e.key === 'ArrowRight') nextLightbox();
            }
        });
    });
</script>

<!-- LIGHTBOX MODAL CONTAINER -->
<div class="modal-overlay" id="lightbox-overlay">
    <button class="lightbox-nav-btn lightbox-prev" id="lightbox-prev" aria-label="Previous Image"><i class="fa-solid fa-chevron-left"></i></button>
    <div class="modal-card lightbox-card">
        <button class="modal-close-btn" id="lightbox-close" aria-label="Close Lightbox" style="background-color: var(--white); color: var(--dark);"><i class="fa-solid fa-xmark"></i></button>
        <img src="" alt="Lightbox View" class="lightbox-img" id="lightbox-img">
        <div class="lightbox-caption" id="lightbox-caption">Caption Text</div>
    </div>
    <button class="lightbox-nav-btn lightbox-next" id="lightbox-next" aria-label="Next Image"><i class="fa-solid fa-chevron-right"></i></button>
</div>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
