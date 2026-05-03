/* ============================================
   MEDICINES PAGE - ADVANCED JAVASCRIPT
   Industry-Standard Interactions
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    initMedicinesPageInteractions();
});

function initMedicinesPageInteractions() {
    // Medicine cards advanced interactions
    const medicineCards = document.querySelectorAll('.pgc');
    medicineCards.forEach((card, index) => {
        // 3D tilt effect
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px) scale(1.02)`;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0) scale(1)';
        });
        
        // Click ripple effect
        card.addEventListener('click', function(e) {
            if (e.target.closest('.pgc-action-btn') || e.target.closest('.pgc-icon-btn')) {
                return; // Don't add ripple if clicking action buttons
            }
            
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(14, 165, 233, 0.4)';
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
    });
    
    // Action buttons enhanced
    const actionButtons = document.querySelectorAll('.pgc-action-btn');
    actionButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.6)';
            ripple.style.width = ripple.style.height = '50px';
            ripple.style.left = e.offsetX - 25 + 'px';
            ripple.style.top = e.offsetY - 25 + 'px';
            ripple.style.transform = 'scale(0)';
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
    
    // Category filter items
    const categoryItems = document.querySelectorAll('.pf-radio');
    categoryItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove checked from all
            categoryItems.forEach(i => i.classList.remove('checked'));
            
            // Add checked to clicked
            this.classList.add('checked');
            
            // Pulse animation
            const dot = this.querySelector('.pf-radio-dot');
            if (dot) {
                dot.style.animation = 'none';
                setTimeout(() => {
                    dot.style.animation = 'checkPulse 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
                }, 10);
            }
        });
        
        // Magnetic hover effect
        item.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            
            this.style.transform = `translateX(${6 + x * 0.1}px)`;
        });
        
        item.addEventListener('mouseleave', function() {
            if (!this.classList.contains('checked')) {
                this.style.transform = 'translateX(0)';
            }
        });
    });
    
    // Search bar interactions
    const searchInput = document.querySelector('.pg-search-input');
    const searchWrap = document.querySelector('.pg-search-wrap');
    
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
    
    // Filter button interaction
    const filterBtn = document.querySelector('.pg-filter-btn');
    if (filterBtn) {
        filterBtn.addEventListener('click', function(e) {
            // Ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(14, 165, 233, 0.4)';
            ripple.style.width = ripple.style.height = '150px';
            ripple.style.left = e.offsetX - 75 + 'px';
            ripple.style.top = e.offsetY - 75 + 'px';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
            
            // Toggle active state
            this.classList.toggle('active');
        });
    }
    
    // Add Medicine button
    const addBtn = document.querySelector('.btn-create-order, .clean-btn-primary');
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
    
    // Dropdown toggles
    const dropdownToggles = document.querySelectorAll('.pf-dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const body = this.nextElementSibling;
            const icon = this.querySelector('.pf-toggle-icon');
            
            if (body && body.classList.contains('pf-dropdown-body')) {
                body.classList.toggle('pf-collapsed');
                
                if (icon) {
                    if (body.classList.contains('pf-collapsed')) {
                        icon.style.transform = 'rotate(0deg)';
                    } else {
                        icon.style.transform = 'rotate(180deg)';
                    }
                }
            }
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
    });
    
    // Icon buttons in card footer
    const iconButtons = document.querySelectorAll('.pgc-icon-btn');
    iconButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(14, 165, 233, 0.5)';
            ripple.style.width = ripple.style.height = '40px';
            ripple.style.left = '50%';
            ripple.style.top = '50%';
            ripple.style.transform = 'translate(-50%, -50%) scale(0)';
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
    
    // Add CSS animations dynamically
    if (!document.getElementById('medicinesAnimations')) {
        const style = document.createElement('style');
        style.id = 'medicinesAnimations';
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
        `;
        document.head.appendChild(style);
    }
    
    // Lazy load images
    const images = document.querySelectorAll('.pgc-img');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.5s';
                setTimeout(() => {
                    img.style.opacity = '1';
                }, 100);
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}
