<?php
require_once 'config.php';
// Include the shared header
include 'includes/header.php';

// Fetch current committee board members (board)
$current_committee = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM `current_committee` WHERE `member_type` = 'board' ORDER BY `sort_order` ASC, `created_at` DESC");
    $stmt->execute();
    $current_committee = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $current_committee = [];
}

// Fetch current executive committee members (executive)
$executive_members = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM `current_committee` WHERE `member_type` = 'executive' ORDER BY `sort_order` ASC, `created_at` DESC");
    $stmt->execute();
    $executive_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $executive_members = [];
}

// Fetch previous executive committee documents
$previous_committee_docs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM `committee_documents` WHERE `doc_type` = 'previous_executive' ORDER BY `year` DESC, `created_at` DESC");
    $stmt->execute();
    $previous_committee_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $previous_committee_docs = [];
}

// Fetch Puja Samiti documents
$puja_samiti_docs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM `committee_documents` WHERE `doc_type` = 'puja_samiti' ORDER BY `year` DESC, `created_at` DESC");
    $stmt->execute();
    $puja_samiti_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $puja_samiti_docs = [];
}

// Fetch Processes documents
$processes_docs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM `committee_documents` WHERE `doc_type` = 'process' ORDER BY `year` DESC, `created_at` DESC");
    $stmt->execute();
    $processes_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $processes_docs = [];
}
?>

<style>
    /* ==========================================================================
       COMMITTEE PAGE SPECIFIC STYLES
       ========================================================================== */
    .comm-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .comm-banner::before {
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

    .comm-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .comm-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .comm-sec {
        padding: 6.5rem 0;
          
    }

    .comm-sec-alt {
        background-color: var(--secondary-bg);
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    /* Core Committee Grid */
    .comm-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2.5rem;
    }

    .comm-card {
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

    .comm-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--gold);
    }

    .comm-img-box {
        position: relative;
        height: 300px;
        background-color: var(--secondary-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .comm-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition-slow);
    }

    .comm-avatar-placeholder {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: var(--red);
        color: var(--white);
        font-size: 2.5rem;
        font-family: var(--font-headings);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        box-shadow: var(--shadow-sm);
    }

    .comm-card:hover .comm-img {
        transform: scale(1.06);
    }

    .comm-body {
        padding: 1.8rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .comm-role {
        color: var(--gold);
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.4rem;
        display: block;
    }

    .comm-name {
        font-size: 1.35rem;
        color: var(--dark);
        margin-bottom: 0.8rem;
    }

    .comm-bio {
        font-size: 0.9rem;
        line-height: 1.6;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .comm-contact {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.45rem;
        padding-top: 1.2rem;
        border-top: 1px solid var(--border-color);
        font-size: 0.88rem;
    }

    .comm-email-link {
        color: var(--red);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .comm-email-link:hover {
        color: var(--vermilion);
    }

    /* Executive Board Grid */
    .exec-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2.5rem;
        margin-top: 2rem;
    }

    /* Past Committee Interactive Section */
    .tabs-wrapper {
        max-width: 800px;
        margin: 0 auto;
    }

    .tab-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 3.5rem;
        border-bottom: 2px solid rgba(33, 26, 23, 0.08);
        padding-bottom: 1.2rem;
    }

    .tab-btn {
        background: none;
        border: none;
        padding: 0.75rem 1.75rem;
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        transition: var(--transition);
        position: relative;
    }

    .tab-btn::after {
        content: '';
        position: absolute;
        bottom: -21px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: var(--red);
        transform: scaleX(0);
        transition: var(--transition);
    }

    .tab-btn.active {
        color: var(--red);
    }

    .tab-btn.active::after {
        transform: scaleX(1);
    }

    .tab-content {
        display: none;
        animation: fadeIn 0.4s ease-in-out;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .past-comm-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .past-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        padding: 1.8rem 1.5rem;
        border-radius: var(--border-radius);
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }

    .past-card:hover {
        border-color: var(--gold);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .past-role {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--gold);
        letter-spacing: 0.5px;
        margin-bottom: 0.35rem;
        display: block;
    }

    .past-name {
        font-family: var(--font-headings);
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0;
    }

    /* Document Cards Style */
    .docs-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2rem;
        margin-top: 2.5rem;
        width: 100%;
    }

    .doc-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        padding: 1.5rem 1.8rem;
        border-radius: var(--border-radius-lg);
        display: flex;
        align-items: center;
        gap: 1.2rem;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-slow);
        flex: 0 1 calc(33.333% - 1.34rem);
        min-width: 290px;
        max-width: 380px;
        box-sizing: border-box;
    }

    .doc-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-color: var(--red);
    }

    .doc-icon-box {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background-color: rgba(212, 63, 58, 0.06);
        color: var(--red);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: var(--transition);
        flex-shrink: 0;
    }

    .doc-card:hover .doc-icon-box {
        background-color: var(--red);
        color: var(--white);
    }

    .doc-info {
        flex-grow: 1;
        overflow: hidden;
    }

    .doc-title {
        font-size: 1.02rem;
        font-weight: 700;
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
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .doc-link {
        font-size: 1.2rem;
        color: var(--red);
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: var(--secondary-bg);
        flex-shrink: 0;
    }

    .doc-link:hover {
        background-color: var(--red);
        color: var(--white);
        transform: scale(1.1);
    }

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS
       ========================================================================== */
    @media (max-width: 991px) {
        .comm-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 2.2rem;
        }
        .exec-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 2.2rem;
        }
        .past-comm-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .docs-grid {
            gap: 1.5rem;
        }
        .doc-card {
            flex: 0 1 calc(50% - 0.75rem);
            max-width: 380px;
        }
    }

    @media (max-width: 680px) {
        .comm-grid {
            grid-template-columns: 1fr;
            max-width: 400px;
            margin: 0 auto;
        }
        .exec-grid {
            grid-template-columns: 1fr;
            max-width: 400px;
            margin: 0 auto;
        }
        .past-comm-grid {
            grid-template-columns: 1fr;
        }
        .doc-card {
            flex: 1 1 100%;
            max-width: 100%;
        }
        .tab-buttons {
            flex-direction: column;
            gap: 0.5rem;
            align-items: center;
        }
        .tab-btn::after {
            display: none;
        }
    }
