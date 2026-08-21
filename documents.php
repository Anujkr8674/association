<?php
require_once 'config.php';
// Include the shared header
include 'includes/header.php';

// Fetch association documents by type
try {
    $stmt_souvenir = $pdo->prepare("SELECT * FROM `association_documents` WHERE `doc_type` = 'souvenir' ORDER BY `year` DESC, `created_at` DESC");
    $stmt_souvenir->execute();
    $souvenirs_docs = $stmt_souvenir->fetchAll(PDO::FETCH_ASSOC);

    $stmt_competition = $pdo->prepare("SELECT * FROM `association_documents` WHERE `doc_type` = 'competition' ORDER BY `year` DESC, `created_at` DESC");
    $stmt_competition->execute();
    $competitions_docs = $stmt_competition->fetchAll(PDO::FETCH_ASSOC);

    $stmt_recognition = $pdo->prepare("SELECT * FROM `association_documents` WHERE `doc_type` = 'recognition' ORDER BY `year` DESC, `created_at` DESC");
    $stmt_recognition->execute();
    $recognitions_docs = $stmt_recognition->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $souvenirs_docs = [];
    $competitions_docs = [];
    $recognitions_docs = [];
}
?>

<style>
    /* ==========================================================================
       DOCUMENTS PAGE SPECIFIC STYLES
       ========================================================================== */
    .docs-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .docs-banner::before {
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

    .docs-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .docs-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .docs-sec {
        padding: 6.5rem 0;
        background-color: var(--cream);
    }

    .docs-sec-alt {
        background-color: var(--white);
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .section-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .section-header h2 {
        font-size: 2.5rem;
        font-family: var(--font-headings);
        color: var(--dark);
        margin-bottom: 0.8rem;
    }

    .section-subtitle {
        color: var(--gray);
        font-size: 1.05rem;
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.8;
        text-align: justify;
    }

    .alpona-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 1.5rem;
    }

    .alpona-divider::before,
    .alpona-divider::after {
        content: '';
        height: 1px;
        width: 80px;
        background-color: var(--border-color);
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
        transition: var(--transition);
        flex: 0 1 calc(33.333% - 1.34rem);
        min-width: 290px;
        max-width: 380px;
    }

    .docs-sec-alt .doc-card {
        background-color: var(--cream);
    }

    .doc-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--red);
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
    }

    .doc-meta {
        font-size: 0.8rem;
        color: var(--gray);
        font-weight: 600;
    }

    .doc-link {
        color: var(--gray);
        font-size: 1.25rem;
        transition: var(--transition);
    }

    .doc-card:hover .doc-link {
        color: var(--red);
    }

    @media (max-width: 991px) {
        .docs-grid {
            gap: 1.5rem;
        }
        .doc-card {
            flex: 0 1 calc(50% - 0.75rem);
            max-width: 380px;
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
<section class="docs-banner">
    <div class="container">
        <h1 class="docs-banner-title">Association Documents</h1>
        <span class="docs-banner-subtitle">Official Publications, Souvenirs & Achievements</span>
    </div>
</section>

<!-- Section 1: Souvenir -->
<section class="docs-sec" id="souvenir">
    <div class="container">
        <div class="section-header">
            <h2>Souvenir</h2>
            <p class="section-subtitle">The Bengali Cultural Association (BCA) publishes its annual Souvenir magazine during the Durga Puja festival. This publication is a compilation of articles, poems, drawings, and well-wishes from our members, alongside greetings from our sponsors and patrons. It is a cherished keepsake documenting our community's creative milestones year after year. Below are the digital PDF versions of our historical souvenirs.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="docs-grid">
            <?php if (empty($souvenirs_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No souvenir documents uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($souvenirs_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <div class="doc-info">
                            <h4 class="doc-title" title="<?php echo htmlspecialchars($doc['title']); ?>"><?php echo htmlspecialchars($doc['title']); ?></h4>
                            <span class="doc-meta">Year: <?php echo htmlspecialchars($doc['year']); ?></span>
                        </div>
                        <?php if (!empty($doc['pdf_path'])): ?>
                            <a href="<?php echo htmlspecialchars($doc['pdf_path']); ?>" target="_blank" class="doc-link" title="Open PDF"><i class="fa-solid fa-up-right-from-square"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section 2: Competitions & Winners -->
<section class="docs-sec docs-sec-alt" id="competitions">
    <div class="container">
        <div class="section-header">
            <h2>Competitions & Winners</h2>
            <p class="section-subtitle">Healthy participation and community integration are promoted through various cultural and sports competitions organized for all age groups during our celebrations. These include drawing, recitation, music, quizzes, conch-blowing, and sports activities. Below you will find the lists of events, rules, and the official winner announcements for the respective years.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="docs-grid">
            <?php if (empty($competitions_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No competition documents uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($competitions_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <div class="doc-info">
                            <h4 class="doc-title" title="<?php echo htmlspecialchars($doc['title']); ?>"><?php echo htmlspecialchars($doc['title']); ?></h4>
                            <span class="doc-meta">Year: <?php echo htmlspecialchars($doc['year']); ?></span>
                        </div>
                        <?php if (!empty($doc['pdf_path'])): ?>
                            <a href="<?php echo htmlspecialchars($doc['pdf_path']); ?>" target="_blank" class="doc-link" title="Open PDF"><i class="fa-solid fa-up-right-from-square"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section 3: Recognition -->
<section class="docs-sec" id="recognition">
    <div class="container">
        <div class="section-header">
            <h2>Recognition</h2>
            <p class="section-subtitle">We take immense pride in celebrating the accolades, awards, and recognitions received by our association as a whole, as well as outstanding individual achievements of our members and children. This section hosts press releases, award certificates, and news clippings that highlight our contributions to Bengali culture and social responsibility.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="docs-grid">
            <?php if (empty($recognitions_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No recognition documents uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($recognitions_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <div class="doc-info">
                            <h4 class="doc-title" title="<?php echo htmlspecialchars($doc['title']); ?>"><?php echo htmlspecialchars($doc['title']); ?></h4>
                            <span class="doc-meta">Year: <?php echo htmlspecialchars($doc['year']); ?></span>
                        </div>
                        <?php if (!empty($doc['pdf_path'])): ?>
                            <a href="<?php echo htmlspecialchars($doc['pdf_path']); ?>" target="_blank" class="doc-link" title="Open PDF"><i class="fa-solid fa-up-right-from-square"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section 4: Pandal Theme (Only Text) -->
<section class="docs-sec docs-sec-alt" id="pandal-theme">
    <div class="container" style="max-width: 900px; margin: 0 auto;">
        <div class="section-header" style="max-width: 100%; margin-bottom: 2.5rem;">
            <h2>Pandal Theme</h2>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>
        
        <div style="font-size: 1.05rem; line-height: 1.8; color: var(--dark); text-align: justify;">
            <p style="margin-bottom: 1.5rem;">The Durga Puja Samiti Sector 62, Noida is celebrating its 14th year of the Puja and as usual a grand celebration has been lined up. As a matter of fact we have come a long way since we started the Puja celebrations in sector 62 in 2001 with a modest beginning. It may not be out of place to mention that we are today one of the largest in terms of scale and size.</p>

            <p style="margin-bottom: 1.5rem;">The Durga Idol is a traditional one as has been over the past 14 years i.e. with the traditional “daker saaj”. The insides of the pandal are on the traditional mold.</p>

            <p>The cultural programme has been designed to cater to the different strata of the devotees, replete with Rabindrasangeet by renowned Bengali singer Srikanta Acharya, Hindi songs of Mukesh and Nusrat Fateh Ali Khan by Avrodeep, Bangla Band, Hasyakaviota by Dinesh Raghubanshi, Mordern /Film songs by Dipa Das and last but not the least a Magic show for the kids.</p>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
