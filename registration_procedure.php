<?php
require_once 'config.php';
// Include the shared header
include 'includes/header.php';
?>

<style>
    /* ==========================================================================
       REGISTRATION PROCEDURE SPECIFIC STYLES
       ========================================================================== */
    .reg-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .reg-banner::before {
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

    .reg-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .reg-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .reg-sec {
        padding: 6.5rem 0;
        background-color: var(--cream);
    }

    .reg-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .reg-content {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 3.5rem;
        box-shadow: var(--shadow-sm);
    }

    .reg-content h2 {
        font-family: var(--font-headings);
        color: var(--red);
        font-size: 2.2rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 0.8rem;
        text-align: center;
    }

    .reg-content h3 {
        font-family: var(--font-headings);
        color: var(--dark);
        font-size: 1.5rem;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
    }

    .reg-content p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin-bottom: 1.5rem;
        text-align: justify;
    }

    .reg-content ul, .reg-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .reg-content li {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin-bottom: 0.8rem;
        text-align: justify;
    }

    .reg-content strong {
        color: var(--red);
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
        .reg-content {
            padding: 2rem 1.5rem;
        }
        .reg-content h2 {
            font-size: 1.8rem;
        }
    }
</style>

<!-- Banner Header -->
<section class="reg-banner">
    <div class="container">
        <h1 class="reg-banner-title">Registration & Authorization</h1>
        <span class="reg-banner-subtitle">Procedure for Registration</span>
    </div>
</section>

<!-- Content Section -->
<section class="reg-sec">
    <div class="reg-container">
        <div class="reg-content">
            <h2>Procedure for Registration - Authorization</h2>
            <p>“You Speak” Menu is designed to encourage member’s participation in the website for sending Blogs, Images, Videos, Personal Imaginative Creations such as Stories, Compositions, Poems, Paintings, Achievements etc., app application, Your Comments, Your Suggestions and such things. It is needless to mention that such documents need to be clean and ethical documents adding value to the members and the community. Hence, the documents will require validation and authorization before submitting into the Portal for viewing by all. Pre-Authorization will be done by giving a unique identity to the members willing to participate. Following steps will be required to follow:</p>

            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>

            <ol>
                <li>A simple “Registration Form” will be filled on line in the website by entering the details required in the Form. The Format of the Form is illustrated separately.</li>
                <li>Members will provide all information desired in the form and create his/her own User ID.</li>
                <li>The filled up Form will be ‘Submitted’ to the Administrator under the Head “Registration of Members”.</li>
                <li>One time Password will be generated from the system and the members will have to remember same. There will be no provision to change Password and in case of the Password being forgotten, the entire procedure will have to be done fresh to generate a new Password.</li>
                <li>While the system will generate a Password, the document will flow to the Administrator automatically and he will review the credentials of the applicant member and authorize the member for posting various “You Speak” ingredients.</li>
                <li>“You Speak” ingredients on submitting by the member will once again go to the Administrator and on his review of the document of its sanctity, hygiene, authenticity, originality etc. will be moved into prescribed Portal Space. Images and Videos will go to the Archive while the other documents will find a space in the ‘You Speak” section only.</li>
                <li>It is important to note here that any duplication of documents will be avoided by the member. Also any document not appearing original, will also be critically reviewed and may not be posted in the portal. Therefore, it may be noted that all documents submitted by members will not necessarily find place in the portal.</li>
                <li>Interface app application will be available for only authorized members as described above. This application is for uploading Images and Videos.</li>
                <li>Any document appearing to have any confidentiality criteria will be avoided by the members. Such documents will be outrightly rejected.</li>
                <li>Connect Links are provided for various interfaces, which also shall be accessible through these Passwords only.</li>
                <li>The Association may in due course of time plan to reward the best document award to the member, whose document has been published.</li>
                <li>Members must write his/her name in Block Letters, Date of Submission and Year of the submission on every document, without which the documents will not be accepted.</li>
            </ol>

            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>

            <p>We would like to make this section as interactive as possible. Member's children can also participate in this section by initiating the entire registration process. For any queries Administrator may be contacted. The details of Administrator shall be indicated on the Home Page.</p>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
