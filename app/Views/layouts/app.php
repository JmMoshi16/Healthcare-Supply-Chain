<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Healthcare Supply Chain') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/premium.css">
    <link rel="stylesheet" href="/assets/css/topbar-fix.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/notifications.css">
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
    ?>

    <!-- SIDEBAR SHELL -->
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
                <button class="sb-rail-icon <?= $isDash  ? 'active' : '' ?>" onclick="location.href='/dashboard'" title="Dashboard"><i class="bi bi-squares-fill"></i></button>
                <button class="sb-rail-icon <?= $isMed   ? 'active' : '' ?>" onclick="location.href='/medicines'" title="Medicines"><i class="bi bi-capsule"></i></button>
                <button class="sb-rail-icon <?= $isBatch ? 'active' : '' ?>" onclick="location.href='/batches'" title="Batches"><i class="bi bi-box-seam"></i></button>
                <button class="sb-rail-icon <?= $isStock ? 'active' : '' ?>" onclick="location.href='/stocks'" title="Stock Transactions"><i class="bi bi-arrow-left-right"></i></button>
                <?php if (has_role('superadmin')): ?>
                <div class="sb-rail-sep"></div>
                <button class="sb-rail-icon <?= $isUsers ? 'active' : '' ?>" onclick="location.href='/users'" title="Users"><i class="bi bi-people"></i></button>
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
                <a href="/dashboard" class="sb-item <?= $isDash  ? 'active' : '' ?>"><i class="bi bi-squares<?= $isDash ? '-fill' : '' ?>"></i><span>Dashboard</span></a>
                <a href="/medicines" class="sb-item <?= $isMed   ? 'active' : '' ?>"><i class="bi bi-capsule"></i><span>Medicines</span></a>
                <a href="/batches"   class="sb-item <?= $isBatch ? 'active' : '' ?>"><i class="bi bi-box-seam"></i><span>Batches</span></a>
                <a href="/stocks"    class="sb-item <?= $isStock ? 'active' : '' ?>"><i class="bi bi-arrow-left-right"></i><span>Stock Transactions</span></a>
                <?php if (has_role('superadmin')): ?>
                <div class="sb-rail-sep" style="width:100%;margin:0.4rem 0;"></div>
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

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper">

        <!-- TOPBAR -->
        <header class="topbar" id="mainTopbar">
            <div class="topbar-left">
                <div class="topbar-brand-pill" style="background:linear-gradient(135deg,#0f4c81,#0ea5e9);padding:0.32rem 0.85rem;border-radius:99px;display:flex;align-items:center;gap:0.4rem;box-shadow:0 2px 8px rgba(14,165,233,0.25);">
                    <span style="font-size:0.78rem;font-weight:800;color:#fff;letter-spacing:-0.01em;">HealthChain</span>
                    <span style="font-size:0.72rem;color:rgba(255,255,255,0.4);font-weight:300;">/</span>
                    <span style="font-size:0.72rem;font-weight:600;color:#bfdbfe;letter-spacing:0.02em;"><?= esc(ucfirst($role)) ?></span>
                </div>
            </div>
            
            <div class="topbar-center">
                <!-- Global Search Bar -->
                <div class="topbar-search-wrap" id="globalSearch">
                    <i class="bi bi-search topbar-search-icon"></i>
                    <input 
                        type="text" 
                        class="topbar-search-input" 
                        placeholder="Search medicines, batches..."
                        id="globalSearchInput"
                        autocomplete="off"
                    >
                    <kbd class="topbar-search-kbd">⌘K</kbd>
                </div>
            </div>
            
            <div class="topbar-right">
                <div class="topbar-date">
                    <i class="bi bi-calendar3"></i>
                    <span><?= date('M d, Y') ?></span>
                </div>
                
                <div class="topbar-divider"></div>
                
                <!-- Notification Bell -->
                <button class="topbar-icon-btn" onclick="toggleNotifications()" title="Notifications" id="notifBellBtn">
                    <i class="bi bi-bell"></i>
                    <span class="topbar-notif-badge" id="notifBadge" style="display:none;">0</span>
                </button>
                
                <div class="topbar-divider"></div>
                
                <!-- User Dropdown -->
                <div class="topbar-user-wrap" id="topbarDropdownWrap">
                    <button class="topbar-user-btn" id="topbarUserBtn" onclick="toggleTopbarDropdown()">
                        <div class="topbar-avatar-ring">
                            <div class="topbar-avatar" style="background:linear-gradient(135deg,#0f4c81,#0ea5e9);"><?= strtoupper(substr(auth()['fullname'] ?? 'U', 0, 1)) ?></div>
                            <div class="topbar-online-dot"></div>
                        </div>
                        <div class="topbar-user-text">
                            <span class="topbar-user-name"><?= esc(explode(' ', auth()['fullname'] ?? 'User')[0]) ?></span>
                            <span class="topbar-user-role"><?= esc(ucfirst($role)) ?></span>
                        </div>
                        <i class="bi bi-chevron-down topbar-chevron" id="topbarChevron"></i>
                    </button>
                    <div id="topbarDropdown" class="topbar-dropdown">
                        <div class="topbar-dd-header">
                            <div class="topbar-dd-avatar" style="background:linear-gradient(135deg,#0f4c81,#0ea5e9);width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.88rem;color:#fff;flex-shrink:0;"><?= strtoupper(substr(auth()['fullname'] ?? 'U', 0, 1)) ?></div>
                            <div>
                                <div class="topbar-dd-name"><?= esc(auth()['fullname'] ?? 'User') ?></div>
                                <div class="topbar-dd-role"><?= esc(ucfirst($role)) ?></div>
                            </div>
                        </div>
                        <div class="topbar-dd-menu">
                            <a href="/logout" class="topbar-dd-item topbar-dd-danger">
                                <span class="topbar-dd-item-icon"><i class="bi bi-box-arrow-right"></i></span>
                                <span>Sign Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Global Search Results Dropdown -->
        <div class="search-results-overlay" id="searchResultsOverlay" style="display:none;">
            <div class="search-results-panel" id="searchResultsPanel">
                <div class="search-results-header">
                    <span class="search-results-title">Search Results</span>
                    <button class="search-results-close" onclick="closeSearchResults()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="search-results-body" id="searchResultsBody">
                    <div class="search-results-empty">
                        <i class="bi bi-search"></i>
                        <p>Start typing to search...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Panel -->
        <div class="notif-overlay" id="notifOverlay" onclick="closeNotifications()"></div>
        <div class="notif-panel" id="notifPanel">
            <div class="notif-header">
                <div class="notif-header-left">
                    <span class="notif-title">Notifications</span>
                    <span class="notif-count" id="notifCount">0</span>
                </div>
                <div class="notif-actions">
                    <button class="notif-action-btn" onclick="markAllAsRead()">
                        <i class="bi bi-check-all"></i> Mark all read
                    </button>
                    <button class="notif-close-btn" onclick="closeNotifications()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
            <div class="notif-body" id="notifBody">
                <div class="notif-loading">
                    <div class="notif-spinner"></div>
                    <p>Loading notifications...</p>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <main class="content-wrapper">
            <?php if ($success = flash('success')): ?>
                <div class="alert d-flex align-items-center mb-4" style="background:#ecfdf5;color:#15803d;border:1px solid #bbf7d0;padding:0.85rem 1.1rem;border-radius:10px;margin-bottom:1.25rem;">
                    <i class="bi bi-check-circle-fill me-2" style="margin-right:0.5rem;"></i><?= esc($success) ?>
                    <button onclick="this.closest('.alert').remove()" style="margin-left:auto;background:none;border:none;font-size:1rem;cursor:pointer;color:#15803d;">×</button>
                </div>
            <?php endif; ?>
            <?php if ($error = flash('error')): ?>
                <div class="alert d-flex align-items-center mb-4" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:0.85rem 1.1rem;border-radius:10px;margin-bottom:1.25rem;">
                    <i class="bi bi-exclamation-circle-fill" style="margin-right:0.5rem;"></i><?= esc($error) ?>
                    <button onclick="this.closest('.alert').remove()" style="margin-left:auto;background:none;border:none;font-size:1rem;cursor:pointer;color:#dc2626;">×</button>
                </div>
            <?php endif; ?>
            <?= $content ?>
        </main>
    </div>
