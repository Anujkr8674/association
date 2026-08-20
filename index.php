<?php
// Include the shared header and db config
include 'includes/header.php';
require_once 'config.php';

// Static Data Arrays with high-quality Unsplash URLs for immediate loading
$hero_slides = [
    [
        'image' => 'https://images.unsplash.com/photo-1561376399-5ef8d0859942?q=80&w=1600',
        'title' => 'Durga Puja Celebrations',
        'subtitle' => 'Celebrating Culture, Preserving Tradition, Connecting Community'
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=1600',
        'title' => 'Vibrant Cultural Programs',
        'subtitle' => 'Nurturing Art, Music, Dance, and Literature Across Generations'
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1600',
        'title' => 'United Community Gathering',
        'subtitle' => 'Fostering Bonding, Togetherness, and Social Development'
    ]
];

$events = [
    [
        'id' => 1,
        'title' => 'Durga Puja 2026',
        'category' => 'Puja & Festival',
        'date' => '15 - 19 October 2026',
        'time' => '10:00 AM - 11:30 PM',
        'location' => 'Association Festival Grounds',
        'image' => 'https://images.unsplash.com/photo-1561376399-5ef8d0859942?q=80&w=600',
        'excerpt' => 'Join us for Bengal\'s grandest festival featuring exquisite idols, food stalls, and traditional ceremonies.',
        'info' => 'Celebrate the triumph of good over evil. The five-day festival includes Anjali, Bhog distribution, sandhi puja, and dhunuchi dance. Cultural evenings will feature renowned artists.'
    ],
    [
        'id' => 2,
        'title' => 'Diwali & Kali Puja',
        'category' => 'Puja & Festival',
        'date' => '8 November 2026',
        'time' => '06:00 PM - 12:00 AM',
        'location' => 'Community Hall',
        'image' => 'https://images.unsplash.com/photo-1605152276897-4f618f831968?q=80&w=600',
        'excerpt' => 'A festival of lights celebrated with traditional Shyama Puja and a spectacular decorative lighting setup.',
        'info' => 'Experience a magical night lit by thousands of clay lamps (diyas). We will perform Kali Puja followed by a fireworks display and a traditional community dinner.'
    ],
    [
        'id' => 3,
        'title' => 'Saraswati Puja',
        'category' => 'Puja & Festival',
        'date' => '13 February 2026',
        'time' => '09:00 AM - 04:00 PM',
        'location' => 'Association Center',
        'image' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?q=80&w=600',
        'excerpt' => 'Worshipping the Goddess of Wisdom and Art, featuring children\'s cultural debuts (Hatey Khori).',
        'info' => 'Worship Saraswati, the patron of knowledge. Children start writing their first alphabets here. Students display books, and we serve traditional yellow khichuri bhog.'
    ],
    [
        'id' => 4,
        'title' => 'Poila Boishakh Celebration',
        'category' => 'Cultural Night',
        'date' => '14 April 2026',
        'time' => '05:30 PM - 09:30 PM',
        'location' => 'Town Auditorium',
        'image' => 'https://images.unsplash.com/photo-1505576399279-565b52d4ac71?q=80&w=600',
        'excerpt' => 'Welcoming the Bengali New Year 1433 with music, poetry, folk dance, and a feast of authentic delicacies.',
        'info' => 'Nabo Barsho celebration kicks off with a morning procession (Prabhat Pheri), followed by Rabindra Sangeet recital, traditional drama, and an authentic Bengali buffet.'
    ],
    [
        'id' => 5,
        'title' => 'Rabindra Jayanti Tribute',
        'category' => 'Cultural Night',
        'date' => '8 May 2026',
        'time' => '06:00 PM - 09:00 PM',
        'location' => 'Association Library Hall',
        'image' => 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?q=80&w=600',
        'excerpt' => 'A cultural evening paying homage to Nobel Laureate Rabindranath Tagore on his birth anniversary.',
        'info' => 'Members and children perform selected songs, dance dramas, and poetry recitations written by Tagore, reflecting on his timeless philosophy.'
    ],
    [
        'id' => 6,
        'title' => 'Annual Community Picnic',
        'category' => 'Social Gathering',
        'date' => '20 December 2026',
        'time' => '08:00 AM - 05:00 PM',
        'location' => 'Botanical Eco-Park',
        'image' => 'https://images.unsplash.com/photo-1526218626217-dc65a29bb444?q=80&w=600',
        'excerpt' => 'A fun-filled day out with sports activities, music, quizzes, and delicious outdoor cooking.',
        'info' => 'An informal outdoor day of networking and relaxation. Includes matches of cricket/badminton, fun games for children and elders, musical jam sessions, and a grand lunch.'
    ]
];

