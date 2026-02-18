/**
 * Theme JavaScript
 * Sidebar toggle, theme switcher, search, interactions
 */

(function() {
    'use strict';
    
    // ================================================
    // SIDEBAR TOGGLE (Mobile)
    // ================================================
    const initSidebar = () => {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.app-sidebar');
        
        if (!sidebarToggle || !sidebar) return;
        
        // Toggle sidebar
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('show');
        });
        
        // Close on outside click (mobile only)
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 992) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
        
        // Close on resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
            }
        });
    };
    
    // ================================================
    // THEME TOGGLE (Dark Mode)
    // ================================================
    const initThemeToggle = () => {
        const themeToggle = document.getElementById('themeToggle');
        if (!themeToggle) return;
        
        const currentTheme = localStorage.getItem('theme') || 'light';
        
        // Only apply if theme doesn't match (inline script may have already applied it)
        const isDarkApplied = document.body.classList.contains('theme-dark');
        const shouldBeDark = currentTheme === 'dark';
        
        if (isDarkApplied !== shouldBeDark) {
            applyTheme(currentTheme);
        } else {
            // Just update the icon without re-applying theme
            updateThemeIcon(currentTheme);
        }
        
        // Toggle on click
        themeToggle.addEventListener('click', () => {
            const theme = document.documentElement.getAttribute('data-theme');
            const newTheme = theme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    };
    
    const updateThemeIcon = (theme) => {
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                if (theme === 'dark') {
                    if (icon.classList.contains('bi-moon-fill')) {
                        icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
                    }
                } else {
                    if (icon.classList.contains('bi-sun-fill')) {
                        icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
                    }
                }
            }
        }
    };
    
    const applyTheme = (theme) => {
        // Apply to both systems for full compatibility
        document.documentElement.setAttribute('data-theme', theme);
        
        if (theme === 'dark') {
            document.body.classList.add('theme-dark');
        } else {
            document.body.classList.remove('theme-dark');
        }
        
        updateThemeIcon(theme);
    };
    
    // ================================================
    // SEARCH
    // ================================================
    const initSearch = () => {
        const searchInput = document.querySelector('.topbar-search input');
        if (!searchInput) return;
        
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = searchInput.value.trim();
                if (query) {
                    window.location.href = `/search?q=${encodeURIComponent(query)}`;
                }
            }
        });
    };
    
    // ================================================
    // TOOLTIPS & POPOVERS (Bootstrap)
    // ================================================
    const initBootstrapComponents = () => {
        // Tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
        
        // Popovers
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        [...popoverTriggerList].map(el => new bootstrap.Popover(el));
    };
    
    // ================================================
    // CARD ANIMATIONS
    // ================================================
    const initCardAnimations = () => {
        const cards = document.querySelectorAll('.card-soft, .card-horizontal');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 50);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            observer.observe(card);
        });
    };
    
    // ================================================
    // SMOOTH SCROLL
    // ================================================
    const initSmoothScroll = () => {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    };
    
    // ================================================
    // LAZY LOADING IMAGES
    // ================================================
    const initLazyLoading = () => {
        const images = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    };
    
    // ================================================
    // UTILITY: Debounce function
    // ================================================
    const debounce = (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };
    
    // ================================================
    // INIT ALL
    // ================================================
    const init = () => {
        initSidebar();
        initThemeToggle();
        initSearch();
        initBootstrapComponents();
        initCardAnimations();
        initSmoothScroll();
        initLazyLoading();
    };
    
    // DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();
