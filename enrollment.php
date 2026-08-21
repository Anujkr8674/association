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
        max-width: 900px;
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
        text-align: center;
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
        text-align: justify;
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
        
        background-color: var(--white);
        color: var(--red) !important;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 1rem;
        letter-spacing: 1px;
        padding: 1.2rem 2.5rem;
        border-radius: 30px;
        text-decoration: none;
        /* box-shadow: 0 4px 15px rgba(212, 63, 58, 0.25); */
        transition: var(--transition);
        border: 2px solid #8B1E1E;
    }

    .enroll-btn:hover {
    
        background-color: var(--red);
        color: var(--white) !important;
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
            <h2>Membership Procedure</h2>
            <p><strong>Membership Procedure:</strong> Noida, Sector 62 is a large mini town and several people live here permanently while many others are temporary or transient. Bengali Cultural Association (BCA) is a thirteen year old organization and already has more than two hundred members. A member is a representative of one family. Many Bengali families coming from outside do not find easy acquaintance with the community nor they know the process of connecting with each other. Remaining socially detached often is a cause of pain and cultural isolation and therefore, this segment has been created in the website.</p>

            <p>The website is a great medium to find how and what of the process but how one knows that BCA exists and there is a website! Most often the new entries come to light during the Puja fund collection drives only. However, our endeavor now will be to communicate to all societies and other institutes including our existing members residing in them or nearby societies, to communicate to the new entrants about the BCA and help them become members. Of course, temporary residents hesitate to join BCA and here the website shall provide assistance and guidance. All the festivals like Durga Puja, Lakshmi Puja, Kali Puja and Saraswati Puja are very close to the Bengali community and everybody look forward to join and celebrate with their family members. Circumstances permitting other cultural events are also organized by the Association.</p>

            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>

            <p>The enrolment gives us the data of the community population, their capabilities, skills etc. which can be utilized during our various celebrations. We have become a ‘brand’ now in Sector 62 as no other festival as large as this happens here. We often cross many families in the market places, and common areas of Sector 62 and can always communicate to them regarding our existence and introduce them to the website. The onus of making new members is upon the existing members only. However, it is likely that one knows about the website and can always contact any other member to understand the process or even browse through the website to understand the process by him.</p>

            <p>Members are the lifeline of the Association and new ideas originate only when new and fresh brains amalgamate. The Association directly and indirectly provides a platform for a united community and lots of interaction. People coming from various parts of the country may share their views and thoughts to enrich the Association and make it a true social organization.</p>

            <h3>Steps for Enrolment as a New Member in BCA</h3>
            <p>Please follow the following steps for enrolment as a new member in BCA:</p>
            <ol>
                <li>There is a <strong>“Membership Enrolment”</strong> Form provided for your assistance. The Form is designed to capture as much cultural details as possible and applicants are requested to expose their Skills & Potential along with their children’s interests and cultural capabilities.</li>
                <li>The Form is designed as <strong>‘Word Document’</strong> and can be copied and saved in your own personal folders. You can fill up the word document mostly through the Electronic media. You can expand and reduce the blank spaces.</li>
                <li>Having filled the document electronically, you have to obtain validation from at least two existing members with their details and signature. Also you have to sign the document. Therefore, you have to get the document printed and signed off from all parties mentioned above.</li>
                <li>You have to scan the document and post it to the Administrator under the Head <strong>“Member’s Records”</strong>.</li>
                <li>Administrator will verify the document and ‘Submit’ it into the Portal for viewing to all and your name will be uploaded as member in the list.</li>
                <li>Simultaneously, you have to hand over the hard copy of the Registration Form to the Administrator for records.</li>
                <li>Any new membership fees etc. shall be notified from time to time through the <strong>‘News Corner’</strong> of the website. Willing applicants are requested to keep visiting the website. Efforts will be made to communicate the information through SMS or E Mail as well, in due course.</li>
            </ol>

            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>

            <p>The Association wishes to have high ethical standards and behavior amongst the community as well as outside, in line with rich culture of Bengalis historically. Your membership will be valuable for the Association and give strength to the continual evolution of the Association in bringing more variety and uniqueness to the events.</p>

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
