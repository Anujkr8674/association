<?php
// Include the shared header
include 'includes/header.php';

// Sponsors static list grouped by tier
$gold_sponsors = [
    [
        'name' => 'Mukherjee Jewellers',
        'logo_initials' => 'MJ',
        'desc' => 'Crafting fine traditional gold ornament collections and custom wedding sets for the Bengali community since 1995.',
        'site' => 'https://example.com'
    ],
    [
        'name' => 'Bengal Travels Inc.',
        'logo_initials' => 'BT',
        'desc' => 'Your trusted travel partner for global flights. Specialized directly in Kolkata connection ticketing and vacation deals.',
        'site' => 'https://example.com'
    ],
    [
        'name' => 'Sen & Associates',
        'logo_initials' => 'SA',
        'desc' => 'Providing premium financial planning, tax accounting services, and legal advisory across the state.',
        'site' => 'https://example.com'
    ]
];

$silver_sponsors = [
    [
        'name' => 'Gitanjali Music House',
        'logo_initials' => 'GM',
        'desc' => 'Supplying authentic musical instruments like harmoniums, sitars, and tablas to local schools.',
        'site' => 'https://example.com'
    ],
    [
        'name' => 'Bose Real Estate',
        'logo_initials' => 'BRE',
        'desc' => 'Helping community families find their dream residential homes and rental apartments.',
        'site' => 'https://example.com'
    ]
];

$other_partners = [
    [
        'name' => 'Kolkata Sweets & Catering',
        'logo_initials' => 'KSC',
        'desc' => 'Official food caterer supplying traditional Rasgullas, Sandesh, and festive buffet dinners.',
        'site' => 'https://example.com',
        'type' => 'Food Partner'
    ],
    [
        'name' => 'ABP Media Network',
        'logo_initials' => 'ABP',
        'desc' => 'Official media partner documenting event photography, videos, and local news releases.',
        'site' => 'https://example.com',
        'type' => 'Media Partner'
    ]
];
?>

