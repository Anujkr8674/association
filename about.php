<?php
// Include the shared header
include 'includes/header.php';
?>


<link rel="stylesheet" href="includes/style.css">
<script src="includes/script.js" defer></script>

<style>
    /* ==========================================================================
       ABOUT PAGE SPECIFIC STYLES
       ========================================================================== */
    /* Page Banner Header */
    .page-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .page-banner::before {
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

    .page-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .page-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    /* Core About Sections */
    .about-sec {
        padding: 6.5rem 0;
    }

    .about-sec-alt {
        background-color: var(--secondary-bg);
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .about-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 4rem;
        align-items: center;
    }

    .about-text-content {
        line-height: 1.85;
        font-size: 1.05rem;
    }

    .about-text-content h3 {
        font-size: 1.9rem;
        margin-bottom: 1.25rem;
        color: var(--red);
    }

    .about-image-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-md);
        position: relative;
        border-top: 4px solid var(--red);
    }

    .about-card-img {
        border-radius: var(--border-radius);
        width: 100%;
        height: 350px;
        object-fit: cover;
    }

    /* Timeline Section Styles */
    .timeline {
        position: relative;
        max-width: 800px;
        margin: 3.5rem auto 0 auto;
        padding: 1rem 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        background-color: rgba(139, 30, 30, 0.2);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 3.5rem;
        width: 50%;
        padding-right: 3rem;
        text-align: right;
    }

    .timeline-item:nth-child(even) {
        left: 50%;
        padding-right: 0;
        padding-left: 3rem;
        text-align: left;
    }

    .timeline-marker {
        width: 16px;
        height: 16px;
        background-color: var(--white);
        border: 4px solid var(--red);
        border-radius: 50%;
        position: absolute;
        top: 6px;
        right: -8px;
        z-index: 10;
        transition: var(--transition);
    }

    .timeline-item:nth-child(even) .timeline-marker {
        right: auto;
        left: -8px;
    }

    .timeline-item:hover .timeline-marker {
        background-color: var(--gold);
        border-color: var(--gold);
        transform: scale(1.3);
    }

    .timeline-year {
        font-family: var(--font-headings);
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--red);
        margin-bottom: 0.5rem;
        display: block;
    }

    .timeline-content {
        background-color: var(--white);
        padding: 1.8rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        display: inline-block;
        max-width: 350px;
        transition: var(--transition);
        border: 1px solid var(--border-color);
        border-top: 3px solid var(--red);
        text-align: left;
    }

    .timeline-item:nth-child(even) .timeline-content {
        text-align: left;
    }

    .timeline-content h4 {
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
        color: var(--dark);
    }

    .timeline-content p {
        font-size: 0.9rem;
        margin-bottom: 0;
        line-height: 1.6;
    }

    .timeline-item:hover .timeline-content {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
        border-color: rgba(201, 154, 46, 0.3);
    }

    /* Vision Mission Grid */
    .vm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        margin-top: 2rem;
    }

    .vm-card {
        background-color: var(--white);
        padding: 3.5rem 2.8rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border-top: 4px solid var(--red);
        transition: var(--transition);
        border-left: 1px solid var(--border-color);
        border-right: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .vm-card-mission {
        border-top-color: var(--gold);
    }

    .vm-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .vm-icon-box {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: var(--secondary-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--red);
        font-size: 1.5rem;
        margin-bottom: 1.8rem;
    }

    .vm-card-mission .vm-icon-box {
        color: var(--gold);
    }

    .vm-title {
        font-size: 1.6rem;
        margin-bottom: 1rem;
        color: var(--dark);
    }

    .vm-text {
        font-size: 0.98rem;
        line-height: 1.75;
        color: var(--text-muted);
        margin-bottom: 0;
    }

    /* Objectives Grid */
    .obj-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.8rem;
        margin-top: 3.5rem;
    }

    .obj-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        padding: 2.5rem 1.8rem;
        border-radius: var(--border-radius);
        text-align: center;
        transition: var(--transition-slow);
    }

    .obj-card:hover {
        background-color: var(--red);
        border-color: var(--red);
        color: var(--white);
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .obj-card:hover h4 {
        color: var(--white);
    }

    .obj-card:hover p {
        color: rgba(255, 255, 255, 0.85);
    }

    .obj-card:hover i {
        color: var(--gold);
    }

    .obj-card i {
        font-size: 2.2rem;
        color: var(--gold);
        margin-bottom: 1.5rem;
        transition: var(--transition);
    }

    .obj-card h4 {
        font-size: 1.2rem;
        margin-bottom: 0.8rem;
        transition: var(--transition);
        color: var(--dark);
    }

    .obj-card p {
        font-size: 0.9rem;
        line-height: 1.55;
        margin-bottom: 0;
        transition: var(--transition);
        color: var(--text-muted);
    }

    /* Activities section */
    .activity-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2.5rem;
    }

    .activity-card {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        display: flex;
        transition: var(--transition-slow);
        border: 1px solid var(--border-color);
        border-left: 5px solid var(--gold);
    }

    .activity-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-left-color: var(--red);
    }

    .activity-img-wrapper {
        width: 180px;
        flex-shrink: 0;
        overflow: hidden;
    }

    .activity-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition-slow);
    }

    .activity-card:hover .activity-img {
        transform: scale(1.06);
    }

    .activity-body {
        padding: 1.8rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .activity-title {
        font-size: 1.35rem;
        margin-bottom: 0.5rem;
        color: var(--dark);
    }

    .activity-text {
        font-size: 0.9rem;
        margin-bottom: 0;
        line-height: 1.65;
        color: var(--text-muted);
    }

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS
       ========================================================================== */
    @media (max-width: 991px) {
        .about-grid {
            grid-template-columns: 1fr;
            gap: 3.5rem;
        }
        .about-image-card {
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
        }
        .vm-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        .obj-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .activity-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .timeline::before {
            left: 20px;
        }
        .timeline-item {
            width: 100%;
            padding-left: 3.5rem;
            padding-right: 0;
            text-align: left;
        }
        .timeline-item:nth-child(even) {
            left: 0;
            padding-left: 3.5rem;
        }
        .timeline-marker {
            left: 12px;
            right: auto;
        }
        .timeline-item:nth-child(even) .timeline-marker {
            left: 12px;
        }
        .timeline-content {
            max-width: 100%;
        }
    }

    @media (max-width: 576px) {
        .obj-grid {
            grid-template-columns: 1fr;
        }
        .activity-card {
            flex-direction: column;
            border-left: none;
            border-top: 5px solid var(--gold);
        }
        .activity-card:hover {
            border-top-color: var(--red);
        }
        .activity-img-wrapper {
            width: 100%;
            height: 180px;
        }
    }

    /* Highlights Section */
    .highlight-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-top: 3.5rem;
    }

    .highlight-card {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        padding: 2.5rem 1.8rem;
        border-radius: var(--border-radius);
        text-align: center;
        transition: var(--transition-slow);
        border-top: 4px solid var(--gold);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .highlight-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-top-color: var(--red);
    }

    .highlight-card i {
        font-size: 2.2rem;
        color: var(--red);
        margin-bottom: 1.25rem;
        background-color: rgba(212, 63, 58, 0.05);
        width: 65px;
        height: 65px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }

    .highlight-card:hover i {
        background-color: var(--red);
        color: var(--gold);
    }

    .highlight-card h4 {
        font-size: 1.25rem;
        margin-bottom: 0.85rem;
        color: var(--red);
        font-family: var(--font-headings);
        font-weight: 700;
    }

    .highlight-card p {
        font-size: 0.92rem;
        line-height: 1.6;
        color: var(--text-muted);
        margin-bottom: 0;
    }

    @media (min-width: 992px) {
        .highlight-card:nth-child(7) {
            grid-column: 2 / 3;
        }
    }

    @media (max-width: 991px) {
        .highlight-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .highlight-card:nth-child(7) {
            grid-column: span 2;
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .highlight-grid {
            grid-template-columns: 1fr;
        }
        .highlight-card:nth-child(7) {
            grid-column: span 1;
            max-width: 100%;
        }
    }
</style>

<!-- Banner Header -->
<section class="page-banner">
    <div class="container">
        <h1 class="page-banner-title">About Our Association</h1>
        <span class="page-banner-subtitle">Preserving Heritage • Connecting Hearts</span>
    </div>
</section>

<!-- Section 1: About Association -->
<!-- <section class="about-sec" id="association">
    <div class="container">
        <div class="about-grid">
            <div class="about-text-content">
                <h3>Our Roots and Identity</h3>
                <p>
                    Founded in the year 2001, the Bengali Cultural Association was formed by a passionate group of families who desired to maintain their traditional ties and cultural expression. What started as a small, informal assembly of ten households has now blossomed into a thriving organization hosting hundreds of active families.
                    <br><br>
                    We serve as a robust platform for promoting traditional arts, classical literature, and the rich culinary heritage of Bengal. Our objective is to cultivate an appreciation for diversity, while keeping the Bengali language and history thriving within diaspora households.
                </p>
            </div>
            <div class="about-image-card">
                <img src="https://images.unsplash.com/photo-1590073844006-33379778ae09?q=80&w=600" alt="Association Members" class="about-card-img" loading="lazy">
            </div>
        </div>
    </div>
</section> -->



<section class="welcome-section">
    <div class="container">
        <div class="welcome-grid">
            <!-- Left: Cultural image wrapper -->
           
            <!-- Right: Introduction text -->
            <div>
                <!-- <span class="welcome-subtitle">স্বাগতম (Welcome)</span> -->
                <h2 class="welcome-title">
History of Bengali Cultural Association, Noida sector 62</h2>
            <div class="welcome-text-container">

                <p class="welcome-text">
                    <b>History of Bengali Cultural Association, Noida Sector 62, Uttar Pradesh</b> is a true 21st Century creation. Housing Societies started sprouting here and there and by <b>2001</b>, it looked like a skeleton. There were hardly proper roads and construction material sprayed all over and the noise of concrete mixers provided a monotonous music in the atmosphere. With this background walked in a few Bengalis; who got early possession of their houses in some societies which were completed or partly completed. It was almost end of <b>2001</b> when this small group moved into <b>Srijan Apartments</b>, all from PDIL. They were M/S Shekhar Chandra Sen Sharma, Atanu Chakraborty, Dhiman Dutta, Late S Chattaraj & Proshanto Mukherjee. Mr. Sen Sharma, who was earlier living in Vasundhara Enclave, had been Secretary of Local Association and was already experienced in organizing <b>Durga Puja</b> there. Having shifted base, the idea remained with him and the small team started discussing and exploring the possibility of organizing a Puja in <b>Sector 62</b>.
                </p>

                <p class="welcome-text">
                    The idea took roots thick and fast and Bengali hunting started. No one knew anyone else living in a few more Apartments that existed by <b>2002</b>. In this half deserted mini township, gradually acquaintances were established through deep exploration and soon M/S Debashish Chatterjee, Kalyan Dey, U. K. Ray, Ujjwal Bhattacharya, Nabendu Lodh, Gautam Roy, Samir Mukherjee, Subroto Bose, Mukul Mitra and T. Bandopadhyay (first highest collector) were traced and the team suddenly became strong with about <b>15 to 20 members</b>.
                </p>

                <p class="welcome-text">
                    Durga Puja is close to every Bengali’s heart and the idea was accepted immediately by everybody. A skeleton Committee was formed and the first of the <b>Sector 62 Durga Puja</b> was launched. A budget of <b>Rs. 70,000/-</b> (Seventy Thousand Only) was estimated and approved but as the collection process started the bag was full with <b>Rs. 3 Lakhs</b> (Three Lakhs). It was nothing but Ma Durga’s blessings and one can easily imagine the elevated spirit of the unnamed Committee. Thus the <b>first Durga Puja in Sector 62</b> was celebrated in <b>2002</b> at a small park adjacent to <b>Nirupam Vatika</b> with great pomp and show. <b>Bengali Cultural Association (BCA)</b> was subsequently registered as an organization in <b>2003</b>.
                </p>

                <p class="welcome-text">
                    Puja was a grand success and a huge encouragement for the few members. The small function soon attracted many more new faces and the committee started bulging. After the first Puja in this park, the venue was shifted to the <b>Tot Mall Park</b> to accommodate big arrangements and a larger crowd. Soon the Durga Puja became the <b>Centre of attraction for the community in Sector 62</b> and the cultural activities broadened and engagement of people multiplied.
                </p>

                <p class="welcome-text">
                    Durga Puja continued to be organized at the <b>Tot Mall Park for 11 years consecutively</b> but had to be shifted to <b>B Block Park</b> due to conversion of the playground into a garden. This marked the <b>Twentieth Year of celebration</b> with a strong membership base of more than <b>two hundred families</b>. Many Patrons pitched in and the fund collection drive was innovated. The committee will always be grateful to these selfless promoters. It is needless to say that the <b>Noida Authorities</b> extended all the help from time to time and they deserve our profuse thanks. Today the <b>Durga Puja is the single largest annual social event in Sector 62</b>.
                </p>

                <p class="welcome-text">
                    <b>Bengali Cultural Association (BCA)</b> is a <b>‘No Profit No Loss’ Socio Religious Cultural Association</b> mooted with the objective of spreading the Bengali traditions and culture amongst the entire community irrespective of where they belong. While <b>Durga Puja</b> is celebrated on a large scale, <b>Lakshmi Puja, Kali Puja and Saraswati Puja</b> are also celebrated with full rituals and devotion. The Pujas are celebrated with a view to keeping the mythological values, written in various scriptures, fresh in the minds of the new generation; accompanied by the joy of festivities through various cultural activities organized during the Puja days.
                </p>

                <p class="welcome-text">
                    This is also an effort to integrate the society at large and remind them about the rich traditions our ancestors had. Cultures are easily absorbed by people when packaged with <b>fun, frolic, prayers, fasting, pious ceremonies, eateries, exhibitions, cultural programs, competitions and shopping</b>, and thus the engagement is severe. Many people literally weep after the celebrations are over; so much is the involvement.
                </p>

                <p class="welcome-text">
                    <b>Lakshmi Puja</b> is celebrated on the Full Moon day after the Vijaya Dashami. Many communities celebrate Lakshmi Puja during Dipawali. In Bengali culture, <b>Kali Puja</b> is celebrated during Dipawali. The story behind this is that Ma Kali destroyed all powerful Rakhshasas (Demons) blessed with lots of boons and liberated the heaven forever from the clutches of the evil powers. <b>Saraswati Puja</b> is celebrated during the Spring Season (Basant) when the mother earth is calm and soothing and is also known as Basant Panchami to many communities. Ma Saraswati is known to be Goddess of Knowledge and small children are inducted into formal scribbling in front of the Devi by the priests or by elders. This is considered to be auspicious. In Bengali it is called <b>“Haate Khori”</b>. Books are also kept at the feet of the Goddess.
                </p>

                <p class="welcome-text">
                    All the above Pujas are celebrated in various forms in different parts of our country by different communities but the key theme everywhere is <b>‘Shakti’</b>. Ma Durga is depicted in her glorious persona killing <b>“Mahishasura”</b>, the most evil Demon, with the trident in her hand. Bengali tradition of Durga Puja is weaved around Ma Parvati’s entire family since <b>Lakshmi, Saraswati, Kartik and Ganesha</b> are known to be her children according to mythology. Thus beneath one canopy; wealth, knowledge, beauty and wisdom are also worshipped.
                </p>

                <p class="welcome-text">
                    <b>Durga Puja is the biggest festival for Bengalis</b> yet millions of Non Bengali devotees join the Puja with equal fun and flair. Bengali style of worshipping is a mix of <b>“Shakti & Peace”</b>. Durga Puja is a family Puja and creates a great bond within the family as well as the community and showers a message of love for each other. <b>Vijaya Dashami</b> is the last day of Puja and the day has many stories of its being but for Bengalis, this is the day of bidding farewell to the Goddess who returns to her eternal abode along with her family.
                </p>

                <p class="welcome-text">
                    The time before the immersion is dedicated to married women who play vermilion with each other seeking blessings from Goddess to bestow long life to their spouse and family. This in Bengali is known as <b>“Sindur Khela”</b>. In the evening people exchange good wishes by hugging each other and celebrate by exchanging sweets which continues for many days. The reverberating sound of <b>Dhaak</b> (a special drum used in Bengal) and sound of conch shell dominates the Puja days and creates a tremendous emotional bonding amongst the Bengalis.
                </p>

                <p class="welcome-text">
                    An extremely dedicated <b>BCA Executive Committee</b> tirelessly works year after year, joined by many volunteers, to make the celebration flawless and enjoyable to the minutest detail. Many selfless Patrons contribute generously and they deserve our highest gratitude as without them the scale of Durga Puja will remain miniscule and probably a non-event. Many more sponsors are showing interest in actively providing funds through various schemes of sponsorship and we welcome and thank them from the bottom of our heart. We also extend our sincere gratitude to the <b>Noida Administration</b> for extending support year after year.
                </p>

                <p class="welcome-text">
                    Traditionally Bengalis buy <b>new clothes</b> and get rid of old ones and can easily be identified in the mob. One will be astonished to see the crowds pouring over the merchandise brought for special sale from all over the country and even from Bangladesh during these days. Hence this is a great opportunity for the merchants to earn good money. Wearing new clothes also signifies leaving old pains and agonies and starting life with gaiety once again.
                </p>

                <p class="welcome-text">
                    We have a vision to include <b>all communities</b> to participate in the grandeur of the Pujas so that the Pujas become a truly <b>socio-religious-cultural event for all</b>. We appeal to all communities to come and join us by taking membership and actively participate in the evolution of the celebration. We intend to create a <b>United Sector 62</b> and exhibit a national responsibility of equality amongst all.
                </p>

                <p class="welcome-text">
                    All readers are welcome at our <b>Pandal in Sector 62, B Block Park</b> this year and take <b>Ma Durga’s blessings</b>.
                </p>

            </div>

            <button type="button" class="btn btn-secondary" id="welcome-read-more-btn">
                Read More <i class="fa-solid fa-chevron-down"></i>
            </button>
            </div>

             <div class="welcome-img-wrapper">
                <img src="https://images.unsplash.com/photo-1590073844006-33379778ae09?q=80&w=1000" alt="Bengali Traditional Welcome Art" class="welcome-img" loading="lazy">
                <!-- SVG traditional conch motif overlay -->
                <svg class="welcome-motif" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5zm-1.5-3.5c-.55 0-1-.45-1-1v-4c0-.55.45-1 1-1s1.45.45 1 1v4c0 .55-.45 1-1 1z"/>
                </svg>
            </div>
        </div>
    </div>
</section>




<!-- Section 2: History & Milestones -->
<section class="about-sec about-sec-alt" id="history">
    <div class="container">
        <div class="section-header">
            <h2>Our Journey</h2>
            <p class="section-subtitle">A walk through the key milestones and events in our association's history.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="timeline">
            <!-- Timeline Item 1 -->
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <span class="timeline-year">2001</span>
                    <h4>The Beginning</h4>
                    <p>A small group of Bengalis moved into Srijan Apartments and began exploring the idea of organizing Durga Puja in Sector 62.</p>
                </div>
            </div>

            <!-- Timeline Item 2 -->
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <span class="timeline-year">2002</span>
                    <h4>First Durga Puja</h4>
                    <p>The first Sector 62 Durga Puja was celebrated near Nirupam Vatika with great enthusiasm and community support.</p>
                </div>
            </div>

            <!-- Timeline Item 3 -->
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <span class="timeline-year">2003</span>
                    <h4>BCA Registered</h4>
                    <p>Bengali Cultural Association was formally registered, creating a strong foundation for its social, religious, and cultural activities.</p>
                </div>
            </div>

            <!-- Timeline Item 4 -->
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <span class="timeline-year">2013</span>
                    <h4>A New Puja Venue</h4>
                    <p>After 11 years at Tot Mall Park, the celebration moved to B Block Park as the earlier playground was converted into a garden.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Vision & Mission -->
<section class="about-sec" id="vision-mission">
    <div class="container">
        <div class="vm-grid">
            <!-- Vision -->
            <div class="vm-card">
                <div class="vm-icon-box">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <h3 class="vm-title">Our Vision</h3>
                <p class="vm-text">
                    To be a leading cultural home that inspires and nurtures the traditional spirit of Bengal. We visualize a community where cultural roots are proudly preserved, integrated with local values, and passed down as an enduring legacy to subsequent generations.
                </p>
            </div>
            <!-- Mission -->
            <div class="vm-card vm-card-mission">
                <div class="vm-icon-box">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <h3 class="vm-title">Our Mission</h3>
                <p class="vm-text">
                    To host high-quality festivals, language programs, and charity drives that unite members. We commit to providing platforms for artists, fostering community support systems, and spreading values of respect, harmony, and socio-cultural advancement.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section: Key Highlights & Activities -->
<section class="about-sec about-sec-alt" id="highlights">
    <div class="container">
        <div class="section-header">
            <h2>Key Activities & Highlights</h2>
            <p class="section-subtitle">A glimpse into the diverse array of events, celebrations, and welfare work we manage.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="highlight-grid">
            <!-- 1. Durga Protima and Pandal -->
            <div class="highlight-card">
                <i class="fa-solid fa-om"></i>
                <h4>Durga Protima & Pandal</h4>
                <p>Every year, we welcome Maa Durga with a magnificent, traditionally crafted clay Protima sculpted by artists from Kumartuli. The grand pandal features intricate thematic decor, serving as a sanctum of peace and devotion for thousands of visitors.</p>
            </div>

            <!-- 2. Cultural programs -->
            <div class="highlight-card">
                <i class="fa-solid fa-masks-theater"></i>
                <h4>Cultural Programs</h4>
                <p>Our stages showcase a vibrant blend of classical music, Rabindra Sangeet recitals, traditional dance dramas, and theatre performances. We invite both national-level artists and provide a platform for local community talent of all age groups.</p>
            </div>

            <!-- 3. Food stalls & Exhibition -->
            <div class="highlight-card">
                <i class="fa-solid fa-bowl-food"></i>
                <h4>Food Stalls & Exhibition</h4>
                <p>Durga Puja is incomplete without Bengal's culinary heritage. The festival grounds feature diverse stalls offering authentic Bengali street food, traditional sweets, handloom sarees, handicrafts, and cultural exhibitions.</p>
            </div>

            <!-- 4. Bengali New Year celebration -->
            <div class="highlight-card">
                <i class="fa-solid fa-calendar-day"></i>
                <h4>Bengali New Year</h4>
                <p>Welcoming Poila Boishakh (Naba Barsho) with cultural processions (Prabhat Pheri), folk music recitals, poetry, and a grand community feast featuring traditional delicacies to usher in peace, prosperity, and joy.</p>
            </div>

            <!-- 5. Annual Picnic -->
            <div class="highlight-card">
                <i class="fa-solid fa-tree"></i>
                <h4>Annual Picnic</h4>
                <p>A winter get-together designed to strengthen family bonds and social networks. Members enjoy outdoor cooking, sports contests like cricket and badminton, group games, and musical jam sessions amidst nature.</p>
            </div>

            <!-- 6. Social Responsibility activities -->
            <div class="highlight-card">
                <i class="fa-solid fa-handshake-angle"></i>
                <h4>Social Responsibility</h4>
                <p>We are committed to giving back to society. The association coordinates blood donation drives, health check-up camps, clothes donation campaigns, and provides financial aid/scholarships to underprivileged students.</p>
            </div>

            <!-- 7. Bhandara -->
            <div class="highlight-card">
                <i class="fa-solid fa-utensils"></i>
                <h4>Bhandara</h4>
                <p>Promoting community harmony and the spirit of sharing through sacred Bhog and Bhandara distribution. We serve hot, freshly cooked nutritious meals to thousands of devotees and local workers during festival days.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Objectives -->
<section class="about-sec" id="objectives">
    <div class="container">
        <div class="section-header">
            <h2>Core Objectives</h2>
            <p class="section-subtitle">The guiding principles behind all our programs and efforts.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="obj-grid">
            <!-- Obj 1 -->
            <div class="obj-card">
                <i class="fa-solid fa-book-open"></i>
                <h4>Language Preservation</h4>
                <p>Teaching Bengali script, conversation, and reading to children through weekend schools.</p>
            </div>
            <!-- Obj 2 -->
            <div class="obj-card">
                <i class="fa-solid fa-guitar"></i>
                <h4>Artistic Platforms</h4>
                <p>Encouraging musical recitals, threatre acts, and dance forms among our diverse members.</p>
            </div>
            <!-- Obj 3 -->
            <div class="obj-card">
                <i class="fa-solid fa-handshake-angle"></i>
                <h4>Charitable Outreach</h4>
                <p>Distributing clothes, feeding campaigns, and funding local healthcare programs annually.</p>
            </div>
            <!-- Obj 4 -->
            <div class="obj-card">
                <i class="fa-solid fa-people-group"></i>
                <h4>Social Bonding</h4>
                <p>Connecting long-time residents and new families, creating active support networks.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section: Association Registration Details -->
<section class="about-sec about-sec-alt" id="registration-details">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <div class="section-header">
                <h2>Association Registration Details</h2>
                <p class="section-subtitle">Official registration and compliance details of the Bengali Cultural Association.</p>
                <div class="alpona-divider">
                    <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                </div>
            </div>
            
            <p style="font-size: 1.05rem; line-height: 1.85; color: var(--text-muted); margin-bottom: 2rem;">
                The Bengali Cultural Association (BCA), Noida Sector 62, is officially registered as a socio-cultural and religious society. We operate under a formalized constitution with an elected Executive Committee, holding annual general body meetings and maintaining transparent financial audits. We are committed to fostering community development, promoting rich cultural exchanges, and conducting social welfare activities.
            </p>
            
            <a href="images/association_registration.webp" target="_blank" class="btn" id="btn">
                <i class="fa-solid fa-certificate" style="color: var(--gold); font-size: 1.1rem;"></i>
                <span>View Registration Certificate</span>
            </a>
        </div>
    </div>
</section>
<style>
    #btn   {
    /* background-color: var(--red); */
     color: var(--red);
      border: 2px solid var(--red);
       padding: 0.8rem 2rem;
        border-radius: 30px;
         font-weight: 700;
          font-size: 0.95rem;
           text-decoration: none;
            display: inline-flex;
             align-items: center;
              gap: 0.6rem;
               transition: var(--transition);
                box-shadow: var(--shadow-md);
    }

    #btn:hover{
        background-color: var(--red);
        color: var(--white);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }
