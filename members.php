<?php
require_once 'config.php';
// Include the shared header
include 'includes/header.php';

// Fetch our members documents
$our_members_docs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM `member_documents` WHERE `doc_type` = 'our_members' ORDER BY `year` DESC, `created_at` DESC");
    $stmt->execute();
    $our_members_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $our_members_docs = [];
}

// Fetch member profile documents
$member_profile_docs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM `member_documents` WHERE `doc_type` = 'member_profile' ORDER BY `year` DESC, `created_at` DESC");
    $stmt->execute();
    $member_profile_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $member_profile_docs = [];
}
?>

<style>
    /* ==========================================================================
       MEMBERS PAGE SPECIFIC STYLES
       ========================================================================== */
    .memb-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .memb-banner::before {
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

    .memb-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .memb-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    /* Structured Introduction Cards */
    .memb-intro-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 3.5rem;
        box-shadow: var(--shadow-sm);
        max-width: 950px;
        margin: 0 auto 3rem auto;
        transition: all 0.3s ease;
    }

    .memb-sec-alt .memb-intro-card {
        background-color: var(--white);
    }

    .memb-intro-card:hover {
        background-color: var(--red);
        border-color: var(--gold);
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
    }

    .memb-intro-card h2 {
        font-family: var(--font-headings);
        color: var(--red);
        font-size: 2.2rem;
        margin-top: 0;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 0.8rem;
        transition: color 0.3s ease, border-color 0.3s ease;
        text-align: center;
    }

    .memb-intro-card:hover h2 {
        color: var(--gold);
        border-bottom-color: rgba(255, 255, 255, 0.15);
    }

    .memb-intro-card p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin: 0;
        text-align: justify;
        transition: color 0.3s ease;
    }

    .memb-intro-card:hover p {
        color: rgba(255, 255, 255, 0.95);
    }

    .alpona-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 1.5rem;
        transition: all 0.3s ease;
    }

    .alpona-divider::before,
    .alpona-divider::after {
        content: '';
        height: 1px;
        width: 80px;
        background-color: var(--border-color);
        transition: background-color 0.3s ease;
    }

    .memb-intro-card:hover .alpona-divider::before,
    .memb-intro-card:hover .alpona-divider::after {
        background-color: rgba(255, 255, 255, 0.15);
    }

    .alpona-divider svg {
        width: 24px;
        height: 24px;
        fill: var(--gold);
        margin: 0 1.2rem;
    }

    /* Document Cards Style */
    .docs-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2rem;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .doc-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        padding: 1.5rem 1.8rem;
        border-radius: var(--border-radius-lg);
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        flex: 0 1 calc(33.333% - 1.34rem);
        min-width: 290px;
        max-width: 380px;
        box-sizing: border-box;
    }

    .memb-sec-alt .doc-card {
        background-color: var(--white);
    }

    .doc-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--gold);
        background-color: var(--red);
    }

    .doc-icon-box {
        width: 50px;
        height: 50px;
        background-color: rgba(139, 30, 30, 0.06);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--red);
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }

    .doc-card:hover .doc-icon-box {
        background-color: var(--white);
        color: var(--red);
    }

    .doc-info {
        flex: 1;
        margin-left: 1.2rem;
        margin-right: 1rem;
        min-width: 0;
    }

    .doc-title {
        font-family: var(--font-headings);
        font-size: 1.1rem;
        color: var(--dark);
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: color 0.3s ease;
    }

    .doc-card:hover .doc-title {
        color: var(--white);
    }

    .doc-meta {
        font-size: 0.8rem;
        color: var(--gray);
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .doc-card:hover .doc-meta {
        color: rgba(255, 255, 255, 0.85);
    }

    .doc-link {
        color: var(--gray);
        font-size: 1.25rem;
        transition: all 0.3s ease;
    }

    .doc-card:hover .doc-link {
        color: var(--gold);
    }

    /* Section Styling */
    .memb-sec {
        padding: 6.5rem 0;
        background-color: var(--cream);
    }
    
    .memb-sec-alt {
        background-color: var(--secondary-bg);
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    /* Bottom CTA Panel */
    .memb-cta-sec {
        background-color: var(--cream);
        padding: 6.5rem 0;
        text-align: center;
        border-top: 1px solid var(--border-color);
    }

    .memb-cta-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 3.5rem;
        box-shadow: var(--shadow-sm);
        max-width: 800px;
        margin: 0 auto;
        transition: all 0.3s ease;
    }

    .memb-cta-card:hover {
        background-color: var(--red);
        border-color: var(--gold);
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
    }

    .memb-cta-card h2 {
        font-size: 2.2rem;
        color: var(--red);
        margin-top: 0;
        margin-bottom: 1rem;
        transition: color 0.3s ease;
    }

    .memb-cta-card:hover h2 {
        color: var(--gold);
    }

    .memb-cta-card p {
        font-size: 1.05rem;
        margin-bottom: 2rem;
        line-height: 1.8;
        color: var(--dark);
        transition: color 0.3s ease;
    }

    .memb-cta-card:hover p {
        color: rgba(255, 255, 255, 0.95);
    }

    .memb-cta-card .btn-primary {
        background-color: var(--red);
        color: var(--white);
        border-color: var(--red);
        transition: all 0.3s ease;
    }

    .memb-cta-card:hover .btn-primary {
        background-color: var(--white);
        color: var(--red) !important;
        border-color: var(--white);
    }

    .memb-cta-card:hover .btn-primary:hover {
        background-color: var(--gold);
        color: var(--white) !important;
        border-color: var(--gold);
        transform: translateY(-2px);
    }

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS
       ========================================================================== */
    @media (max-width: 991px) {
        .docs-grid {
            gap: 1.5rem;
        }
        .doc-card {
            flex: 0 1 calc(50% - 0.75rem);
            max-width: 380px;
        }
    }

    @media (max-width: 768px) {
        .memb-intro-card, .memb-cta-card {
            padding: 2.2rem 1.8rem;
        }
        .memb-intro-card h2, .memb-cta-card h2 {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 680px) {
        .doc-card {
            flex: 1 1 100%;
            max-width: 100%;
        }
    }
</style>

<!-- Banner Header -->
<section class="memb-banner">
    <div class="container">
        <h1 class="memb-banner-title">Association Directory</h1>
        <span class="memb-banner-subtitle">Celebrating Our Valued Members</span>
    </div>
</section>

<!-- Section 1: Our Members -->
<section class="memb-sec" id="our-members">
    <div class="container">
        <div class="memb-intro-card">
            <h2>Our Members</h2>
            <p>Access the verified lists of registered general and life members of our association.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="docs-grid">
            <?php if (empty($our_members_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No member documents uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($our_members_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="doc-info">
                            <h4 class="doc-title" title="<?php echo htmlspecialchars($doc['title']); ?>"><?php echo htmlspecialchars($doc['title']); ?></h4>
                            <span class="doc-meta">Year: <?php echo htmlspecialchars($doc['year']); ?></span>
                        </div>
                        <a href="<?php echo htmlspecialchars($doc['pdf_path']); ?>" target="_blank" class="doc-link" title="Open PDF"><i class="fa-solid fa-up-right-from-square"></i></a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section 2: Member Profile (Table) -->
<section class="memb-sec memb-sec-alt" id="member-profile">
    <div class="container">
        <div class="memb-intro-card">
            <h2>Member Profile (Table)</h2>
            <p>Download and review the profiles, roles, tenures, and general compliance registers.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="docs-grid">
            <?php if (empty($member_profile_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No member profile documents uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($member_profile_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <div class="doc-info">
                            <h4 class="doc-title" title="<?php echo htmlspecialchars($doc['title']); ?>"><?php echo htmlspecialchars($doc['title']); ?></h4>
                            <span class="doc-meta">Year: <?php echo htmlspecialchars($doc['year']); ?></span>
                        </div>
                        <a href="<?php echo htmlspecialchars($doc['pdf_path']); ?>" target="_blank" class="doc-link" title="Open PDF"><i class="fa-solid fa-up-right-from-square"></i></a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Bottom CTA Section -->
<section class="memb-cta-sec">
    <div class="container">
        <div class="memb-cta-card">
            <h2>Want to Join the Directory?</h2>
            <p>Become a registered member of the Bengali Cultural Association to participate in our administrative voting, cultural events, and community support groups.</p>
            <a href="join-us.php" class="btn btn-primary">Submit Application Today <i class="fa-solid fa-user-plus"></i></a>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
