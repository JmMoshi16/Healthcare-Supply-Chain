<?php use App\Core\View; View::layout('app'); $title = 'Dashboard'; ?>
<?php
$totalMeds     = (int)($stats['total_medicines']    ?? 0);
$activeMeds    = (int)($stats['active_medicines']   ?? 0);
$inactiveMeds  = (int)($stats['inactive_medicines'] ?? 0);
$totalBatch    = (int)($stats['total_batches']      ?? 0);
$activeBatch   = (int)($stats['active_batches']     ?? 0);
$expiredBatch  = (int)($stats['expired_batches']    ?? 0);
$expiringItems = $stats['expiring_soon']            ?? [];
$expiringCount = (int)($stats['expiring_count']     ?? 0);
$lowStockItems = $stats['low_stock']                ?? [];
$lowStockCount = (int)($stats['low_stock_count']    ?? 0);
$recentTx      = $stats['recent_transactions']      ?? [];
$todayTx       = (int)($stats['today_transactions'] ?? 0);
$stockValue    = (float)($stats['total_stock_value']?? 0);
$calendarExp   = $stats['calendar_expirations']     ?? [];
$userName      = auth()['fullname'] ?? 'User';
$userRole      = auth()['role']     ?? 'staff';
$hour          = (int)date('H');
$greeting      = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
$divMeds       = $totalMeds > 0 ? $totalMeds : 1;
$divBatch      = $totalBatch > 0 ? $totalBatch : 1;
?>
<link rel="stylesheet" href="/assets/css/modern-dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Page Title -->
<div class="hcd-page-title">Dashboard</div>

<!-- ═══ HERO ═══ -->
<div class="hcd-hero">
    <div class="hcd-hero-left">
        <div class="hcd-hero-greeting"><?= $greeting ?></div>
        <h1 class="hcd-hero-name"><?= esc(explode(' ',$userName)[0]) ?></h1>
        <div class="hcd-hero-role">
            <i class="bi bi-shield-check"></i>
            <span><?= esc(ucfirst($userRole)) ?></span>
            <span class="hcd-hero-divider">•</span>
            <span><?= date('l, M j, Y') ?></span>
        </div>
    </div>
    <div class="hcd-hero-stats">
        <div class="hcd-hero-stat">
            <div class="hcd-hero-stat-icon" style="background:#e0f2fe;color:#0284c7;">
                <i class="bi bi-capsule"></i>
            </div>
            <div class="hcd-hero-stat-content">
                <div class="hcd-hero-stat-value"><?= $activeMeds ?></div>
                <div class="hcd-hero-stat-label">Active Medicines</div>
            </div>
        </div>
        <div class="hcd-hero-stat">
            <div class="hcd-hero-stat-icon" style="background:#fef3c7;color:#d97706;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="hcd-hero-stat-content">
                <div class="hcd-hero-stat-value"><?= $expiringCount ?></div>
                <div class="hcd-hero-stat-label">Expiring Soon</div>
            </div>
        </div>
        <div class="hcd-hero-stat">
            <div class="hcd-hero-stat-icon" style="background:#fee2e2;color:#dc2626;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="hcd-hero-stat-content">
                <div class="hcd-hero-stat-value"><?= $lowStockCount ?></div>
                <div class="hcd-hero-stat-label">Low Stock</div>
            </div>
        </div>
    </div>
    <div class="hcd-hero-actions">
        <?php if (can('medicines.*')): ?>
        <a href="/medicines/create" class="hcd-hero-btn hcd-hero-btn-primary">
            <i class="bi bi-plus-circle"></i>
            <span>Add Medicine</span>
        </a>
        <?php endif; ?>
        <a href="/batches" class="hcd-hero-btn hcd-hero-btn-secondary">
            <i class="bi bi-box-seam"></i>
            <span>Manage Batches</span>
        </a>
    </div>
</div>

