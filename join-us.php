<?php
// Include the shared header
include 'includes/header.php';
?>

<style>
    /* ==========================================================================
       JOIN US PAGE SPECIFIC STYLES
       ========================================================================== */
    .join-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .join-banner::before {
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

    .join-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .join-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .join-sec {
        padding: 6.5rem 0;
        background-color: var(--primary-bg);
    }

    .join-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 4rem;
        align-items: flex-start;
    }

    /* Benefits Column */
    .benefits-title {
        font-size: 2rem;
        color: var(--red);
        margin-bottom: 1.5rem;
    }

    .benefits-lead {
        font-size: 1.05rem;
        line-height: 1.7;
        margin-bottom: 2.5rem;
    }

    .benefit-item {
        display: flex;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .benefit-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: var(--secondary-bg);
        color: var(--red);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
        box-shadow: var(--shadow-sm);
    }

    .benefit-content h4 {
        font-size: 1.15rem;
        margin-bottom: 0.4rem;
        color: var(--dark);
    }

    .benefit-content p {
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* Form Column Card */
    .form-card {
        background-color: var(--white);
        padding: 3rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        border-top: 4px solid var(--red);
    }

    .form-title {
        font-size: 1.6rem;
        margin-bottom: 0.5rem;
    }

    .form-subtitle {
        font-size: 0.88rem;
        color: var(--text-muted);
        margin-bottom: 2rem;
        display: block;
    }

    .form-group {
        margin-bottom: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-label {
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--dark);
    }

    .form-control {
        width: 100%;
        padding: 0.8rem 1.2rem;
        border: 2px solid var(--border-color);
        border-radius: var(--border-radius);
        font-family: var(--font-body);
        font-size: 0.95rem;
        transition: var(--transition);
        outline: none;
        background-color: var(--primary-bg);
    }

    .form-control:focus {
        border-color: var(--red);
        background-color: var(--white);
        box-shadow: 0 0 10px rgba(139, 30, 30, 0.05);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    .form-error {
        color: var(--vermilion);
        font-size: 0.78rem;
        font-weight: 600;
        display: none;
    }

    .submit-btn {
        width: 100%;
        padding: 1rem;
        border-radius: 30px;
        font-size: 1rem;
        font-weight: 700;
        margin-top: 1rem;
        border: none;
    }

    /* Process steps styles */
    .join-steps-sec {
        background-color: var(--secondary-bg);
        padding: 6.5rem 0;
        border-top: 1px solid var(--border-color);
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        margin-top: 3rem;
    }

    .step-card {
        background-color: var(--white);
        border-radius: var(--border-radius);
        padding: 2.2rem 1.8rem;
        border: 1px solid var(--border-color);
        text-align: center;
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .step-number {
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 40px;
        background-color: var(--red);
        color: var(--white);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        border: 3px solid var(--white);
        box-shadow: var(--shadow-sm);
    }

    .step-card h4 {
        margin-top: 0.5rem;
        font-size: 1.15rem;
        margin-bottom: 0.6rem;
    }

    .step-card p {
        font-size: 0.88rem;
        line-height: 1.5;
        margin-bottom: 0;
    }

    /* Success Modal Styling */
    .success-modal {
        background-color: var(--white);
        border-radius: var(--border-radius-lg);
        width: 100%;
        max-width: 480px;
        padding: 3rem 2rem;
        text-align: center;
        box-shadow: var(--shadow-lg);
        transform: scale(0.9);
        transition: var(--transition-slow);
    }

    .modal-overlay.open .success-modal {
        transform: scale(1);
    }

    .success-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: rgba(37, 211, 102, 0.1);
        color: #25D366;
        font-size: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem auto;
        animation: scalePulse 2s infinite ease-in-out;
    }

    @keyframes scalePulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }

    .success-title {
        font-size: 1.75rem;
        margin-bottom: 1rem;
        color: var(--dark);
    }

    .success-text {
        font-size: 0.95rem;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    /* ==========================================================================
       RESPONSIVE BREAKPOINTS
       ========================================================================== */
    @media (max-width: 991px) {
        .join-grid {
            grid-template-columns: 1fr;
            gap: 4rem;
        }
        .form-card {
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }
        .steps-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 2.5rem;
        }
    }

    @media (max-width: 576px) {
        .form-card {
            padding: 2rem 1.5rem;
        }
        .steps-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Banner Header -->
<section class="join-banner">
    <div class="container">
        <h1 class="join-banner-title">Join Our Community</h1>
        <span class="join-banner-subtitle">Membership Registration</span>
    </div>
</section>

<!-- Form & Content Section -->
<section class="join-sec">
    <div class="container">
        <div class="join-grid">
            <!-- Left: Membership Benefits Info -->
            <div>
                <h2 class="benefits-title">Why Join the Association?</h2>
                <p class="benefits-lead">
                    Our association thrives on the strength and dedication of its members. By joining, you support cultural preservation, open educational gates for children, and access exclusive community events.
                </p>

                <!-- Benefit 1 -->
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <div class="benefit-content">
                        <h4>Exclusive Event Admission</h4>
                        <p>Members receive free or heavily discounted passes to cultural programs, musical concerts, drama theatres, and delicious seasonal lunches.</p>
                    </div>
                </div>

                <!-- Benefit 2 -->
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div class="benefit-content">
                        <h4>Educational Platforms</h4>
                        <p>Access enrollment to our weekend Bengali Language school and special creative workshops led by professional art masters.</p>
                    </div>
                </div>

                <!-- Benefit 3 -->
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fa-solid fa-hands-holding-child"></i>
                    </div>
                    <div class="benefit-content">
                        <h4>Volunteering & Leadership</h4>
                        <p>Be part of event planning committees, run social relief projects, and develop networks with local professionals and leaders.</p>
                    </div>
                </div>
            </div>

            <!-- Right: Application Form Card -->
            <div class="form-card">
                <h3 class="form-title">Membership Request</h3>
                <span class="form-subtitle">Fill in the fields below. Our executive board will review and follow up soon.</span>

                <form id="membership-form" novalidate>
                    <!-- Name Field -->
                    <div class="form-group">
                        <label class="form-label" for="full-name">Full Name *</label>
                        <input type="text" id="full-name" class="form-control" placeholder="Enter your full name" required>
                        <span class="form-error" id="name-error">Please enter your name (minimum 3 characters).</span>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address *</label>
                        <input type="email" id="email" class="form-control" placeholder="example@domain.com" required>
                        <span class="form-error" id="email-error">Please enter a valid email address.</span>
                    </div>

                    <!-- Phone Field -->
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number *</label>
                        <input type="tel" id="phone" class="form-control" placeholder="Enter 10-digit mobile number" required>
                        <span class="form-error" id="phone-error">Please enter a valid 10-digit phone number.</span>
                    </div>

                    <!-- Address Field -->
                    <div class="form-group">
                        <label class="form-label" for="address">Address *</label>
                        <input type="text" id="address" class="form-control" placeholder="Residential address" required>
                        <span class="form-error" id="address-error">Please enter your residential address.</span>
                    </div>

                    <!-- Membership Type -->
                    <div class="form-group">
                        <label class="form-label" for="member-type">Membership Tier *</label>
                        <select id="member-type" class="form-control" required>
                            <option value="" disabled selected>Select membership tier</option>
                            <option value="general">General Member (Annual Subscription)</option>
                            <option value="life">Life Member (One-time Fee)</option>
                            <option value="patron">Patron Member (Sponsor/Supporter)</option>
                        </select>
                        <span class="form-error" id="type-error">Please select a membership tier.</span>
                    </div>

                    <!-- Message Field -->
                    <div class="form-group">
                        <label class="form-label" for="message">Message / Reference (Optional)</label>
                        <textarea id="message" class="form-control" placeholder="Write any specific notes or how you heard about us..."></textarea>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary submit-btn">Submit Membership Request</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Bottom Timeline Steps Section -->
<section class="join-steps-sec">
    <div class="container">
        <div class="section-header">
            <h2>The Application Process</h2>
            <p class="section-subtitle">How your membership request is processed by our administration desk.</p>
            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h4>Submit Form</h4>
                <p>Fill out the online application request form with correct contact details.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h4>Review Process</h4>
                <p>Our Secretary evaluates eligibility and directories setup (approx 24-48h).</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h4>Payment Link</h4>
                <p>Receive invoice / banking payment guidelines for subscription fees.</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h4>Get Welcome Kit</h4>
                <p>Welcome email package, ID cards issued, and mailing circular list access.</p>
            </div>
        </div>
    </div>
</section>

<!-- Submission Confirmation Overlay -->
<div class="modal-overlay" id="success-modal-overlay">
    <div class="success-modal">
        <div class="success-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h3 class="success-title">Request Submitted!</h3>
        <p class="success-text">
            Thank you for applying to the Bengali Cultural Association. Your application has been logged.
            <br><br>
            Our Treasurer or Joint Secretary will review the application and contact you on email/phone within <strong>48 hours</strong> with subscription details and verification instructions.
        </p>
        <button class="btn btn-primary" id="success-modal-close" style="padding: 0.8rem 2.5rem;">Okay, Got It</button>
    </div>
</div>

<!-- Validation & Form JS -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('membership-form');
        const successModal = document.getElementById('success-modal-overlay');
        const successClose = document.getElementById('success-modal-close');

        // Inputs
        const nameInput = document.getElementById('full-name');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const addressInput = document.getElementById('address');
        const typeInput = document.getElementById('member-type');

        // Helper functions
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(String(email).toLowerCase());
        }

        function validatePhone(phone) {
            const re = /^\d{10}$/; // Simple 10 digit validation
            return re.test(phone.replace(/[\s-()]/g, '')); // Strip spaces or special chars
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            let isValid = true;

            // Name validation
            if (nameInput.value.trim().length < 3) {
                document.getElementById('name-error').style.display = 'block';
                nameInput.style.borderColor = 'var(--vermilion)';
                isValid = false;
            } else {
                document.getElementById('name-error').style.display = 'none';
                nameInput.style.borderColor = '';
            }

            // Email validation
            if (!validateEmail(emailInput.value.trim())) {
                document.getElementById('email-error').style.display = 'block';
                emailInput.style.borderColor = 'var(--vermilion)';
                isValid = false;
            } else {
                document.getElementById('email-error').style.display = 'none';
                emailInput.style.borderColor = '';
            }

            // Phone validation
            if (!validatePhone(phoneInput.value.trim())) {
                document.getElementById('phone-error').style.display = 'block';
                phoneInput.style.borderColor = 'var(--vermilion)';
                isValid = false;
            } else {
                document.getElementById('phone-error').style.display = 'none';
                phoneInput.style.borderColor = '';
            }

            // Address validation
            if (addressInput.value.trim() === '') {
                document.getElementById('address-error').style.display = 'block';
                addressInput.style.borderColor = 'var(--vermilion)';
                isValid = false;
            } else {
                document.getElementById('address-error').style.display = 'none';
                addressInput.style.borderColor = '';
            }

            // Membership type validation
            if (typeInput.value === '') {
                document.getElementById('type-error').style.display = 'block';
                typeInput.style.borderColor = 'var(--vermilion)';
                isValid = false;
            } else {
                document.getElementById('type-error').style.display = 'none';
                typeInput.style.borderColor = '';
            }

            // If form passes all validation, trigger success overlay popup
            if (isValid) {
                successModal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        });

        // Reset and close modal
        successClose.addEventListener('click', function () {
            successModal.classList.remove('open');
            document.body.style.overflow = '';
            form.reset();
        });
    });
</script>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
