<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Healthcare Supply Chain') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/premium.css">
    <link rel="stylesheet" href="/assets/css/sticky-fix.css">
    <link rel="stylesheet" href="/assets/css/sidebar-advanced.css">
    <link rel="stylesheet" href="/assets/css/topbar-fix.css">
    <link rel="stylesheet" href="/assets/css/notifications.css">
    <link rel="stylesheet" href="/assets/css/calendar.css">
</head>
<body>
<div class="layout-wrapper">

<?php
$uri     = $_SERVER['REQUEST_URI'];
$role    = auth()['role'] ?? 'staff';
$isDash  = str_contains($uri, 'dashboard');
$isMed   = str_contains($uri, 'medicines');
$isBatch = str_contains($uri, 'batches');
$isStock = str_contains($uri, 'stocks');
$isUsers = str_contains($uri, 'users');
$isCat   = str_contains($uri, 'categories');
?>

<!-- ═══════════════════════════════════════
     SIDEBAR SHELL (icon rail + expanded panel)
     ═══════════════════════════════════════ -->
<div class="sb-shell" id="sbShell">

    <!-- ICON RAIL -->
    <div class="sb-rail">
        <div class="sb-rail-top">
            <div class="sb-rail-logo" title="HealthChain">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" stroke="white" stroke-width="1.7"/>
                    <path d="M12 7v10M7 12h10" stroke="white" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </div>
            <button class="sb-rail-icon <?= $isDash  ? 'active' : '' ?>" onclick="location.href='/dashboard'"       title="Dashboard"><i class="bi bi-grid-fill"></i></button>
            <button class="sb-rail-icon <?= $isMed   ? 'active' : '' ?>" onclick="location.href='/medicines'"       title="Medicines"><i class="bi bi-capsule"></i></button>
            <button class="sb-rail-icon <?= $isCat   ? 'active' : '' ?>" onclick="location.href='/categories'"      title="Categories"><i class="bi bi-tags"></i></button>
            <button class="sb-rail-icon <?= $isBatch ? 'active' : '' ?>" onclick="location.href='/batches'"         title="Batches"><i class="bi bi-box-seam"></i></button>
            <button class="sb-rail-icon <?= $isStock ? 'active' : '' ?>" onclick="location.href='/stocks'"          title="Stock Transactions"><i class="bi bi-arrow-left-right"></i></button>
            <?php if (has_role('superadmin')): ?>
            <div class="sb-rail-sep"></div>
            <button class="sb-rail-icon <?= $isUsers ? 'active' : '' ?>" onclick="location.href='/users'"           title="Users"><i class="bi bi-people"></i></button>
            <?php endif; ?>
        </div>
        <div class="sb-rail-bottom">
            <button class="sb-rail-icon sb-pin-btn" id="sbPinBtn" onclick="sbTogglePin()" title="Pin sidebar"><i class="bi bi-layout-sidebar" id="sbPinIcon"></i></button>
            <button class="sb-rail-icon" onclick="sbToggleDark()" title="Toggle theme"><i class="bi bi-circle-half"></i></button>
            <button class="sb-rail-icon sb-rail-danger" onclick="location.href='/logout'" title="Sign Out"><i class="bi bi-box-arrow-right"></i></button>
        </div>
    </div>

    <!-- EXPANDED PANEL -->
    <div class="sb-panel" id="sbPanel">
        <div class="sb-panel-header">
            <div class="sb-panel-logo">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" stroke="white" stroke-width="1.8"/>
                    <path d="M12 7v10M7 12h10" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="sb-panel-brand">
                <div class="sb-panel-brand-name">HealthChain</div>
                <div class="sb-panel-brand-sub">Supply Chain Management</div>
            </div>
            <button class="sb-panel-chevron" onclick="sbToggleDark()" title="Toggle theme"><i class="bi bi-circle-half"></i></button>
        </div>

        <button class="sb-switch-btn" onclick="sbTogglePin()">
            <i class="bi bi-pin-angle" id="sbSwitchIcon"></i>
            <span id="sbSwitchLabel">Pin sidebar</span>
        </button>

        <nav class="sb-nav">
            <a href="/dashboard" class="sb-item <?= $isDash  ? 'active' : '' ?>"><i class="bi bi-grid<?= $isDash ? '-fill' : '' ?>"></i><span>Dashboard</span></a>
            <a href="/medicines" class="sb-item <?= $isMed   ? 'active' : '' ?>"><i class="bi bi-capsule"></i><span>Medicines</span></a>
            <a href="/categories" class="sb-item <?= $isCat   ? 'active' : '' ?>"><i class="bi bi-tags"></i><span>Categories</span></a>
            <a href="/batches"   class="sb-item <?= $isBatch ? 'active' : '' ?>"><i class="bi bi-box-seam"></i><span>Batches</span></a>
            <a href="/stocks"    class="sb-item <?= $isStock ? 'active' : '' ?>"><i class="bi bi-arrow-left-right"></i><span>Stock Transactions</span></a>
            <?php if (has_role('superadmin')): ?>
            <div class="sb-rail-sep" style="width:100%;margin:.4rem 0;"></div>
            <a href="/users" class="sb-item <?= $isUsers ? 'active' : '' ?>"><i class="bi bi-people<?= $isUsers ? '-fill' : '' ?>"></i><span>Users</span></a>
            <?php endif; ?>
        </nav>

        <div class="sb-user">
            <div class="sb-user-avatar">
                <?= strtoupper(substr(auth()['fullname'] ?? 'U', 0, 1)) ?>
                <span class="sb-online-dot"></span>
            </div>
            <div class="sb-user-info">
                <div class="sb-user-name"><?= esc(auth()['fullname'] ?? 'User') ?></div>
                <div class="sb-user-role"><?= esc($role) ?></div>
            </div>
            <div class="sb-user-btns">
                <a href="/logout" class="sb-user-btn sb-user-btn-out" title="Sign Out"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════
     MAIN WRAPPER
     ═══════════════════════════════════════ -->