$gallery_images_fallback = [
    ['image' => 'https://images.unsplash.com/photo-1561376399-5ef8d0859942?q=80&w=600', 'title' => 'Sindur Khela on Dashami', 'category' => 'Durga Puja'],
    ['image' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?q=80&w=600', 'title' => 'Children performing Rabindra Nritya', 'category' => 'Cultural Program'],
    ['image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=600', 'title' => 'Bhog Distribution to Community', 'category' => 'Festivals'],
    ['image' => 'https://images.unsplash.com/photo-1601050690597-df056fb4ce78?q=80&w=600', 'title' => 'Dhunuchi Dance Competition', 'category' => 'Durga Puja'],
    ['image' => 'https://images.unsplash.com/photo-1590073844006-33379778ae09?q=80&w=600', 'title' => 'Alpona Floor Art Workshop', 'category' => 'Community'],
    ['image' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=600', 'title' => 'Bengali Drama Performance', 'category' => 'Cultural Program']
];

$blogs_fallback = [
    [
        'id' => 1,
        'image' => 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?q=80&w=600',
        'category' => 'Heritage',
        'date' => 'July 15, 2026',
        'title' => 'Preserving Bengali Culture in Modern Times',
        'excerpt' => 'How diaspora communities keep traditional practices alive for the next generation.'
    ],
    [
        'id' => 2,
        'image' => 'https://images.unsplash.com/photo-1502134249126-9f3755a50d78?q=80&w=600',
        'category' => 'Ritual & History',
        'date' => 'October 02, 2026',
        'title' => 'The Significance of Durga Puja Art',
        'excerpt' => 'Exploring the craft of clay sculpting and decorative sholapith art in pandal making.'
    ],
    [
        'id' => 3,
        'image' => 'https://images.unsplash.com/photo-1536304997881-a372c179924b?q=80&w=600',
        'category' => 'Festivals',
        'date' => 'April 10, 2026',
        'title' => 'Celebrating Poila Boishakh Together',
        'excerpt' => 'The rich history behind Nababarsho and how we can celebrate it with environmental awareness.'
    ]
];

// Fetch dynamic gallery images from database
$gallery_images = [];
try {
    if (isset($pdo)) {
        $stmt_gal = $pdo->query("SELECT * FROM `gallery` ORDER BY `id` DESC LIMIT 6");
        $db_gallery = $stmt_gal->fetchAll(PDO::FETCH_ASSOC);
        foreach ($db_gallery as $g_item) {
            $gallery_images[] = [
                'image' => $g_item['image'],
                'title' => $g_item['title'],
                'category' => ucwords(strtolower(str_replace('-', ' ', $g_item['category'])))
            ];
        }
    }
} catch (PDOException $e) {
    // Fail silently
}
if (empty($gallery_images)) {
    $gallery_images = $gallery_images_fallback;
}

// Fetch dynamic blogs from database
$blogs = [];
try {
    if (isset($pdo)) {
        $stmt_blg = $pdo->query("SELECT * FROM `blogs` ORDER BY `date` DESC LIMIT 3");
        $db_blogs = $stmt_blg->fetchAll(PDO::FETCH_ASSOC);
        foreach ($db_blogs as $b_item) {
            $blogs[] = [
                'id' => $b_item['id'],
                'image' => $b_item['image'],
                'category' => ucwords(strtolower(str_replace('-', ' ', $b_item['category']))),
                'date' => date('F d, Y', strtotime($b_item['date'])),
                'title' => $b_item['title'],
                'excerpt' => $b_item['excerpt']
            ];
        }
    }
} catch (PDOException $e) {
    // Fail silently
}
if (empty($blogs)) {
    $blogs = $blogs_fallback;
}

// Fetch dynamic testimonial videos from database (limit 6)
$testimonial_videos = [];
try {
    if (isset($pdo)) {
        $stmt_vid = $pdo->query("SELECT * FROM `testimonial_videos` ORDER BY `created_at` DESC LIMIT 6");
        $db_videos = $stmt_vid->fetchAll(PDO::FETCH_ASSOC);
        foreach ($db_videos as $v_item) {
            $testimonial_videos[] = [
                'title' => $v_item['title'],
                'url' => $v_item['url']
            ];
        }
    }
} catch (PDOException $e) {
    // Fail silently
}
if (empty($testimonial_videos)) {
    $testimonial_videos = [
        ['title' => 'Sindur Khela on Dashami celebration', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ['title' => 'Anjali and Evening Aarti Highlights', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ['title' => 'Dhunuchi Dance Competition 2026', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ['title' => 'Bengali Cultural Drama Performance', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ['title' => 'Rabindra Sangeet & Recital Tribute', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
        ['title' => 'Annual Picnic & Sports Highlights', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']
    ];
}

$faqs = [
    [
        'q' => 'What is the Bengali Cultural Association?',
        'a' => 'The Bengali Cultural Association is a non-profit community group dedicated to celebrating, preserving, and promoting the rich cultural heritage, traditions, language, and values of Bengal through festivals, cultural programs, charity initiatives, and social gatherings.'
    ],
    [
        'q' => 'How can I become a member?',
        'a' => 'You can register online by visiting our "Join Us" page and filling out the membership request form. Alternatively, you can contact our Treasurer or President at any community event, or send us a WhatsApp message to get the registration package.'
    ],
    [
        'q' => 'Who is eligible to join the association?',
        'a' => 'Anyone who appreciates Bengali culture, values diversity, and wants to participate in community activities is welcome to join, regardless of their linguistic or regional background.'
    ],
    [
        'q' => 'How can I participate or perform in cultural events?',
        'a' => 'Prior to every major event (like Durga Puja or Poila Boishakh), our Cultural Secretary releases performance enrollment forms. Members can sign up their children or themselves for group dances, choirs, plays, and recitation rehearsals.'
    ],
    [
        'q' => 'How can I volunteer for the organization?',
        'a' => 'We are always looking for volunteers! You can help with pandal decoration, food distribution, guest coordination, marketing, or logistics. Simply speak to any committee member or fill out the inquiry form on our Contact page.'
    ],
    [
        'q' => 'How can I partner or sponsor events?',
        'a' => 'Local businesses, food brands, and individual patrons can purchase corporate stalls or advertise in our annual souvenirs. Please visit our Partners page or drop an email to sponsor@bengalicultural.org for our media kit.'
    ]
];

$testimonials = [
    [
        'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=100',
        'name' => 'Dr. Sourav Ganguly',
        'text' => 'Being part of this association makes us feel right at home. The Durga Puja is celebrated with so much authenticity and devotion. Highly recommended for families.',
        'date' => '2 weeks ago',
        'initials' => 'SG'
    ],
    [
        'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=100',
        'name' => 'Payel Mukherjee',
        'text' => 'The cultural events are incredibly well-managed. My children have learned Rabindra Sangeet and traditional dances here, keeping them connected to their roots.',
        'date' => '1 month ago',
        'initials' => 'PM'
    ],
    [
        'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=100',
        'name' => 'Anirban Sen',
        'text' => 'A wonderful group of people who are always supportive. Beyond festivals, their social work and community help during times of need is highly commendable.',
        'date' => '3 months ago',
        'initials' => 'AS'
    ],
    [
        'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=100',
        'name' => 'Debarati Roy',
        'text' => 'A beautiful blend of tradition and modern community management. The new generation is given a platform to express their artistic talents. Extremely proud member.',
        'date' => '4 months ago',
        'initials' => 'DR'
    ]
];
?>

<style>
    /* ==========================================================================
       1. HERO SECTION STYLES
       ========================================================================== */
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
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0) 250px);
        z-index: 3;
    }

    .hero-content {
        position: absolute;
        top: 55%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: 720px;
        text-align: center;
        z-index: 10;
        color: var(--white);
        background-color: rgba(21, 15, 13, 0.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: clamp(1.8rem, 4vw, 2.6rem) clamp(1.5rem, 4vw, 2.2rem);
        border-radius: var(--border-radius-lg);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.45);
    }

    .hero-title {
        font-size: clamp(1.6rem, 3.5vw, 2.5rem);
        font-family: var(--font-headings);
        font-weight: 700;
        margin-bottom: 1.2rem;
        color: var(--white); /* Explicitly set title color to white */
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.3s;
        text-shadow: 0 4px 15px rgba(0,0,0,0.5);
    }

    .hero-subtitle {
        font-size: clamp(0.95rem, 1.8vw, 1.2rem);
        margin-bottom: 2rem;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.5s;
        font-weight: 400;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        color: var(--secondary-bg);
    }

    .hero-buttons {
        opacity: 0;
        transform: translateY(15px);
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.7s;
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

    /* Animation trigger class */
    .carousel-slide.active .hero-title,
    .carousel-slide.active .hero-subtitle,
    .carousel-slide.active .hero-buttons {
        opacity: 1;
        transform: translateY(0);
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

    /* ==========================================================================
       2. COUNTER SECTION STYLES
       ========================================================================== */
    .counter-section {
        background-color: var(--secondary-bg);
        padding: 5.5rem 0;
        position: relative;
        border-bottom: 1px solid var(--border-color);
    }

    .counter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2.2rem;
    }

    .counter-card {
        background-color: var(--white);
        padding: 2.8rem 1.8rem;
        border-radius: var(--border-radius);
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-slow);
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
    }

    .counter-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--red), var(--gold));
        opacity: 0;
        transition: var(--transition);
    }

    .counter-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(201, 154, 46, 0.3);
    }

    .counter-card:hover::before {
        opacity: 1;
    }

    .counter-number {
        font-family: var(--font-headings);
        font-size: 3.2rem;
        font-weight: 700;
        color: var(--red);
        margin-bottom: 0.5rem;
        display: block;
    }

    .counter-label {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--dark);
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* ==========================================================================
       3. WELCOME SECTION STYLES
       ========================================================================== */
    .welcome-section {
        background-color: var(--primary-bg);
        padding: 8rem 0;
        position: relative;
    }

    .welcome-grid {
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

    /* Pattern overlay decoration */
    .welcome-motif {
        position: absolute;
        bottom: 20px;
        right: 20px;
        width: 80px;
        height: 80px;
        fill: var(--gold);
        opacity: 0.9;
        z-index: 2;
        filter: drop-shadow(0 2px 8px rgba(0,0,0,0.3));
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
        background: linear-gradient(to top, var(--primary-bg) 0%, rgba(255, 251, 240, 0) 100%);
        pointer-events: none;
        transition: var(--transition);
        opacity: 1;
    }

    .welcome-text-container.expanded::after {
        opacity: 0;
    }

    /* Custom scrollbar for welcome container */
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

    /* ==========================================================================
       4. EVENTS SECTION STYLES
       ========================================================================== */
    .events-section {
        background-color: var(--secondary-bg);
        padding: 8rem 0;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .events-grid-4col {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }

    .event-card {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-slow);
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 1px solid var(--border-color);
    }

    .event-img-wrapper {
        position: relative;
        overflow: hidden;
        height: 220px;
        background-color: var(--secondary-bg);
    }

    .event-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition-slow);
    }

    .event-category {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: var(--red);
        color: var(--white);
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: var(--shadow-sm);
    }

    .event-body {
        padding: 1.8rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .event-meta {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }

    .event-meta span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .event-meta i {
        color: var(--gold);
        width: 16px;
    }

    .event-card-title {
        font-size: 1.35rem;
        margin-bottom: 0.8rem;
        color: var(--dark);
        line-height: 1.3;
    }

    .event-excerpt {
        font-size: 0.9rem;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 1.8rem;
    }

    .event-cta-btn {
        margin-top: auto;
        padding: 0.8rem 0;
        text-align: center;
        background-color: var(--secondary-bg);
        color: var(--red);
        font-weight: 700;
        font-size: 0.9rem;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: var(--transition);
        border: none;
        width: 100%;
    }

    /* Hover effects for event cards */
    .event-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(201, 154, 46, 0.3);
    }

    .event-card:hover .event-img {
        transform: scale(1.08);
    }

    .event-card:hover .event-cta-btn {
        background-color: var(--red);
        color: var(--white);
        box-shadow: 0 4px 10px rgba(139, 30, 30, 0.25);
    }

    /* ==========================================================================
       5. GALLERY PREVIEW SECTION STYLES
       ========================================================================== */
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

    /* ==========================================================================
       5.1 TESTIMONIAL VIDEO PREVIEW SECTION STYLES
       ========================================================================== */
    .video-preview-section {
        background-color: var(--secondary-bg);
        padding: 8rem 0;
        overflow: hidden;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .video-carousel-wrapper {
        position: relative;
        margin-bottom: 3.5rem;
    }

    .video-carousel-track-container {
        overflow: hidden;
        padding: 10px 0;
    }

    .video-carousel-track {
        display: flex;
        transition: transform 0.5s ease-in-out;
        gap: 1.5rem;
    }

    .video-item {
        flex: 0 0 calc((100% - (3 * 1.5rem)) / 4); /* Exactly 4 visible items in the row on desktop */
        position: relative;
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        height: 200px;
        cursor: pointer;
        background-color: var(--dark);
        border: 1px solid var(--border-color);
    }

    .video-item-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.8;
        transition: var(--transition-slow);
    }

    .video-item-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(0deg, rgba(33, 26, 23, 0.9) 0%, rgba(33, 26, 23, 0.2) 80%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        padding: 1.5rem;
        transition: var(--transition);
    }

    .video-play-btn-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: var(--red);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: auto;
        margin-top: 1.5rem;
        box-shadow: var(--shadow-md);
        transition: var(--transition-slow);
        padding-left: 3px; /* visual alignment of play icon */
    }

    .video-item:hover .video-play-btn-circle {
        transform: scale(1.15);
        background-color: var(--gold);
        color: var(--dark);
    }

    .video-item-title {
        color: var(--white);
        font-family: var(--font-headings);
        font-size: 1.05rem;
        margin-top: 1rem;
        text-align: center;
        line-height: 1.4;
        transition: var(--transition-slow);
    }

    .video-item:hover .video-item-img {
        transform: scale(1.08);
        opacity: 0.9;
    }

    /* Controls */
    .video-ctrl-btn {
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

    .video-ctrl-btn:hover {
        background-color: var(--red);
        color: var(--white);
        box-shadow: 0 4px 15px rgba(139, 30, 30, 0.3);
    }

    .video-ctrl-prev { left: -25px; }
    .video-ctrl-next { right: -25px; }

    .video-ctrl-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .video-footer {
        text-align: center;
    }

    /* Responsive media queries */
    @media (max-width: 1200px) {
        .video-item {
            flex: 0 0 calc((100% - (2 * 1.5rem)) / 3); /* 3 items visible */
        }
    }
    @media (max-width: 768px) {
        .video-item {
            flex: 0 0 calc((100% - (1 * 1.5rem)) / 2); /* 2 items visible */
        }
        .video-ctrl-prev { left: -10px; }
        .video-ctrl-next { right: -10px; }
    }
    @media (max-width: 480px) {
        .video-item {
            flex: 0 0 100%; /* 1 item visible */
        }
    }

    /* ==========================================================================
       6. BLOG PREVIEW SECTION STYLES
       ========================================================================== */
    .blogs-section {
        background-color: var(--primary-bg);
        padding: 8rem 0;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .blogs-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-bottom: 4.5rem;
    }

    .blog-card {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-slow);
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 1px solid var(--border-color);
    }

    .blog-img-wrapper {
        height: 230px;
        overflow: hidden;
    }

    .blog-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition-slow);
    }

    .blog-card-body {
        padding: 1.8rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .blog-card-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.8rem;
    }

    .blog-card-meta span {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .blog-card-meta i {
        color: var(--gold);
    }

    .blog-card-title {
        font-size: 1.35rem;
        margin-bottom: 0.8rem;
        line-height: 1.35;
        color: var(--dark);
    }

    .blog-card-excerpt {
        font-size: 0.9rem;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 1.8rem;
    }

    .blog-card-link {
        margin-top: auto;
        color: var(--red);
        font-weight: 700;
        font-size: 0.92rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .blog-card-link:hover {
        color: var(--vermilion);
    }

    .blog-card-link:hover i {
        transform: translateX(4px);
    }

    .blog-card-link i {
        transition: var(--transition);
        font-size: 0.75rem;
    }

    .blog-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(201, 154, 46, 0.3);
    }

    .blog-card:hover .blog-card-img {
        transform: scale(1.06);
    }

    .blogs-footer {
        text-align: center;
    }

    /* ==========================================================================
       7. FAQ SECTION STYLES (ACCORDION)
       ========================================================================== */
    .faq-section {
        background-color: var(--primary-bg);
        padding: 8rem 0;
    }

    .faq-max-width {
        max-width: 850px;
        margin: 0 auto;
    }

    .faq-accordion {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .faq-item {
        background-color: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: var(--transition);
    }

    .faq-question {
        padding: 1.4rem 2.2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-weight: 600;
        font-size: 1.1rem;
        color: var(--dark);
        transition: var(--transition);
        user-select: none;
    }

    .faq-question:hover {
        background-color: var(--secondary-bg);
        color: var(--red);
    }

    .faq-icon {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background-color: var(--secondary-bg);
        color: var(--red);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        font-size: 0.75rem;
    }

    .faq-item.active .faq-icon {
        background-color: var(--red);
        color: var(--white);
        transform: rotate(180deg);
    }

    .faq-item.active .faq-question {
        border-bottom: 1px solid var(--border-color);
        background-color: var(--secondary-bg);
        color: var(--red);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out;
    }

    .faq-answer-inner {
        padding: 1.8rem 2.2rem;
        font-size: 0.95rem;
        line-height: 1.7;
        color: var(--text-muted);
        background-color: var(--white);
    }

    /* ==========================================================================
       8. TESTIMONIALS SECTION STYLES
       ========================================================================== */
    .testimonials-section {
        background-color: var(--secondary-bg);
        padding: 8rem 0;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .testimonial-carousel-container {
        position: relative;
        margin-bottom: 3.5rem;
    }

    .testimonial-track-container {
        overflow: hidden;
        padding: 10px 0;
    }

    .testimonial-track {
        display: flex;
        transition: transform 0.5s ease-in-out;
        gap: 2rem;
    }

    .testimonial-card {
        flex: 0 0 calc((100% - (2 * 2rem)) / 3); /* 3 visible items in the row on desktop */
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        padding: 2.2rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        height: 100%;
        min-height: 310px;
        position: relative;
        border: 1px solid var(--border-color);
    }

    /* Google Review badge styling */
    .review-google-badge {
        position: absolute;
        top: 22px;
        right: 22px;
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #4285F4;
    }

    .review-google-badge i {
        font-size: 0.85rem;
    }

    .testimonial-stars {
        color: var(--gold);
        display: flex;
        gap: 0.2rem;
    }

    .testimonial-text {
        font-size: 0.88rem;
        line-height: 1.65;
        color: var(--text-muted);
        flex-grow: 1;
        font-style: italic;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        border-top: 1px solid var(--border-color);
        padding-top: 1.2rem;
    }

    .testimonial-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: var(--shadow-sm);
        border: 2px solid var(--secondary-bg);
    }

    .testimonial-info {
        display: flex;
        flex-direction: column;
    }

    .testimonial-name {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--dark);
    }

    .testimonial-date {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .testimonial-carousel-container {
        position: relative;
        padding: 0 50px;
    }

    .testimonial-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background-color: var(--white);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark);
        cursor: pointer;
        transition: var(--transition);
        z-index: 10;
    }

    .testimonial-arrow:hover {
        background-color: var(--red);
        color: var(--white);
        border-color: var(--red);
        box-shadow: var(--shadow-md);
    }

    .testimonial-arrow-prev {
        left: -10px;
    }

    .testimonial-arrow-next {
        right: -10px;
    }

    @media (max-width: 768px) {
        .testimonial-carousel-container {
            padding: 0 0;
        }
        .testimonial-arrow {
            display: none;
        }
    }

    /* Navigation dots */
    .testimonial-dots {
        display: flex;
        justify-content: center;
        gap: 0.6rem;
        margin-top: 2rem;
    }

    .testimonial-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: rgba(33, 26, 23, 0.15);
        border: none;
        cursor: pointer;
        transition: var(--transition);
    }

    .testimonial-dot.active {
        background-color: var(--red);
        transform: scale(1.2);
    }

    .testimonials-footer {
        text-align: center;
    }

    /* ==========================================================================
       9. JOIN OUR ASSOCIATION CTA STYLES
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

    /* ==========================================================================
       10. COMMON DYNAMIC DIALOG / MODAL (EVENTS & LIGHTBOX)
       ========================================================================== */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(33, 26, 23, 0.85);
        backdrop-filter: blur(5px);
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
        max-width: 700px;
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

    .modal-img-wrapper {
        height: 300px;
        background-color: var(--secondary-bg);
    }

    .modal-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .modal-body {
        padding: 2.5rem;
    }

    .modal-title {
        font-size: 1.8rem;
        margin-bottom: 1rem;
        color: var(--red);
    }

    .modal-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        padding: 1.25rem;
        background-color: var(--secondary-bg);
        border-radius: var(--border-radius);
        margin-bottom: 1.8rem;
        font-size: 0.9rem;
        border: 1px solid var(--border-color);
    }

    .modal-meta-item {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        color: var(--text-muted);
    }

    .modal-meta-item i {
        color: var(--red);
    }

    .modal-desc {
        line-height: 1.75;
        color: var(--text-muted);
    }

    /* Lightbox Modal specific overrides */
    .lightbox-card {
        background-color: transparent;
        box-shadow: none;
        border: none;
        max-width: 950px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .lightbox-img {
        max-height: 75vh;
        max-width: 100%;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: var(--shadow-lg);
        border: 4px solid var(--white);
    }

    .lightbox-caption {
        color: var(--white);
        margin-top: 1.25rem;
        font-family: var(--font-headings);
        font-size: 1.3rem;
        text-align: center;
        text-shadow: 0 2px 8px rgba(0,0,0,0.8);
    }

    .lightbox-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--white);
        font-size: 2.5rem;
        cursor: pointer;
        z-index: 10001;
        transition: var(--transition);
        opacity: 0.6;
    }

    .lightbox-nav-btn:hover {
        opacity: 1;
        color: var(--gold);
    }

    .lightbox-prev { left: 15px; }
    .lightbox-next { right: 15px; }

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS (index.php specific grids)
       ========================================================================== */
    @media (max-width: 1200px) {
        .events-grid-4col {
            grid-template-columns: repeat(3, 1fr);
        }
        .gallery-item {
            flex: 0 0 calc((100% - (2 * 1.5rem)) / 3); /* 3 visible on smaller desktop */
        }
        .testimonial-card {
            flex: 0 0 calc((100% - (2 * 2rem)) / 3); /* 3 visible */
        }
    }

    @media (max-width: 991px) {
        .welcome-grid {
            grid-template-columns: 1fr;
            gap: 3rem;
        }
        .welcome-img {
            height: 380px;
        }
        .counter-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        .events-grid-4col {
            grid-template-columns: repeat(2, 1fr);
        }
        .blogs-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .gallery-item {
            flex: 0 0 calc((100% - (1 * 1.5rem)) / 2); /* 2 visible on tablet */
        }
        .testimonial-card {
            flex: 0 0 calc((100% - (1 * 2rem)) / 2); /* 2 visible */
        }
        .carousel-btn {
            width: 44px;
            height: 44px;
            font-size: 1rem;
        }
        .carousel-btn-prev { left: 15px; }
        .carousel-btn-next { right: 15px; }
    }

    @media (max-width: 768px) {
        .welcome-section,
        .events-section,
        .gallery-preview-section,
        .blogs-section,
        .faq-section,
        .testimonials-section,
        .join-cta-section {
            padding: 5.5rem 0;
        }
    }

    @media (max-width: 576px) {
        .counter-grid {
            grid-template-columns: 1fr;
        }
        .events-grid-4col {
            grid-template-columns: 1fr;
        }
        .blogs-grid {
            grid-template-columns: 1fr;
        }
        .gallery-item {
            flex: 0 0 100%; /* 1 visible on mobile */
        }
        .testimonial-card {
            flex: 0 0 100%; /* 1 visible */
        }
        .join-cta-buttons {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
            padding: 0 1.5rem;
        }
        .modal-body {
            padding: 1.8rem;
        }
        .modal-meta-grid {
            grid-template-columns: 1fr;
        }
        .lightbox-nav-btn {
            font-size: 1.8rem;
        }
    }
