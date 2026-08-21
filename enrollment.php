<?php
require_once 'config.php';
// Include the shared header
include 'includes/header.php';
?>

<style>
    /* ==========================================================================
       ENROLLMENT PAGE SPECIFIC STYLES
       ========================================================================== */
    .enroll-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .enroll-banner::before {
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

    .enroll-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .enroll-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .enroll-sec {
        padding: 6.5rem 0;
        background-color: var(--cream);
    }

    .enroll-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .enroll-content {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 3.5rem;
        box-shadow: var(--shadow-sm);
    }

    .enroll-content h2 {
        font-family: var(--font-headings);
        color: var(--red);
        font-size: 2.2rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 0.8rem;
    }

    .enroll-content h3 {
        font-family: var(--font-headings);
        color: var(--dark);
        font-size: 1.5rem;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
    }

    .enroll-content p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin-bottom: 1.5rem;
    }

    .enroll-content ul, .enroll-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .enroll-content li {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin-bottom: 0.8rem;
    }

    .enroll-content strong {
        color: var(--red);
    }

    .enroll-cta-box {
        text-align: center;
        margin-top: 3.5rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border-color);
    }

    .enroll-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        background-color: var(--red);
        color: var(--white) !important;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 1rem;
        letter-spacing: 1px;
        padding: 1.2rem 2.5rem;
        border-radius: 30px;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(212, 63, 58, 0.25);
        transition: var(--transition);
        border: 2px solid transparent;
    }

    .enroll-btn:hover {
        background-color: var(--white);
        color: var(--red) !important;
        border-color: var(--red);
        box-shadow: 0 6px 20px rgba(212, 63, 58, 0.35);
        transform: translateY(-2px);
    }

    .enroll-btn i {
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
        .enroll-content {
            padding: 2rem 1.5rem;
        }
        .enroll-content h2 {
            font-size: 1.8rem;
        }
    }
</style>

<!-- Banner Header -->
<section class="enroll-banner">
    <div class="container">
        <h1 class="enroll-banner-title">Membership Enrollment</h1>
        <span class="enroll-banner-subtitle">Join the Bengali Cultural Association</span>
    </div>
</section>

<!-- Content Section -->
<section class="enroll-sec">
    <div class="enroll-container">
        <div class="enroll-content">
            <h2>Association Membership Guidelines</h2>
            <p>Welcome to the Bengali Cultural Association. We are a vibrant community dedicated to celebrating, preserving, and sharing the rich cultural heritage, traditions, and literature of Bengal. By enrolling as a registered member, you gain voting rights in administrative elections, invitations to all official cultural celebrations, and access to our member network and community support programs.</p>

            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>

            <h3>Types of Membership</h3>
            <p>Our association offers three distinct membership models tailored to different levels of support and commitment:</p>
            <ul>
                <li><strong>Patron Membership:</strong> Designed for prominent community members wishing to provide strong long-term mentorship and support. Benefits include lifetime administrative privileges and front-row reservations during major annual festivals.</li>
                <li><strong>Life Membership:</strong> A one-time subscription contribution that secures lifetime voting rights and participation in all core committees and general body meetings.</li>
                <li><strong>General Membership:</strong> An annual subscription-based model ideal for active families and individuals. Requires yearly renewals to maintain voting rights and register for cultural performances.</li>
            </ul>

            <h3>Eligibility & Procedure</h3>
            <p>To successfully register as a member, please follow these guidelines:</p>
            <ol>
                <li>You must be at least 18 years of age at the time of submitting the application.</li>
                <li>The membership application must be sponsored or recommended by at least one existing active member of the association.</li>
                <li>Download the official <strong>Enrollment Form</strong> using the action button below.</li>
                <li>Fill out all required details in the form, attach a passport-sized photograph, and sign the declaration.</li>
                <li>Submit the completed physical copy to the Executive Committee Office along with the applicable membership subscription fee.</li>
            </ol>

            <div class="enroll-cta-box">
                <p>Click below to open the registration form. You can print, fill, and submit it directly to our office.</p>
                <a href="images/membership_enrolment_form.docx" target="_blank" download="membership_enrolment_form.docx" class="enroll-btn">
                    <i class="fa-solid fa-file-word"></i>
                    <span>Enrollment Form</span>
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