<style>
    /* ==========================================================================
       PARTNERS PAGE SPECIFIC STYLES
       ========================================================================== */
    .part-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .part-banner::before {
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

    .part-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .part-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .part-sec {
        padding: 6rem 0;
    }

    .part-sec-alt {
        background-color: var(--secondary-bg);
    }

    /* Grid layouts */
    .part-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2.5rem;
    }

    .part-grid-4col {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .part-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 2.5rem 1.75rem;
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-slow);
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
    }

    .part-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    /* Monogram Logo Box */
    .part-logo-box {
        width: 90px;
        height: 90px;
        border-radius: var(--border-radius);
        background-color: var(--primary-bg);
        border: 2px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--red);
        font-family: var(--font-headings);
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        transition: var(--transition-slow);
    }

    .part-card:hover .part-logo-box {
        background-color: var(--red);
        color: var(--white);
        border-color: var(--red);
        transform: rotate(5deg);
    }

    .part-tier-badge {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        margin-bottom: 1rem;
        letter-spacing: 0.5px;
    }

    /* Sponsor Tiers Color overrides */
    .badge-gold {
        background-color: rgba(201, 154, 46, 0.1);
        color: var(--gold);
        border: 1px solid rgba(201, 154, 46, 0.2);
    }

    .badge-silver {
        background-color: rgba(33, 26, 23, 0.05);
        color: #7f8c8d;
        border: 1px solid rgba(127, 140, 141, 0.2);
    }

    .badge-partner {
        background-color: rgba(139, 30, 30, 0.08);
        color: var(--red);
        border: 1px solid rgba(139, 30, 30, 0.15);
    }

    .part-name {
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
        color: var(--dark);
    }

    .part-desc {
        font-size: 0.88rem;
        line-height: 1.6;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .part-btn {
        width: 100%;
        padding: 0.6rem 0;
        text-align: center;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        transition: var(--transition);
        margin-top: auto;
    }

    .part-card:hover .part-btn {
        background-color: var(--red);
        border-color: var(--red);
        color: var(--white);
        box-shadow: 0 4px 10px rgba(139, 30, 30, 0.2);
    }

    /* Call for sponsor block */
    .sponsor-cta-block {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        padding: 4rem;
        border: 1px dashed var(--red);
        text-align: center;
        max-width: 800px;
        margin: 4rem auto 0 auto;
        box-shadow: var(--shadow-sm);
    }

    .sponsor-cta-title {
        font-size: 1.75rem;
        color: var(--red);
        margin-bottom: 1rem;
    }

    .sponsor-cta-text {
        font-size: 1rem;
        line-height: 1.6;
        max-width: 600px;
        margin: 0 auto 2.5rem auto;
    }

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS
       ========================================================================== */
    @media (max-width: 991px) {
        .part-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }
        .part-grid-4col {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        .sponsor-cta-block {
            padding: 3rem 2rem;
        }
    }

    @media (max-width: 576px) {
        .part-grid {
            grid-template-columns: 1fr;
            max-width: 350px;
            margin: 0 auto;
        }
        .part-grid-4col {
            grid-template-columns: 1fr;
            max-width: 350px;
            margin: 0 auto;
        }
    }
</style>

<!-- Banner Header -->
<section class="part-banner">
    <div class="container">
        <h1 class="part-banner-title">Sponsors & Partners</h1>
        <span class="part-banner-subtitle">Celebrating Our Community Supporters</span>
    </div>
</section>

<!-- Section 1: Gold Sponsors -->
<section class="part-sec">
    <div class="container">
        <div class="section-header">
            <h2>Gold Sponsors</h2>
            <p class="section-subtitle">The primary corporate patrons who finance our seasonal souvenirs and major festival pandals.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="part-grid">
            <?php foreach ($gold_sponsors as $sponsor): ?>
                <div class="part-card">
                    <div class="part-logo-box" style="border-color: var(--gold); color: var(--gold);">
                        <?php echo $sponsor['logo_initials']; ?>
                    </div>
                    <span class="part-tier-badge badge-gold">Gold Sponsor</span>
                    <h3 class="part-name"><?php echo $sponsor['name']; ?></h3>
                    <p class="part-desc"><?php echo $sponsor['desc']; ?></p>
                    <a href="<?php echo $sponsor['site']; ?>" target="_blank" class="part-btn">Visit Website</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section 2: Silver Sponsors -->
<section class="part-sec part-sec-alt">
    <div class="container">
        <div class="section-header">
            <h2>Silver Sponsors</h2>
            <p class="section-subtitle">Local establishments supporting community circulars and children's sports programs.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="part-grid" style="grid-template-columns: repeat(2, 1fr); max-width: 800px; margin: 0 auto;">
            <?php foreach ($silver_sponsors as $sponsor): ?>
                <div class="part-card">
                    <div class="part-logo-box">
                        <?php echo $sponsor['logo_initials']; ?>
                    </div>
                    <span class="part-tier-badge badge-silver">Silver Sponsor</span>
                    <h3 class="part-name"><?php echo $sponsor['name']; ?></h3>
                    <p class="part-desc"><?php echo $sponsor['desc']; ?></p>
                    <a href="<?php echo $sponsor['site']; ?>" target="_blank" class="part-btn">Visit Website</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section 3: Food & Media Partners -->
<section class="part-sec">
    <div class="container">
        <div class="section-header">
            <h2>Food & Media Partners</h2>
            <p class="section-subtitle">Collaborators who document our memories and serve authentic culinary delights.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="part-grid" style="grid-template-columns: repeat(2, 1fr); max-width: 800px; margin: 0 auto;">
            <?php foreach ($other_partners as $partner): ?>
                <div class="part-card">
                    <div class="part-logo-box" style="border-color: var(--red); color: var(--red);">
                        <?php echo $partner['logo_initials']; ?>
                    </div>
                    <span class="part-tier-badge badge-partner"><?php echo $partner['type']; ?></span>
                    <h3 class="part-name"><?php echo $partner['name']; ?></h3>
                    <p class="part-desc"><?php echo $partner['desc']; ?></p>
                    <a href="<?php echo $partner['site']; ?>" target="_blank" class="part-btn">Visit Website</a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Call to action sponsor card -->
        <div class="sponsor-cta-block">
            <h3 class="sponsor-cta-title">Become a Partner</h3>
            <p class="sponsor-cta-text">Want to showcase your business or support our cultural events? We offer multiple branding packages, souvenirs advertisements, and food festival stall space. Download our sponsorship kit or contact our treasurer.</p>
            <a href="contact.php" class="btn btn-primary">Partner with Us <i class="fa-solid fa-handshake"></i></a>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