<!-- ═══ MAIN GRID ═══ -->
<div class="hcd-grid">

    <!-- ── LEFT COLUMN ── -->
    <div class="hcd-left">

        <!-- KPI Cards -->
        <div class="hcd-stat-row">
            <div class="hcd-kpi" onclick="location.href='/medicines'" style="cursor:pointer;">
                <div class="hcd-kpi-icon hcd-kpi-icon-blue"><i class="bi bi-capsule"></i></div>
                <div class="hcd-kpi-label">Medicines Registered</div>
                <div class="hcd-kpi-value"><?= number_format($totalMeds) ?></div>
                <div class="hcd-kpi-footer"><?= $activeMeds ?> active medicines <i class="bi bi-arrow-right"></i></div>
            </div>
            <div class="hcd-kpi" onclick="location.href='/batches'" style="cursor:pointer;">
                <div class="hcd-kpi-icon hcd-kpi-icon-green"><i class="bi bi-box-seam-fill"></i></div>
                <div class="hcd-kpi-label">Total Batches</div>
                <div class="hcd-kpi-value"><?= number_format($totalBatch) ?></div>
                <div class="hcd-kpi-footer"><?= $activeBatch ?> active batches <i class="bi bi-arrow-right"></i></div>
            </div>
            <div class="hcd-kpi" onclick="<?= $expiringCount > 0 ? 'showExpiringModal()' : '' ?>" style="<?= $expiringCount > 0 ? 'cursor:pointer' : '' ?>">
                <div class="hcd-kpi-icon hcd-kpi-icon-orange"><i class="bi bi-clock-history"></i></div>
                <div class="hcd-kpi-label">Expiring Soon</div>
                <div class="hcd-kpi-value"><?= str_pad($expiringCount,2,'0',STR_PAD_LEFT) ?></div>
                <div class="hcd-kpi-footer">Medicines within 30 days <?= $expiringCount > 0 ? '<i class="bi bi-arrow-right"></i>' : '' ?></div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="hcd-chart-row">

            <!-- Donut Chart -->
            <div class="hcd-card">
                <div class="hcd-card-hdr">
                    <span class="hcd-card-title">Stock Breakdown</span>
                </div>
                <?php
                $circ   = 439.82; // 2*pi*70
                $actArc = $totalMeds > 0 ? ($activeMeds  / $divMeds) * $circ : 0;
                $inArc  = $totalMeds > 0 ? ($inactiveMeds/ $divMeds) * $circ : 0;
                $expArc = $totalMeds > 0 ? ($expiringCount/ $divMeds) * $circ : 0;
                $lowArc = $totalMeds > 0 ? ($lowStockCount/ $divMeds) * $circ : 0;
                ?>
                <div class="hcd-donut-wrap">
                    <svg class="hcd-donut-svg" viewBox="0 0 160 160">
                        <circle cx="80" cy="80" r="70" fill="none" stroke="#e5e7eb" stroke-width="22"/>
                        <circle cx="80" cy="80" r="70" fill="none" stroke="#0f4c81" stroke-width="22"
                            stroke-dasharray="<?= round($actArc,2) ?> <?= $circ ?>" stroke-dashoffset="0"/>
                        <circle cx="80" cy="80" r="70" fill="none" stroke="#8b5cf6" stroke-width="22"
                            stroke-dasharray="<?= round($inArc,2) ?> <?= $circ ?>"
                            stroke-dashoffset="-<?= round($actArc,2) ?>"/>
                        <circle cx="80" cy="80" r="70" fill="none" stroke="#f59e0b" stroke-width="22"
                            stroke-dasharray="<?= round($expArc,2) ?> <?= $circ ?>"
                            stroke-dashoffset="-<?= round($actArc+$inArc,2) ?>"/>
                    </svg>
                    <div class="hcd-donut-ctr">
                        <div class="hcd-donut-num"><?= $totalMeds ?></div>
                        <div class="hcd-donut-lbl">Medicines</div>
                    </div>
                </div>
                <div class="hcd-legend">
                    <div class="hcd-legend-row"><span class="hcd-legend-dot" style="background:#0f4c81"></span><span>Active</span><strong><?= $totalMeds > 0 ? round($activeMeds/$divMeds*100) : 0 ?>%</strong></div>
                    <div class="hcd-legend-row"><span class="hcd-legend-dot" style="background:#8b5cf6"></span><span>Inactive</span><strong><?= $totalMeds > 0 ? round($inactiveMeds/$divMeds*100) : 0 ?>%</strong></div>
                    <div class="hcd-legend-row"><span class="hcd-legend-dot" style="background:#f59e0b"></span><span>Expiring</span><strong><?= $totalMeds > 0 ? round($expiringCount/$divMeds*100) : 0 ?>%</strong></div>
                    <div class="hcd-legend-row"><span class="hcd-legend-dot" style="background:#ef4444"></span><span>Low Stock</span><strong><?= $totalMeds > 0 ? round($lowStockCount/$divMeds*100) : 0 ?>%</strong></div>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="hcd-card">
                <div class="hcd-card-hdr">
                    <span class="hcd-card-title">Stock Overview</span>
                    <div class="hcd-tabs">
                        <button class="hcd-tab active" onclick="switchBarChart(this,'weekly')">Weekly</button>
                        <button class="hcd-tab"        onclick="switchBarChart(this,'monthly')">Monthly</button>
                    </div>
                </div>
                <?php
                $days   = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                $wVals  = [45,62,38,71,55,48,66];
                $maxW   = max($wVals);
                ?>
                <div class="hcd-bars" id="barChartWrap">
                    <?php foreach($days as $i => $d): ?>
                    <div class="hcd-bar-col">
                        <div class="hcd-bar-inner">
                            <div class="hcd-bar-fill <?= $i===3?'':'dim' ?>" style="height:<?= round($wVals[$i]/$maxW*100) ?>%"></div>
                        </div>
                        <span class="hcd-bar-lbl"><?= $d ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="hcd-chart-meta">
                    <span>Today's transactions: <strong><?= $todayTx ?></strong></span>
                    <span>Stock value: <strong>₱<?= number_format($stockValue,0) ?></strong></span>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="hcd-bottom-row">

            <!-- Line Chart -->
            <div class="hcd-card">
                <div class="hcd-card-hdr">
                    <span class="hcd-card-title">Stock Value Overview</span>
                    <span style="font-size:.75rem;color:var(--text-muted);">Last 6 Months</span>
                </div>
                <div class="hcd-rev-meta">
                    <div><div class="hcd-rev-lbl">Total Value</div><div class="hcd-rev-val">₱<?= number_format($stockValue,2) ?></div></div>
                    <div><div class="hcd-rev-lbl">Active Batches</div><div class="hcd-rev-val"><?= $activeBatch ?></div></div>
                </div>
                <div class="hcd-line-wrap"><canvas id="stockLineChart"></canvas></div>
            </div>

            <!-- Recent Transactions -->
            <div class="hcd-card">
                <div class="hcd-card-hdr">
                    <span class="hcd-card-title">Recent Transactions</span>
                    <a href="/stocks" class="hcd-card-link">View All <i class="bi bi-arrow-right"></i></a>
                </div>
                <?php if(empty($recentTx)): ?>
                <div class="hcd-empty"><i class="bi bi-inbox"></i><p>No recent transactions</p></div>
                <?php else: ?>
                <table class="hcd-tbl">
                    <thead><tr><th>Medicine</th><th>Type</th><th>Qty</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach(array_slice($recentTx,0,5) as $tx):
                        $typ  = strtolower($tx['transaction_type'] ?? 'in');
                        $bcls = $typ==='in' ? 'hcd-badge-in' : 'hcd-badge-out';
                    ?>
                    <tr>
                        <td>
                            <div class="hcd-tbl-primary"><?= esc($tx['medicine_name'] ?? '—') ?></div>
                            <div class="hcd-tbl-sub"><?= date('M d, Y', strtotime($tx['created_at'])) ?></div>
                        </td>
                        <td><span class="hcd-badge <?= $bcls ?>"><?= ucfirst($typ) ?></span></td>
                        <td><?= number_format((int)($tx['quantity']??0)) ?></td>
                        <td><a href="/stocks" class="hcd-tbl-btn" title="View"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- end .hcd-left -->

    <!-- ── RIGHT COLUMN ── -->
    <div class="hcd-right">

        <!-- Expiring Soon Panel -->
        <div class="hcd-panel">
            <div class="hcd-panel-hdr">
                <span class="hcd-panel-title">Expiring Soon</span>
                <a href="#" class="hcd-panel-link" onclick="showExpiringModal();return false;">View All <i class="bi bi-arrow-right"></i></a>
            </div>
            <!-- Mini Calendar -->
            <div class="hcd-mini-calendar">
                <div class="hcd-mini-cal-header">
                    <button class="hcd-mini-cal-nav" onclick="miniCalChangeMonth(-1)"><i class="bi bi-chevron-left"></i></button>
                    <span class="hcd-mini-cal-month" id="miniCalMonth"><?= date('F Y') ?></span>
                    <button class="hcd-mini-cal-nav" onclick="miniCalChangeMonth(1)"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div class="hcd-mini-cal-grid" id="miniCalGrid">
                    <!-- Generated by JavaScript -->
                </div>
            </div>
            <!-- Items -->
            <div class="hcd-alert-list">
                <?php if(empty($expiringItems)): ?>
                <div class="hcd-empty"><i class="bi bi-check-circle"></i><p>No medicines expiring soon</p></div>
                <?php else:
                    foreach(array_slice($expiringItems,0,4) as $idx => $item):
                        $dl   = max(0, floor((strtotime($item['expiry_date'])-time())/86400));
                        $avcl = $dl<=7?'hcd-av-red':($dl<=15?'hcd-av-orange':'hcd-av-blue');
                        $feat = $idx===1?'featured':'';
                        $let  = strtoupper(substr($item['medicine_name'],0,1));
                ?>
                <div class="hcd-alert-item <?= $feat ?>">
                    <div class="hcd-av <?= $avcl ?>"><?= $let ?></div>
                    <div>
                        <div class="hcd-alert-name"><?= esc($item['medicine_name']) ?></div>
                        <div class="hcd-alert-sub"><i class="bi bi-box-seam"></i><?= esc($item['batch_number']) ?></div>
                        <div class="hcd-alert-time">
                            <i class="bi bi-calendar-x"></i>
                            <strong style="color:#ef4444;"><?= date('M j, Y', strtotime($item['expiry_date'])) ?></strong>
                            <span style="margin:0 4px;color:#cbd5e1;">•</span>
                            <?= $dl ?> day<?= $dl!==1?'s':'' ?> left
                        </div>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- Low Stock Panel -->
        <div class="hcd-panel">
            <div class="hcd-panel-hdr">
                <span class="hcd-panel-title">Low Stock Alert</span>
                <a href="#" class="hcd-panel-link" onclick="showLowStockModal();return false;">View All <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="hcd-alert-list">
                <?php if(empty($lowStockItems)): ?>
                <div class="hcd-empty"><i class="bi bi-check-circle"></i><p>All medicines sufficiently stocked</p></div>
                <?php else:
                    foreach(array_slice($lowStockItems,0,5) as $item):
                        $st   = (int)$item['total_stock'];
                        $avcl = $st==0?'hcd-av-red':($st<=5?'hcd-av-orange':'hcd-av-green');
                        $let  = strtoupper(substr($item['name'],0,1));
                ?>
                <div class="hcd-alert-item">
                    <div class="hcd-av <?= $avcl ?>"><?= $let ?></div>
                    <div style="flex:1;min-width:0;">
                        <div class="hcd-alert-name"><?= esc($item['name']) ?></div>
                        <div class="hcd-alert-sub"><i class="bi bi-tag"></i><?= esc($item['category']) ?></div>
                        <div class="hcd-alert-time"><i class="bi bi-archive"></i><?= $st==0?'Out of stock':$st.' units left' ?></div>
                    </div>
                    <?php if(can('batches.*')): ?>
                    <a href="/batches/create" class="hcd-tbl-btn" title="Add Batch" style="flex-shrink:0;"><i class="bi bi-plus"></i></a>
                    <?php endif; ?>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

    </div><!-- end .hcd-right -->
