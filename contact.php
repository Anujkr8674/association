<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($fullName) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill out all required fields.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO `contact_messages` (`full_name`, `email`, `phone`, `subject`, `message`) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fullName, $email, $phone, $subject, $message]);
        echo json_encode(['status' => 'success', 'message' => 'Thank you for writing to the Bengali Cultural Association. Your message has been logged successfully!']);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

// Include the shared header
include 'includes/header.php';
?>

<style>
    /* ==========================================================================
       CONTACT PAGE SPECIFIC STYLES
       ========================================================================== */
    .cont-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .cont-banner::before {
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

    .cont-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .cont-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .cont-sec {
        padding: 6.5rem 0;
        background-color: var(--primary-bg);
    }

    .cont-grid {
        display: grid;
        grid-template-columns: 0.9fr 1.1fr;
        gap: 4rem;
        align-items: flex-start;
    }

    /* Contact Details Stack */
    .contact-info-cards {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .contact-info-card {
        background-color: var(--white);
        padding: 1.5rem 2rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        display: flex;
        gap: 1.5rem;
        align-items: center;
        transition: var(--transition);
    }

    .contact-info-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--gold);
    }

    .contact-info-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: var(--secondary-bg);
        color: var(--red);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .contact-info-text h4 {
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
        color: var(--dark);
    }

    .contact-info-text p {
        font-size: 0.9rem;
        margin-bottom: 0;
        line-height: 1.5;
    }

    .contact-info-text a:hover {
        color: var(--red);
    }

    /* Google Maps Styled Iframe */
    .map-wrapper {
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 2px solid var(--border-color);
        height: 320px;
        background-color: var(--secondary-bg);
    }

    .map-frame {
        width: 100%;
        height: 100%;
        border: none;
    }

    /* Message Form Card */
    .contact-form-card {
        background-color: var(--white);
        padding: 3rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        border-top: 4px solid var(--red);
    }

    .contact-form-title {
        font-size: 1.6rem;
        margin-bottom: 0.5rem;
    }

    .contact-form-subtitle {
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
        min-height: 150px;
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

    /* Hours Section */
    .hours-sec {
        background-color: var(--secondary-bg);
        padding: 6.5rem 0;
        border-top: 1px solid var(--border-color);
    }

    .hours-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .hours-list-card {
        background-color: var(--white);
        padding: 3rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .hours-row {
        display: flex;
        justify-content: space-between;
        padding: 0.8rem 0;
        border-bottom: 1px dashed var(--border-color);
        font-size: 0.95rem;
    }

    .hours-row:last-child {
        border-bottom: none;
    }

    .hours-day {
        font-weight: 700;
        color: var(--dark);
    }

    .hours-time {
        color: var(--red);
        font-weight: 600;
    }

    .hours-note-box {
        padding: 1.5rem;
        background-color: var(--primary-bg);
        border-left: 4px solid var(--gold);
        border-radius: 4px;
        margin-top: 1.5rem;
        font-size: 0.88rem;
        line-height: 1.5;
        color: var(--text-muted);
    }

    /* Modal Overlay */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: var(--transition-slow);
    }

    .modal-overlay.open {
        opacity: 1;
        pointer-events: auto;
    }

    /* Success Modal Styling */
    .success-modal.error .success-icon {
        background-color: rgba(200, 59, 45, 0.1);
        color: var(--vermilion);
    }

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
        .cont-grid {
            grid-template-columns: 1fr;
            gap: 4rem;
        }
        .contact-form-card {
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }
        .hours-grid {
            grid-template-columns: 1fr;
            gap: 3rem;
        }
        .hours-list-card {
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .contact-form-card {
            padding: 2rem 1.5rem;
        }
        .hours-list-card {
            padding: 2rem 1.5rem;
        }
    }
</style>

<!-- Banner Header -->
<section class="cont-banner">
    <div class="container">
        <h1 class="cont-banner-title">Contact Us</h1>
        <span class="cont-banner-subtitle">Get in Touch with Our Committee</span>
    </div>
</section>

<!-- Main Details & Form section -->
<section class="cont-sec">
    <div class="container">
        <div class="cont-grid">
            
            <!-- Left: Information Cards & Maps -->
            <div>
                <div class="contact-info-cards">
                    <!-- Address Card -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="contact-info-text">
                            <h4>Physical Address</h4>
                            <p>Khel Shakti Park, Block B, Sector 62, Noida</p>
                        </div>
                    </div>

                    <!-- Phone Card -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="contact-info-text">
                            <h4>Phone / Hotline</h4>
                            <p><a href="tel:+919811639155">+91 98116 39155</a>, <br><a href="tel:+917260818065">+91 72608 18065</a></p>
                        </div>
                    </div>

                    <!-- Email Card -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="contact-info-text">
                            <h4>General Inquiry</h4>
                            <p><a href="mailto:bengaliculturalassociation2003@gmail.com">bengaliculturalassociation2003@gmail.com</a></p>
                        </div>
                    </div>

                    <!-- WhatsApp Card -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon" style="background-color: #25D366; color: var(--white);">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div class="contact-info-text">
                            <h4>WhatsApp Support</h4>
                            <p><a href="https://wa.me/919811639155" target="_blank">+91 98116 39155</a> (Direct Support)</p>
                        </div>
                    </div>
                </div>

                <!-- Map embed -->
                <div class="contact-map-container" style="border: 1px solid var(--border-color); border-radius: var(--border-radius); overflow: hidden; height: 320px; box-shadow: var(--shadow-sm);">
                    <iframe src="https://maps.google.com/maps?q=Khel%20Shakti%20Park%2C%20Block%20B%2C%20Sector%2062%2C%20Noida&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <!-- Right: Inquiry Form -->
            <div class="contact-form-card">
                <h3 class="contact-form-title">Send a Message</h3>
                <span class="contact-form-subtitle">Have a question or feedback? Write to us and we will respond shortly.</span>

                <form id="contact-form" novalidate>
                    <!-- Name Field -->
                    <div class="form-group">
                        <label class="form-label" for="contact-name">Full Name *</label>
                        <input type="text" id="contact-name" class="form-control" placeholder="Enter your full name" required>
                        <span class="form-error" id="name-error">Please enter your name (minimum 3 characters).</span>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label class="form-label" for="contact-email">Email Address *</label>
                        <input type="email" id="contact-email" class="form-control" placeholder="example@domain.com" required>
                        <span class="form-error" id="email-error">Please enter a valid email address.</span>
                    </div>

                    <!-- Phone Field -->
                    <div class="form-group">
                        <label class="form-label" for="contact-phone">Phone Number *</label>
                        <input type="tel" id="contact-phone" class="form-control" placeholder="Enter 10-digit mobile number" required>
                        <span class="form-error" id="phone-error">Please enter a valid 10-digit phone number.</span>
                    </div>

                    <!-- Subject Field -->
                    <div class="form-group">
                        <label class="form-label" for="contact-subject">Subject *</label>
                        <select id="contact-subject" class="form-control" required>
                            <option value="" disabled selected>Select message category</option>
                            <option value="general">General Inquiry</option>
                            <option value="membership">Membership Subscription</option>
                            <option value="events">Cultural Performance / Stall</option>
                            <option value="sponsorship">Sponsorship & Donations</option>
                        </select>
                        <span class="form-error" id="subject-error">Please select a subject category.</span>
                    </div>

                    <!-- Message Field -->
                    <div class="form-group">
                        <label class="form-label" for="contact-message">Message *</label>
                        <textarea id="contact-message" class="form-control" placeholder="Type your message details here..." required></textarea>
                        <span class="form-error" id="message-error">Please type your message details (minimum 10 characters).</span>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary submit-btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Bottom Office Hours Section -->
<section class="hours-sec">
    <div class="container">
        <div class="hours-grid">
            <div>
                <span class="welcome-subtitle">OFFICE HOURS</span>
                <h3 style="font-size: 2.2rem; color: var(--red); margin-bottom: 1rem;">When to Visit Us?</h3>
                <p style="font-size: 1.05rem; line-height: 1.7; color: var(--text-muted);">
                    Our administrative center is located inside the Cultural Zone of Rabindra Sarani. While you can send online inquiries at any time, you are welcome to visit our offices during weekends to buy souvenir tickets, pay subscriptions, or register rehearsal details.
                </p>
                <div class="hours-note-box">
                    <strong>Note:</strong> Rehearsals for cultural functions normally occur on Saturdays (04:00 PM - 06:00 PM) and Sundays (10:00 AM - 01:00 PM) at the main activity grounds.
                </div>
            </div>
            
            <div class="hours-list-card">
                <div class="hours-row">
                    <span class="hours-day">Monday - Friday</span>
                    <span class="hours-time" style="color: var(--text-muted);">Closed (Online only)</span>
                </div>
                <div class="hours-row">
                    <span class="hours-day">Saturday</span>
                    <span class="hours-time">02:00 PM - 06:00 PM</span>
                </div>
                <div class="hours-row">
                    <span class="hours-day">Sunday</span>
                    <span class="hours-time">10:00 AM - 04:00 PM</span>
                </div>
                <div class="hours-row" style="border-bottom: none;">
                    <span class="hours-day">Puja Festival Days</span>
                    <span class="hours-time">08:00 AM - 11:30 PM</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Submission Confirmation Overlay -->
<div class="modal-overlay" id="success-modal-overlay">
    <div class="success-modal" style="position: relative;">
        <!-- Close icon button top-right -->
        <button type="button" id="success-modal-x" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; opacity: 0.6; transition: var(--transition); outline: none;"><i class="fa-solid fa-xmark"></i></button>
        
        <div class="success-icon">
            <i class="fa-solid fa-circle-check" id="modal-icon"></i>
        </div>
        <h3 class="success-title" id="modal-title">Message Sent!</h3>
        <p class="success-text" id="modal-text">
            Thank you for writing to the Bengali Cultural Association. Your message has been routed successfully.
        </p>
        <button class="btn btn-primary" id="success-modal-close" style="padding: 0.8rem 2.5rem;">Close</button>
    </div>
</div>

<!-- Validation & Form JS -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('contact-form');
        const successModal = document.getElementById('success-modal-overlay');
        const successClose = document.getElementById('success-modal-close');
        const successX = document.getElementById('success-modal-x');

        // Inputs
        const nameInput = document.getElementById('contact-name');
        const emailInput = document.getElementById('contact-email');
        const phoneInput = document.getElementById('contact-phone');
        const subjectInput = document.getElementById('contact-subject');
        const messageInput = document.getElementById('contact-message');

        // Helper functions
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(String(email).toLowerCase());
        }

        function validatePhone(phone) {
            const re = /^\d{10}$/; // Simple 10 digit validation
            return re.test(phone.replace(/[\s-()]/g, '')); // Strip spaces or special chars
        }

        let autoCloseTimeout;

        function showModal(isSuccess, message) {
            const titleEl = document.getElementById('modal-title');
            const textEl = document.getElementById('modal-text');
            const iconEl = document.getElementById('modal-icon');
            const modalContainer = document.querySelector('.success-modal');

            if (isSuccess) {
                modalContainer.classList.remove('error');
                iconEl.className = 'fa-solid fa-circle-check';
                titleEl.innerText = 'Message Sent!';
                textEl.innerHTML = message || 'Thank you for writing to the Bengali Cultural Association. Your message has been logged successfully.';
            } else {
                modalContainer.classList.add('error');
                iconEl.className = 'fa-solid fa-circle-xmark';
                titleEl.innerText = 'Submission Failed!';
                textEl.innerText = message || 'There was an error submitting your message. Please try again.';
            }

            successModal.classList.add('open');
            document.body.style.overflow = 'hidden';

            // Clear any previous timeout
            if (autoCloseTimeout) {
                clearTimeout(autoCloseTimeout);
            }

            // Auto close after 3 seconds
            autoCloseTimeout = setTimeout(() => {
                closeModal();
            }, 3000);
        }

        function closeModal() {
            successModal.classList.remove('open');
            document.body.style.overflow = '';
            if (autoCloseTimeout) {
                clearTimeout(autoCloseTimeout);
            }
            form.reset();
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

            // Subject validation
            if (subjectInput.value === '') {
                document.getElementById('subject-error').style.display = 'block';
                subjectInput.style.borderColor = 'var(--vermilion)';
                isValid = false;
            } else {
                document.getElementById('subject-error').style.display = 'none';
                subjectInput.style.borderColor = '';
            }

            // Message validation
            if (messageInput.value.trim().length < 10) {
                document.getElementById('message-error').style.display = 'block';
                messageInput.style.borderColor = 'var(--vermilion)';
                isValid = false;
            } else {
                document.getElementById('message-error').style.display = 'none';
                messageInput.style.borderColor = '';
            }

            if (isValid) {
                const formData = new FormData();
                formData.append('full_name', nameInput.value.trim());
                formData.append('email', emailInput.value.trim());
                formData.append('phone', phoneInput.value.trim());
                formData.append('subject', subjectInput.value);
                formData.append('message', messageInput.value.trim());

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'contact.php', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (res.status === 'success') {
                                showModal(true, res.message);
                            } else {
                                showModal(false, res.message);
                            }
                        } catch(e) {
                            showModal(false, 'Invalid response from server.');
                        }
                    } else {
                        showModal(false, 'Server error: ' + xhr.status);
                    }
                };
                xhr.onerror = function() {
                    showModal(false, 'A network error occurred.');
                };
                xhr.send(formData);
            }
        });

        // Close handlers
        successClose.addEventListener('click', closeModal);
        successX.addEventListener('click', closeModal);
    });
</script>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
