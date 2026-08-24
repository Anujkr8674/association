<?php
require_once 'config.php';
// Include the shared header
include 'includes/header.php';

// Fetch partner documents by type
try {
    $stmt_sponsor = $pdo->prepare("SELECT * FROM `partner_documents` WHERE `doc_type` = 'sponsor' ORDER BY `year` DESC, `created_at` DESC");
    $stmt_sponsor->execute();
    $sponsors_docs = $stmt_sponsor->fetchAll(PDO::FETCH_ASSOC);

    $stmt_patron = $pdo->prepare("SELECT * FROM `partner_documents` WHERE `doc_type` = 'patron' ORDER BY `year` DESC, `created_at` DESC");
    $stmt_patron->execute();
    $patrons_docs = $stmt_patron->fetchAll(PDO::FETCH_ASSOC);

    $stmt_authority = $pdo->prepare("SELECT * FROM `partner_documents` WHERE `doc_type` = 'authority' ORDER BY `year` DESC, `created_at` DESC");
    $stmt_authority->execute();
    $authorities_docs = $stmt_authority->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $sponsors_docs = [];
    $patrons_docs = [];
    $authorities_docs = [];
}
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
        padding: 6.5rem 0;
        background-color: var(--cream);
    }

    .part-sec-alt {
        background-color: var(--secondary-bg);
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    /* Structured Introduction Cards */
    .part-intro-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 3.5rem;
        box-shadow: var(--shadow-sm);
        max-width: 950px;
        margin: 0 auto 3rem auto;
        transition: all 0.3s ease;
    }

    .part-sec-alt .part-intro-card {
        background-color: var(--white);
    }

    .part-intro-card:hover {
        background-color: var(--red);
        border-color: var(--gold);
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
    }

    .part-intro-card h2 {
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

    .part-intro-card:hover h2 {
        color: var(--gold);
        border-bottom-color: rgba(255, 255, 255, 0.15);
    }

    .part-intro-card p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin: 0;
        text-align: justify;
        transition: color 0.3s ease;
    }

    .part-intro-card:hover p {
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

    .part-intro-card:hover .alpona-divider::before,
    .part-intro-card:hover .alpona-divider::after {
        background-color: rgba(255, 255, 255, 0.15);
    }

    .alpona-divider svg {
        width: 24px;
        height: 24px;
        fill: var(--gold);
        margin: 0 1.2rem;
    }

    /* PDF Card Lists Grid */
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
        border-radius: var(--border-radius-lg);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        flex: 0 1 calc(33.333% - 1.34rem);
        min-width: 290px;
        max-width: 380px;
    }

    .part-sec-alt .doc-card {
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

    /* Bottom CTA Panel */
    .part-cta-sec {
        background-color: var(--cream);
        padding: 6.5rem 0;
        text-align: center;
        border-top: 1px solid var(--border-color);
    }

    .part-cta-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 3.5rem;
        box-shadow: var(--shadow-sm);
        max-width: 800px;
        margin: 0 auto;
        transition: all 0.3s ease;
    }

    .part-cta-card:hover {
        background-color: var(--red);
        border-color: var(--gold);
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
    }

    .part-cta-card h2 {
        font-size: 2.2rem;
        color: var(--red);
        margin-top: 0;
        margin-bottom: 1rem;
        transition: color 0.3s ease;
    }

    .part-cta-card:hover h2 {
        color: var(--gold);
    }

    .part-cta-card p {
        font-size: 1.05rem;
        margin-bottom: 2rem;
        line-height: 1.8;
        color: var(--dark);
        transition: color 0.3s ease;
    }

    .part-cta-card:hover p {
        color: rgba(255, 255, 255, 0.95);
    }

    .part-cta-card .btn-primary {
        background-color: var(--red);
        color: var(--white);
        border-color: var(--red);
        transition: all 0.3s ease;
    }

    .part-cta-card:hover .btn-primary {
        background-color: var(--white);
        color: var(--red) !important;
        border-color: var(--white);
    }

    .part-cta-card:hover .btn-primary:hover {
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
        .part-intro-card, .part-cta-card {
            padding: 2.2rem 1.8rem;
        }
        .part-intro-card h2, .part-cta-card h2 {
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
<section class="part-banner">
    <div class="container">
        <h1 class="part-banner-title">Sponsors & Patrons</h1>
        <span class="part-banner-subtitle">Celebrating Our Community Supporters</span>
    </div>
</section>

<!-- Section 1: Thanksgiving to Sponsors -->
<section class="part-sec" id="sponsors">
    <div class="container">
        <div class="part-intro-card">
            <h2>Thanksgiving to Sponsors</h2>
            <p>Sponsorship is a tool to earn recognition by participating in the events organized during various celebrations. Year after year, various reputed organizations have sponsored and have become the foundation; foundation that provides strength to our Institution without whom it would be impossible to manage such programs like Durga Puja. We are indeed grateful to the enthusiasm shown by various corporate and business houses that selflessly contributed funds generously for the success of such Community Initiative and encouraged us to do more. What gives us pleasure is that more and more sponsors are coming forward to participate with us and we welcome them in their efforts and assure of full cooperation from our side. This website will be able to capture and display their names and shall remain accessible for ever. We convey our heartfelt thanks to all of them and wish them good luck in their business endeavor.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="docs-grid">
            <?php if (empty($sponsors_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No sponsor documents uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($sponsors_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-handshake"></i>
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

<!-- Section 2: Thanksgiving to Patrons -->
<section class="part-sec part-sec-alt" id="patrons">
    <div class="container">
        <div class="part-intro-card">
            <h2>Thanksgiving to Patrons</h2>
            <p>By its nomenclature itself one would understand the fact these are the pillars and guides who show us not only direction but motivate us continuously by sizable donations year after year. They do it without any selfish motives and always remain behind the scene; so is their modesty. This website will be able to now mark their names for ever. They are all legends. People will come and go but BCA will never ever forget their contribution to the smooth functioning trail that they have created. Whenever we were in a bit of trouble they came forward to rescue us. We pay our deepest and sincerest respect and convey our thanks for showing us the right path always to accomplish our mission well in time.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="docs-grid">
            <?php if (empty($patrons_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No patron documents uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($patrons_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-gem"></i>
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

<!-- Section 3: Thanksgiving to Authorities -->
<section class="part-sec" id="authorities">
    <div class="container">
        <div class="part-intro-card">
            <h2>Thanksgiving to Authorities</h2>
            <p>In a civilized society, there are many agencies that play micro to major roles, which generally remain unnoticed. With this column we would like to extend our profuse thanks to various Noida Authorities, who provide us Space, Power, Vigilance, Water Supply, Housekeeping Services, Parking infrastructure, Safety to the community and many other services which make the Big Puja Celebration a grand success, year after year. We sincerely apologize if any agency has not been named due to our ignorance but we consider everyone invaluable and extend our sincere appreciation to all. It would be inappropriate to forget the role played by the Local MLAs who have virtually patronized the event and provided immense encouragement to BCA by attending the Puja Functions as per their convenience. Due to the vigilance by the Law Enforcement Agencies, we never had any problems handling massive crowds during our various cultural evenings and Kudos to them for ensuring good traffic discipline during these Puja days. Significant are the Power Distribution Agencies, who practically provide uninterrupted Power Supply during the celebration. They also deserve our special gratitude. The Noida Authorities have proven that Administration and the Local community can coexist when the purpose is right. Thank you all once again.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="docs-grid">
            <?php if (empty($authorities_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No authority documents uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($authorities_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-building-shield"></i>
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
<section class="part-cta-sec">
    <div class="container">
        <div class="part-cta-card">
            <h2>Partner with BCA</h2>
            <p>Want to showcase your business or support our cultural events? We offer multiple branding packages, souvenirs advertisements, and food festival stall space.</p>
            <a href="contact.php" class="btn btn-primary">Partner with Us <i class="fa-solid fa-handshake"></i></a>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
