/* ============================================
   BATCHES PAGE - ADVANCED JAVASCRIPT
   Industry-Standard Interactions
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    initBatchesPageInteractions();
});

function initBatchesPageInteractions() {
    // Page header interactions
    const pageTitle = document.querySelector('.sl-title');
    const pageSubtitle = document.querySelector('.sl-subtitle');
    
    if (pageTitle) {
        pageTitle.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px) scale(1.02)';
        });
        
        pageTitle.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0) scale(1)';
        });
    }
    
    if (pageSubtitle) {
        pageSubtitle.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px)';
            this.style.opacity = '1';
        });
        
        pageSubtitle.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
            this.style.opacity = '';
        });
    }
    
    // Add Batch button interactions
    const addBatchBtn = document.querySelector('.btn-create-order');
    if (addBatchBtn) {
        addBatchBtn.addEventListener('mouseenter', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'rotate(90deg) scale(1.2)';
            }
        });
        
        addBatchBtn.addEventListener('mouseleave', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'rotate(0deg) scale(1)';
            }
        });
        
        addBatchBtn.addEventListener('click', function(e) {
            // Ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.6)';
            ripple.style.width = ripple.style.height = '200px';
            ripple.style.left = e.offsetX - 100 + 'px';
            ripple.style.top = e.offsetY - 100 + 'px';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            ripple.style.zIndex = '10';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    }
    
    // Stat cards advanced interactions
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        // 3D tilt effect
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 15;
            const rotateY = (centerX - x) / 15;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px) scale(1.02)`;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0) scale(1)';
        });
        
        // Click ripple effect
        card.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.5)';
            ripple.style.width = ripple.style.height = '100px';
            ripple.style.left = e.offsetX - 50 + 'px';
            ripple.style.top = e.offsetY - 50 + 'px';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            ripple.style.zIndex = '10';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
        
        // Counter animation
        const valueEl = card.querySelector('.stat-card-value');
        if (valueEl) {
            const target = parseInt(valueEl.textContent) || 0;
            animateCounter(valueEl, 0, target, 1000, index * 100);
        }
        
        // Icon animation on hover
        const icon = card.querySelector('i');
        if (icon) {
            card.addEventListener('mouseenter', function() {
                icon.style.transform = 'scale(1.2) rotate(10deg)';
            });
            
            card.addEventListener('mouseleave', function() {
                icon.style.transform = 'scale(1) rotate(0deg)';
            });
        }
    });
    
    // Table rows advanced interactions
    const tableRows = document.querySelectorAll('.smro-table tbody tr[data-filter]');
    tableRows.forEach((row, index) => {
        // Magnetic hover effect
        row.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            
            this.style.transform = `translateX(${8 + x * 0.02}px)`;
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
        
        // Individual cell animations
        const cells = row.querySelectorAll('td');
        cells.forEach((cell, cellIndex) => {
            cell.addEventListener('mouseenter', function() {
                // Batch number cell (2nd column)
                if (cellIndex === 1) {
                    this.style.color = 'var(--accent-primary)';
                    this.style.transform = 'translateX(3px)';
                }
                // Medicine ID cell (3rd column)
                else if (cellIndex === 2) {
                    this.style.color = 'var(--accent-teal)';
                    this.style.fontWeight = '600';
                }
                // Supplier cell (5th column)
                else if (cellIndex === 4) {
                    this.style.color = 'var(--text-heading)';
                    this.style.fontWeight = '500';
                }
            });
            
            cell.addEventListener('mouseleave', function() {
                if (cellIndex === 1) {
                    this.style.color = '';
                    this.style.transform = '';
                }
                else if (cellIndex === 2 || cellIndex === 4) {
                    this.style.color = '';
                    this.style.fontWeight = '';
                }
            });
        });
        
        // Click ripple effect
        row.addEventListener('click', function(e) {
            if (e.target.closest('.action-btn') || e.target.closest('button')) {
                return; // Don't add ripple if clicking action buttons
            }
            
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(14, 165, 233, 0.2)';
            ripple.style.width = ripple.style.height = '50px';
            ripple.style.left = e.offsetX - 25 + 'px';
            ripple.style.top = e.offsetY - 25 + 'px';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.5s ease-out';
            ripple.style.pointerEvents = 'none';
            ripple.style.zIndex = '1';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 500);
        });
    });
    
    // Icon buttons (pgc-icon-btn) enhanced
    const iconButtons = document.querySelectorAll('.pgc-icon-btn');
    iconButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1.2)';
            }
        });
        
        btn.addEventListener('mouseleave', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
        
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            
            // Check if it's a delete button
            const isDelete = this.style.borderColor === 'rgb(254, 202, 202)' || 
                           this.style.borderColor === '#fecaca';
            
            ripple.style.background = isDelete 
                ? 'rgba(239, 68, 68, 0.5)' 
                : 'rgba(14, 165, 233, 0.5)';
            ripple.style.width = ripple.style.height = '60px';
            ripple.style.left = '50%';
            ripple.style.top = '50%';
            ripple.style.transform = 'translate(-50%, -50%) scale(0)';
            ripple.style.animation = 'ripple 0.5s ease-out';
            ripple.style.pointerEvents = 'none';
            ripple.style.zIndex = '10';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 500);
            
            // Bounce animation
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'buttonBounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
            }, 10);
        });
    });
    
    // Action buttons enhanced
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = this.classList.contains('danger') 
                ? 'rgba(239, 68, 68, 0.5)' 
                : 'rgba(14, 165, 233, 0.5)';
            ripple.style.width = ripple.style.height = '40px';
            ripple.style.left = '50%';
            ripple.style.top = '50%';
            ripple.style.transform = 'translate(-50%, -50%) scale(0)';
            ripple.style.animation = 'ripple 0.5s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 500);
            
            // Bounce animation
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'buttonBounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
            }, 10);
        });
    });
    
    // Filter tabs
    const filterTabs = document.querySelectorAll('.pill-tab');
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active from all
            filterTabs.forEach(t => t.classList.remove('active'));
            
            // Add active to clicked
            this.classList.add('active');
            
            // Pulse animation
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'tabActive 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
            }, 10);
        });
        
        // Ripple on click
        tab.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(14, 165, 233, 0.3)';
            ripple.style.width = ripple.style.height = '100px';
            ripple.style.left = e.offsetX - 50 + 'px';
            ripple.style.top = e.offsetY - 50 + 'px';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
    
    // Search bar interactions
    const searchInput = document.querySelector('.pg-search-input, .topbar-search-input');
    const searchWrap = document.querySelector('.pg-search-wrap, .topbar-search-wrap');
    
    if (searchInput && searchWrap) {
        searchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                searchWrap.classList.add('has-value');
            } else {
                searchWrap.classList.remove('has-value');
            }
        });
        
        // Typing animation
        let typingTimer;
        searchInput.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            searchWrap.style.borderColor = 'var(--accent-primary)';
            
            typingTimer = setTimeout(() => {
                searchWrap.style.borderColor = '';
            }, 500);
        });
    }
    
    // Add Batch button
    const addBtn = document.querySelector('.btn-dark, .btn-primary, .btn');
    if (addBtn) {
        addBtn.addEventListener('click', function(e) {
            // Ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.5)';
            ripple.style.width = ripple.style.height = '200px';
            ripple.style.left = e.offsetX - 100 + 'px';
            ripple.style.top = e.offsetY - 100 + 'px';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    }
    
    // Badges hover effect with enhanced interactions
    const badges = document.querySelectorAll('.badge');
    badges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.15)';
            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.2)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '';
        });
        
        // Add pulse effect on click
        badge.addEventListener('click', function(e) {
            e.stopPropagation();
            
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'badgePulseClick 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
            }, 10);
        });
    });
    
    // Pagination links
    const pageLinks = document.querySelectorAll('.page-link');
    pageLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.classList.contains('active')) {
                // Remove active from all
                pageLinks.forEach(l => l.classList.remove('active'));
                
                // Add active to clicked
                this.classList.add('active');
                
                // Pulse animation
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'pageActive 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
                }, 10);
            }
        });
        
        // Ripple effect
        link.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(14, 165, 233, 0.4)';
            ripple.style.width = ripple.style.height = '50px';
            ripple.style.left = e.offsetX - 25 + 'px';
            ripple.style.top = e.offsetY - 25 + 'px';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.5s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 500);
        });
    });
    
    // Smooth scroll to top on page change
    const contentWrapper = document.querySelector('.content-wrapper');
    if (contentWrapper) {
        pageLinks.forEach(link => {
            link.addEventListener('click', function() {
                contentWrapper.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    }
    
    // Table header sort indicators
    const tableHeaders = document.querySelectorAll('.smro-table thead th');
    tableHeaders.forEach(header => {
        header.addEventListener('mouseenter', function() {
            this.style.color = 'var(--accent-primary)';
            this.style.transform = 'translateY(-2px)';
        });
        
        header.addEventListener('mouseleave', function() {
            this.style.color = '';
            this.style.transform = 'translateY(0)';
        });
        
        header.addEventListener('click', function() {
            // Add sort animation
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'headerPulse 0.3s ease-out';
            }, 10);
        });
    });
    
    // Batch number cells - special emphasis
    const batchNumberCells = document.querySelectorAll('.smro-table tbody td:nth-child(2)');
    batchNumberCells.forEach(cell => {
        cell.addEventListener('mouseenter', function() {
            this.style.color = 'var(--accent-primary)';
            this.style.transform = 'translateX(3px)';
            this.style.textShadow = '0 2px 4px rgba(14, 165, 233, 0.2)';
            this.style.cursor = 'pointer';
        });
        
        cell.addEventListener('mouseleave', function() {
            this.style.color = '';
            this.style.transform = '';
            this.style.textShadow = '';
            this.style.cursor = '';
        });
    });
    
    // Medicine ID cells - interactive
    const medicineIdCells = document.querySelectorAll('.smro-table tbody td:nth-child(3)');
    medicineIdCells.forEach(cell => {
        cell.addEventListener('mouseenter', function() {
            this.style.color = 'var(--accent-teal)';
            this.style.fontWeight = '600';
            this.style.cursor = 'pointer';
        });
        
        cell.addEventListener('mouseleave', function() {
            this.style.color = '';
            this.style.fontWeight = '';
            this.style.cursor = '';
        });
    });
    
    // Supplier cells - hover effect
    const supplierCells = document.querySelectorAll('.smro-table tbody td:nth-child(5)');
    supplierCells.forEach(cell => {
        cell.addEventListener('mouseenter', function() {
            this.style.color = 'var(--text-heading)';
            this.style.fontWeight = '500';
        });
        
        cell.addEventListener('mouseleave', function() {
            this.style.color = '';
            this.style.fontWeight = '';
        });
    });
    
    // Add CSS animations dynamically
    if (!document.getElementById('batchesAnimations')) {
        const style = document.createElement('style');
        style.id = 'batchesAnimations';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            @keyframes buttonBounce {
                0%, 100% { transform: scale(1) rotate(0deg); }
                25% { transform: scale(1.2) rotate(-10deg); }
                75% { transform: scale(1.15) rotate(10deg); }
            }
            @keyframes headerPulse {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-3px); }
            }
            @keyframes badgePulseClick {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.2); }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Smooth scroll behavior
    const contentWrapper = document.querySelector('.content-wrapper');
    if (contentWrapper) {
        contentWrapper.style.scrollBehavior = 'smooth';
    }
    
    // Add loading state simulation for dynamic content
    const tableWrapper = document.querySelector('.table-wrapper');
    if (tableWrapper) {
        tableWrapper.addEventListener('scroll', function() {
            const scrollPercentage = (this.scrollTop / (this.scrollHeight - this.clientHeight)) * 100;
            
            if (scrollPercentage > 90) {
                // Near bottom - could trigger lazy loading
                console.log('Near bottom of table');
            }
        });
    }
    
    // Add intersection observer for staggered animations on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe table rows for scroll animations
    tableRows.forEach(row => {
        observer.observe(row);
    });
}

// Counter animation helper
function animateCounter(element, start, end, duration, delay = 0) {
    setTimeout(() => {
        const startTime = performance.now();
        const range = end - start;
        
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = Math.floor(start + (range * easeOutQuart));
            
            element.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent = end;
            }
        }
        
        requestAnimationFrame(update);
    }, delay);
}