</style>

<!-- 1. HERO CAROUSEL SECTION -->
<section class="hero-carousel" id="hero">
    <?php foreach ($hero_slides as $index => $slide): ?>
        <div class="carousel-slide <?php echo ($index == 0) ? 'active' : ''; ?>" style="background-image: url('<?php echo $slide['image']; ?>');">
            <div class="hero-content">
                <h1 class="hero-title"><?php echo $slide['title']; ?></h1>
                <p class="hero-subtitle"><?php echo $slide['subtitle']; ?></p>
                <div class="hero-buttons">
                    <a href="#events" class="btn btn-white">Explore Events</a>
                    <a href="join-us.php" class="btn btn-hero-secondary">Join Our Association</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Navigation buttons -->
    <button class="carousel-btn carousel-btn-prev" aria-label="Previous Slide"><i class="fa-solid fa-chevron-left"></i></button>
    <button class="carousel-btn carousel-btn-next" aria-label="Next Slide"><i class="fa-solid fa-chevron-right"></i></button>

    <!-- Pagination dots -->
    <div class="carousel-dots">
        <?php foreach ($hero_slides as $index => $slide): ?>
            <button class="carousel-dot <?php echo ($index == 0) ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>" aria-label="Go to slide <?php echo $index + 1; ?>"></button>
        <?php endforeach; ?>
    </div>