</div><!-- end .hcd-grid -->

<!-- ═══ MODALS ═══ -->
<div class="hcd-modal-overlay" id="expiringModal" style="display:none;">
    <div class="hcd-modal">
        <div class="hcd-modal-hdr">
            <h3><i class="bi bi-clock-history" style="color:#d97706;margin-right:.5rem;"></i>Expiring Medicines (30 Days)</h3>
            <button onclick="closeModal('expiringModal')" class="hcd-modal-close"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="hcd-modal-body">
            <?php if(empty($expiringItems)): ?>
            <div class="hcd-empty"><i class="bi bi-check-circle"></i><p>No medicines expiring soon</p></div>
            <?php else: ?>
            <table class="hcd-tbl">
                <thead><tr><th>Medicine</th><th>Batch</th><th>Expiry Date</th><th>Stock</th><th>Days Left</th></tr></thead>
                <tbody>
                <?php foreach($expiringItems as $item):
                    $dl=$max=max(0,floor((strtotime($item['expiry_date'])-time())/86400));
                    $ug=$dl<=7?'hcd-badge-out':($dl<=15?'hcd-badge-warn':'');
                ?>
                <tr>
                    <td class="hcd-tbl-primary"><?= esc($item['medicine_name']) ?></td>
                    <td><?= esc($item['batch_number']) ?></td>
                    <td><?= date('M d, Y',strtotime($item['expiry_date'])) ?></td>
                    <td><?= number_format((int)$item['current_quantity']) ?></td>
                    <td><span class="hcd-badge <?= $ug ?>" style="<?= !$ug?'background:#f1f5f9;color:#64748b;':'' ?>"><?= $dl ?> days</span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="hcd-modal-overlay" id="lowStockModal" style="display:none;">
    <div class="hcd-modal">
        <div class="hcd-modal-hdr">
            <h3><i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;margin-right:.5rem;"></i>Low Stock Medicines</h3>
            <button onclick="closeModal('lowStockModal')" class="hcd-modal-close"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="hcd-modal-body">
            <?php if(empty($lowStockItems)): ?>
            <div class="hcd-empty"><i class="bi bi-check-circle"></i><p>All medicines have sufficient stock</p></div>
            <?php else: ?>
            <table class="hcd-tbl">
                <thead><tr><th>Medicine</th><th>Category</th><th>Stock</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach($lowStockItems as $item):
                    $st=(int)$item['total_stock'];
                    $ug=$st==0?'hcd-badge-out':'hcd-badge-warn';
                ?>
                <tr>
                    <td class="hcd-tbl-primary"><?= esc($item['name']) ?></td>
                    <td><?= esc($item['category']) ?></td>
                    <td><?= number_format($st) ?></td>
                    <td><span class="hcd-badge <?= $ug ?>"><?= $st==0?'Out of stock':'Low stock' ?></span></td>
                    <td><a href="/batches/create" class="badge badge-primary" style="text-decoration:none;">+ Add Batch</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Modals
