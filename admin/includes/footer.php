        </main>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (toggleBtn && sidebar && overlay) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            });

            overlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }

        // Committee Dropdown Toggle
        const commToggle = document.getElementById('committee-dropdown-toggle');
        const commMenu = document.getElementById('committee-dropdown-menu');
        if (commToggle && commMenu) {
            commToggle.addEventListener('click', function() {
                this.classList.toggle('open');
                commMenu.classList.toggle('show');
            });
        }

        // Members Dropdown Toggle
        const membToggle = document.getElementById('members-dropdown-toggle');
        const membMenu = document.getElementById('members-dropdown-menu');
        if (membToggle && membMenu) {
            membToggle.addEventListener('click', function() {
                this.classList.toggle('open');
                membMenu.classList.toggle('show');
            });
        }
    });
    </script>
</body>
</html>