</section>

<!-- 2. COUNTER SECTION -->
<section class="counter-section">
    <div class="container">
        <div class="counter-grid">
            <div class="counter-card">
                <span class="counter-number" data-target="500">0</span>
                <span class="counter-label">Members</span>
            </div>
            <div class="counter-card">
                <span class="counter-number" data-target="50">0</span>
                <span class="counter-label">Events Hosted</span>
            </div>
            <div class="counter-card">
                <span class="counter-number" data-target="25">0</span>
                <span class="counter-label">Years of Culture</span>
            </div>
            <div class="counter-card">
                <span class="counter-number" data-target="100">0</span>
                <span class="counter-label">Activities</span>
            </div>
        </div>
    </div>
</section>

<!-- 3. WELCOME SECTION -->
<section class="welcome-section">
    <div class="container">
        <div class="welcome-grid">
            <!-- Left: Cultural image wrapper -->
            <div class="welcome-img-wrapper">
                <img src="https://images.unsplash.com/photo-1590073844006-33379778ae09?q=80&w=1000" alt="Bengali Traditional Welcome Art" class="welcome-img" loading="lazy">
                <!-- SVG traditional conch motif overlay -->
                <svg class="welcome-motif" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5zm-1.5-3.5c-.55 0-1-.45-1-1v-4c0-.55.45-1 1-1s1.45.45 1 1v4c0 .55-.45 1-1 1z"/>
                </svg>
            </div>
            <!-- Right: Introduction text -->
            <div>
                <span class="welcome-subtitle">স্বাগতম (Welcome)</span>
                <h2 class="welcome-title">Welcome to Bengali Cultural Association</h2>
               <div class="welcome-text-container">
                    <p class="welcome-text">
                        Welcome to Bengali Cultural Association, Sector 62. Ever expanding Noida was evolving cluster after cluster in small steps and Sector 62 Residential areas, Commercial Areas, Shops and Malls and large number of Multinational Companies were sprouting up. A no man’s land a few years back has started seeing hustle and bustle of large number of settlers, who came from different backgrounds of Corporates, Private and Public-Sector enterprises with immense wealth of Intellectual background and took to their new homes. This is nothing new as it happens everywhere but the rich intellectual wealth that assembled here were unmatched and there was no community that could be overlooked. People gelled slowly and a diversified culture was born who had tremendous appetite to burst the bubble and create a new energetic community. To name a few; IOC, Railways, BHEL, NTPC, PDIL, SAIL, IT Sector Companies, Telecom Sector, Mecon, Builders and Consultants, Ministry of External Affairs, (my apologies for not indicating all societies) found their nests here. By 2008 Sector 62 became a giant with several well connected and influential people of eminence started knowing each other.
                    </p>

                    <p class="welcome-text">
                        Out of this big group, a small team of Bengalis decided to launch a social group that will dedicate them in social and Community Activities. The group they proudly named Bengali Cultural Association and in 2002 with a meagre collection of Rs 30000.00 (Thirty Thousand Only), they organized a small Durga Puja amidst round the clock participation and commitment in a small Park. This group got registered as a full official group in 2003 with the Noida Authorities and got the license to spread their wings. Year after year more and more people joined and the association swelled to more than 300 members.
                    </p>

                    <p class="welcome-text">
                        The group had many energetic and creative minds who slowly added value to the program by involving families from other communities too in various rich cultural programmes, children were given platform to learn and perform, The Group supported highly talented dancing females who participated in an all Noida, NCR Competitions and won laurels and the name of Sector 62 was in everybody’s mouth. Durga Puja is not only dear to Bengalis but many communities in the East and thus got huge participation. Durga Puja having started from a small Park moved to Tot Mall Field (Later converted into a Park) and subsequently in the B Block Ground behind Fortis Hospital. In the evening hours, the best of Indian Talents are invited year after year to perform at our stages and mesmerize the crowd with their Variety Programs were not limited to Bengali but many other languages, Folk Dances which many in the North India have not even seen, were astonished and deeply appreciated. Collectively we have a footfall of more than 5000 people in the 4/5 days of the program.
                    </p>

                    <p class="welcome-text">
                        Life is not easy though. As the time passes organizing such events are becoming expensive and mind it most of the funds are managed by our in-house volunteers. Each volunteer and Executive Member passes sleepless nights. Ma Durga always comes to our assistance. Once we achieve a level of excellence is expected to better the same every year. We seek suggestions of our people. We have around 200 permanent Bengali members here but why Bengalis only, we hereby invite non-Bengalis also to come forward and participate hand in had shoulder to shoulder with us. Puja is not anyone’s property and hence if other communities join us only Sector 62 will benefit. Hence this year we shall be putting extra thought and effort on it. We need huge funds to celebrate such mega event and most of the people who started the Puja are catching on age and hence young volunteers are also requested to join us. We already have a system of registration and we shall make it more effective. Our break-even membership (Full) is about 350 but we are not able to reach that level. Those who want to know more may contact Executive Committee Members.
                    </p>

                    <p class="welcome-text">
                        We also need young legs and commitment to collect funds. Let us pursue other communities, who, I am sure, will do everything not to let down the association. In no way we are saying that things are bad but we just short of gaining the excellence. We have Bhog on Saptami, Ashtami and Navami and Vijaya Milan on Dussehra is very interesting. The Puja extends till Kali Puja and then also till Saraswati Puja. This Puja in a real sense committee Puja and we call it Sarbojonin (Means all) Durga Puja and hence there is no discrimination. Four Fun filled days. Apart from this there are several stalls are installed serving all Indian cuisine.
                    </p>

                    <p class="welcome-text">
                        We shall display this home page and invite volunteers from all communities soon. We maintain extreme decorum, hygiene and cleanliness commensurate with the Environment laws, and all new members must help us maintain a very high standard. Most of the Puja days often are working and hence it appears that it is only a retired man’s Puja but what about the talented women at home? Our group gives a very high impetus upon the house wife who should form at least quarter of our volunteer group. Anyone who has seen Gujarat, will be able to vouch for the women leadership to make the programme successful. Mind it they organize it for nine days, then why not in Sector 62. This is an open invitation to all. Highest fund collectors are felicitated and applauded.
                    </p>

                    <p class="welcome-text">
                        We all together shall make it a most optimum celebration with exchange of ideas. Language will not be any barrier. Believe it Sector 62 Puja is the biggest Puja in Noida and we shall keep it best. We must elevate out levels to steer past the best every year. Look for every opportunity to get funds, either through subscriptions or Advertisements or simple donations. This is one time when we remember our innocent childhood days asking parents to buy silly things like balloons, wooden toys, small flutes and we were so happy making noise. So, we all should become small child during the puja days and enjoy the festival to its brim. Ma Durga comes with her children hence our children must go there to greet her children.
                    </p>
                </div>
                <button type="button" class="btn btn-secondary" id="welcome-read-more-btn">Read More <i class="fa-solid fa-chevron-down"></i></button>
            </div>
        </div>
    </div>