function showExpiringModal(){ document.getElementById('expiringModal').style.display='flex'; }
function showLowStockModal(){ document.getElementById('lowStockModal').style.display='flex'; }
function closeModal(id){ document.getElementById(id).style.display='none'; }
document.querySelectorAll('.hcd-modal-overlay').forEach(o=>o.addEventListener('click',e=>{if(e.target===o)o.style.display='none';}));

// Mini Calendar - Server-side rendering with PHP data
let miniCalDate = new Date();
let miniCalExpirations = <?= json_encode($calendarExp) ?>;

console.log('Calendar expirations loaded from PHP:', miniCalExpirations);

function renderMiniCalendar() {
    const year = miniCalDate.getFullYear();
    const month = miniCalDate.getMonth();
    const today = new Date();
    
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('miniCalMonth').textContent = `${monthNames[month]} ${year}`;
    
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    
    let html = '';
    const dayHeaders = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
    dayHeaders.forEach(day => {
        html += `<div class="hcd-mini-cal-day-header">${day}</div>`;
    });
    
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        html += `<div class="hcd-mini-cal-day other-month">${day}</div>`;
    }
    
    console.log('Rendering calendar for:', year, month + 1);
    console.log('Expirations data:', miniCalExpirations);
    
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const isToday = date.toDateString() === today.toDateString();
        const hasExpiry = miniCalExpirations[dateStr] && miniCalExpirations[dateStr].length > 0;
        
        console.log(`Day ${day} (${dateStr}): hasExpiry=${hasExpiry}`);
        
        let classes = 'hcd-mini-cal-day';
        if (isToday) classes += ' today';
        if (hasExpiry) classes += ' expiring';
        
        const title = hasExpiry ? `${miniCalExpirations[dateStr].length} medicine(s) expiring` : '';
        html += `<div class="${classes}" onclick="showExpiryDetails('${dateStr}')" onmouseenter="showExpiryTooltip(event, '${dateStr}')" onmouseleave="hideExpiryTooltip()" title="${title}">${day}</div>`;
    }
    
    const totalCells = firstDay + daysInMonth;
    const remainingCells = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
    for (let day = 1; day <= remainingCells; day++) {
        html += `<div class="hcd-mini-cal-day other-month">${day}</div>`;
    }
    
    document.getElementById('miniCalGrid').innerHTML = html;
}

