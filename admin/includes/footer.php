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

        // Partners Dropdown Toggle
        const partToggle = document.getElementById('partners-dropdown-toggle');
        const partMenu = document.getElementById('partners-dropdown-menu');
        if (partToggle && partMenu) {
            partToggle.addEventListener('click', function() {
                this.classList.toggle('open');
                partMenu.classList.toggle('show');
            });
        }

        // Documents Dropdown Toggle
        const docsToggle = document.getElementById('documents-dropdown-toggle');
        const docsMenu = document.getElementById('documents-dropdown-menu');
        if (docsToggle && docsMenu) {
            docsToggle.addEventListener('click', function() {
                this.classList.toggle('open');
                docsMenu.classList.toggle('show');
            });
        }

        // Key Messages Dropdown Toggle
        const msgsToggle = document.getElementById('messages-dropdown-toggle');
        const msgsMenu = document.getElementById('messages-dropdown-menu');
        if (msgsToggle && msgsMenu) {
            msgsToggle.addEventListener('click', function() {
                this.classList.toggle('open');
                msgsMenu.classList.toggle('show');
            });
        }
    });
    </script>
</body>
</html>