</section>

<!-- 4. EVENTS CALENDAR SECTION -->
<?php include 'includes/calender.php'; ?>

<!-- 3. THE SPIRIT OF BENGAL SECTION -->
<section class="spirit-section" id="spirit">
    <div class="container">
        <div class="section-header">
            <span class="welcome-subtitle">OUR HERITAGE</span>
            <h2>The Spirit of Bengal</h2>
            <p class="section-subtitle">A celebration of the traditions, creativity and emotions that make Bengali culture timeless.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="spirit-grid">
            <!-- Music Card -->
            <div class="spirit-card" data-category="Music">
                <div class="spirit-card-icon"><i class="fa-solid fa-music"></i></div>
                <h3 class="spirit-card-title">Music</h3>
                <p class="spirit-card-desc">Traditional songs, Rabindra Sangeet, Nazrul Geeti, and folk tunes that define Bengal's musical landscape.</p>
                <button type="button" class="btn-read-more" data-title="Music" data-icon="fa-music" data-desc="Traditional songs, Rabindra Sangeet, Nazrul Geeti, and folk tunes (like Baul and Bhatiali) form the core of Bengal's musical landscape. During Durga Puja, the atmosphere is charged with pushpanjali chants, devotional hymns, and the daily sandhya aarati accompanied by rhythmic Dhak beats. The stage comes alive each evening with performances celebrating both classical ragas and modern tunes, keeping the community connected to their musical lineage.">Read More</button>
            </div>
            <!-- Literature Card -->
            <div class="spirit-card" data-category="Literature">
                <div class="spirit-card-icon"><i class="fa-solid fa-book-open"></i></div>
                <h3 class="spirit-card-title">Literature</h3>
                <p class="spirit-card-desc">The words of Tagore, Nazrul, and Sarat Chandra that inspire generations of readers.</p>
                <button type="button" class="btn-read-more" data-title="Literature" data-icon="fa-book-open" data-desc="Bengal has a rich literary tradition led by Nobel Laureate Rabindranath Tagore, Kazi Nazrul Islam, Bankim Chandra Chattopadhyay, and Sarat Chandra. Literature is celebrated during Durga Puja through the publication of 'Sharadiya Patrika' ( Puja special magazines), poetry recitations, literary discussions, and book exhibitions. It represents a celebration of knowledge, poetry, and storytelling.">Read More</button>
            </div>
            <!-- Dance & Arts Card -->
            <div class="spirit-card" data-category="Dance & Arts">
                <div class="spirit-card-icon"><i class="fa-solid fa-palette"></i></div>
                <h3 class="spirit-card-title">Dance & Arts</h3>
                <p class="spirit-card-desc">From Gaudiya Nritya to sholapith crafts and pandal alpana, art is a way of expression.</p>
                <button type="button" class="btn-read-more" data-title="Dance & Arts" data-icon="fa-palette" data-desc="From Gaudiya Nritya (classical dance) to traditional clay sculpting, sholapith decorations, and intricate Alpana floor art, fine art is woven into our celebrations. Durga Puja acts as a grand public art gallery where visual crafts, hand-painted backdrops, and classical dancing recitals are displayed by members and children to keep traditional aesthetics alive.">Read More</button>
            </div>
            <!-- Festivals Card -->
            <div class="spirit-card" data-category="Festivals">
                <div class="spirit-card-icon"><i class="fa-solid fa-calendar-days"></i></div>
                <h3 class="spirit-card-title">Festivals</h3>
                <p class="spirit-card-desc">Celebrating Poila Boishakh, Saraswati Puja, Kali Puja, and the grand Durga Puja.</p>
                <button type="button" class="btn-read-more" data-title="Festivals" data-icon="fa-calendar-days" data-desc="While Durga Puja is the crown jewel, the Bengali calendar is marked by various festivals celebrating seasonal changes and deities. We celebrate Poila Boishakh (Bengali New Year), Saraswati Puja (honoring the goddess of learning), Kali Puja / Diwali, and Lakshmi Puja. Each festival brings distinct rituals, songs, and food, keeping our calendar vibrant all year.">Read More</button>
            </div>
            <!-- Cuisine Card -->
            <div class="spirit-card" data-category="Bengali Cuisine">
                <div class="spirit-card-icon"><i class="fa-solid fa-bowl-food"></i></div>
                <h3 class="spirit-card-title">Bengali Cuisine</h3>
                <p class="spirit-card-desc">A culinary journey from the sacred festive Bhog to phuchkas, sweets, and savories.</p>
                <button type="button" class="btn-read-more" data-title="Bengali Cuisine" data-icon="fa-bowl-food" data-desc="Food is an integral part of Bengali culture. During Durga Puja, we serve sacred Bhog consisting of khichuri, mixed vegetables (labra), chutney, and payesh to thousands of devotees. In addition, festive stalls offer street food favorites like phuchka (pani puri), kathi rolls, and traditional Bengali sweets (roshogolla, sandesh, and mishti doi) that capture the rich culinary traditions of Bengal.">Read More</button>
            </div>
            <!-- Community Card -->
            <div class="spirit-card" data-category="Community">
                <div class="spirit-card-icon"><i class="fa-solid fa-people-group"></i></div>
                <h3 class="spirit-card-title">Community</h3>
                <p class="spirit-card-desc">Fostering bonding, social responsibility, and celebrating cultural heritage together.</p>
                <button type="button" class="btn-read-more" data-title="Community" data-icon="fa-people-group" data-desc="Durga Puja is a time of reunion and collective celebration. It is an occasion where members of all backgrounds volunteer, coordinate security, manage food distributions, and fund-raise for local charities. It builds social harmony, bridges generational gaps, and creates a welcoming space where non-Bengali residents are equally invited to participate.">Read More</button>
            </div>
        </div>
    </div>