<div class="main-wrapper">

    <!-- TOPBAR -->
    <header class="topbar" id="mainTopbar">
        <div class="topbar-left">
            <div class="topbar-brand-pill" style="background:linear-gradient(135deg,#0f4c81,#0ea5e9);padding:.32rem .85rem;border-radius:99px;display:flex;align-items:center;gap:.4rem;box-shadow:0 2px 8px rgba(14,165,233,.25);">
                <span style="font-size:.78rem;font-weight:800;color:#fff;letter-spacing:-.01em;">HealthChain</span>
                <span style="font-size:.72rem;color:rgba(255,255,255,.4);font-weight:300;">/</span>
                <span style="font-size:.72rem;font-weight:600;color:#bfdbfe;letter-spacing:.02em;"><?= esc(ucfirst($role)) ?></span>
            </div>
        </div>

        <div class="topbar-center">
            <div class="topbar-search-wrap" id="globalSearch" style="position:relative;">
                <input type="text" class="topbar-search-input" placeholder="Search medicines, batches..." id="globalSearchInput" autocomplete="off" style="padding-left:1rem;">
                <!-- Search Dropdown -->
                <div id="searchResultsPanel" style="display:none;position:absolute;top:calc(100% + 8px);left:50%;transform:translateX(-50%);width:540px;max-width:80vw;background:var(--bg-card);border:1px solid var(--border);border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.14);z-index:600;overflow:hidden;">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem 1.25rem;border-bottom:1px solid var(--border);">
                        <span style="font-weight:600;font-size:.875rem;color:var(--text-heading);">Search Results</span>
                        <button onclick="closeSearchResults()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1rem;"><i class="bi bi-x"></i></button>
                    </div>
                    <div id="searchResultsBody" style="max-height:380px;overflow-y:auto;"></div>
                </div>
            </div>
        </div>

        <div class="topbar-right">
            <div class="topbar-date">
                <i class="bi bi-calendar3"></i>
                <span><?= date('M d, Y') ?></span>
            </div>
            <div class="topbar-divider"></div>
            <button class="topbar-icon-btn" onclick="sbToggleDark()" title="Toggle Theme">
                <i class="bi bi-moon-stars-fill"></i>
            </button>
            <button class="topbar-icon-btn" onclick="toggleNotifications()" title="Notifications" id="notifBellBtn">
                <i class="bi bi-bell-fill"></i>
                <span class="topbar-notif-badge" id="notifBadge" style="display:none;">0</span>
            </button>
            <div class="topbar-divider"></div>
            <div style="position:relative;" id="topbarDropdownWrap">
                <button onclick="toggleTopbarDropdown()" style="display:flex;align-items:center;gap:.55rem;background:none;border:none;cursor:pointer;padding:.2rem;">
                    <div class="topbar-avatar-ring" style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0f4c81,#0ea5e9);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.82rem;color:#fff;position:relative;">
                        <?= strtoupper(substr(auth()['fullname'] ?? 'U', 0, 2)) ?>
                        <div style="position:absolute;bottom:0;right:0;width:9px;height:9px;background:#22c55e;border-radius:50%;border:2px solid #fff;"></div>
                    </div>
                    <div style="text-align:left;">
                        <div style="font-size:.8rem;font-weight:700;color:var(--text-heading);"><?= esc(explode(' ', auth()['fullname'] ?? 'User')[0]) ?></div>
                        <div style="font-size:.7rem;color:var(--text-muted);"><?= esc(ucfirst($role)) ?></div>
                    </div>
                    <i class="bi bi-chevron-down" style="font-size:.7rem;color:var(--text-muted);" id="topbarChevron"></i>
                </button>
                <div id="topbarDropdown" class="topbar-dropdown" style="display:none;position:absolute;top:calc(100%+8px);right:0;width:220px;background:var(--bg-card);border:1px solid var(--border);border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,.12);z-index:200;overflow:hidden;">
                    <div style="padding:1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:.75rem;background:var(--bg-surface);">
                        <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#0f4c81,#0ea5e9);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.85rem;color:#fff;"><?= strtoupper(substr(auth()['fullname'] ?? 'U', 0, 1)) ?></div>
                        <div>
                            <div style="font-size:.85rem;font-weight:700;color:var(--text-heading);"><?= esc(auth()['fullname'] ?? 'User') ?></div>
                            <div style="font-size:.75rem;color:var(--text-muted);"><?= esc(ucfirst($role)) ?></div>
                        </div>
                    </div>
                    <a href="/logout" style="display:flex;align-items:center;gap:.65rem;padding:.8rem 1rem;font-size:.85rem;color:#dc2626;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                        <i class="bi bi-box-arrow-right"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </header>


    <!-- Notification Panel -->
    <div class="notif-overlay" id="notifOverlay" onclick="closeNotifications()"></div>
    <div class="notif-panel" id="notifPanel">
        <div class="notif-header">
            <div class="notif-header-left">
                <span class="notif-title">Notifications</span>
                <span class="notif-count" id="notifCount">0</span>
            </div>
            <div class="notif-actions">
                <button class="notif-action-btn" onclick="markAllAsRead()"><i class="bi bi-check-all"></i> Mark all read</button>
                <button class="notif-close-btn" onclick="closeNotifications()"><i class="bi bi-x"></i></button>
            </div>
        </div>
        <div class="notif-body" id="notifBody">
            <div class="notif-loading"><div class="notif-spinner"></div><p>Loading...</p></div>
        </div>
    </div>

    <!-- Calendar Modal -->
    <div class="calendar-modal-overlay" id="calendarModal" style="display:none;" onclick="if(event.target===this)closeCalendarModal()">
        <div class="calendar-modal">
            <div class="calendar-modal-header">
                <div class="calendar-modal-title"><i class="bi bi-calendar3"></i> Calendar & Notes</div>
                <button class="calendar-modal-close" onclick="closeCalendarModal()"><i class="bi bi-x"></i></button>
            </div>
            <div class="calendar-modal-body">
                <div class="calendar-section">
                    <div class="calendar-controls">
                        <div class="calendar-month-display" id="calendarMonthDisplay"></div>
                        <div class="calendar-nav-btns">
                            <button class="calendar-nav-btn" onclick="changeMonth(-1)"><i class="bi bi-chevron-left"></i></button>
                            <button class="calendar-nav-btn" onclick="changeMonth(0)">Today</button>
                            <button class="calendar-nav-btn" onclick="changeMonth(1)"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="calendar-grid" id="calendarGrid"></div>
                </div>
                <div class="notes-section">
                    <div class="notes-header">
                        <div class="notes-date-display" id="notesDateDisplay">Select a date</div>
                        <button class="add-note-btn" onclick="openNoteForm()" id="addNoteBtn" style="display:none;"><i class="bi bi-plus"></i> Add Note</button>
                    </div>
                    <div class="notes-list" id="notesList">
                        <div class="empty-notes"><i class="bi bi-calendar-check"></i><p>Select a date to view notes</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Note Form Modal -->
    <div class="note-form-modal" id="noteFormModal" style="display:none;" onclick="if(event.target===this)closeNoteForm()">
        <div class="note-form-container">
            <div class="note-form-header">
                <div class="note-form-title" id="noteFormTitle">Add Note</div>
                <button class="calendar-modal-close" onclick="closeNoteForm()"><i class="bi bi-x"></i></button>
            </div>
            <div class="note-form-body">
                <form id="noteForm" onsubmit="saveNote(event)">
                    <input type="hidden" id="noteId" name="id">
                    <input type="hidden" id="noteDate" name="note_date">
                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-input" id="noteTitle" name="title" required placeholder="Enter note title">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-textarea" id="noteDescription" name="description" placeholder="Enter note description (optional)"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Priority</label>
                        <select class="form-select" id="notePriority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="note-form-footer">
                <button class="btn-cancel" onclick="closeNoteForm()">Cancel</button>
                <button class="btn-save" onclick="document.getElementById('noteForm').requestSubmit()">Save Note</button>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <main class="content-wrapper">
        <?php if ($success = flash('success')): ?>
        <div class="alert-flash alert-flash-success">
            <i class="bi bi-check-circle-fill"></i><?= esc($success) ?>
            <button onclick="this.closest('.alert-flash').remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:1rem;color:#15803d;">×</button>
        </div>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
        <div class="alert-flash alert-flash-error">
            <i class="bi bi-exclamation-circle-fill"></i><?= esc($error) ?>
            <button onclick="this.closest('.alert-flash').remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:1rem;color:#dc2626;">×</button>
        </div>
        <?php endif; ?>
        <?= $content ?>
    </main>