</style>

<!-- Section 5: Community Activities -->
<!-- <section class="about-sec about-sec-alt" id="activities">
    <div class="container">
        <div class="section-header">
            <h2>Regular Community Activities</h2>
            <p class="section-subtitle">We remain active throughout the year with various welfare and cultural workshops.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="activity-grid">
          
            <div class="activity-card">
                <div class="activity-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=600" alt="Bengali Language Class" class="activity-img" loading="lazy">
                </div>
                <div class="activity-body">
                    <h3 class="activity-title">Bengali Language School</h3>
                    <p class="activity-text">We run a weekend class teaching reading, writing, and literature to children of the diaspora. Help kids speak their native tongue fluently.</p>
                </div>
            </div>
      
            <div class="activity-card">
                <div class="activity-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=600" alt="Drama Workshop" class="activity-img" loading="lazy">
                </div>
                <div class="activity-body">
                    <h3 class="activity-title">Theatre & Drama Workshops</h3>
                    <p class="activity-text">Bi-annual drama workshop leading to public performances. Teaches stage direction, screenwriting, acting, and traditional Bengali folk plays.</p>
                </div>
            </div>
          
            <div class="activity-card">
                <div class="activity-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=600" alt="Blood Drive" class="activity-img" loading="lazy">
                </div>
                <div class="activity-body">
                    <h3 class="activity-title">Health Camps & Blood Drives</h3>
                    <p class="activity-text">Organizing health check-ups, dental screenings, and blood donation drives to support general community health services twice a year.</p>
                </div>
            </div>
            

            <div class="activity-card">
                <div class="activity-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=600" alt="Youth Club Activities" class="activity-img" loading="lazy">
                </div>
                <div class="activity-body">
                    <h3 class="activity-title">Youth Wing & Debating Club</h3>
                    <p class="activity-text">Providing teenagers with a voice. The club organizes debates, quizzes, environment workshops, and charity fundraisers throughout the year.</p>
                </div>
            </div>
        </div>
    </div>
</section> -->

<?php
// Include the shared footer
include 'includes/footer.php';
?>
