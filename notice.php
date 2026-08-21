<?php
// Include the shared header
include 'includes/header.php';

// Announcements list
$announcements = [
    [
        'id' => 1,
        'title' => 'Durga Puja Souvenir Magazine "Sharodiyo Patrika 2026" - Call for Submissions',
        'category' => 'General',
        'date_day' => '15',
        'date_month' => 'AUG',
        'date_year' => '2026',
        'tag' => 'Magazine',
        'excerpt' => 'Submit your articles, poems, short stories, and kids paintings for our annual souvenir. Deadline is September 10, 2026.',
        'full' => 'We invite all member families, children, and cultural patrons to contribute to the 2026 edition of "Sharodiyo Patrika". Submissions are accepted in both Bengali and English. We welcome short essays on heritage, poetry, travelogues, drawings, and paintings. Send your files to magazine@bengalicultural.org. Please attach your name, a short bio, and a high-resolution photo.'
    ],
    [
        'id' => 2,
        'title' => 'Annual General Meeting (AGM) 2026 Notification',
        'category' => 'Notices',
        'date_day' => '05',
        'date_month' => 'SEP',
        'date_year' => '2026',
        'tag' => 'Official Meeting',
        'excerpt' => 'All registered life and general members are requested to attend the Annual General Meeting to elect new committee members.',
        'full' => 'The Annual General Meeting of the Bengali Cultural Association will be held on Sunday, September 27, 2026, at 10:30 AM in the Association Hall. Agenda: (1) Approval of the audited accounts for FY 2025-26, (2) Secretary\'s annual operations report, (3) Election of Executive Committee members for the term 2026-2028, and (4) Discussion on Durga Puja pandal budget. Buffet lunch will be served post-adjournment.'
    ],
    [
        'id' => 3,
        'title' => 'Bengali Folk Dance Rehearsals for Durga Puja 2026',
        'category' => 'Events',
        'date_day' => '20',
        'date_month' => 'AUG',
        'date_year' => '2026',
        'tag' => 'Rehearsals',
        'excerpt' => 'Rehearsals for the primary kids group and adult ladies folk dance begin this Sunday at the community center.',
        'full' => 'Our Cultural Secretary, Shri. Arindam Das, will coordinate dance and choir practices starting August 23, 2026. Practices will occur every Saturday (04:00 PM - 06:00 PM) and Sunday (10:00 AM - 12:00 PM). Parents who wish to enroll their children (ages 6 to 15) for the Durga Puja cultural night inaugurals must register with the cultural desk before rehearsals start.'
    ],
    [
        'id' => 4,
        'title' => 'Emergency Relief Fund Support for Floods',
        'category' => 'Notices',
        'date_day' => '29',
        'date_month' => 'JUL',
        'date_year' => '2026',
        'tag' => 'Social Work',
        'excerpt' => 'The association is collecting funds and dry rations to support rural families affected by recent monsoon inundations.',
        'full' => 'In response to the severe flooding in nearby rural districts, the association is establishing a flood relief operations team. We are accepting monetary donations directly to our bank account (BCA Welfare Trust). Furthermore, clean clothes, milk packets, grains, and dry medicines can be dropped off at our main office center between 09:00 AM and 05:00 PM daily.'
    ]
];
?>