</style>

<!-- Banner Header -->
<section class="comm-banner">
    <div class="container">
        <h1 class="comm-banner-title">Executive Committee</h1>
        <span class="comm-banner-subtitle">The Leaders Steering Our Association</span>
    </div>
</section>

<!-- Section 1: Executive Board -->
<section class="comm-sec" id="current">
    <div class="container">
        <div class="section-header">
            <h2>Current Committee Board</h2>
            <p class="section-subtitle">Meet the team elected to run the general administration and events for the year 2025-2026.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="comm-grid">
            <?php foreach ($current_committee as $member): ?>
                <div class="comm-card">
                    <div class="comm-img-box">
                        <!-- Remotely load Unsplash image -->
                        <?php if (!empty($member['image'])): ?>
                            <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" class="comm-img" loading="lazy">
                        <?php else: ?>
                            <div class="comm-avatar-placeholder">
                                <?php echo $member['initials']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="comm-body">
                        <span class="comm-role"><?php echo $member['position']; ?></span>
                        <h3 class="comm-name"><?php echo $member['name']; ?></h3>
                        <p class="comm-bio"><?php echo $member['bio']; ?></p>
                        <?php if (!empty($member['email']) || !empty($member['phone'])): ?>
                            <div class="comm-contact">
                                <?php if (!empty($member['email'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" class="comm-email-link">
                                        <i class="fa-regular fa-envelope"></i> <?php echo htmlspecialchars($member['email']); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($member['phone'])): ?>
                                    <a href="tel:<?php echo htmlspecialchars($member['phone']); ?>" class="comm-email-link" style="color: var(--dark); font-weight: 500;">
                                        <i class="fa-solid fa-phone" style="color: var(--gray);"></i> <?php echo htmlspecialchars($member['phone']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section 2: Executive Members -->
<!-- <section class="comm-sec comm-sec-alt">
    <div class="container">
        <div class="section-header">
            <h2>Executive Board Members</h2>
            <p class="section-subtitle">Dedicated members driving execution of activities, pandals, and technical support.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="exec-grid">
            <?php if (empty($executive_members)): ?>
                <div style="grid-column: span 3; text-align: center; color: var(--gray); font-style: italic; padding: 2rem;">No executive board members found.</div>
            <?php else: ?>
                <?php foreach ($executive_members as $member): ?>
                    <div class="comm-card">
                        <div class="comm-img-box" style="height: 250px;">
                            <?php if (!empty($member['image'])): ?>
                                <img src="<?php echo htmlspecialchars($member['image']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="comm-img" loading="lazy">
                            <?php else: ?>
                                <div class="comm-avatar-placeholder" style="width: 80px; height: 80px; font-size: 2rem;">
                                    <?php 
                                    $words = explode(' ', $member['name']);
                                    $initials = '';
                                    foreach ($words as $w) {
                                        $initials .= strtoupper(substr($w, 0, 1));
                                    }
                                    echo htmlspecialchars(substr($initials, 0, 2));
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="comm-body">
                            <span class="comm-role"><?php echo htmlspecialchars($member['position']); ?></span>
                            <h3 class="comm-name" style="font-size: 1.25rem;"><?php echo htmlspecialchars($member['name']); ?></h3>
                            <p class="comm-bio" style="font-size: 0.88rem;"><?php echo htmlspecialchars($member['bio']); ?></p>
                            <?php if (!empty($member['email']) || !empty($member['phone'])): ?>
                                <div class="comm-contact" style="margin-top: auto;">
                                    <?php if (!empty($member['email'])): ?>
                                        <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" class="comm-email-link">
                                            <i class="fa-regular fa-envelope"></i> <?php echo htmlspecialchars($member['email']); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['phone'])): ?>
                                        <a href="tel:<?php echo htmlspecialchars($member['phone']); ?>" class="comm-email-link" style="color: var(--dark); font-weight: 500;">
                                            <i class="fa-solid fa-phone" style="color: var(--gray);"></i> <?php echo htmlspecialchars($member['phone']); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section> -->

<!-- Section 3: Previous Committees -->
<section class="comm-sec" id="previous">
    <div class="container">
        <div class="section-header">
            <h2>Previous Committees</h2>
            <p class="section-subtitle">We honor the former executive members and leadership groups who built this association.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="docs-grid">
            <?php if (empty($previous_committee_docs)): ?>
                <div style="grid-column: span 3; text-align: center; color: var(--gray); font-style: italic; padding: 2rem;">No previous committee documents uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($previous_committee_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-file-pdf"></i>
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

<!-- Section 4: Sarbojonin Puja Samiti -->
<section class="comm-sec comm-sec-alt" id="puja-samiti">
    <div class="container">
        <div class="section-header">
            <h2>Sarbojonin Puja Samiti</h2>
            <p class="section-subtitle">Former and current organizing committee structures for our grand annual Durga Puja celebrations.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="docs-grid">
            <?php if (empty($puja_samiti_docs)): ?>
                <div style="grid-column: span 3; text-align: center; color: var(--gray); font-style: italic; padding: 2rem;">No Puja Samiti documents uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($puja_samiti_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-place-of-worship"></i>
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

<!-- Section 5: Processes -->
<section class="comm-sec" id="processes">
    <div class="container">
        <div class="section-header">
            <h2>Processes & Compliance</h2>
            <p class="section-subtitle">Review election processes, general body meeting guidelines, and constitution bylaws.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="docs-grid">
            <?php if (empty($processes_docs)): ?>
                <div style="grid-column: span 3; text-align: center; color: var(--gray); font-style: italic; padding: 2rem;">No process guidelines uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($processes_docs as $doc): ?>
                    <div class="doc-card">
                        <div class="doc-icon-box">
                            <i class="fa-solid fa-file-contract"></i>
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

<?php
// Include the shared footer
include 'includes/footer.php';
?>
