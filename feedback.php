<?php
require_once 'config.php';
// Include the shared header
include 'includes/header.php';
?>

<style>
    /* ==========================================================================
       FEEDBACK PAGE SPECIFIC STYLES
       ========================================================================== */
    .feed-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .feed-banner::before {
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

    .feed-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .feed-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .feed-sec {
        padding: 6.5rem 0;
        background-color: var(--cream);
    }

    .feed-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .feed-content {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 3.5rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    .feed-content:hover {
        background-color: var(--red);
        border-color: var(--gold);
        box-shadow: var(--shadow-lg);
    }

    .feed-content:hover h2 {
        color: var(--gold);
        border-bottom-color: rgba(255, 255, 255, 0.15);
    }

    .feed-content:hover h3 {
        color: var(--white);
    }

    .feed-content:hover p,
    .feed-content:hover li {
        color: rgba(255, 255, 255, 0.9);
    }

    .feed-content:hover strong {
        color: var(--gold) !important;
    }

    .feed-content:hover .feed-cta-box {
        border-top-color: rgba(255, 255, 255, 0.15);
    }

    .feed-content:hover .feed-btn {
        background-color: var(--white);
        color: var(--red) !important;
        border-color: var(--white);
    }

    .feed-content:hover .feed-btn:hover {
        background-color: var(--gold);
        color: var(--white) !important;
        border-color: var(--gold);
    }

    .feed-content h2 {
        font-family: var(--font-headings);
        color: var(--red);
        font-size: 2.2rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 0.8rem;
        text-align: center;
    }

    .feed-content h3 {
        font-family: var(--font-headings);
        color: var(--dark);
        font-size: 1.5rem;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
    }

    .feed-content p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin-bottom: 1.5rem;
        text-align: justify;
    }

    .feed-content ul, .feed-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .feed-content li {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin-bottom: 0.8rem;
        text-align: justify;
    }

    .feed-content strong {
        color: var(--red);
    }

    .feed-cta-box {
        text-align: center;
        margin-top: 3.5rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border-color);
    }

    .feed-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        background-color: var(--white);
        color: var(--red) !important;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 1rem;
        letter-spacing: 1px;
        padding: 1.2rem 2.5rem;
        border-radius: 30px;
        text-decoration: none;
        transition: var(--transition);
        border: 2px solid #8B1E1E;
    }

    .feed-btn:hover {
        background-color: var(--red);
        color: var(--white) !important;
        border-color: var(--red);
        box-shadow: 0 6px 20px rgba(212, 63, 58, 0.35);
        transform: translateY(-2px);
    }

    .feed-btn i {
        font-size: 1.2rem;
    }

    /* Alpona Divider */
    .alpona-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 2rem 0;
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

    @media (max-width: 768px) {
        .feed-content {
            padding: 2rem 1.5rem;
        }
        .feed-content h2 {
            font-size: 1.8rem;
        }
    }
</style>

<!-- Banner Header -->
<section class="feed-banner">
    <div class="container">
        <h1 class="feed-banner-title">Feedback & Suggestions</h1>
        <span class="feed-banner-subtitle">Your Voice Matters</span>
    </div>
</section>

<!-- Content Section -->
<section class="feed-sec">
    <div class="feed-container">
        <div class="feed-content">
            <h2>Community Feedback & Suggestions Guidelines</h2>
            <p>At the Bengali Cultural Association (BCA), we continuously strive to build a more connected, engaged, and vibrant community. We welcome all constructive opinions, suggestions, complaints, and creative ideas from our members to improve our website content, cultural festivals, and community welfare initiatives.</p>

            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>

            <p>Feedback is critical in shaping the future events of the association, and we strongly encourage members and their children to share their valuable recommendations. If you have any suggestions regarding event execution, budget allocations, website improvements, or new cultural activities, please fill out the official Feedback Form.</p>

            <h3>How to submit your feedback:</h3>
            <ol>
                <li>Download the official <strong>Feedback Form</strong> using the action button below.</li>
                <li>Open the downloaded Word Document on your device. You can fill it out electronically by typing directly into the spaces provided.</li>
                <li>Alternatively, you can print the document and fill it out manually with your comments and contact details.</li>
                <li>Once completed, you can email a scanned copy or the filled Word document directly to the Association Administrator.</li>
                <li>You may also submit a printed physical copy of the form directly at the next Executive Committee meeting.</li>
            </ol>

            <div class="feed-cta-box">
                <p>Click below to open and download the official Feedback Form.</p>
                <a href="images/Feedback Form.docx" target="_blank" download="Feedback Form.docx" class="feed-btn">
                    <i class="fa-solid fa-file-word"></i>
                    <span>Feedback Form</span>
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