</section>

<style>
    /* THE SPIRIT OF BENGAL */
    .spirit-section {
        background-color: var(--primary-bg);
        padding: 8rem 0;
        position: relative;
        border-bottom: 1px solid var(--border-color);
    }

    .spirit-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2.2rem;
    }

    .spirit-card {
        background-color: var(--white);
        padding: 3rem 2.2rem;
        border-radius: var(--border-radius);
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-slow);
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
    }

    .spirit-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--gold);
        opacity: 0.8;
        transition: var(--transition);
    }

    .spirit-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(201, 154, 46, 0.3);
    }

    .spirit-card:hover::before {
        background: var(--red);
    }

    .spirit-card-icon {
        font-size: 2.5rem;
        color: var(--gold);
        margin-bottom: 1.5rem;
        transition: var(--transition);
    }

    .spirit-card:hover .spirit-card-icon {
        color: var(--red);
        transform: scale(1.1);
    }

    .spirit-card-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1rem;
        font-family: var(--font-headings);
    }

    .spirit-card-desc {
        font-size: 0.9rem;
        line-height: 1.6;
        color: var(--text-muted);
        margin-bottom: 1.8rem;
        flex-grow: 1;
    }

    .spirit-card .btn-read-more {
        padding: 0.6rem 1.6rem;
        font-size: 0.85rem;
        border-radius: 20px;
        background-color: var(--secondary-bg);
        color: var(--red);
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: var(--transition);
    }

    .spirit-card .btn-read-more:hover {
        background-color: var(--red);
        color: var(--white);
        box-shadow: var(--shadow-sm);
    }

    /* DIALOG / MODAL STYLES */
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

    @media (max-width: 1200px) {
        .spirit-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.8rem;
        }
    }
    @media (max-width: 768px) {
        .spirit-grid {
            grid-template-columns: 1fr;
            max-width: 440px;
            margin: 0 auto;
        }
    }
</style>

<!-- 5. GALLERY PREVIEW SECTION -->
<section class="gallery-preview-section">
    <div class="container">
        <div class="section-header">
            <h2>Moments We Shared</h2>
            <p class="section-subtitle">A glimpse into our past programs, pandal decors, and smiles.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="gallery-carousel-wrapper">
            <button class="gallery-ctrl-btn gallery-ctrl-prev" id="gallery-prev" aria-label="Previous Slide"><i class="fa-solid fa-chevron-left"></i></button>
            
            <div class="gallery-carousel-track-container">
                <div class="gallery-carousel-track" id="gallery-track">
                    <?php foreach ($gallery_images as $idx => $g_img): ?>
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
            <a href="gallery.php" class="btn btn-secondary">View Full Gallery <i class="fa-solid fa-image"></i></a>
        </div>
    </div>
</section>

<!-- 6. BLOG SECTION -->
<section class="blogs-section">
    <div class="container">
        <div class="section-header">
            <h2>From Our Community</h2>
            <p class="section-subtitle">Read thoughts, research, and stories shared by our members.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="blogs-grid">
            <?php foreach ($blogs as $blog): ?>
                <div class="blog-card">
                    <div class="blog-img-wrapper">
                        <img src="<?php echo $blog['image']; ?>" alt="<?php echo $blog['title']; ?>" class="blog-card-img" loading="lazy">
                    </div>
                    <div class="blog-card-body">
                        <div class="blog-card-meta">
                            <span><i class="fa-solid fa-tag"></i> <?php echo $blog['category']; ?></span>
                            <span><i class="fa-regular fa-calendar"></i> <?php echo $blog['date']; ?></span>
                        </div>
                        <h3 class="blog-card-title"><?php echo $blog['title']; ?></h3>
                        <p class="blog-card-excerpt"><?php echo $blog['excerpt']; ?></p>
                        <a href="blog-details.php?id=<?php echo $blog['id']; ?>" class="blog-card-link">Read More <i class="fa-solid fa-arrow-right-long"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="blogs-footer">
            <a href="blogs.php" class="btn btn-secondary">View All Blogs <i class="fa-solid fa-newspaper"></i></a>
        </div>
    </div>
</section>

<!-- TESTIMONIAL VIDEO CAROUSEL SECTION -->
<section class="video-preview-section">
    <div class="container">
        <div class="section-header">
            <h2>Celebrations in Motion</h2>
            <p class="section-subtitle">Experience the spirit of Durga Puja through joyful celebrations, soulful performances, beautiful traditions, and cherished moments together.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="video-carousel-wrapper">
            <button class="video-ctrl-btn video-ctrl-prev" id="video-prev" aria-label="Previous Slide"><i class="fa-solid fa-chevron-left"></i></button>
            
            <div class="video-carousel-track-container">
                <div class="video-carousel-track" id="video-track">
                    <?php foreach ($testimonial_videos as $idx => $v): ?>
                        <?php
                        // Extract video ID to render cover image
                        $v_id = '';
                        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $v['url'], $match)) {
                            $v_id = $match[1];
                        }
                        $cover_img = $v_id ? "https://img.youtube.com/vi/{$v_id}/mqdefault.jpg" : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=600';
                        ?>
                        <div class="video-item" data-index="<?php echo $idx; ?>" data-video-id="<?php echo htmlspecialchars($v_id); ?>">
                            <img src="<?php echo htmlspecialchars($cover_img); ?>" alt="<?php echo htmlspecialchars($v['title']); ?>" class="video-item-img" loading="lazy">
                            <div class="video-item-overlay">
                                <div class="video-play-btn-circle">
                                    <i class="fa-solid fa-play"></i>
                                </div>
                                <h4 class="video-item-title"><?php echo htmlspecialchars($v['title']); ?></h4>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <button class="video-ctrl-btn video-ctrl-next" id="video-next" aria-label="Next Slide"><i class="fa-solid fa-chevron-right"></i></button>
        </div>

        <div class="video-footer">
            <a href="videos.php" class="btn btn-secondary">View All Videos <i class="fa-solid fa-circle-play"></i></a>
        </div>
    </div>
</section>

<!-- 7. FAQ SECTION (ACCORDION) -->
<section class="faq-section">
    <div class="container faq-max-width">
        <div class="section-header">
            <h2>Frequently Asked Questions</h2>
            <p class="section-subtitle">Find fast answers to common inquiries about the association.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="faq-accordion">
            <?php foreach ($faqs as $faq): ?>
                <div class="faq-item">
                    <div class="faq-question">
                        <span><?php echo $faq['q']; ?></span>
                        <div class="faq-icon"><i class="fa-solid fa-plus"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p><?php echo $faq['a']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 8. TESTIMONIAL SECTION -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header">
            <h2>What Our Members Say</h2>
            <p class="section-subtitle">Honest feedback and ratings shared by our active community members.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="testimonial-carousel-container">
            <!-- Left Arrow -->
            <button type="button" class="testimonial-arrow testimonial-arrow-prev" id="testimonial-prev" aria-label="Previous Reviews">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="testimonial-track-container">
                <div class="testimonial-track" id="testimonial-track">
                    <?php foreach ($testimonials as $t): ?>
                        <div class="testimonial-card">
                            <div class="review-google-badge">
                                <i class="fa-brands fa-google"></i> Review
                            </div>
                            <div class="testimonial-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <p class="testimonial-text">"<?php echo $t['text']; ?>"</p>
                            <div class="testimonial-author">
                                <img src="<?php echo $t['avatar']; ?>" alt="<?php echo $t['name']; ?>" class="testimonial-avatar" loading="lazy">
                                <div class="testimonial-info">
                                    <span class="testimonial-name"><?php echo $t['name']; ?></span>
                                    <span class="testimonial-date"><?php echo $t['date']; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Arrow -->
            <button type="button" class="testimonial-arrow testimonial-arrow-next" id="testimonial-next" aria-label="Next Reviews">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            
            <div class="testimonial-dots" id="testimonial-dots">
                <!-- Dots generated in JavaScript -->
            </div>
        </div>

        <div class="testimonials-footer">
            <a href="about.php#activities" class="btn btn-secondary">View More Reviews <i class="fa-solid fa-quote-left"></i></a>
        </div>
    </div>
</section>

