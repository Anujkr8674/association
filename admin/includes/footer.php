        </main>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        // Restore and persist sidebar scroll position across page reloads
        if (sidebar) {
            const sidebarScroll = sessionStorage.getItem('sidebarScroll');
            if (sidebarScroll !== null) {
                sidebar.scrollTop = parseInt(sidebarScroll, 10);
            } else {
                const activeLink = sidebar.querySelector('.menu-link.active');
                if (activeLink) {
                    activeLink.scrollIntoView({ block: 'nearest' });
                }
            }

            // Save scroll position when navigating away
            window.addEventListener('beforeunload', function() {
                sessionStorage.setItem('sidebarScroll', sidebar.scrollTop);
            });

            // Save scroll position on link click
            sidebar.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    sessionStorage.setItem('sidebarScroll', sidebar.scrollTop);
                });
            });
        }

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

        // Dropdown menus coordination: only one dropdown open at a time
        const dropdowns = [
            { toggle: document.getElementById('committee-dropdown-toggle'), menu: document.getElementById('committee-dropdown-menu') },
            { toggle: document.getElementById('members-dropdown-toggle'), menu: document.getElementById('members-dropdown-menu') },
            { toggle: document.getElementById('partners-dropdown-toggle'), menu: document.getElementById('partners-dropdown-menu') },
            { toggle: document.getElementById('documents-dropdown-toggle'), menu: document.getElementById('documents-dropdown-menu') },
            { toggle: document.getElementById('messages-dropdown-toggle'), menu: document.getElementById('messages-dropdown-menu') },
            { toggle: document.getElementById('notices-dropdown-toggle'), menu: document.getElementById('notices-dropdown-menu') }
        ];

        dropdowns.forEach(item => {
            if (item.toggle && item.menu) {
                item.toggle.addEventListener('click', function() {
                    const isOpen = this.classList.contains('open');
                    
                    // Close all other dropdowns
                    dropdowns.forEach(other => {
                        if (other.toggle && other.menu && other.toggle !== item.toggle) {
                            other.toggle.classList.remove('open');
                            other.menu.classList.remove('show');
                        }
                    });
                    
                    // Toggle current dropdown
                    if (isOpen) {
                        this.classList.remove('open');
                        item.menu.classList.remove('show');
                    } else {
                        this.classList.add('open');
                        item.menu.classList.add('show');
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
