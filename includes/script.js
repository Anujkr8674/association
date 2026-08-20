document.addEventListener('DOMContentLoaded', function () {
    // Welcome Read More toggle
    const welcomeBtn = document.getElementById('welcome-read-more-btn');
    const welcomeContainer = document.querySelector('.welcome-text-container');

    if (welcomeBtn && welcomeContainer) {
        welcomeBtn.addEventListener('click', function () {
            const isExpanded = welcomeContainer.classList.contains('expanded');

            if (isExpanded) {
                welcomeContainer.classList.remove('expanded');
                welcomeContainer.style.overflowY = 'hidden';
                welcomeContainer.scrollTop = 0; // Reset scroll position to top
                welcomeBtn.innerHTML = 'Read More <i class="fa-solid fa-chevron-down"></i>';
            } else {
                welcomeContainer.classList.add('expanded');
                welcomeContainer.style.overflowY = 'auto';
                welcomeBtn.innerHTML = 'Read Less <i class="fa-solid fa-chevron-up"></i>';

                // Programmatically scroll down by 140px (approx 5 lines of text) and stay there
                setTimeout(() => {
                    welcomeContainer.scrollTo({
                        top: 140,
                        behavior: 'smooth'
                    });
                }, 300);
            }
        });
    }
});