<!-- 9. JOIN OUR ASSOCIATION CTA -->
<section class="join-cta-section" style="background-image: url('https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=1600');">
    <div class="join-cta-container">
        <h2 class="join-cta-title">Join Our Association</h2>
        <p class="join-cta-text">Be a part of our warm community and help us celebrate, preserve and promote Bengali culture and traditions. Expand your circle, support charity, and give your kids a platform to learn their heritage.</p>
        <div class="join-cta-buttons">
            <a href="tel:+919876543210" class="btn btn-gold"><i class="fa-solid fa-phone"></i> Call Us Directly</a>
            <a href="https://wa.me/919876543210" target="_blank" class="btn btn-primary" style="background-color: #25D366; border-color: #25D366;"><i class="fa-brands fa-whatsapp"></i> WhatsApp Us</a>
        </div>
    </div>
</section>

<!-- 10. EVENTS DETAIL MODAL CONTAINER -->
<div class="modal-overlay" id="event-modal-overlay">
    <div class="modal-card">
        <button class="modal-close-btn" id="event-modal-close" aria-label="Close Details"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-img-wrapper">
            <img src="" alt="Event Detail Image" class="modal-img" id="modal-event-img">
        </div>
        <div class="modal-body">
            <h3 class="modal-title" id="modal-event-title">Event Title</h3>
            <div class="modal-meta-grid">
                <div class="modal-meta-item">
                    <i class="fa-regular fa-calendar-days"></i>
                    <div>
                        <strong>Date:</strong>
                        <div id="modal-event-date">Oct 17, 2026</div>
                    </div>
                </div>
                <div class="modal-meta-item">
                    <i class="fa-regular fa-clock"></i>
                    <div>
                        <strong>Time:</strong>
                        <div id="modal-event-time">10:00 AM - 10:00 PM</div>
                    </div>
                </div>
                <div class="modal-meta-item" style="grid-column: span 2;">
                    <i class="fa-solid fa-location-dot"></i>
                    <div>
                        <strong>Venue:</strong>
                        <div id="modal-event-venue">Association Hall</div>
                    </div>
                </div>
            </div>
            <p class="modal-desc" id="modal-event-info">Detailed information goes here.</p>
        </div>
    </div>
</div>

<!-- 11. LIGHTBOX MODAL CONTAINER -->
<div class="modal-overlay" id="lightbox-overlay">
    <button class="lightbox-nav-btn lightbox-prev" id="lightbox-prev" aria-label="Previous Image"><i class="fa-solid fa-chevron-left"></i></button>
    <div class="modal-card lightbox-card">
        <button class="modal-close-btn" id="lightbox-close" aria-label="Close Lightbox" style="background-color: var(--white); color: var(--dark);"><i class="fa-solid fa-xmark"></i></button>
        <img src="" alt="Lightbox View" class="lightbox-img" id="lightbox-img">
        <div class="lightbox-caption" id="lightbox-caption">Caption Text</div>
    </div>
    <button class="lightbox-nav-btn lightbox-next" id="lightbox-next" aria-label="Next Image"><i class="fa-solid fa-chevron-right"></i></button>
</div>

