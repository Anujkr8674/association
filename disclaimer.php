<?php
require_once 'config.php';
// Include the shared header
include 'includes/header.php';
?>

<style>
    /* ==========================================================================
       DISCLAIMER PAGE SPECIFIC STYLES
       ========================================================================== */
    .disc-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .disc-banner::before {
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

    .disc-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .disc-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .disc-sec {
        padding: 6.5rem 0;
        background-color: var(--cream);
    }

    .disc-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .disc-content {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 3.5rem;
        box-shadow: var(--shadow-sm);
    }

    .disc-content h2 {
        font-family: var(--font-headings);
        color: var(--red);
        font-size: 2.2rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 0.8rem;
        text-align: center;
    }

    .disc-content p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin-bottom: 1.5rem;
        text-align: justify;
    }

    .disc-content strong {
        color: var(--red);
    }

    /* Alpona Divider */
    .alpona-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 2.5rem 0;
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
        .disc-content {
            padding: 2rem 1.5rem;
        }
        .disc-content h2 {
            font-size: 1.8rem;
        }
    }
</style>

<!-- Banner Header -->
<section class="disc-banner">
    <div class="container">
        <h1 class="disc-banner-title">Disclaimer</h1>
        <span class="disc-banner-subtitle">Terms of Information & Content Use</span>
    </div>
</section>

<!-- Content Section -->
<section class="disc-sec">
    <div class="disc-container">
        <div class="disc-content">
            <h2>Disclaimer</h2>
            <p>The content of this website is for general information only. We do not represent any individual or community particularly, in constructing the content and give no warranties of any nature about the accuracy, completeness, availability, authenticity and applicability of any information, pictures and graphics provided or will be provided in future, in the website. Neither is this site meant to provide any kind of commercial or non commercial services and exclusively meant for basic information about the evolution of the Bengali Cultural Association, Sector 62, Noida. Any reliance placed by the viewer on the content of this website will be at his/her own risk and the Association takes no responsibility whatsoever. However, our endeavor will be to keep all information as up to date, correct and interactive as possible. We are not liable for any consequential loss, emotional or any other, directly or indirectly, because of the content of the website. The design has been done for sharing information with the community at large but at the same time it has been ensured that ethical and moral practices are ensured by all members while interacting with the website interfaces. The website also features interactive platforms, and views and suggestions of the members are encouraged to be received through proper administrative control. We do not undertake any responsibility of publishing all such communications; without having adequate control about value addition or having objectionable content. Any views expressed by readers will be his/her own and will not be taken as endorsed by the Executive Committee or any authorized administrator for the website. There are many interfaces/links with other websites/ media in our website. We neither take any responsibility about the availability of these sites/ media, nor about the authenticity of the contents thereof. The facility is installed for the general purpose of creating awareness and a bond in the community beyond Sector 62, Noida. Every effort will be made to keep the website up and active, however, the Association takes no responsibility or guarantee for making the website available at all times due to reasons beyond our control.</p>

            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>

            <p><strong>Disclaimer:</strong> The above document is only to generate enthusiasm among the community and not to force anybody’s ideology. This is 17th year nonstop the Durga Puja is celebrated and hats off to the organizers who toil hard. Let us give a different color this time.</p>

            <p>Wish you all a happy web journey.</p>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
