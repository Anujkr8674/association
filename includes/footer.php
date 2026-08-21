    <!-- Footer Section -->
    <style>
        /* ==========================================================================
           FOOTER STYLING
           ========================================================================== */
        .site-footer {
            background-color: var(--dark);
            color: rgba(255, 255, 255, 0.7);
            padding: 5rem 0 2rem 0;
            border-top: 5px solid var(--gold);
            font-size: 0.92rem;
            position: relative;
        }

        .site-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 150px;
            height: 15px;
            background-color: var(--gold);
            clip-path: polygon(0 0, 100% 0, 80% 100%, 20% 100%);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 3rem;
            margin-bottom: 4rem;
        }

        .footer-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .footer-brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .footer-logo-img {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        .footer-logo-title {
            font-family: var(--font-headings);
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 0.5px;
        }

        .footer-logo-subtitle {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gold);
            display: block;
            margin-top: -3px;
        }

        .footer-desc {
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.6);
        }

        .footer-social-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--white);
            margin-top: 0.5rem;
        }

        .footer-socials {
            display: flex;
            gap: 0.8rem;
        }

        .social-link {
            width: 36px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .social-link:hover {
            background-color: var(--vermilion);
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(200, 59, 45, 0.3);
            border-color: var(--vermilion);
        }

        .footer-title {
            color: var(--white);
            font-size: 1.15rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background-color: var(--gold);
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .footer-link-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
            color: rgba(255, 255, 255, 0.6);
        }

        .footer-link-item:hover {
            color: var(--gold);
            padding-left: 5px;
        }

        .footer-link-item i {
            font-size: 0.75rem;
            color: var(--gold);
        }

        .footer-contact-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-contact-item {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .footer-contact-item i {
            color: var(--gold);
            font-size: 1rem;
            margin-top: 0.25rem;
        }

        .footer-contact-text {
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.6);
        }

        .footer-contact-text a:hover {
            color: var(--gold);
        }

        /* Footer Bottom */
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .footer-copy {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.85rem;
        }

        .footer-credit {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.85rem;
        }

        .footer-credit a {
            color: var(--gold);
            font-weight: 500;
        }

        /* Scroll To Top Button */
        .scroll-top-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 45px;
            height: 45px;
            background-color: var(--red);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-md);
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 999;
            border: 2px solid transparent;
        }

        .scroll-top-btn.visible {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top-btn:hover {
            background-color: var(--vermilion);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(200, 59, 45, 0.4);
        }

        /* ==========================================================================
           RESPONSIVE BREAKPOINTS (Footer)
           ========================================================================== */
        @media (max-width: 991px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 2.5rem;
            }
        }

        @media (max-width: 576px) {
            .site-footer {
                padding: 4rem 0 2rem 0;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .footer-bottom {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
        }
    </style>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Column 1: Brand Info -->
                <div class="footer-column">
                    <div class="footer-brand-logo">
                        <img src="images/logo_new.png" class="footer-logo-img" alt="BCA Logo">
                        <!-- <div>
                            <span class="footer-logo-title">BENGALI CULTURAL</span>
                            <span class="footer-logo-subtitle">Association</span>
                        </div> -->
                    </div>
                    <p class="footer-desc">
                        Celebrating the rich heritage, language, traditions, and arts of Bengal. We are dedicated to connecting families, creating lifelong memories, and supporting community development.
                    </p>
                    <span class="footer-social-title">Follow Our Journey</span>
                    <div class="footer-socials">
                        <a href="https://facebook.com" target="_blank" class="social-link" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://instagram.com" target="_blank" class="social-link" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://youtube.com" target="_blank" class="social-link" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="footer-column">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="index.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Home</a></li>
                         <li><a href="durga_puja.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Durga Puja</a></li>
                        <li><a href="about.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> About Us</a></li>
                        <li><a href="committee.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Committee</a></li>
                        <li><a href="members.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Members</a></li>
                        <li><a href="registration_procedure.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Registration</a></li>
                        <li><a href="feedback.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Feedback</a></li>
                         <li><a href="documents.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Documents</a></li>
                    </ul>
                </div>

                <!-- Column 3: Explore -->
                <div class="footer-column">
                    <h4 class="footer-title">Explore</h4>
                    <ul class="footer-links">
                        <li><a href="announcements.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Announcements</a></li>
                        <li><a href="partners.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Partners & Sponsors</a></li>
                        <li><a href="gallery.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Media Gallery</a></li>
                        <li><a href="blogs.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Blog Posts</a></li>
                        <li><a href="documents.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Association Documents</a></li>
                        <li><a href="keymessages.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Key Messages</a></li>
                        <li><a href="contact.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Contact Us</a></li>
                         <li><a href="disclaimer.php" class="footer-link-item"><i class="fa-solid fa-angle-right"></i> Disclaimer</a></li>
                    </ul>
                </div>

                <!-- Column 4: Contact -->
                <div class="footer-column">
                    <h4 class="footer-title">Get in Touch</h4>
                    <ul class="footer-contact-list">
                        <li class="footer-contact-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span class="footer-contact-text">
                                22/A Rabindra Sarani, Cultural Zone,<br>
                                Kolkata, West Bengal - 700001
                            </span>
                        </li>
                        <li class="footer-contact-item">
                            <i class="fa-solid fa-phone"></i>
                            <span class="footer-contact-text">
                                <a href="tel:+919876543210">+91 98765 43210</a>
                            </span>
                        </li>
                        <li class="footer-contact-item">
                            <i class="fa-solid fa-envelope"></i>
                            <span class="footer-contact-text">
                                <a href="mailto:info@bengalicultural.org">info@bengalicultural.org</a>
                            </span>
                        </li>
                        <li class="footer-contact-item">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span class="footer-contact-text">
                                <a href="https://wa.me/919876543210" target="_blank">+91 98765 43210</a>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <span class="footer-copy">
                    &copy; 2026 Bengali Cultural Association. All Rights Reserved.
                </span>
                <span class="footer-credit">
                    Designed with <i class="fa-solid fa-heart" style="color: var(--vermilion);"></i> for Bengali Heritage.
                </span>
            </div>
        </div>
    </footer>

    <!-- Back to Top Arrow Button -->
    <div class="scroll-top-btn" id="scroll-top" aria-label="Scroll to top">
        <i class="fa-solid fa-arrow-up"></i>
    </div>

    <!-- Scroll Top JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scrollTopBtn = document.getElementById('scroll-top');
            
            window.addEventListener('scroll', function () {
                if (window.scrollY > 400) {
                    scrollTopBtn.classList.add('visible');
                } else {
                    scrollTopBtn.classList.remove('visible');
                }
            });
            
            scrollTopBtn.addEventListener('click', function () {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
