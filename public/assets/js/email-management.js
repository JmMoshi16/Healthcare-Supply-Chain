/* ═══════════════════════════════════════════════════════════════
   PREMIUM EMAIL MANAGEMENT JAVASCRIPT
   Advanced Interactions & Real-time Updates
   ═══════════════════════════════════════════════════════════════ */

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initEmailManagement();
    loadEmailStats();
    animateOnScroll();
    initParticles();
});

/**
 * Initialize Email Management
 */
function initEmailManagement() {
    // Add ripple effect to all clickable cards
    document.querySelectorAll('.trigger-card, .stat-card, .config-item, .setting-item').forEach(card => {
        card.addEventListener('click', createRipple);
    });
    
    // Add magnetic effect to buttons
    document.querySelectorAll('.btn-modern').forEach(btn => {
        btn.addEventListener('mousemove', magneticEffect);
        btn.addEventListener('mouseleave', resetMagnetic);
    });
    
    // Animate cards on load
    animateCards();
}

/**
 * Create ripple effect
 */
function createRipple(e) {
    const card = e.currentTarget;
    const ripple = document.createElement('span');
    
    const rect = card.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.3) 0%, transparent 70%);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
        z-index: 10;
    `;
    
    card.style.position = 'relative';
    card.style.overflow = 'hidden';
    card.appendChild(ripple);
    
    setTimeout(() => ripple.remove(), 600);
}

/**
 * Magnetic button effect
 */
function magneticEffect(e) {
    const btn = e.currentTarget;
    const rect = btn.getBoundingClientRect();
    const x = e.clientX - rect.left - rect.width / 2;
    const y = e.clientY - rect.top - rect.height / 2;
    
    btn.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
}

function resetMagnetic(e) {
    const btn = e.currentTarget;
    btn.style.transform = 'translate(0, 0)';
}

/**
 * Animate cards on scroll
 */
function animateOnScroll() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.email-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(card);
    });
}

/**
 * Animate cards on load
 */
function animateCards() {
    const cards = document.querySelectorAll('.email-card, .stat-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Load email statistics
 */
function loadEmailStats() {
    // Simulate loading stats (replace with actual API call)
    setTimeout(() => {
        animateCounter('emailsSentToday', 0, 47, 1500);
        animateCounter('emailsPending', 0, 3, 1500);
        animateCounter('emailsFailed', 0, 1, 1500);
    }, 500);
}

/**
 * Animate counter
 */
function animateCounter(elementId, start, end, duration) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= end) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

/**
 * Send test email
 */
function sendTestEmail() {
    const email = document.getElementById('testEmail').value;
    const btnText = document.getElementById('testBtnText');
    const btnLoader = document.getElementById('testBtnLoader');
    const result = document.getElementById('testResult');
    
    if (!email) {
        showAlert(result, 'error', 'Please enter an email address');
        return;
    }
    
    if (!validateEmail(email)) {
        showAlert(result, 'error', 'Please enter a valid email address');
        return;
    }
    
    // Show loading state
    btnText.style.display = 'none';
    btnLoader.style.display = 'flex';
    
    const formData = new FormData();
    formData.append('email', email);
    formData.append('csrf_token', getCsrfToken());
    
    fetch('/emails/test', {
        method: 'POST',
        body: formData
    })
    .then(r => {
        if (!r.ok) throw new Error(`Server error ${r.status}`);
        return r.json();
    })
    .then(data => {
        if (data.success) {
            showAlert(result, 'success', data.message);
            confetti();
            refreshNotificationBell();
        } else {
            showAlert(result, 'error', data.message);
        }
    })
    .catch(err => {
        showAlert(result, 'error', 'Request failed: ' + err.message + '. Make sure SMTP is configured.');
    })
    .finally(() => {
        btnText.style.display = 'block';
        btnLoader.style.display = 'none';
    });
}

/**
 * Send alert
 */
function sendAlert(type) {
    const result = document.getElementById('alertResult');
    const card   = document.querySelector(`.trigger-card.${type}`);
    const endpoints = {
        'expiry':   '/emails/send-expiry-alerts',
        'lowstock': '/emails/send-low-stock-alerts',
        'daily':    '/emails/send-daily-report',
        'weekly':   '/emails/send-weekly-report'
    };
    
    const messages = {
        'expiry':   'Sending expiry alerts…',
        'lowstock': 'Sending low stock alerts…',
        'daily':    'Generating daily report…',
        'weekly':   'Generating weekly report…'
    };
    
    showAlert(result, 'info', messages[type]);
    if (card) card.style.opacity = '0.6';
    
    const formData = new FormData();
    formData.append('csrf_token', getCsrfToken());
    
    fetch(endpoints[type], {
        method: 'POST',
        body: formData
    })
    .then(r => {
        if (!r.ok) throw new Error(`Server error ${r.status}`);
        return r.json();
    })
    .then(data => {
        if (data.success) {
            showAlert(result, 'success', data.message);
            confetti();
            updateStats();
            // Reload the notification bell so the new notification appears immediately
            refreshNotificationBell();
        } else {
            showAlert(result, 'error', data.message);
        }
    })
    .catch(err => {
        showAlert(result, 'error', 'Request failed: ' + err.message + '. CSRF or server error.');
    })
    .finally(() => {
        if (card) card.style.opacity = '1';
    });
}

/**
 * Show alert with animation
 */
function showAlert(container, type, message) {
    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-x-circle-fill',
        info: 'bi-info-circle-fill'
    };
    
    const alert = document.createElement('div');
    alert.className = `alert-modern alert-${type}`;
    alert.innerHTML = `
        <i class="bi ${icons[type]}"></i>
        <span>${message}</span>
    `;
    
    container.innerHTML = '';
    container.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.style.animation = 'slideOutUp 0.3s ease';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}

/**
 * Get CSRF token from meta tag or cookie
 */
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.content;
    
    const cookies = document.cookie.split(';');
    for (let cookie of cookies) {
        const [name, value] = cookie.trim().split('=');
        if (name === 'csrf_token') return value;
    }
    return '';
}

/**
 * Validate email
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Update statistics
 */
function updateStats() {
    const sentToday = document.getElementById('emailsSentToday');
    if (sentToday) {
        const current = parseInt(sentToday.textContent) || 0;
        animateCounter('emailsSentToday', current, current + 1, 500);
    }
}

/**
 * Refresh notification bell in the topbar
 * Calls the global loadNotifications() defined in app.php layout
 */
function refreshNotificationBell() {
    // Small delay so the DB write completes first
    setTimeout(() => {
        if (typeof loadNotifications === 'function') {
            loadNotifications();
        }
        // Also pulse the bell icon to draw attention
        const bell = document.getElementById('notifBellBtn');
        if (bell) {
            bell.style.animation = 'none';
            bell.style.transform = 'scale(1.4)';
            bell.style.color = '#f59e0b';
            setTimeout(() => {
                bell.style.transition = 'all 0.4s cubic-bezier(0.34,1.56,0.64,1)';
                bell.style.transform = 'scale(1)';
                bell.style.color = '';
            }, 300);
        }
    }, 600);
}

/**
 * Confetti effect
 */
function confetti() {
    const colors = ['#667eea', '#764ba2', '#10b981', '#f59e0b', '#ef4444'];
    const confettiCount = 50;
    
    for (let i = 0; i < confettiCount; i++) {
        createConfetti(colors[Math.floor(Math.random() * colors.length)]);
    }
}

function createConfetti(color) {
    const confetti = document.createElement('div');
    confetti.style.cssText = `
        position: fixed;
        width: 10px;
        height: 10px;
        background: ${color};
        top: -10px;
        left: ${Math.random() * 100}vw;
        opacity: 1;
        transform: rotate(${Math.random() * 360}deg);
        pointer-events: none;
        z-index: 9999;
    `;
    
    document.body.appendChild(confetti);
    
    const animation = confetti.animate([
        { 
            transform: `translate(0, 0) rotate(0deg)`,
            opacity: 1
        },
        { 
            transform: `translate(${(Math.random() - 0.5) * 200}px, ${window.innerHeight + 10}px) rotate(${Math.random() * 720}deg)`,
            opacity: 0
        }
    ], {
        duration: 3000 + Math.random() * 2000,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
    });
    
    animation.onfinish = () => confetti.remove();
}

/**
 * Initialize particles background
 */
function initParticles() {
    const hero = document.querySelector('.email-hero-header');
    if (!hero) return;
    
    for (let i = 0; i < 20; i++) {
        createParticle(hero);
    }
}

function createParticle(container) {
    const particle = document.createElement('div');
    particle.style.cssText = `
        position: absolute;
        width: ${Math.random() * 4 + 2}px;
        height: ${Math.random() * 4 + 2}px;
        background: rgba(255, 255, 255, ${Math.random() * 0.5 + 0.2});
        border-radius: 50%;
        top: ${Math.random() * 100}%;
        left: ${Math.random() * 100}%;
        pointer-events: none;
        animation: float ${Math.random() * 10 + 10}s ease-in-out infinite;
        animation-delay: ${Math.random() * 5}s;
    `;
    
    container.appendChild(particle);
}

/**
 * Add CSS animations dynamically
 */
if (!document.getElementById('emailAnimations')) {
    const style = document.createElement('style');
    style.id = 'emailAnimations';
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        @keyframes slideOutUp {
            to {
                transform: translateY(-20px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Ctrl/Cmd + T = Test Email
    if ((e.ctrlKey || e.metaKey) && e.key === 't') {
        e.preventDefault();
        document.getElementById('testEmail')?.focus();
    }
});

/**
 * Show browser notification
 */
function showNotification(title, message, type = 'info') {
    // Request permission if not granted
    if (!('Notification' in window)) {
        return;
    }
    
    if (Notification.permission === 'granted') {
        createNotification(title, message, type);
    } else if (Notification.permission !== 'denied') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                createNotification(title, message, type);
            }
        });
    }
}

function createNotification(title, message, type) {
    const icons = {
        success: '✅',
        error: '❌',
        info: 'ℹ️',
        warning: '⚠️'
    };
    
    const notification = new Notification(icons[type] + ' ' + title, {
        body: message,
        icon: '/assets/images/logo.png',
        badge: '/assets/images/badge.png',
        tag: 'email-notification',
        requireInteraction: true,
        silent: false
    });
    
    notification.onclick = function() {
        window.focus();
        notification.close();
    };
    
    setTimeout(() => notification.close(), 10000);
}

// Request notification permission on load
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// Auto-refresh stats every 30 seconds
setInterval(loadEmailStats, 30000);