<style>
    /* ==========================================================================
       ANNOUNCEMENTS PAGE SPECIFIC STYLES
       ========================================================================== */
    .ann-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .ann-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(circle at 20% 50%, rgba(201, 154, 46, 0.15) 0%, transparent 50%),
                          radial-gradient(circle at 80% 50%, rgba(200, 59, 45, 0.15) 0%, transparent 50%);
        z-index: 1;
    }

    .ann-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .ann-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .ann-sec {
        padding: 6.5rem 0;
        background-color: var(--primary-bg);
    }

    /* Filter Toolbar */
    .ann-filters {
        display: flex;
        justify-content: center;
        gap: 0.8rem;
        margin-bottom: 4rem;
        flex-wrap: wrap;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 1.5rem;
    }

    .ann-filter-btn {
        background-color: var(--white);
        border: 2px solid var(--border-color);
        padding: 0.6rem 1.6rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--dark);
        border-radius: 30px;
        cursor: pointer;
        transition: var(--transition);
    }

    .ann-filter-btn:hover {
        background-color: var(--secondary-bg);
        border-color: var(--red);
        color: var(--red);
    }

    .ann-filter-btn.active {
        background-color: var(--red);
        border-color: var(--red);
        color: var(--white);
        box-shadow: 0 4px 10px rgba(139, 30, 30, 0.15);
    }

    /* Announcements List Stack */
    .ann-list {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
        max-width: 900px;
        margin: 0 auto;
    }

    .ann-card {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        display: flex;
        overflow: hidden;
        transition: var(--transition-slow);
    }

    .ann-card.hide {
        display: none;
    }

    .ann-card:hover {
        transform: translateX(5px);
        box-shadow: var(--shadow-md);
        border-color: rgba(201, 154, 46, 0.4);
    }

    /* Left Calendar-style block */
    .ann-date-block {
        width: 140px;
        background-color: var(--secondary-bg);
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        flex-shrink: 0;
        text-align: center;
        transition: var(--transition);
    }

    .ann-card:hover .ann-date-block {
        background-color: rgba(139, 30, 30, 0.04);
    }

    .ann-date-day {
        font-family: var(--font-headings);
        font-size: 3rem;
        font-weight: 700;
        color: var(--red);
        line-height: 1;
        margin-bottom: 0.2rem;
        display: block;
    }

    .ann-date-month {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--dark);
        letter-spacing: 1.5px;
        display: block;
        margin-bottom: 0.25rem;
    }

    .ann-date-year {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* Right content block */
    .ann-content-block {
        padding: 2.25rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .ann-header-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }

    .ann-cat-badge {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        letter-spacing: 0.5px;
        background-color: var(--secondary-bg);
        color: var(--red);
        border: 1px solid rgba(139, 30, 30, 0.1);
    }

    .ann-tag-text {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .ann-title {
        font-size: 1.4rem;
        margin-bottom: 1rem;
        line-height: 1.35;
        color: var(--dark);
    }

    .ann-excerpt {
        font-size: 0.95rem;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* Smooth expanding text block */
    .ann-full-text {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s cubic-bezier(0, 1, 0, 1); /* Quick expanding transition */
    }

    .ann-full-text-inner {
        padding-top: 1.5rem;
        margin-top: 1.5rem;
        border-top: 1px dashed var(--border-color);
        font-size: 0.95rem;
        line-height: 1.7;
        color: var(--text-muted);
    }

    .ann-more-btn {
        background: none;
        border: none;
        color: var(--red);
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 1.5rem;
        align-self: flex-start;
        transition: var(--transition);
        padding: 0.2rem 0;
    }

    .ann-more-btn:hover {
        color: var(--vermilion);
    }

    .ann-more-btn i {
        font-size: 0.75rem;
        transition: var(--transition);
    }

    .ann-card.expanded .ann-more-btn i {
        transform: rotate(180deg);
    }

    /* No items found placeholder */
    .ann-no-items {
        display: none;
        text-align: center;
        padding: 4rem 2rem;
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        width: 100%;
    }

    .ann-no-items i {
        font-size: 3rem;
        color: var(--gold);
        margin-bottom: 1.5rem;
    }

    .ann-no-items h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .ann-no-items p {
        color: var(--text-muted);
        margin-bottom: 0;
    }

    /* Bottom Newsletter CTA */
    .ann-news-sec {
        background-color: var(--secondary-bg);
        padding: 6rem 0;
        text-align: center;
        border-top: 1px solid var(--border-color);
    }

    .ann-news-card {
        max-width: 600px;
        margin: 0 auto;
    }

    .ann-news-card h3 {
        font-size: 1.8rem;
        color: var(--red);
        margin-bottom: 0.8rem;
    }

    .ann-news-card p {
        font-size: 0.95rem;
        margin-bottom: 2rem;
    }

    .newsletter-form {
        display: flex;
        gap: 0.75rem;
        max-width: 500px;
        margin: 0 auto;
    }

    .news-input {
        flex-grow: 1;
        padding: 0.8rem 1.4rem;
        border: 2px solid var(--border-color);
        border-radius: 30px;
        outline: none;
        font-family: var(--font-body);
        font-size: 0.92rem;
    }

    .news-input:focus {
        border-color: var(--red);
    }

    .news-submit {
        padding: 0.8rem 1.8rem;
        border-radius: 30px;
        background-color: var(--red);
        color: var(--white);
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: var(--transition);
    }

    .news-submit:hover {
        background-color: var(--vermilion);
    }

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS
       ========================================================================== */
    @media (max-width: 768px) {
        .ann-card {
            flex-direction: column;
        }
        .ann-date-block {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid var(--border-color);
            flex-direction: row;
            justify-content: flex-start;
            gap: 1rem;
            padding: 1rem 2.25rem;
        }
        .ann-date-day {
            font-size: 2.2rem;
        }
        .ann-date-month {
            margin-bottom: 0;
        }
        .ann-content-block {
            padding: 2rem;
        }
        .newsletter-form {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }
    }
</style>

<!-- Banner Header -->
<section class="ann-banner">
    <div class="container">
        <h1 class="ann-banner-title">Official Announcements</h1>
        <span class="ann-banner-subtitle">Stay Informed with the Notice Board</span>
    </div>
</section>

<!-- Main Listing Section -->
<section class="ann-sec">
    <div class="container">
        
        <!-- Category Filters -->
        <div class="ann-filters">
            <button class="ann-filter-btn active" data-filter="all">All Bulletins</button>
            <button class="ann-filter-btn" data-filter="Notices">Notices</button>
            <button class="ann-filter-btn" data-filter="Events">Events Info</button>
            <button class="ann-filter-btn" data-filter="General">General News</button>
        </div>

        <!-- Announcements Stack -->
        <div class="ann-list">
            <?php foreach ($announcements as $item): ?>
                <div class="ann-card" data-category="<?php echo $item['category']; ?>">
                    <!-- Date Column -->
                    <div class="ann-date-block">
                        <span class="ann-date-day"><?php echo $item['date_day']; ?></span>
                        <div>
                            <span class="ann-date-month"><?php echo $item['date_month']; ?></span>
                            <span class="ann-date-year"><?php echo $item['date_year']; ?></span>
                        </div>
                    </div>

                    <!-- Content Column -->
                    <div class="ann-content-block">
                        <div class="ann-header-meta">
                            <span class="ann-cat-badge"><?php echo $item['category']; ?></span>
                            <span class="ann-tag-text"><i class="fa-solid fa-hashtag" style="color: var(--gold); font-size: 0.75rem;"></i> <?php echo $item['tag']; ?></span>
                        </div>
                        <h3 class="ann-title"><?php echo $item['title']; ?></h3>
                        <p class="ann-excerpt"><?php echo $item['excerpt']; ?></p>

                        <!-- Expandable full text -->
                        <div class="ann-full-text">
                            <div class="ann-full-text-inner">
                                <p><?php echo $item['full']; ?></p>
                            </div>
                        </div>

                        <!-- Read More toggle button -->
                        <button class="ann-more-btn">
                            <span class="btn-text">Read Full Notice</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Fallback No Items Card -->
            <div class="ann-no-items" id="ann-empty">
                <i class="fa-regular fa-folder-open"></i>
                <h3>No Announcements</h3>
                <p>There are no active notices or announcements under this category. Please check again later.</p>
            </div>
        </div>
    </div>
</section>

<!-- Bottom newsletter CTA -->
<!-- <section class="ann-news-sec">
    <div class="container">
        <div class="ann-news-card">
            <h3>Receive Notices in Your Inbox</h3>
            <p>Subscribe to our announcement mailing list to get real-time emails about festivals, rehearsals, meetings, and programs.</p>
            <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Subscribed successfully!'); this.reset();">
                <input type="email" class="news-input" placeholder="Your primary email address" required>
                <button type="submit" class="news-submit">Subscribe Now</button>
            </form>
        </div>
    </div>
</section> -->

<!-- Vanilla JS filters and accordion -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. FILTERING FUNCTIONALITY
        const filterBtns = document.querySelectorAll('.ann-filter-btn');
        const annCards = document.querySelectorAll('.ann-card');
        const emptyState = document.getElementById('ann-empty');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                // Toggle active style
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filterValue = this.getAttribute('data-filter');
                let countVisible = 0;

                annCards.forEach(card => {
                    const cardCat = card.getAttribute('data-category');
                    
                    // Close expanded card if transitioning category
                    if (card.classList.contains('expanded')) {
                        toggleAccordion(card);
                    }

                    if (filterValue === 'all' || cardCat === filterValue) {
                        card.classList.remove('hide');
                        countVisible++;
                    } else {
                        card.classList.add('hide');
                    }
                });

                if (countVisible === 0) {
                    emptyState.style.display = 'block';
                } else {
                    emptyState.style.display = 'none';
                }
            });
        });

        // 2. ACCORDION EXPANSION FUNCTIONALITY
        const toggleButtons = document.querySelectorAll('.ann-more-btn');

        function toggleAccordion(card) {
            const content = card.querySelector('.ann-full-text');
            const btnText = card.querySelector('.btn-text');
            
            if (card.classList.contains('expanded')) {
                // Collapse
                content.style.maxHeight = null;
                card.classList.remove('expanded');
                btnText.innerText = 'Read Full Notice';
            } else {
                // Expand
                content.style.maxHeight = content.scrollHeight + "px";
                card.classList.add('expanded');
                btnText.innerText = 'Collapse Notice';
            }
        }

        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const card = this.closest('.ann-card');
                toggleAccordion(card);
            });
        });
    });
</script>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