</div>

<script>
(function(){ if(localStorage.getItem('sbDark')==='true') document.documentElement.setAttribute('data-theme','dark'); })();
function sbToggleDark(){ const d=document.documentElement.getAttribute('data-theme')==='dark'; document.documentElement.setAttribute('data-theme',d?'light':'dark'); localStorage.setItem('sbDark',String(!d)); }
function sbTogglePin(){ const s=document.getElementById('sbShell'),p=document.getElementById('sbPinBtn'),pi=document.getElementById('sbPinIcon'),sl=document.getElementById('sbSwitchLabel'),si=document.getElementById('sbSwitchIcon'),pinned=s.classList.toggle('pinned'); p.classList.toggle('pinned-active',pinned); pi.className=pinned?'bi bi-layout-sidebar-reverse':'bi bi-layout-sidebar'; si.className=pinned?'bi bi-pin-angle-fill':'bi bi-pin-angle'; sl.textContent=pinned?'Unpin sidebar':'Pin sidebar'; localStorage.setItem('sbPinned',pinned); }
(function(){ if(localStorage.getItem('sbPinned')==='true'){ const s=document.getElementById('sbShell'); if(!s)return; s.classList.add('pinned'); const p=document.getElementById('sbPinBtn'),pi=document.getElementById('sbPinIcon'),sl=document.getElementById('sbSwitchLabel'),si=document.getElementById('sbSwitchIcon'); if(p)p.classList.add('pinned-active'); if(pi)pi.className='bi bi-layout-sidebar-reverse'; if(si)si.className='bi bi-pin-angle-fill'; if(sl)sl.textContent='Unpin sidebar'; } })();
function toggleTopbarDropdown(){ const dd=document.getElementById('topbarDropdown'),ch=document.getElementById('topbarChevron'),o=dd.classList.contains('open'); dd.classList.toggle('open',!o); ch.classList.toggle('open',!o); }
document.addEventListener('click',function(e){ const w=document.getElementById('topbarDropdownWrap'); if(w&&!w.contains(e.target)){ document.getElementById('topbarDropdown').classList.remove('open'); document.getElementById('topbarChevron').classList.remove('open'); } });