function miniCalChangeMonth(offset) {
    miniCalDate.setMonth(miniCalDate.getMonth() + offset);
    renderMiniCalendar();
    loadExpiringMedicines();
}

function loadExpiringMedicines() {
    const month = `${miniCalDate.getFullYear()}-${String(miniCalDate.getMonth() + 1).padStart(2, '0')}`;
    console.log('=== LOADING EXPIRATIONS FOR:', month, '===');
    
    fetch(`/api/calendar/expiring?month=${month}`)
        .then(r => {
            console.log('Response status:', r.status);
            return r.json();
        })
        .then(data => {
            console.log('API Response:', data);
            if (data.success) {
                miniCalExpirations = data.expirations;
                console.log('Stored expirations:', miniCalExpirations);
                console.log('Number of dates with expirations:', Object.keys(miniCalExpirations).length);
                renderMiniCalendar();
            } else {
                console.error('API returned success=false');
            }
        })
        .catch(err => {
            console.error('Failed to load expiring medicines:', err);
        });
}

let expiryTooltip = null;

function showExpiryTooltip(event, dateStr) {
    const medicines = miniCalExpirations[dateStr];
    if (!medicines || medicines.length === 0) return;
    
    hideExpiryTooltip();
    
    const tooltip = document.createElement('div');
    tooltip.className = 'expiry-tooltip show';
    tooltip.id = 'expiryTooltip';
    
    const date = new Date(dateStr);
    const dateFormatted = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    
    let html = `<div class="expiry-tooltip-header"><i class="bi bi-calendar-x"></i> Expiring on ${dateFormatted}</div>`;
    
    medicines.forEach(med => {
        const daysLeft = parseInt(med.days_until_expiry);
        const urgency = daysLeft <= 7 ? 'critical' : 'warning';
        html += `
            <div class="expiry-tooltip-item">
                <div class="expiry-tooltip-icon"><i class="bi bi-capsule"></i></div>
                <div class="expiry-tooltip-content">
                    <div class="expiry-tooltip-name">${escapeHtml(med.medicine_name)}</div>
                    <div class="expiry-tooltip-meta">
                        <span><i class="bi bi-box-seam"></i> ${escapeHtml(med.batch_number)}</span>
                        <span><i class="bi bi-archive"></i> ${med.current_quantity} units</span>
                    </div>
                    <div style="margin-top:4px;">
                        <span class="expiry-tooltip-badge ${urgency}">
                            <i class="bi bi-clock"></i> ${daysLeft} day${daysLeft !== 1 ? 's' : ''} left
                        </span>
                    </div>
                </div>
            </div>
        `;
    });
    
    tooltip.innerHTML = html;
    document.body.appendChild(tooltip);
    
    const rect = event.target.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();
    
    let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
    let top = rect.top - tooltipRect.height - 8;
    
    if (left < 10) left = 10;
    if (left + tooltipRect.width > window.innerWidth - 10) {
        left = window.innerWidth - tooltipRect.width - 10;
    }
    if (top < 10) {
        top = rect.bottom + 8;
    }
    
    tooltip.style.left = left + 'px';
    tooltip.style.top = top + 'px';
    
    expiryTooltip = tooltip;
}

