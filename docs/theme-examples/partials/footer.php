    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme JS -->
    <script>
        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.app-sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 992) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
        }
        
        // Theme Toggle (Dark Mode)
        const themeToggle = document.getElementById('themeToggle');
        const currentTheme = localStorage.getItem('theme') || 'light';
        
        // Apply saved theme
        if (currentTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            if (themeToggle) {
                themeToggle.querySelector('i').classList.replace('bi-moon-fill', 'bi-sun-fill');
            }
        }
        
        // Toggle theme
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const theme = document.documentElement.getAttribute('data-theme');
                const newTheme = theme === 'dark' ? 'light' : 'dark';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                const icon = themeToggle.querySelector('i');
                if (newTheme === 'dark') {
                    icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
                } else {
                    icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
                }
            });
        }
        
        // Search functionality (example)
        const searchInput = document.querySelector('.topbar-search input');
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = searchInput.value.trim();
                    if (query) {
                        window.location.href = `<?= base_path('/search') ?>?q=${encodeURIComponent(query)}`;
                    }
                }
            });
        }
    </script>
    
    <?php if (!empty($footerScripts)): ?>
        <?= $footerScripts ?>
    <?php endif; ?>
    
</body>
</html>