// Global Search Functionality
let searchTimeout;
const searchInput = document.getElementById('globalSearchInput');
const searchOverlay = document.getElementById('searchResultsOverlay');
const searchBody = document.getElementById('searchResultsBody');

if(searchInput) {
    document.addEventListener('keydown', function(e) {
        if((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
        if(e.key === 'Escape') {
            closeSearchResults();
        }
    });
    
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        clearTimeout(searchTimeout);
        
        if(query.length >= 2) {
            searchTimeout = setTimeout(() => performSearch(query), 300);
        } else {
            closeSearchResults();
        }
    });
    
    searchInput.addEventListener('focus', function() {
        if(searchInput.value.trim().length >= 2) {
            searchOverlay.style.display = 'block';
        }
    });
}

function performSearch(query) {
    searchOverlay.style.display = 'block';
    searchBody.innerHTML = '<div class="search-loading"><div class="spinner"></div><p>Searching...</p></div>';
    
    fetch(`/api/search?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => displaySearchResults(data))
        .catch(err => {
            searchBody.innerHTML = '<div class="search-results-empty"><i class="bi bi-exclamation-circle"></i><p>Search failed. Please try again.</p></div>';
        });
}

function displaySearchResults(data) {
    if(!data.medicines?.length && !data.batches?.length && !data.stocks?.length) {
        searchBody.innerHTML = '<div class="search-results-empty"><i class="bi bi-inbox"></i><p>No results found</p></div>';
        return;
    }
    
    let html = '';
    
    if(data.medicines?.length) {
        html += '<div class="search-section"><div class="search-section-title"><i class="bi bi-capsule"></i>Medicines</div>';
        data.medicines.forEach(item => {
            html += `<a href="/medicines/${item.id}" class="search-result-item">
                <div class="search-item-icon"><i class="bi bi-capsule"></i></div>
                <div class="search-item-content">
                    <div class="search-item-title">${item.name}</div>
                    <div class="search-item-meta">${item.category} • ${item.unit}</div>
                </div>
                <i class="bi bi-arrow-right search-item-arrow"></i>
            </a>`;
        });
        html += '</div>';
    }
    
    if(data.batches?.length) {
        html += '<div class="search-section"><div class="search-section-title"><i class="bi bi-box-seam"></i>Batches</div>';
        data.batches.forEach(item => {
            html += `<a href="/batches/${item.id}" class="search-result-item">
                <div class="search-item-icon"><i class="bi bi-box-seam"></i></div>
                <div class="search-item-content">
                    <div class="search-item-title">${item.batch_number}</div>
                    <div class="search-item-meta">Qty: ${item.current_quantity} • Expires: ${item.expiry_date}</div>
                </div>
                <i class="bi bi-arrow-right search-item-arrow"></i>
            </a>`;
        });
        html += '</div>';
    }
    
    searchBody.innerHTML = html;
}

function closeSearchResults() {
    searchOverlay.style.display = 'none';
}

// ============================================
// NOTIFICATION CENTER
// ============================================
function toggleNotifications() {
    const notifPanel = document.getElementById('notifPanel');
    const notifOverlay = document.getElementById('notifOverlay');
    
    if (!notifPanel || !notifOverlay) return;
    
    const isOpen = notifPanel.classList.contains('active');
    
    if (isOpen) {
        notifPanel.classList.remove('active');
        notifOverlay.classList.remove('active');
    } else {
        notifPanel.classList.add('active');
        notifOverlay.classList.add('active');
        loadNotifications();
    }
}

function closeNotifications() {
    const notifPanel = document.getElementById('notifPanel');
    const notifOverlay = document.getElementById('notifOverlay');
    if (notifPanel) notifPanel.classList.remove('active');
    if (notifOverlay) notifOverlay.classList.remove('active');
}

function loadNotifications() {
    const notifBody = document.getElementById('notifBody');
    if (!notifBody) return;
    
    fetch('/api/notifications')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge(data.unread_count);
                renderNotifications(data.notifications);
            }
        })
        .catch(err => {
            console.error('Failed to load notifications:', err);
            notifBody.innerHTML = '<div class="notif-empty"><i class="bi bi-exclamation-circle"></i><p>Failed to load</p></div>';
        });
}

function updateNotificationBadge(count) {
    const notifBadge = document.getElementById('notifBadge');
    const notifCount = document.getElementById('notifCount');
    
    if (!notifBadge || !notifCount) return;
    
    if (count > 0) {
        notifBadge.textContent = count > 99 ? '99+' : count;
        notifBadge.style.display = 'flex';
        notifCount.textContent = count;
    } else {
        notifBadge.style.display = 'none';
        notifCount.textContent = '0';
    }
}

function renderNotifications(notifications) {
    const notifBody = document.getElementById('notifBody');
    if (!notifBody) return;
    
    if (!notifications || notifications.length === 0) {
        notifBody.innerHTML = '<div class="notif-empty"><i class="bi bi-bell-slash"></i><p>No notifications</p></div>';
        return;
    }
    
    let html = '';
    notifications.forEach(notif => {
        const unreadClass = notif.unread ? 'unread' : '';
        const urgencyClass = notif.urgency || 'low';
        html += `<a href="${notif.link}" class="notif-item ${unreadClass}" onclick="markAsRead('${notif.id}', event)"><div class="notif-icon ${urgencyClass}"><i class="bi bi-${notif.icon}"></i></div><div class="notif-content"><div class="notif-item-title">${notif.title}</div><div class="notif-item-message">${notif.message}</div><div class="notif-item-time">${notif.time}</div></div><i class="bi bi-chevron-right notif-arrow"></i></a>`;
    });
    notifBody.innerHTML = html;
}

function markAsRead(notifId, event) {
    fetch('/api/notifications/mark-read', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('input[name="csrf_token"]')?.value || ''},
        body: JSON.stringify({ id: notifId })
    }).then(() => {
        if (event.currentTarget) event.currentTarget.classList.remove('unread');
        updateNotificationBadge(document.querySelectorAll('.notif-item.unread').length);
    });
}

function markAllAsRead() {
    fetch('/api/notifications/mark-all-read', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('input[name="csrf_token"]')?.value || ''}
    }).then(res => res.json()).then(data => {
        if (data.success) {
            document.querySelectorAll('.notif-item.unread').forEach(item => item.classList.remove('unread'));
            updateNotificationBadge(0);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadNotifications);
} else {
    loadNotifications();
}
setInterval(loadNotifications, 120000);
</script>
</body>
</html>
