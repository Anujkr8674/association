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
        max-width: 950px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .enroll-grid {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
    }

    .enroll-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 3.5rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    .enroll-card:hover {
        background-color: var(--red);
        border-color: var(--gold);
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
    }

    .enroll-card h2, .enroll-card h3 {
        font-family: var(--font-headings);
        color: var(--red);
        font-size: 2.2rem;
        margin-top: 0;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 0.8rem;
        transition: color 0.3s ease, border-color 0.3s ease;
    }

    .enroll-card h3 {
        font-size: 1.6rem;
    }

    .enroll-card:hover h2, .enroll-card:hover h3 {
        color: var(--gold);
        border-bottom-color: rgba(255, 255, 255, 0.15);
    }

    .enroll-card p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin-bottom: 1.5rem;
        text-align: justify;
        transition: color 0.3s ease;
    }

    .enroll-card p:last-of-type {
        margin-bottom: 0;
    }

    .enroll-card:hover > p {
        color: rgba(255, 255, 255, 0.95);
    }

    .enroll-card:hover .enroll-cta-box p {
        color: rgba(255, 255, 255, 0.95);
    }

    .enroll-card strong {
        color: var(--red);
        transition: color 0.3s ease;
    }

    .enroll-card:hover > p strong,
    .enroll-card:hover .enroll-cta-box p strong {
        color: var(--gold) !important;
    }

    /* Steps Layout styling */
    .steps-container {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        margin-top: 2rem;
    }

    .step-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 1.5rem 2rem;
        display: flex;
        gap: 1.5rem;
        align-items: center;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    .step-card:hover {
        background-color: var(--red);
        border-color: var(--gold);
        box-shadow: var(--shadow-lg);
        transform: translateX(5px);
    }

    .step-number {
        font-family: var(--font-headings);
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--gold);
        background-color: var(--primary-bg);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    .step-card:hover .step-number {
        background-color: var(--white);
        color: var(--red);
    }

    .step-text {
        font-size: 1.05rem;
        line-height: 1.7;
        color: var(--dark);
        margin: 0 !important;
        text-align: justify;
        transition: color 0.3s ease;
        flex-grow: 1;
    }

    .step-card:hover .step-text {
        color: rgba(255, 255, 255, 0.95);
    }

    .step-card:hover .step-text strong {
        color: var(--gold) !important;
    }

    /* CTA Section */
    .enroll-cta-box {
        text-align: center;
        margin-top: 1.5rem;
        border-top: 1px solid var(--border-color);
        padding-top: 2rem;
        transition: border-color 0.3s ease;
    }

    .enroll-card:hover .enroll-cta-box {
        border-top-color: rgba(255, 255, 255, 0.15);
    }

    .enroll-btn {
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
        transition: all 0.3s ease;
        border: 2px solid #8B1E1E;
    }

    .enroll-card:hover .enroll-btn {
        background-color: var(--white);
        color: var(--red) !important;
        border-color: var(--white);
    }

    .enroll-card:hover .enroll-btn:hover {
        background-color: var(--gold);
        color: var(--white) !important;
        border-color: var(--gold);
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

    .enroll-card:hover .alpona-divider::before,
    .enroll-card:hover .alpona-divider::after {
        background-color: rgba(255, 255, 255, 0.15);
    }

    .alpona-divider svg {
        width: 24px;
        height: 24px;
        fill: var(--gold);
        margin: 0 1.2rem;
        transition: fill 0.3s ease;
    }

    .enroll-card:hover .alpona-divider svg {
        fill: var(--gold);
    }

    @media (max-width: 768px) {
        .enroll-card {
            padding: 2.2rem 1.8rem;
        }
        .enroll-card h2 {
            font-size: 1.8rem;
        }
        .step-card {
            flex-direction: column;
            gap: 1rem;
            padding: 1.5rem;
            align-items: flex-start;
        }
        .step-number {
            width: 44px;
            height: 44px;
            font-size: 1.5rem;
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
        <div class="enroll-grid">
            
            <!-- Card 1: Introduction -->
            <div class="enroll-card">
                <h2>Membership Procedure</h2>
                <p><strong>Membership Procedure:</strong> Noida, Sector 62 is a large mini town and several people live here permanently while many others are temporary or transient. Bengali Cultural Association (BCA) is a thirteen year old organization and already has more than two hundred members. A member is a representative of one family. Many Bengali families coming from outside do not find easy acquaintance with the community nor they know the process of connecting with each other. Remaining socially detached often is a cause of pain and cultural isolation and therefore, this segment has been created in the website.</p>
                <p>The website is a great medium to find how and what of the process but how one knows that BCA exists and there is a website! Most often the new entries come to light during the Puja fund collection drives only. However, our endeavor now will be to communicate to all societies and other institutes including our existing members residing in them or nearby societies, to communicate to the new entrants about the BCA and help them become members. Of course, temporary residents hesitate to join BCA and here the website shall provide assistance and guidance. All the festivals like Durga Puja, Lakshmi Puja, Kali Puja and Saraswati Puja are very close to the Bengali community and everybody look forward to join and celebrate with their family members. Circumstances permitting other cultural events are also organized by the Association.</p>
            </div>

            <!-- Card 2: Community Impact & Onus -->
            <div class="enroll-card">
                <h3>Community Impact & Network</h3>
                <p>The enrolment gives us the data of the community population, their capabilities, skills etc. which can be utilized during our various celebrations. We have become a ‘brand’ now in Sector 62 as no other festival as large as this happens here. We often cross many families in the market places, and common areas of Sector 62 and can always communicate to them regarding our existence and introduce them to the website. The onus of making new members is upon the existing members only. However, it is likely that one knows about the website and can always contact any other member to understand the process or even browse through the website to understand the process by him.</p>
                <p>Members are the lifeline of the Association and new ideas originate only when new and fresh brains amalgamate. The Association directly and indirectly provides a platform for a united community and lots of interaction. People coming from various parts of the country may share their views and thoughts to enrich the Association and make it a true social organization.</p>
            </div>

            <!-- Card 3: Step-by-Step Enrollment Steps -->
            <div class="enroll-card">
                <h3>Steps for Enrolment as a New Member in BCA</h3>
                <p>Please follow the following steps for enrolment as a new member in BCA:</p>
                
                <div class="steps-container">
                    <div class="step-card">
                        <div class="step-number">01</div>
                        <p class="step-text">There is a <strong>“Membership Enrolment”</strong> Form provided for your assistance. The Form is designed to capture as much cultural details as possible and applicants are requested to expose their Skills & Potential along with their children’s interests and cultural capabilities.</p>
                    </div>
                    
                    <div class="step-card">
                        <div class="step-number">02</div>
                        <p class="step-text">The Form is designed as <strong>‘Word Document’</strong> and can be copied and saved in your own personal folders. You can fill up the word document mostly through the Electronic media. You can expand and reduce the blank spaces.</p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">03</div>
                        <p class="step-text">Having filled the document electronically, you have to obtain validation from at least two existing members with their details and signature. Also you have to sign the document. Therefore, you have to get the document printed and signed off from all parties mentioned above.</p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">04</div>
                        <p class="step-text">You have to scan the document and post it to the Administrator under the Head <strong>“Member’s Records”</strong>.</p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">05</div>
                        <p class="step-text">Administrator will verify the document and ‘Submit’ it into the Portal for viewing to all and your name will be uploaded as member in the list.</p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">06</div>
                        <p class="step-text">Simultaneously, you have to hand over the hard copy of the Registration Form to the Administrator for records.</p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">07</div>
                        <p class="step-text">Any new membership fees etc. shall be notified from time to time through the <strong>‘News Corner’</strong> of the website. Willing applicants are requested to keep visiting the website. Efforts will be made to communicate the information through SMS or E Mail as well, in due course.</p>
                    </div>
                </div>
            </div>

            <!-- Card 4: Conclusion & Download CTA -->
            <div class="enroll-card">
                <h3>Our Shared Values</h3>
                <p>The Association wishes to have high ethical standards and behavior amongst the community as well as outside, in line with rich culture of Bengalis historically. Your membership will be valuable for the Association and give strength to the continual evolution of the Association in bringing more variety and uniqueness to the events.</p>
                
                <div class="alpona-divider">
                    <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                </div>

                <div class="enroll-cta-box">
                    <p>Click below to open the registration form. You can print, fill, and submit it directly to our office.</p>
                    <a href="images/membership_enrolment_form.docx" target="_blank" download="membership_enrolment_form.docx" class="enroll-btn">
                        <i class="fa-solid fa-file-word"></i>
                        <span>Enrollment Form</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