function hideExpiryTooltip() {
    if (expiryTooltip) {
        expiryTooltip.remove();
        expiryTooltip = null;
    }
}

function showExpiryDetails(dateStr) {
    const medicines = miniCalExpirations[dateStr];
    if (!medicines || medicines.length === 0) return;
    
    const date = new Date(dateStr);
    const dateFormatted = date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
    
    let html = `
        <div style="margin-bottom:1rem;">
            <h4 style="font-size:1.1rem;font-weight:700;color:#0f172a;margin-bottom:.5rem;">
                <i class="bi bi-calendar-x" style="color:#ef4444;margin-right:.5rem;"></i>
                Medicines Expiring on ${dateFormatted}
            </h4>
            <p style="font-size:.85rem;color:#64748b;">Total: ${medicines.length} medicine${medicines.length !== 1 ? 's' : ''}</p>
        </div>
        <table class="hcd-tbl">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Batch</th>
                    <th>Quantity</th>
                    <th>Days Left</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    medicines.forEach(med => {
        const daysLeft = parseInt(med.days_until_expiry);
        const urgency = daysLeft <= 7 ? 'hcd-badge-out' : 'hcd-badge-warn';
        html += `
            <tr>
                <td>
                    <div class="hcd-tbl-primary">${escapeHtml(med.medicine_name)}</div>
                    <div class="hcd-tbl-sub">${escapeHtml(med.category)}</div>
                </td>
                <td>${escapeHtml(med.batch_number)}</td>
                <td>${med.current_quantity} units</td>
                <td><span class="hcd-badge ${urgency}">${daysLeft} day${daysLeft !== 1 ? 's' : ''}</span></td>
                <td><a href="/medicines/${med.medicine_id || '#'}" class="hcd-tbl-btn" title="View"><i class="bi bi-eye"></i></a></td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    
    const modal = document.getElementById('expiringModal');
    if (modal) {
        modal.querySelector('.hcd-modal-body').innerHTML = html;
        modal.style.display = 'flex';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('miniCalGrid')) {
        // Data already loaded from PHP, just render
        renderMiniCalendar();
        
        // Auto-navigate to first month with expirations
        if (Object.keys(miniCalExpirations).length > 0) {
            const firstDate = Object.keys(miniCalExpirations)[0];
            const parts = firstDate.split('-');
            miniCalDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, 1);
            renderMiniCalendar();
        }
    }
});