<!-- ==========================================================================
     VANILLA JAVASCRIPT LOGIC
     ========================================================================== */ -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // ==========================================================================
    // A. HERO CAROUSEL CONTROLLER
    // ==========================================================================
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    const prevBtn = document.querySelector('.carousel-btn-prev');
    const nextBtn = document.querySelector('.carousel-btn-next');
    let currentSlide = 0;
    let slideInterval;

    function showSlide(index) {
        slides[currentSlide].classList.remove('active');
        dots[currentSlide].classList.remove('active');
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
    // B. COUNTER NUMBERS ANIMATION
    // ==========================================================================
    const counters = document.querySelectorAll('.counter-number');
    const counterSpeed = 200; // The higher the slower

    const startCounter = (counter) => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const inc = target / counterSpeed;

            if (count < target) {
                counter.innerText = Math.ceil(count + inc);
                setTimeout(updateCount, 10);
            } else {
                counter.innerText = target + "+";
            }
        };
        updateCount();
    };

    // Intersection Observer to trigger counter on scroll
    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                startCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => {
        counterObserver.observe(counter);
    });

    // ==========================================================================
    // C. EVENTS DYNAMIC DETAILS MODAL
    // ==========================================================================
    const eventModal = document.getElementById('event-modal-overlay');
    const eventClose = document.getElementById('event-modal-close');
    const eventButtons = document.querySelectorAll('.view-event-btn');

    eventButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal-event-title').innerText = this.getAttribute('data-title');
            document.getElementById('modal-event-date').innerText = this.getAttribute('data-date');
            document.getElementById('modal-event-time').innerText = this.getAttribute('data-time');
            document.getElementById('modal-event-venue').innerText = this.getAttribute('data-venue');
            document.getElementById('modal-event-info').innerText = this.getAttribute('data-info');
            document.getElementById('modal-event-img').src = this.getAttribute('data-image');
            
            eventModal.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeEventModal() {
        eventModal.classList.remove('open');
        document.body.style.overflow = '';
    }

    eventClose.addEventListener('click', closeEventModal);
    eventModal.addEventListener('click', function(e) {
        if (e.target === eventModal) {
            closeEventModal();
        }
    });

    // ==========================================================================
    // D. GALLERY CAROUSEL & LIGHTBOX
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
        currentLightboxIdx = (currentLightboxIdx - 1 + imagesData.length) % imagesData.length;
        openLightbox(currentLightboxIdx);
    }

    function nextLightbox() {
        currentLightboxIdx = (currentLightboxIdx + 1) % imagesData.length;
        openLightbox(currentLightboxIdx);
    }

    lightboxClose.addEventListener('click', closeLightbox);
    lightboxPrevBtn.addEventListener('click', prevLightbox);
    lightboxNextBtn.addEventListener('click', nextLightbox);
    
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });

    // Keyboard controls for lightbox
    document.addEventListener('keydown', function(e) {
        if (lightbox.classList.contains('open')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') prevLightbox();
            if (e.key === 'ArrowRight') nextLightbox();
        }
    });

    // ==========================================================================
    // E. FAQ ACCORDION (SINGLE EXPAND)
    // ==========================================================================
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close all FAQ Items first (Single-expand requirement!)
            faqItems.forEach(otherItem => {
                otherItem.classList.remove('active');
                otherItem.querySelector('.faq-answer').style.maxHeight = null;
                otherItem.querySelector('.faq-icon i').className = 'fa-solid fa-plus';
            });
            
            // If item was not active, open it
            if (!isActive) {
                item.classList.add('active');
                answer.style.maxHeight = answer.scrollHeight + "px";
                item.querySelector('.faq-icon i').className = 'fa-solid fa-minus';
            }
        });
    });

    // ==========================================================================
    // F. TESTIMONIAL CAROUSEL
    // ==========================================================================
    const testimonialTrack = document.getElementById('testimonial-track');
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    const dotsContainer = document.getElementById('testimonial-dots');
    let testimonialIdx = 0;

    function getTestimonialColumns() {
        if (window.innerWidth <= 576) return 1;
        if (window.innerWidth <= 991) return 2;
        return 3;
    }

    function buildTestimonialDots() {
        dotsContainer.innerHTML = '';
        const cols = getTestimonialColumns();
        const numDots = Math.max(1, testimonialCards.length - cols + 1);

        for (let i = 0; i < numDots; i++) {
            const dot = document.createElement('button');
            dot.classList.add('testimonial-dot');
            if (i === 0) dot.classList.add('active');
            dot.setAttribute('aria-label', `Go to testimonial slide ${i + 1}`);
            dot.addEventListener('click', () => {
                testimonialIdx = i;
                updateTestimonials();
            });
            dotsContainer.appendChild(dot);
        }
    }

    const testimonialPrevBtn = document.getElementById('testimonial-prev');
    const testimonialNextBtn = document.getElementById('testimonial-next');

    function updateTestimonialArrowsState() {
        const cols = getTestimonialColumns();
        const maxIdx = Math.max(0, testimonialCards.length - cols);
        
        if (testimonialPrevBtn && testimonialNextBtn) {
            if (testimonialIdx <= 0) {
                testimonialPrevBtn.style.opacity = '0.35';
                testimonialPrevBtn.style.pointerEvents = 'none';
            } else {
                testimonialPrevBtn.style.opacity = '1';
                testimonialPrevBtn.style.pointerEvents = 'auto';
            }

            if (testimonialIdx >= maxIdx) {
                testimonialNextBtn.style.opacity = '0.35';
                testimonialNextBtn.style.pointerEvents = 'none';
            } else {
                testimonialNextBtn.style.opacity = '1';
                testimonialNextBtn.style.pointerEvents = 'auto';
            }
        }
    }

    function updateTestimonials() {
        const cardWidth = testimonialCards[0].getBoundingClientRect().width;
        const gap = 32; // 2rem
        const cols = getTestimonialColumns();
        const maxIdx = testimonialCards.length - cols;

        if (testimonialIdx > maxIdx) testimonialIdx = maxIdx;
        if (testimonialIdx < 0) testimonialIdx = 0;

        const moveAmount = testimonialIdx * (cardWidth + gap);
        testimonialTrack.style.transform = `translateX(-${moveAmount}px)`;

        // Update dots highlight
        const dots = dotsContainer.querySelectorAll('.testimonial-dot');
        dots.forEach((dot, i) => {
            if (i === testimonialIdx) dot.classList.add('active');
            else dot.classList.remove('active');
        });

        // Update navigation arrows active state
        updateTestimonialArrowsState();
    }

    if (testimonialCards.length > 0) {
        buildTestimonialDots();
        window.addEventListener('resize', () => {
            buildTestimonialDots();
            updateTestimonials();
        });

        // Register prev/next arrow events
        if (testimonialPrevBtn && testimonialNextBtn) {
            testimonialPrevBtn.addEventListener('click', () => {
                if (testimonialIdx > 0) {
                    testimonialIdx--;
                    updateTestimonials();
                }
            });
            testimonialNextBtn.addEventListener('click', () => {
                const cols = getTestimonialColumns();
                const maxIdx = Math.max(0, testimonialCards.length - cols);
                if (testimonialIdx < maxIdx) {
                    testimonialIdx++;
                    updateTestimonials();
                }
            });
        }

        updateTestimonials();
    }

    // Welcome Read More toggle
    const welcomeBtn = document.getElementById('welcome-read-more-btn');
    const welcomeContainer = document.querySelector('.welcome-text-container');
    
    if (welcomeBtn && welcomeContainer) {
        welcomeBtn.addEventListener('click', function () {
            const isExpanded = welcomeContainer.classList.contains('expanded');
            
            if (isExpanded) {
                welcomeContainer.classList.remove('expanded');
                welcomeContainer.style.overflowY = 'hidden';
                welcomeContainer.scrollTop = 0; // Reset scroll position to top
                welcomeBtn.innerHTML = 'Read More <i class="fa-solid fa-chevron-down"></i>';
            } else {
                welcomeContainer.classList.add('expanded');
                welcomeContainer.style.overflowY = 'auto';
                welcomeBtn.innerHTML = 'Read Less <i class="fa-solid fa-chevron-up"></i>';
                
                // Programmatically scroll down by 140px (approx 5 lines of text) and stay there
                setTimeout(() => {
                    welcomeContainer.scrollTo({
                        top: 140,
                        behavior: 'smooth'
                    });
                }, 300);
            }
        });
    }

    // ==========================================================================
    // C. THE SPIRIT OF BENGAL: CULTURAL OVERLAY MODALS
    // ==========================================================================
    const cultureModal = document.getElementById('culture-modal');
    const modalClose = document.getElementById('culture-modal-close');
    const cultureButtons = document.querySelectorAll('.spirit-card .btn-read-more');

    if (cultureModal && modalClose) {
        cultureButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const title = this.getAttribute('data-title');
                const iconClass = this.getAttribute('data-icon');
                const desc = this.getAttribute('data-desc');

                document.getElementById('modal-culture-title').innerText = title;
                document.getElementById('modal-culture-desc').innerText = desc;
                document.getElementById('modal-culture-icon-box').innerHTML = `<i class="fa-solid ${iconClass}"></i>`;
                
                cultureModal.classList.add('open');
                document.body.style.overflow = 'hidden';
            });
        });

        function closeCultureModal() {
            cultureModal.classList.remove('open');
            document.body.style.overflow = '';
        }

        modalClose.addEventListener('click', closeCultureModal);
        
        cultureModal.addEventListener('click', function(e) {
            if (e.target === cultureModal) {
                closeCultureModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (cultureModal.classList.contains('open') && e.key === 'Escape') {
                closeCultureModal();
            }
        });
    }

    // ==========================================================================
    // G. TESTIMONIAL VIDEO CAROUSEL & PLAYBACK
    // ==========================================================================
    const videoTrack = document.getElementById('video-track');
    const videoItems = document.querySelectorAll('.video-item');
    const videoPrev = document.getElementById('video-prev');
    const videoNext = document.getElementById('video-next');
    let videoIndex = 0;

    function getVideosPerSlide() {
        if (window.innerWidth <= 576) return 1;
        if (window.innerWidth <= 991) return 2;
        if (window.innerWidth <= 1200) return 3;
        return 4;
    }

    function updateVideoPosition() {
        if (videoItems.length === 0) return;
        const itemWidth = videoItems[0].getBoundingClientRect().width;
        const gap = 24; // 1.5rem
        const vPerSlide = getVideosPerSlide();
        const maxIndex = videoItems.length - vPerSlide;
        
        if (videoIndex > maxIndex) videoIndex = maxIndex;
        if (videoIndex < 0) videoIndex = 0;

        // Toggle buttons active/disabled
        if (videoPrev) videoPrev.disabled = (videoIndex === 0);
        if (videoNext) videoNext.disabled = (videoIndex === maxIndex);

        const amountToMove = videoIndex * (itemWidth + gap);
        videoTrack.style.transform = `translateX(-${amountToMove}px)`;
    }

    if (videoItems.length > 0) {
        if (videoNext) {
            videoNext.addEventListener('click', () => {
                const vPerSlide = getVideosPerSlide();
                if (videoIndex < videoItems.length - vPerSlide) {
                    videoIndex++;
                    updateVideoPosition();
                }
            });
        }

        if (videoPrev) {
            videoPrev.addEventListener('click', () => {
                if (videoIndex > 0) {
                    videoIndex--;
                    updateVideoPosition();
                }
            });
        }

        window.addEventListener('resize', updateVideoPosition);
        updateVideoPosition();
    }

    // Video Playback Lightbox Modal
    const homeVideoModal = document.getElementById('home-video-overlay');
    const homeVideoIframe = document.getElementById('home-video-iframe');
    const homeVideoClose = document.getElementById('home-video-close');

    videoItems.forEach(item => {
        item.addEventListener('click', () => {
            const vidId = item.getAttribute('data-video-id');
            if (vidId && homeVideoModal && homeVideoIframe) {
                homeVideoIframe.src = `https://www.youtube.com/embed/${vidId}?autoplay=1`;
                homeVideoModal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        });
    });

    function closeHomeVideoModal() {
        if (homeVideoModal && homeVideoIframe) {
            homeVideoModal.classList.remove('open');
            homeVideoIframe.src = '';
            document.body.style.overflow = '';
        }
    }

    if (homeVideoClose) {
        homeVideoClose.addEventListener('click', closeHomeVideoModal);
    }

    if (homeVideoModal) {
        homeVideoModal.addEventListener('click', function(e) {
            if (e.target === homeVideoModal) {
                closeHomeVideoModal();
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (homeVideoModal && homeVideoModal.classList.contains('open') && e.key === 'Escape') {
            closeHomeVideoModal();
        }
    });
});
</script>

<!-- 7. CULTURAL CARD DETAILS MODAL -->
<div class="modal-overlay" id="culture-modal">
    <div class="modal-card">
        <button class="modal-close-btn" id="culture-modal-close" aria-label="Close Modal"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-body" style="padding-top: 3.5rem;">
            <div id="modal-culture-icon-box" style="font-size: 3.5rem; color: var(--gold); text-align: center; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-music"></i>
            </div>
            <h3 class="modal-title" id="modal-culture-title" style="text-align: center; margin-bottom: 1.5rem;">Culture Title</h3>
            <div class="modal-desc" id="modal-culture-desc" style="max-height: 50vh; overflow-y: auto; padding-right: 0.5rem;">
                Detailed cultural text...
            </div>
        </div>
    </div>
</div>

<!-- TESTIMONIAL VIDEO LIGHTBOX MODAL -->
<div class="modal-overlay" id="home-video-overlay">
    <div class="modal-card" style="max-width: 800px; padding: 0; background-color: #000; border: 2px solid var(--white);">
        <button class="modal-close-btn" id="home-video-close" aria-label="Close Playback" style="background-color: var(--white); color: var(--dark); top: -20px; right: -20px;"><i class="fa-solid fa-xmark"></i></button>
        <div style="position: relative; padding-top: 56.25%; width: 100%; height: 0;">
            <iframe id="home-video-iframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
        </div>
    </div>
</div>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