</div><!-- end .main-wrapper -->
</div><!-- end .layout-wrapper -->

<style>
.alert-flash{display:flex;align-items:center;gap:.65rem;padding:.85rem 1.1rem;border-radius:10px;margin-bottom:1.25rem;font-size:.875rem;font-weight:500;}
.alert-flash-success{background:#ecfdf5;color:#15803d;border:1px solid #bbf7d0;}
.alert-flash-error  {background:#fef2f2;color:#dc2626; border:1px solid #fecaca;}
.search-results-overlay{position:fixed;inset:0;background:rgba(0,0,0,.3);z-index:450;}
</style>

<script>
/* Animate Header and Sidebar on Load */
document.addEventListener('DOMContentLoaded', function() {
    animateHeaderAndSidebar();
    addScrollEffect();
    initSidebarInteractions();
});

function initSidebarInteractions() {
    // Add ripple effect to nav items
    const navItems = document.querySelectorAll('.sb-item');
    navItems.forEach((item, index) => {
        // Ripple on click
        item.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.classList.add('sb-item-ripple');
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
        
        // Magnetic hover effect
        item.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const deltaX = (x - centerX) / centerX;
            const deltaY = (y - centerY) / centerY;
            
            this.style.transform = `translate(${deltaX * 3}px, ${deltaY * 2}px)`;
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translate(0, 0)';
        });
        
        // Staggered entrance animation
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            item.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 100 + (index * 50));
    });
    
    // Rail icon advanced interactions
    const railIcons = document.querySelectorAll('.sb-rail-icon');
    railIcons.forEach((icon, index) => {
        // Magnetic effect
        icon.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            this.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px) scale(1.2) rotate(10deg)`;
        });
        
        icon.addEventListener('mouseleave', function() {
            this.style.transform = 'translate(0, 0) scale(1) rotate(0deg)';
        });
        
        // Click animation
        icon.addEventListener('click', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'iconBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
            }, 10);
        });
        
        // Staggered entrance
        icon.style.opacity = '0';
        icon.style.transform = 'translateY(10px)';
        setTimeout(() => {
            icon.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            icon.style.opacity = '1';
            icon.style.transform = 'translateY(0)';
        }, 200 + (index * 40));
    });
    
    // User card advanced interaction
    const userCard = document.querySelector('.sb-user');
    if (userCard) {
        // Tilt effect
        userCard.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            
            const avatar = this.querySelector('.sb-user-avatar');
            if (avatar) {
                avatar.style.transform = `scale(1.15) rotate(${rotateY}deg)`;
            }
        });
        
        userCard.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
            
            const avatar = this.querySelector('.sb-user-avatar');
            if (avatar) {
                avatar.style.transform = 'scale(1) rotate(0deg)';
            }
        });
        
        // Click pulse
        userCard.addEventListener('click', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'pulse 0.4s ease-out';
            }, 10);
        });
    }
    
    // Logo advanced interactions
    const logos = document.querySelectorAll('.sb-rail-logo, .sb-panel-logo');
    logos.forEach(logo => {
        // Hover 3D effect
        logo.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            this.style.transform = `perspective(500px) rotateY(${x * 0.2}deg) rotateX(${-y * 0.2}deg) scale(1.15)`;
        });
        
        logo.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(500px) rotateY(0deg) rotateX(0deg) scale(1)';
        });
        
        // Click bounce
        logo.addEventListener('click', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'iconBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
            }, 10);
        });
    });
    
    // Pin button enhanced feedback
    const pinBtn = document.getElementById('sbPinBtn');
    if (pinBtn) {
        pinBtn.addEventListener('click', function(e) {
            // Ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(14, 165, 233, 0.5)';
            ripple.style.width = ripple.style.height = '100px';
            ripple.style.left = e.offsetX - 50 + 'px';
            ripple.style.top = e.offsetY - 50 + 'px';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
            
            // Icon animation
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.animation = 'none';
                setTimeout(() => {
                    icon.style.animation = 'iconBounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
                }, 10);
            }
        });
    }
    
    // Switch button advanced interaction
    const switchBtn = document.querySelector('.sb-switch-btn');
    if (switchBtn) {
        switchBtn.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Create ripple
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(14, 165, 233, 0.4)';
            ripple.style.width = ripple.style.height = '200px';
            ripple.style.left = x - 100 + 'px';
            ripple.style.top = y - 100 + 'px';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
        
        // Magnetic hover
        switchBtn.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            this.style.transform = `translate(${x * 0.1}px, ${y * 0.1}px)`;
        });
        
        switchBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translate(0, 0)';
        });
    }
    
    // User buttons enhanced
    const userBtns = document.querySelectorAll('.sb-user-btn');
    userBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(220, 38, 38, 0.4)';
            ripple.style.width = ripple.style.height = '80px';
            ripple.style.left = e.offsetX - 40 + 'px';
            ripple.style.top = e.offsetY - 40 + 'px';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.5s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 500);
        });
        
        // Shake on hover
        btn.addEventListener('mouseenter', function() {
            this.style.animation = 'shake 0.5s ease-in-out';
        });
        
        btn.addEventListener('animationend', function() {
            this.style.animation = '';
        });
    });
    
    // Panel chevron interaction
    const chevron = document.querySelector('.sb-panel-chevron');
    if (chevron) {
        chevron.addEventListener('click', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'spin 0.5s ease-out';
            }, 10);
        });
    }
    
    // Add CSS animations dynamically
    if (!document.getElementById('sidebarAnimations')) {
        const style = document.createElement('style');
        style.id = 'sidebarAnimations';
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-3px) rotate(-5deg); }
                75% { transform: translateX(3px) rotate(5deg); }
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Smooth scroll for sidebar
    const sbNav = document.querySelector('.sb-nav');
    if (sbNav) {
        sbNav.style.scrollBehavior = 'smooth';
    }
}

function addScrollEffect() {
    const contentWrapper = document.querySelector('.content-wrapper');
    const topbar = document.getElementById('mainTopbar');
    
    if (contentWrapper && topbar) {
        contentWrapper.addEventListener('scroll', function() {
            if (contentWrapper.scrollTop > 10) {
                topbar.classList.add('scrolled');
            } else {
                topbar.classList.remove('scrolled');
            }
        });
    }
}

function animateHeaderAndSidebar() {
    const topbar = document.getElementById('mainTopbar');
    const sidebar = document.getElementById('sbShell');
    
    // Animate topbar
    if (topbar) {
        topbar.style.opacity = '0';
        topbar.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            topbar.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            topbar.style.opacity = '1';
            topbar.style.transform = 'translateY(0)';
        }, 100);
    }
    
    // Animate sidebar
    if (sidebar) {
        sidebar.style.opacity = '0';
        sidebar.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            sidebar.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            sidebar.style.opacity = '1';
            sidebar.style.transform = 'translateX(0)';
        }, 200);
    }
    
    // Animate sidebar items
    const sidebarItems = document.querySelectorAll('.sb-rail-icon, .sb-item');
    sidebarItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-10px)';
        setTimeout(() => {
            item.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 400 + (index * 50));
    });
}

/* Theme */
(function(){ if(localStorage.getItem('sbDark')==='true') document.documentElement.setAttribute('data-theme','dark'); })();
function sbToggleDark(){ const d=document.documentElement.getAttribute('data-theme')==='dark'; document.documentElement.setAttribute('data-theme',d?'light':'dark'); localStorage.setItem('sbDark',String(!d)); }

/* Sidebar Pin */
function sbTogglePin(){
    const s=document.getElementById('sbShell'),pinned=s.classList.toggle('pinned');
    document.getElementById('sbPinBtn').classList.toggle('pinned-active',pinned);
    document.getElementById('sbPinIcon').className=pinned?'bi bi-layout-sidebar-reverse':'bi bi-layout-sidebar';
    document.getElementById('sbSwitchIcon').className=pinned?'bi bi-pin-angle-fill':'bi bi-pin-angle';
    document.getElementById('sbSwitchLabel').textContent=pinned?'Unpin sidebar':'Pin sidebar';
    localStorage.setItem('sbPinned',pinned);
}
(function(){
    if(localStorage.getItem('sbPinned')==='true'){
        const s=document.getElementById('sbShell'); if(!s)return; s.classList.add('pinned');
        const p=document.getElementById('sbPinBtn'),pi=document.getElementById('sbPinIcon'),sl=document.getElementById('sbSwitchLabel'),si=document.getElementById('sbSwitchIcon');
        if(p)p.classList.add('pinned-active'); if(pi)pi.className='bi bi-layout-sidebar-reverse';
        if(si)si.className='bi bi-pin-angle-fill'; if(sl)sl.textContent='Unpin sidebar';
    }
})();

/* Topbar Dropdown */
function toggleTopbarDropdown(){
    const dd=document.getElementById('topbarDropdown');
    dd.style.display=dd.style.display==='none'?'block':'none';
}
document.addEventListener('click',function(e){
    const w=document.getElementById('topbarDropdownWrap');
    if(w&&!w.contains(e.target)) document.getElementById('topbarDropdown').style.display='none';
});

/* Search */
let _st;
const _si=document.getElementById('globalSearchInput');
if(_si){
    document.addEventListener('keydown',e=>{ if(e.key==='Escape')closeSearchResults(); });
    document.addEventListener('click',e=>{ if(!document.getElementById('globalSearch').contains(e.target)) closeSearchResults(); });
    _si.addEventListener('keydown',e=>{
        if(e.key==='Enter'){ e.preventDefault(); const q=_si.value.trim(); clearTimeout(_st); if(q.length>=2) performSearch(q); else closeSearchResults(); }
    });
    _si.addEventListener('input',e=>{ const q=e.target.value.trim(); clearTimeout(_st); if(q.length>=2) _st=setTimeout(()=>performSearch(q),300); else closeSearchResults(); });
    _si.addEventListener('focus',()=>{ if(_si.value.trim().length>=2) document.getElementById('searchResultsPanel').style.display='block'; });
}
function performSearch(q){
    document.getElementById('searchResultsPanel').style.display='block';
    document.getElementById('searchResultsBody').innerHTML='<div style="text-align:center;padding:2rem;color:#94a3b8;"><p>Searching...</p></div>';
    fetch(`/api/search?q=${encodeURIComponent(q)}`).then(r=>r.json()).then(displaySearchResults).catch(()=>{
        document.getElementById('searchResultsBody').innerHTML='<div style="text-align:center;padding:2rem;color:#94a3b8;"><p>Search failed.</p></div>';
    });
}
function displaySearchResults(data){
    if(!data.medicines?.length&&!data.batches?.length){
        document.getElementById('searchResultsBody').innerHTML='<div style="text-align:center;padding:2rem;color:#94a3b8;"><i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i><p>No results found</p></div>';
        return;
    }
    let h='';
    if(data.medicines?.length){
        h+='<div style="padding:.5rem 0;"><div style="padding:.5rem 1.25rem;font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;"><i class="bi bi-capsule"></i> Medicines</div>';
        data.medicines.forEach(i=>{ h+=`<a href="/medicines/${i.id}" style="display:flex;align-items:center;gap:.85rem;padding:.7rem 1.25rem;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='none'"><div style="width:34px;height:34px;background:#eef3ff;color:#3d52d5;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;"><i class="bi bi-capsule"></i></div><div><div style="font-size:.85rem;font-weight:500;color:var(--text-heading);">${i.name}</div><div style="font-size:.75rem;color:#94a3b8;">${i.category??''} · ${i.unit??''}</div></div><i class="bi bi-arrow-right" style="color:#94a3b8;margin-left:auto;"></i></a>`; });
        h+='</div>';
    }
    if(data.batches?.length){
        h+='<div style="padding:.5rem 0;"><div style="padding:.5rem 1.25rem;font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;"><i class="bi bi-box-seam"></i> Batches</div>';
        data.batches.forEach(i=>{ h+=`<a href="/batches/${i.id}" style="display:flex;align-items:center;gap:.85rem;padding:.7rem 1.25rem;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='none'"><div style="width:34px;height:34px;background:#ecfdf5;color:#059669;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;"><i class="bi bi-box-seam"></i></div><div><div style="font-size:.85rem;font-weight:500;color:var(--text-heading);">${i.batch_number}</div><div style="font-size:.75rem;color:#94a3b8;">Qty: ${i.current_quantity} · Expires: ${i.expiry_date}</div></div><i class="bi bi-arrow-right" style="color:#94a3b8;margin-left:auto;"></i></a>`; });
        h+='</div>';
    }
    document.getElementById('searchResultsBody').innerHTML=h;
}
function closeSearchResults(){ document.getElementById('searchResultsPanel').style.display='none'; }

/* Notifications */
function toggleNotifications(){
    const np=document.getElementById('notifPanel'),no=document.getElementById('notifOverlay');
    if(!np||!no)return;
    const open=np.classList.contains('active');
    np.classList.toggle('active',!open); no.classList.toggle('active',!open);
    if(!open) loadNotifications();
}
function closeNotifications(){ document.getElementById('notifPanel')?.classList.remove('active'); document.getElementById('notifOverlay')?.classList.remove('active'); }
function loadNotifications(){
    const nb=document.getElementById('notifBody'); if(!nb)return;
    fetch('/api/notifications').then(r=>r.json()).then(data=>{ if(data.success){updateNotificationBadge(data.unread_count);renderNotifications(data.notifications);} }).catch(()=>{ nb.innerHTML='<div class="notif-empty"><i class="bi bi-exclamation-circle"></i><p>Failed to load</p></div>'; });
}
function updateNotificationBadge(count){
    const b=document.getElementById('notifBadge'),c=document.getElementById('notifCount');
    if(!b||!c)return;
    if(count>0){b.textContent=count>99?'99+':count;b.style.display='flex';c.textContent=count;}
    else{b.style.display='none';c.textContent='0';}
}
function renderNotifications(notifications){
    const nb=document.getElementById('notifBody'); if(!nb)return;
    if(!notifications?.length){nb.innerHTML='<div class="notif-empty"><i class="bi bi-bell-slash"></i><p>No notifications</p></div>';return;}
    let h='';
    notifications.forEach(n=>{h+=`<a href="${n.link}" class="notif-item ${n.unread?'unread':''}" onclick="markAsRead('${n.id}',event)"><div class="notif-icon ${n.urgency||'low'}"><i class="bi bi-${n.icon}"></i></div><div class="notif-content"><div class="notif-item-title">${n.title}</div><div class="notif-item-message">${n.message}</div><div class="notif-item-time">${n.time}</div></div><i class="bi bi-chevron-right notif-arrow"></i></a>`;});
    nb.innerHTML=h;
}
function markAsRead(id,event){
    fetch('/api/notifications/mark-read',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})}).then(()=>{ if(event.currentTarget)event.currentTarget.classList.remove('unread'); updateNotificationBadge(document.querySelectorAll('.notif-item.unread').length); });
}
function markAllAsRead(){
    fetch('/api/notifications/mark-all-read',{method:'POST',headers:{'Content-Type':'application/json'}}).then(r=>r.json()).then(data=>{ if(data.success){document.querySelectorAll('.notif-item.unread').forEach(i=>i.classList.remove('unread'));updateNotificationBadge(0);} });
}
if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',loadNotifications);
else loadNotifications();
setInterval(loadNotifications,120000);

/* ═══════════════════════════════════════
   CALENDAR SYSTEM
   ═══════════════════════════════════════ */
let currentDate = new Date();
let selectedDate = null;
let calendarNotes = {};

function openCalendarModal() {
    document.getElementById('calendarModal').style.display = 'flex';
    renderCalendar();
    loadCalendarNotes();
}

function closeCalendarModal() {
    document.getElementById('calendarModal').style.display = 'none';
}

function changeMonth(offset) {
    if (offset === 0) {
        currentDate = new Date();
    } else {
        currentDate.setMonth(currentDate.getMonth() + offset);
    }
    renderCalendar();
    loadCalendarNotes();
}

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const today = new Date();
    
    // Update month display
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('calendarMonthDisplay').textContent = `${monthNames[month]} ${year}`;
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    
    let html = '';
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        html += `<div class="calendar-day-header">${day}</div>`;
    });
    
    // Previous month days
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        html += `<div class="calendar-day other-month">${day}</div>`;
    }
    
    // Current month days
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        const dateStr = formatDate(date);
        const isToday = date.toDateString() === today.toDateString();
        const isSelected = selectedDate && date.toDateString() === selectedDate.toDateString();
        const hasNotes = calendarNotes[dateStr] && calendarNotes[dateStr].length > 0;
        
        let classes = 'calendar-day';
        if (isToday) classes += ' today';
        if (isSelected) classes += ' selected';
        if (hasNotes) classes += ' has-notes';
        
        html += `<div class="${classes}" onclick="selectDate(new Date(${year}, ${month}, ${day}))">${day}</div>`;
    }
    
    // Next month days
    const totalCells = firstDay + daysInMonth;
    const remainingCells = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
    for (let day = 1; day <= remainingCells; day++) {
        html += `<div class="calendar-day other-month">${day}</div>`;
    }
    
    document.getElementById('calendarGrid').innerHTML = html;
}

function selectDate(date) {
    selectedDate = date;
    renderCalendar();
    displayNotesForDate(date);
}

function displayNotesForDate(date) {
    const dateStr = formatDate(date);
    const notes = calendarNotes[dateStr] || [];
    
    // Update date display
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('notesDateDisplay').textContent = date.toLocaleDateString('en-US', options);
    document.getElementById('addNoteBtn').style.display = 'flex';
    
    // Render notes
    const notesList = document.getElementById('notesList');
    if (notes.length === 0) {
        notesList.innerHTML = '<div class="empty-notes"><i class="bi bi-calendar-plus"></i><p>No notes for this date</p></div>';
        return;
    }
    
    let html = '';
    notes.forEach(note => {
        html += `
            <div class="note-card priority-${note.priority}">
                <div class="note-card-header">
                    <div class="note-title">${escapeHtml(note.title)}</div>
                    <div class="note-actions">
                        <button class="note-action-btn" onclick="editNote(${note.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="note-action-btn delete" onclick="deleteNote(${note.id})" title="Delete"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
                ${note.description ? `<div class="note-description">${escapeHtml(note.description)}</div>` : ''}
                <div class="note-meta">
                    <span class="note-priority-badge ${note.priority}">${note.priority}</span>
                    <span>${formatTime(note.created_at)}</span>
                </div>
            </div>
        `;
    });
    notesList.innerHTML = html;
}

function loadCalendarNotes() {
    const month = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}`;
    fetch(`/api/calendar/notes?month=${month}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                calendarNotes = {};
                data.notes.forEach(note => {
                    if (!calendarNotes[note.note_date]) {
                        calendarNotes[note.note_date] = [];
                    }
                    calendarNotes[note.note_date].push(note);
                });
                renderCalendar();
                if (selectedDate) {
                    displayNotesForDate(selectedDate);
                }
            }
        });
}

function openNoteForm(noteId = null) {
    if (!selectedDate) return;
    
    document.getElementById('noteFormModal').style.display = 'flex';
    document.getElementById('noteDate').value = formatDate(selectedDate);
    
    if (noteId) {
        // Edit mode
        const dateStr = formatDate(selectedDate);
        const note = calendarNotes[dateStr]?.find(n => n.id == noteId);
        if (note) {
            document.getElementById('noteFormTitle').textContent = 'Edit Note';
            document.getElementById('noteId').value = note.id;
            document.getElementById('noteTitle').value = note.title;
            document.getElementById('noteDescription').value = note.description || '';
            document.getElementById('notePriority').value = note.priority;
        }
    } else {
        // Add mode
        document.getElementById('noteFormTitle').textContent = 'Add Note';
        document.getElementById('noteForm').reset();
        document.getElementById('noteId').value = '';
        document.getElementById('noteDate').value = formatDate(selectedDate);
    }
}

function closeNoteForm() {
    document.getElementById('noteFormModal').style.display = 'none';
    document.getElementById('noteForm').reset();
}

function saveNote(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());
    const isEdit = !!data.id;
    
    const url = '/api/calendar/notes';
    const method = isEdit ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            closeNoteForm();
            loadCalendarNotes();
        }
    });
}

function editNote(noteId) {
    openNoteForm(noteId);
}

function deleteNote(noteId) {
    if (!confirm('Are you sure you want to delete this note?')) return;
    
    fetch('/api/calendar/notes', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: noteId })
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            loadCalendarNotes();
        }
    });
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    
    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days < 7) return `${days}d ago`;
    return date.toLocaleDateString();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
</body>
</html>