// Bar chart tab
const wData=['Mon','Tue','Wed','Thu','Fri','Sat','Sun'].map((l,i)=>({l,v:[45,62,38,71,55,48,66][i]}));
const mData=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'].map((l,i)=>({l,v:[310,420,380,510,460,490,430,380,510,440,360,410][i]}));
function switchBarChart(btn, mode){
    document.querySelectorAll('.hcd-tab').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    const data  = mode==='weekly' ? wData : mData;
    const maxV  = Math.max(...data.map(d=>d.v));
    const today = new Date().getDay();
    const wrap  = document.getElementById('barChartWrap');
    wrap.innerHTML = data.map((d,i)=>
        `<div class="hcd-bar-col">
            <div class="hcd-bar-inner">
                <div class="hcd-bar-fill ${(mode==='weekly'&&i===today-1)||i===3?'':'dim'}" style="height:${Math.round(d.v/maxV*100)}%"></div>
            </div>
            <span class="hcd-bar-lbl">${d.l}</span>
        </div>`
    ).join('');
}

// Line chart
document.addEventListener('DOMContentLoaded',function(){
    const ctx=document.getElementById('stockLineChart');
    if(!ctx)return;
    const dark=document.documentElement.getAttribute('data-theme')==='dark';
    const gc=dark?'rgba(255,255,255,.06)':'rgba(0,0,0,.05)';
    const tc=dark?'#4d7a9e':'#94a3b8';
    new Chart(ctx,{
        type:'line',
        data:{
            labels:['Jan','Feb','Mar','Apr','May','Jun'],
            datasets:[{
                label:'Stock Value (₱)',
                data:[42000,55000,48000,61000,58000,<?= round($stockValue) ?>],
                borderColor:'#0f4c81',
                backgroundColor:'rgba(15,76,129,.1)',
                fill:true, tension:.45,
                pointRadius:4, pointBackgroundColor:'#0f4c81',
                pointBorderColor:'#fff', pointBorderWidth:2,
            },{
                label:'Batches',
                data:[8,12,9,14,11,<?= $activeBatch ?>],
                borderColor:'#8b5cf6',
                backgroundColor:'rgba(139,92,246,.06)',
                fill:true, tension:.45,
                pointRadius:4, pointBackgroundColor:'#8b5cf6',
                pointBorderColor:'#fff', pointBorderWidth:2,
                yAxisID:'y2',
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            interaction:{mode:'index',intersect:false},
            plugins:{
                legend:{display:false},
                tooltip:{backgroundColor:'#0f1117',titleColor:'#fff',bodyColor:'rgba(255,255,255,.75)',padding:12,cornerRadius:10}
            },
            scales:{
                x:{grid:{color:gc},ticks:{color:tc,font:{size:11}}},
                y:{grid:{color:gc},ticks:{color:tc,font:{size:11},callback:v=>'₱'+v.toLocaleString()}},
                y2:{display:false}
            }
        }
    });
});
</script>
