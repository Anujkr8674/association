<?php
require_once 'config.php';
// Include the shared header
include 'includes/header.php';

// Fetch key messages by type
try {
    $stmt_pres_samiti = $pdo->prepare("SELECT * FROM `key_messages` WHERE `doc_type` = 'president_samiti' ORDER BY `year` DESC, `created_at` DESC");
    $stmt_pres_samiti->execute();
    $pres_samiti_docs = $stmt_pres_samiti->fetchAll(PDO::FETCH_ASSOC);

    $stmt_sec_samiti = $pdo->prepare("SELECT * FROM `key_messages` WHERE `doc_type` = 'secretary_samiti' ORDER BY `year` DESC, `created_at` DESC");
    $stmt_sec_samiti->execute();
    $sec_samiti_docs = $stmt_sec_samiti->fetchAll(PDO::FETCH_ASSOC);

    $stmt_eminent = $pdo->prepare("SELECT * FROM `key_messages` WHERE `doc_type` = 'eminent' ORDER BY `year` DESC, `created_at` DESC");
    $stmt_eminent->execute();
    $eminent_docs = $stmt_eminent->fetchAll(PDO::FETCH_ASSOC);

    $stmt_pres_india = $pdo->prepare("SELECT * FROM `key_messages` WHERE `doc_type` = 'president_india' ORDER BY `year` DESC, `created_at` DESC");
    $stmt_pres_india->execute();
    $pres_india_docs = $stmt_pres_india->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pres_samiti_docs = [];
    $sec_samiti_docs = [];
    $eminent_docs = [];
    $pres_india_docs = [];
}
?>

<style>
    /* ==========================================================================
       KEY MESSAGES SPECIFIC STYLES
       ========================================================================== */
    .msg-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .msg-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(circle at 20% 50%, rgba(201, 154, 46, 0.12) 0%, transparent 50%),
                          radial-gradient(circle at 80% 50%, rgba(200, 59, 45, 0.12) 0%, transparent 50%);
        z-index: 1;
    }

    .msg-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .msg-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .msg-sec {
        padding: 6.5rem 0;
        background-color: var(--cream);
    }

    .msg-sec-alt {
        background-color: var(--secondary-bg);
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    /* Structured Introduction Cards */
    .msg-intro-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 3.5rem;
        box-shadow: var(--shadow-sm);
        max-width: 950px;
        margin: 0 auto 3rem auto;
        transition: all 0.3s ease;
    }

    .msg-sec-alt .msg-intro-card {
        background-color: var(--white);
    }

    .msg-intro-card:hover {
        background-color: var(--red);
        border-color: var(--gold);
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
    }

    .msg-intro-card h2 {
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

    .msg-intro-card:hover h2 {
        color: var(--gold);
        border-bottom-color: rgba(255, 255, 255, 0.15);
    }

    .msg-intro-card p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin: 0;
        text-align: justify;
        transition: color 0.3s ease;
    }

    .msg-intro-card:hover p {
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

    .msg-intro-card:hover .alpona-divider::before,
    .msg-intro-card:hover .alpona-divider::after {
        background-color: rgba(255, 255, 255, 0.15);
    }

    .alpona-divider svg {
        width: 24px;
        height: 24px;
        fill: var(--gold);
        margin: 0 1.2rem;
    }

    /* PDF Card Grid Layout */
    .msg-grid {
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

    .msg-sec-alt .doc-card {
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

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS
       ========================================================================== */
    @media (max-width: 991px) {
        .msg-grid {
            gap: 1.5rem;
        }
        .doc-card {
            flex: 0 1 calc(50% - 0.75rem);
            max-width: 380px;
        }
    }

    @media (max-width: 768px) {
        .msg-intro-card {
            padding: 2.2rem 1.8rem;
        }
        .msg-intro-card h2 {
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
<section class="msg-banner">
    <div class="container">
        <h1 class="msg-banner-title">Key Messages</h1>
        <span class="msg-banner-subtitle">Official Greetings, Patron Notes & Commendations</span>
    </div>
</section>

<!-- Section 1: President of Sarbojonin Puja Samiti -->
<section class="msg-sec" id="president-samiti">
    <div class="container">
        <div class="msg-intro-card">
            <h2>President of Sarbojonin Puja Samiti</h2>
            <p>The President of the Sarbojonin Puja Samiti guides our celebration framework with a focus on harmony, tradition, and collective participation. In this message, our President highlights the spiritual essence, historical milestones, and community-centric achievements that define Bengali Cultural Association (BCA). Below you will find the President's key messages from current and past years.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="msg-grid">
            <?php if (empty($pres_samiti_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No President messages uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($pres_samiti_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-user-tie"></i>
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

<!-- Section 2: Secretary of Sarbojonin Puja Samiti -->
<section class="msg-sec msg-sec-alt" id="secretary-samiti">
    <div class="container">
        <div class="msg-intro-card">
            <h2>Secretary of Sarbojonin Puja Samiti</h2>
            <p>The Secretary coordinates the extensive operational, cultural, and financial efforts that transform our vision into reality. This message details the tireless work of our executive committee, volunteers, and the structural progress of our association. Below you will find the Secretary's updates and greetings for the respective years.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="msg-grid">
            <?php if (empty($sec_samiti_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No Secretary messages uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($sec_samiti_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-address-card"></i>
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

<!-- Section 3: Key Messages from Eminent Personalities -->
<section class="msg-sec" id="eminent-personalities">
    <div class="container">
        <div class="msg-intro-card">
            <h2>Key Messages from Eminent Personalities</h2>
            <p>We are deeply honored to receive blessings, appreciations, and words of encouragement from leading writers, artists, scholars, and public figures. These messages highlight the cultural values we preserve and inspire our community to strive for excellence. Below are the key messages received from eminent personalities over the years.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="msg-grid">
            <?php if (empty($eminent_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No eminent personality messages uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($eminent_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-envelope-open-text"></i>
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

<!-- Section 4: Message from President of India -->
<section class="msg-sec msg-sec-alt" id="president-india">
    <div class="container">
        <div class="msg-intro-card">
            <h2>Message from President of India</h2>
            <p>A matter of absolute prestige, the Bengali Cultural Association (BCA) periodically receives warm greetings and formal commendations from the highest office of our nation. These official messages recognize our endeavors in fostering national integration and preserving regional cultural heritage. Below are the digital PDF versions of the messages received from the Honorable President of India.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="msg-grid">
            <?php if (empty($pres_india_docs)): ?>
                <div style="text-align: center; color: var(--gray); font-style: italic; padding: 2rem; width: 100%;">No President of India messages uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($pres_india_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-flag"></i>
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

<?php
// Include the shared footer
include 'includes/footer.php';
?>
