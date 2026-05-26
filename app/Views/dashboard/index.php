<?php use App\Core\View; View::layout('app'); $title = 'Dashboard'; ?>
<?php
$allMedicines  = $stats['all_medicines']        ?? [];
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
$weeklyStats   = $stats['weekly_stats']             ?? [];
$monthlyStats  = $stats['monthly_stats']            ?? [];
$userName      = auth()['fullname'] ?? 'User';
$userRole      = auth()['role']     ?? 'staff';
$hour          = (int)date('H');
$greeting      = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
$divMeds       = $totalMeds > 0 ? $totalMeds : 1;
$divBatch      = $totalBatch > 0 ? $totalBatch : 1;
?>
<link rel="stylesheet" href="/assets/css/modern-dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- ═══ HERO ═══ -->
<div class="hcd-hero">
    <div class="hcd-hero-left">
        <div class="hcd-hero-welcome">Welcome back, <?= esc(explode(' ',$userName)[0]) ?>!</div>
        <h1 class="hcd-hero-headline">Here's what's happening<br>in your supply chain today.</h1>
    </div>

    <div class="hcd-hero-stats">
        <div class="hcd-hero-stat" data-stat="medicines">
            <div class="hcd-hero-stat-top">
                <div class="hcd-hero-stat-value" data-target="<?= $activeMeds ?>">0</div>
                <div class="hcd-hero-stat-icon-sm" style="background:rgba(255,255,255,0.15);">
                    <i class="bi bi-capsule-pill"></i>
                </div>
            </div>
            <div class="hcd-hero-stat-label">Active Medicines</div>
            <div class="hcd-hero-stat-trend up"><i class="bi bi-arrow-up"></i> <?= $activeMeds > 0 ? round(($activeMeds/$divMeds)*100) : 0 ?>% of total</div>
        </div>
        <div class="hcd-hero-stat-divider"></div>
        <div class="hcd-hero-stat" data-stat="expiring">
            <div class="hcd-hero-stat-top">
                <div class="hcd-hero-stat-value" data-target="<?= $expiringCount ?>">0</div>
                <div class="hcd-hero-stat-icon-sm" style="background:rgba(255,255,255,0.15);">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <div class="hcd-hero-stat-label">Expiring Soon</div>
            <div class="hcd-hero-stat-trend <?= $expiringCount > 0 ? 'warn' : 'up' ?>">
                <i class="bi bi-<?= $expiringCount > 0 ? 'exclamation-triangle' : 'check-circle' ?>"></i>
                <?= $expiringCount > 0 ? 'Within 30 days' : 'All clear' ?>
            </div>
        </div>
        <div class="hcd-hero-stat-divider"></div>
        <div class="hcd-hero-stat" data-stat="lowstock">
            <div class="hcd-hero-stat-top">
                <div class="hcd-hero-stat-value" data-target="<?= $lowStockCount ?>">0</div>
                <div class="hcd-hero-stat-icon-sm" style="background:rgba(255,255,255,0.15);">
                    <i class="bi bi-exclamation-octagon"></i>
                </div>
            </div>
            <div class="hcd-hero-stat-label">Low Stock</div>
            <div class="hcd-hero-stat-trend <?= $lowStockCount > 0 ? 'warn' : 'up' ?>">
                <i class="bi bi-<?= $lowStockCount > 0 ? 'arrow-down' : 'check-circle' ?>"></i>
                <?= $lowStockCount > 0 ? 'Needs restock' : 'Sufficient' ?>
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

    <div class="hcd-hero-particles">
        <div class="hcd-hero-particle"></div>
        <div class="hcd-hero-particle"></div>
        <div class="hcd-hero-particle"></div>
    </div>
</div>

<!-- ═══ MAIN GRID ═══ -->
<div class="hcd-grid">

    <!-- ── LEFT COLUMN ── -->
    <div class="hcd-left">

        <!-- KPI Cards -->
        <div class="hcd-stat-row">
            <div class="hcd-kpi hcd-kpi-animated" onclick="location.href='/medicines'" style="cursor:pointer;" data-count="<?= $totalMeds ?>">
                <div class="hcd-kpi-icon hcd-kpi-icon-blue"><i class="bi bi-capsule"></i></div>
                <div class="hcd-kpi-label">Medicines Registered</div>
                <div class="hcd-kpi-value" data-target="<?= $totalMeds ?>">0</div>
                <div class="hcd-kpi-footer"><?= $activeMeds ?> active medicines <i class="bi bi-arrow-right"></i></div>
            </div>
            <div class="hcd-kpi hcd-kpi-animated" onclick="location.href='/batches'" style="cursor:pointer;" data-count="<?= $totalBatch ?>">
                <div class="hcd-kpi-icon hcd-kpi-icon-green"><i class="bi bi-box-seam-fill"></i></div>
                <div class="hcd-kpi-label">Total Batches</div>
                <div class="hcd-kpi-value" data-target="<?= $totalBatch ?>">0</div>
                <div class="hcd-kpi-footer"><?= $activeBatch ?> active batches <i class="bi bi-arrow-right"></i></div>
            </div>
            <div class="hcd-kpi hcd-kpi-animated" onclick="<?= $expiringCount > 0 ? 'showExpiringModal()' : '' ?>" style="<?= $expiringCount > 0 ? 'cursor:pointer' : '' ?>" data-count="<?= $expiringCount ?>">
                <div class="hcd-kpi-icon hcd-kpi-icon-orange"><i class="bi bi-clock-history"></i></div>
                <div class="hcd-kpi-label">Expiring Soon</div>
                <div class="hcd-kpi-value" data-target="<?= $expiringCount ?>">0</div>
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
                // For accurate breakdown, we need to categorize medicines into mutually exclusive groups
                // Priority: Expiring > Low Stock > Inactive > Active
                
                $expiringMedicineIds = [];
                foreach ($expiringItems as $item) {
                    if (isset($item['medicine_id'])) {
                        $expiringMedicineIds[] = (int)$item['medicine_id'];
                    }
                }
                $expiringMedicineIds = array_unique($expiringMedicineIds);
                
                $lowStockMedicineIds = [];
                foreach ($lowStockItems as $item) {
                    if (isset($item['id'])) {
                        $lowStockMedicineIds[] = (int)$item['id'];
                    }
                }
                $lowStockMedicineIds = array_unique($lowStockMedicineIds);
                
                // Get inactive medicine IDs
                $inactiveMedicineIds = [];
                foreach ($allMedicines as $med) {
                    if (isset($med['is_active']) && $med['is_active'] == 0) {
                        $inactiveMedicineIds[] = (int)$med['id'];
                    }
                }
                
                // Count medicines in each category (mutually exclusive)
                $expiringOnlyCount = count($expiringMedicineIds);
                $lowStockOnlyCount = count(array_diff($lowStockMedicineIds, $expiringMedicineIds));
                $inactiveOnlyCount = count(array_diff($inactiveMedicineIds, array_merge($expiringMedicineIds, $lowStockMedicineIds)));
                $activeOnlyCount = $totalMeds - $expiringOnlyCount - $lowStockOnlyCount - $inactiveOnlyCount;
                
                // Ensure no negative values
                $activeOnlyCount = max(0, $activeOnlyCount);
                $inactiveOnlyCount = max(0, $inactiveOnlyCount);
                $lowStockOnlyCount = max(0, $lowStockOnlyCount);
                $expiringOnlyCount = max(0, $expiringOnlyCount);
                
                $totalItems = $totalMeds > 0 ? $totalMeds : 1;
                
                // Calculate percentages
                $activePercent = round(($activeOnlyCount / $totalItems) * 100);
                $inactivePercent = round(($inactiveOnlyCount / $totalItems) * 100);
                $expiringPercent = round(($expiringOnlyCount / $totalItems) * 100);
                $lowStockPercent = round(($lowStockOnlyCount / $totalItems) * 100);
                
                // Adjust to ensure total is 100%
                $total = $activePercent + $inactivePercent + $expiringPercent + $lowStockPercent;
                if ($total > 100) {
                    $activePercent -= ($total - 100);
                } elseif ($total < 100 && $activePercent > 0) {
                    $activePercent += (100 - $total);
                }
                
                // Ensure no negative percentages
                $activePercent = max(0, $activePercent);
                $inactivePercent = max(0, $inactivePercent);
                $expiringPercent = max(0, $expiringPercent);
                $lowStockPercent = max(0, $lowStockPercent);
                
                // Calculate arc lengths for SVG
                $circ   = 439.82; // 2*pi*70
                $actArc = ($activePercent / 100) * $circ;
                $inArc  = ($inactivePercent / 100) * $circ;
                $expArc = ($expiringPercent / 100) * $circ;
                $lowArc = ($lowStockPercent / 100) * $circ;
                ?>
                <div class="hcd-donut-wrap">
                    <svg class="hcd-donut-svg" viewBox="0 0 160 160">
                        <circle cx="80" cy="80" r="70" fill="none" stroke="#e5e7eb" stroke-width="22"/>
                        <?php if ($activePercent > 0): ?>
                        <circle cx="80" cy="80" r="70" fill="none" stroke="#0f4c81" stroke-width="22"
                            stroke-dasharray="<?= round($actArc,2) ?> <?= $circ ?>" stroke-dashoffset="0"
                            style="cursor:pointer;transition:all 0.3s;" data-segment="0"/>
                        <?php endif; ?>
                        <?php if ($inactivePercent > 0): ?>
                        <circle cx="80" cy="80" r="70" fill="none" stroke="#8b5cf6" stroke-width="22"
                            stroke-dasharray="<?= round($inArc,2) ?> <?= $circ ?>"
                            stroke-dashoffset="-<?= round($actArc,2) ?>"
                            style="cursor:pointer;transition:all 0.3s;" data-segment="1"/>
                        <?php endif; ?>
                        <?php if ($expiringPercent > 0): ?>
                        <circle cx="80" cy="80" r="70" fill="none" stroke="#f59e0b" stroke-width="22"
                            stroke-dasharray="<?= round($expArc,2) ?> <?= $circ ?>"
                            stroke-dashoffset="-<?= round($actArc+$inArc,2) ?>"
                            style="cursor:pointer;transition:all 0.3s;" data-segment="2"/>
                        <?php endif; ?>
                        <?php if ($lowStockPercent > 0): ?>
                        <circle cx="80" cy="80" r="70" fill="none" stroke="#ef4444" stroke-width="22"
                            stroke-dasharray="<?= round($lowArc,2) ?> <?= $circ ?>"
                            stroke-dashoffset="-<?= round($actArc+$inArc+$expArc,2) ?>"
                            style="cursor:pointer;transition:all 0.3s;" data-segment="3"/>
                        <?php endif; ?>
                    </svg>
                    <div class="hcd-donut-ctr">
                        <div class="hcd-donut-num"><?= $totalMeds ?></div>
                        <div class="hcd-donut-lbl">Medicines</div>
                    </div>
                </div>
                <div class="hcd-legend">
                    <div class="hcd-legend-row" style="cursor:pointer;" data-legend="0"><span class="hcd-legend-dot" style="background:#0f4c81"></span><span>Active</span><strong><?= $activePercent ?>%</strong></div>
                    <div class="hcd-legend-row" style="cursor:pointer;" data-legend="1"><span class="hcd-legend-dot" style="background:#8b5cf6"></span><span>Inactive</span><strong><?= $inactivePercent ?>%</strong></div>
                    <div class="hcd-legend-row" style="cursor:pointer;" data-legend="2"><span class="hcd-legend-dot" style="background:#f59e0b"></span><span>Expiring</span><strong><?= $expiringPercent ?>%</strong></div>
                    <div class="hcd-legend-row" style="cursor:pointer;" data-legend="3"><span class="hcd-legend-dot" style="background:#ef4444"></span><span>Low Stock</span><strong><?= $lowStockPercent ?>%</strong></div>
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
                    <div><div class="hcd-rev-lbl">Total Value</div><div class="hcd-rev-val" id="lineChartValue">₱<?= number_format($stockValue,2) ?></div></div>
                    <div><div class="hcd-rev-lbl">Active Batches</div><div class="hcd-rev-val" id="lineChartBatches"><?= $activeBatch ?></div></div>
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
                <table class="hcd-tbl hcd-tbl-interactive">
                    <thead><tr><th>Medicine</th><th>Type</th><th>Qty</th><th>Action</th></tr></thead>
                    <tbody id="recentTransactionsBody">
                    <?php foreach(array_slice($recentTx,0,5) as $idx => $tx):
                        $typ  = strtolower($tx['transaction_type'] ?? 'in');
                        $bcls = $typ==='in' ? 'hcd-badge-in' : 'hcd-badge-out';
                    ?>
                    <tr style="animation: slideInRow 0.4s cubic-bezier(0.4, 0, 0.2, 1) <?= $idx * 0.08 ?>s both;">
                        <td>
                            <div class="hcd-tbl-primary"><?= esc($tx['medicine_name'] ?? '—') ?></div>
                            <div class="hcd-tbl-sub"><?= date('M d, Y', strtotime($tx['created_at'])) ?></div>
                        </td>
                        <td><span class="hcd-badge <?= $bcls ?>"><?= ucfirst($typ) ?></span></td>
                        <td><span class="hcd-qty-badge"><?= number_format((int)($tx['quantity']??0)) ?></span></td>
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
        <div class="hcd-panel hcd-panel-animated">
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
            <div class="hcd-alert-list" id="expiringAlertList">
                <?php if(empty($expiringItems)): ?>
                <div class="hcd-empty"><i class="bi bi-check-circle"></i><p>No medicines expiring soon</p></div>
                <?php else:
                    foreach(array_slice($expiringItems,0,4) as $idx => $item):
                        $dl   = max(0, floor((strtotime($item['expiry_date'])-time())/86400));
                        $avcl = $dl<=7?'hcd-av-red':($dl<=15?'hcd-av-orange':'hcd-av-blue');
                        $feat = $idx===1?'featured':'';
                        $let  = strtoupper(substr($item['medicine_name'],0,1));
                ?>
                <div class="hcd-alert-item <?= $feat ?>" style="animation: slideInAlert 0.5s cubic-bezier(0.4, 0, 0.2, 1) <?= $idx * 0.1 ?>s both;">
                    <div class="hcd-av <?= $avcl ?>"><?= $let ?></div>
                    <div>
                        <div class="hcd-alert-name"><?= esc($item['medicine_name']) ?></div>
                        <div class="hcd-alert-sub"><i class="bi bi-box-seam"></i><?= esc($item['batch_number']) ?></div>
                        <div class="hcd-alert-time">
                            <i class="bi bi-calendar-x"></i>
                            <strong style="color:#ef4444;"><?= date('M j, Y', strtotime($item['expiry_date'])) ?></strong>
                            <span style="margin:0 4px;color:#cbd5e1;">•</span>
                            <span class="hcd-days-left"><?= $dl ?> day<?= $dl!==1?'s':'' ?> left</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- Low Stock Panel -->
        <div class="hcd-panel hcd-panel-animated">
            <div class="hcd-panel-hdr">
                <span class="hcd-panel-title">Low Stock Alert</span>
                <a href="/reorder" class="hcd-panel-link">Reorder List <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="hcd-alert-list" id="lowStockAlertList">
                <?php if(empty($lowStockItems)): ?>
                <div class="hcd-empty"><i class="bi bi-check-circle"></i><p>All medicines sufficiently stocked</p></div>
                <?php else:
                    foreach(array_slice($lowStockItems,0,5) as $idx => $item):
                        $st   = (int)$item['total_stock'];
                        $avcl = $st==0?'hcd-av-red':($st<=5?'hcd-av-orange':'hcd-av-green');
                        $let  = strtoupper(substr($item['name'],0,1));
                ?>
                <div class="hcd-alert-item" style="animation: slideInAlert 0.5s cubic-bezier(0.4, 0, 0.2, 1) <?= $idx * 0.1 ?>s both;">
                    <div class="hcd-av <?= $avcl ?>"><?= $let ?></div>
                    <div style="flex:1;min-width:0;">
                        <div class="hcd-alert-name"><?= esc($item['name']) ?></div>
                        <div class="hcd-alert-sub"><i class="bi bi-tag"></i><?= esc($item['category']) ?></div>
                        <div class="hcd-alert-time"><i class="bi bi-archive"></i><span class="hcd-stock-status"><?= $st==0?'Out of stock':$st.' / '.$item['minimum_stock'].' (Min)' ?></span></div>
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

// Animate KPI Cards on page load
function animateKPICards() {
    const kpiCards = document.querySelectorAll('.hcd-kpi-animated');
    
    kpiCards.forEach((card, index) => {
        // Stagger animation
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }, index * 100);
        
        // Animate counter
        const valueEl = card.querySelector('.hcd-kpi-value');
        const target = parseInt(valueEl.dataset.target) || 0;
        
        setTimeout(() => {
            animateCounter(valueEl, 0, target, 1200);
        }, 300 + (index * 100));
        
        // Add pulse effect to icon on hover
        const icon = card.querySelector('.hcd-kpi-icon');
        card.addEventListener('mouseenter', () => {
            icon.style.transform = 'scale(1.1) rotate(5deg)';
            icon.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
        });
        
        card.addEventListener('mouseleave', () => {
            icon.style.transform = 'scale(1) rotate(0deg)';
        });
    });
}

// Animate Hero Banner
function animateHeroBanner() {
    const hero = document.querySelector('.hcd-hero');
    const heroStats = document.querySelectorAll('.hcd-hero-stat');
    const heroActions = document.querySelectorAll('.hcd-hero-btn');
    const heroLeft = document.querySelector('.hcd-hero-left');
    
    // Animate hero entrance
    if (hero) {
        hero.style.opacity = '0';
        hero.style.transform = 'translateY(-20px)';
        
        setTimeout(() => {
            hero.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
            hero.style.opacity = '1';
            hero.style.transform = 'translateY(0)';
        }, 100);
    }
    
    // Animate left content
    if (heroLeft) {
        const greeting = heroLeft.querySelector('.hcd-hero-greeting');
        const name = heroLeft.querySelector('.hcd-hero-name');
        const role = heroLeft.querySelector('.hcd-hero-role');
        
        [greeting, name, role].forEach((el, i) => {
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    el.style.opacity = '1';
                    el.style.transform = 'translateX(0)';
                }, 300 + (i * 100));
            }
        });
    }
    
    // Animate hero stats with counters
    heroStats.forEach((stat, index) => {
        stat.style.opacity = '0';
        stat.style.transform = 'scale(0.8)';
        
        setTimeout(() => {
            stat.style.transition = 'all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
            stat.style.opacity = '1';
            stat.style.transform = 'scale(1)';
            
            // Animate counter
            const valueEl = stat.querySelector('.hcd-hero-stat-value');
            const target = parseInt(valueEl.dataset.target) || 0;
            animateCounter(valueEl, 0, target, 1000);
        }, 600 + (index * 150));
        
        // Add hover effects
        stat.addEventListener('mouseenter', () => {
            stat.style.transform = 'scale(1.05) translateY(-2px)';
            const icon = stat.querySelector('.hcd-hero-stat-icon');
            if (icon) {
                icon.style.transform = 'rotate(10deg) scale(1.1)';
            }
        });
        
        stat.addEventListener('mouseleave', () => {
            stat.style.transform = 'scale(1) translateY(0)';
            const icon = stat.querySelector('.hcd-hero-stat-icon');
            if (icon) {
                icon.style.transform = 'rotate(0deg) scale(1)';
            }
        });
    });
    
    // Animate action buttons
    heroActions.forEach((btn, index) => {
        btn.style.opacity = '0';
        btn.style.transform = 'translateX(20px)';
        
        setTimeout(() => {
            btn.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            btn.style.opacity = '1';
            btn.style.transform = 'translateX(0)';
        }, 900 + (index * 100));
    });
    
    // Animate particles
    animateParticles();
    
    // Update time every second
    updateDateTime();
    setInterval(updateDateTime, 1000);
}

function animateParticles() {
    const particles = document.querySelectorAll('.hcd-hero-particle');
    particles.forEach((particle, index) => {
        const duration = 15 + Math.random() * 10;
        const delay = index * 2;
        const size = 4 + Math.random() * 8;
        
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDuration = duration + 's';
        particle.style.animationDelay = delay + 's';
    });
}

function updateDateTime() {
    const dateTimeEl = document.getElementById('heroDateTime');
    if (dateTimeEl) {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
        const dateStr = now.toLocaleDateString('en-US', options);
        const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        dateTimeEl.textContent = `${dateStr} • ${timeStr}`;
    }
}

// Animate side panels
function animateSidePanels() {
    const panels = document.querySelectorAll('.hcd-panel-animated');
    
    panels.forEach((panel, index) => {
        panel.style.opacity = '0';
        panel.style.transform = 'translateX(30px)';
        
        setTimeout(() => {
            panel.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            panel.style.opacity = '1';
            panel.style.transform = 'translateX(0)';
        }, 1200 + (index * 200));
    });
    
    // Add pulse animation to days left
    const daysLeftElements = document.querySelectorAll('.hcd-days-left');
    daysLeftElements.forEach(el => {
        setInterval(() => {
            el.style.animation = 'none';
            setTimeout(() => {
                el.style.animation = 'pulse 1s ease-in-out';
            }, 10);
        }, 5000);
    });
    
    // Add pulse animation to stock status
    const stockStatusElements = document.querySelectorAll('.hcd-stock-status');
    stockStatusElements.forEach(el => {
        if (el.textContent.includes('Out of stock')) {
            setInterval(() => {
                el.style.animation = 'none';
                setTimeout(() => {
                    el.style.animation = 'pulse 1s ease-in-out';
                }, 10);
            }, 3000);
        }
    });
}

function animateCounter(element, start, end, duration) {
    const startTime = performance.now();
    const range = end - start;
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function for smooth animation
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        const current = Math.floor(start + (range * easeOutQuart));
        
        element.textContent = current.toString().padStart(2, '0');
        
        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            element.textContent = end.toString().padStart(2, '0');
        }
    }
    
    requestAnimationFrame(update);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    animateHeroBanner();
    animateKPICards();
    animateSidePanels();
});

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

// Bar chart with real data and advanced animations
const weeklyStatsData = <?= json_encode($weeklyStats) ?>;
const monthlyStatsData = <?= json_encode($monthlyStats) ?>;

// Process weekly data
const dayMap = {0:'Sun',1:'Mon',2:'Tue',3:'Wed',4:'Thu',5:'Fri',6:'Sat'};
const weeklyData = {};
weeklyStatsData.forEach(stat => {
    const dayNum = parseInt(stat.day_num) - 1; // MySQL DAYOFWEEK returns 1-7, JS uses 0-6
    weeklyData[dayNum] = {
        label: dayMap[dayNum],
        value: parseInt(stat.transaction_count) || 0,
        quantity: parseInt(stat.total_quantity) || 0
    };
});

// Fill missing days with 0
const wData = [];
for(let i=1; i<=7; i++) {
    const idx = i % 7;
    wData.push(weeklyData[idx] || {label: dayMap[idx], value: 0, quantity: 0});
}

// Process monthly data (last 12 months)
const monthMap = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const monthlyData = {};
monthlyStatsData.forEach(stat => {
    const monthNum = parseInt(stat.month_num) - 1;
    monthlyData[monthNum] = {
        label: monthMap[monthNum],
        value: parseInt(stat.transaction_count) || 0,
        quantity: parseInt(stat.total_quantity) || 0
    };
});

// Fill all 12 months
const mData = monthMap.map((label, i) => monthlyData[i] || {label, value: 0, quantity: 0});

function switchBarChart(btn, mode){
    document.querySelectorAll('.hcd-tab').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    
    const data  = mode==='weekly' ? wData : mData;
    const maxV  = Math.max(...data.map(d=>d.value), 1);
    const today = new Date().getDay();
    const wrap  = document.getElementById('barChartWrap');
    
    // Animate bars with stagger effect
    wrap.style.opacity = '0';
    setTimeout(() => {
        wrap.innerHTML = data.map((d,i)=>{
            const isHighlight = (mode==='weekly' && i===today) || (mode==='monthly' && i===new Date().getMonth());
            const height = Math.round((d.value/maxV)*100);
            return `
                <div class="hcd-bar-col" style="animation: barSlideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) ${i*0.05}s both;">
                    <div class="hcd-bar-inner">
                        <div class="hcd-bar-value">${d.value} transaction${d.value !== 1 ? 's' : ''}</div>
                        <div class="hcd-bar-fill ${isHighlight?'':'dim'}" 
                             style="height:0%;" 
                             data-height="${height}%"
                             title="${d.value} transactions, ${d.quantity} items">
                        </div>
                    </div>
                    <span class="hcd-bar-lbl">${d.label}</span>
                </div>
            `;
        }).join('');
        
        wrap.style.opacity = '1';
        
        // Animate bar heights
        setTimeout(() => {
            wrap.querySelectorAll('.hcd-bar-fill').forEach((bar, i) => {
                setTimeout(() => {
                    bar.style.height = bar.dataset.height;
                }, i * 50);
            });
        }, 100);
    }, 150);
}

// Add CSS animation
if (!document.getElementById('barChartAnimations')) {
    const style = document.createElement('style');
    style.id = 'barChartAnimations';
    style.textContent = `
        @keyframes barSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
}

// Initialize bar chart on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('barChartWrap')) {
        switchBarChart(document.querySelector('.hcd-tab.active'), 'weekly');
    }
    
    // Interactive Donut Chart
    initDonutChart();
});

// Donut Chart Interactivity
function initDonutChart() {
    const donutData = [
        { name: 'Active', value: <?= $activeOnlyCount ?>, color: '#0f4c81', percent: <?= $activePercent ?> },
        { name: 'Inactive', value: <?= $inactiveOnlyCount ?>, color: '#8b5cf6', percent: <?= $inactivePercent ?> },
        { name: 'Expiring', value: <?= $expiringOnlyCount ?>, color: '#f59e0b', percent: <?= $expiringPercent ?> },
        { name: 'Low Stock', value: <?= $lowStockOnlyCount ?>, color: '#ef4444', percent: <?= $lowStockPercent ?> }
    ];
    
    const donutNum = document.querySelector('.hcd-donut-num');
    const donutLbl = document.querySelector('.hcd-donut-lbl');
    const legendRows = document.querySelectorAll('.hcd-legend-row');
    const donutCircles = document.querySelectorAll('.hcd-donut-svg circle[data-segment]');
    
    if (!donutNum || !donutLbl) return;
    
    const originalNum = donutNum.textContent;
    const originalLbl = donutLbl.textContent;
    
    // Add hover to legend items
    legendRows.forEach((row) => {
        const legendIndex = parseInt(row.getAttribute('data-legend'));
        const data = donutData[legendIndex];
        
        if (!data || data.percent === 0) return;
        
        row.addEventListener('mouseenter', () => {
            // Update center text
            donutNum.textContent = data.value;
            donutLbl.textContent = data.name;
            donutNum.style.color = data.color;
            donutNum.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
            donutNum.style.transform = 'scale(1.1)';
            donutLbl.style.transition = 'all 0.3s';
            donutLbl.style.transform = 'translateY(2px)';
            
            // Highlight legend row
            legendRows.forEach(r => r.classList.remove('active'));
            row.classList.add('active');
            row.style.transform = 'translateX(5px)';
            row.style.background = 'rgba(14, 165, 233, 0.05)';
            
            // Dim other segments and enlarge hovered segment
            donutCircles.forEach((circle) => {
                const segmentIndex = parseInt(circle.getAttribute('data-segment'));
                if (segmentIndex === legendIndex) {
                    circle.style.strokeWidth = '26';
                    circle.style.filter = 'drop-shadow(0 0 8px ' + data.color + ')';
                } else {
                    circle.style.opacity = '0.3';
                    circle.style.strokeWidth = '22';
                }
            });
        });
        
        row.addEventListener('mouseleave', () => {
            // Reset center text
            donutNum.textContent = originalNum;
            donutLbl.textContent = originalLbl;
            donutNum.style.color = '';
            donutNum.style.transform = 'scale(1)';
            donutLbl.style.transform = 'translateY(0)';
            
            // Remove highlight
            row.classList.remove('active');
            row.style.transform = 'translateX(0)';
            row.style.background = '';
            
            // Reset opacity and size
            donutCircles.forEach(circle => {
                circle.style.opacity = '1';
                circle.style.strokeWidth = '22';
                circle.style.filter = 'none';
            });
        });
    });
    
    // Add hover to donut segments
    donutCircles.forEach((circle) => {
        const segmentIndex = parseInt(circle.getAttribute('data-segment'));
        const data = donutData[segmentIndex];
        
        if (!data || data.percent === 0) return;
        
        circle.addEventListener('mouseenter', () => {
            donutNum.textContent = data.value;
            donutLbl.textContent = data.name;
            donutNum.style.color = data.color;
            donutNum.style.transform = 'scale(1.1)';
            donutLbl.style.transform = 'translateY(2px)';
            
            // Enlarge hovered segment
            circle.style.strokeWidth = '26';
            circle.style.filter = 'drop-shadow(0 0 8px ' + data.color + ')';
            
            // Highlight corresponding legend
            legendRows.forEach(r => {
                const legendIndex = parseInt(r.getAttribute('data-legend'));
                if (legendIndex === segmentIndex) {
                    r.classList.add('active');
                    r.style.transform = 'translateX(5px)';
                    r.style.background = 'rgba(14, 165, 233, 0.05)';
                } else {
                    r.classList.remove('active');
                }
            });
            
            // Dim other segments
            donutCircles.forEach((c) => {
                const cIndex = parseInt(c.getAttribute('data-segment'));
                if (cIndex !== segmentIndex) {
                    c.style.opacity = '0.3';
                    c.style.strokeWidth = '22';
                }
            });
        });
        
        circle.addEventListener('mouseleave', () => {
            donutNum.textContent = originalNum;
            donutLbl.textContent = originalLbl;
            donutNum.style.color = '';
            donutNum.style.transform = 'scale(1)';
            donutLbl.style.transform = 'translateY(0)';
            
            // Reset segment size
            circle.style.strokeWidth = '22';
            circle.style.filter = 'none';
            
            legendRows.forEach(r => {
                r.classList.remove('active');
                r.style.transform = 'translateX(0)';
                r.style.background = '';
            });
            
            donutCircles.forEach(c => {
                c.style.opacity = '1';
                c.style.strokeWidth = '22';
            });
        });
    });
    
    // Animate donut on load
    donutCircles.forEach((circle, index) => {
        const length = circle.getTotalLength();
        circle.style.strokeDasharray = `0 ${length}`;
        
        setTimeout(() => {
            const dashArray = circle.getAttribute('stroke-dasharray').split(' ');
            circle.style.transition = 'stroke-dasharray 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
            circle.style.strokeDasharray = dashArray.join(' ');
        }, 300 + (index * 150));
    });
}

// Line chart with interactivity
let stockLineChartInstance = null;

document.addEventListener('DOMContentLoaded',function(){
    const ctx=document.getElementById('stockLineChart');
    if(!ctx)return;
    const dark=document.documentElement.getAttribute('data-theme')==='dark';
    const gc=dark?'rgba(255,255,255,.06)':'rgba(0,0,0,.05)';
    const tc=dark?'#4d7a9e':'#94a3b8';
    
    const valueEl = document.getElementById('lineChartValue');
    const batchEl = document.getElementById('lineChartBatches');
    const originalValue = valueEl.textContent;
    const originalBatch = batchEl.textContent;
    
    stockLineChartInstance = new Chart(ctx,{
        type:'line',
        data:{
            labels:['Jan','Feb','Mar','Apr','May','Jun'],
            datasets:[{
                label:'Stock Value',
                data:[42000,55000,48000,61000,58000,<?= round($stockValue) ?>],
                borderColor:'#0f4c81',
                backgroundColor:'rgba(15,76,129,.1)',
                fill:true, tension:.45,
                pointRadius:5, pointHoverRadius:8,
                pointBackgroundColor:'#0f4c81',
                pointBorderColor:'#fff', pointBorderWidth:2,
                pointHoverBorderWidth:3,
            },{
                label:'Batches',
                data:[8,12,9,14,11,<?= $activeBatch ?>],
                borderColor:'#8b5cf6',
                backgroundColor:'rgba(139,92,246,.06)',
                fill:true, tension:.45,
                pointRadius:5, pointHoverRadius:8,
                pointBackgroundColor:'#8b5cf6',
                pointBorderColor:'#fff', pointBorderWidth:2,
                pointHoverBorderWidth:3,
                yAxisID:'y2',
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            interaction:{mode:'index',intersect:false},
            animation:{
                duration: 1500,
                easing: 'easeInOutQuart',
                onProgress: function(animation) {
                    const progress = animation.currentStep / animation.numSteps;
                    if (progress < 1) {
                        ctx.style.opacity = progress;
                    }
                }
            },
            plugins:{
                legend:{display:false},
                tooltip:{
                    enabled: true,
                    backgroundColor:'#0f1117',
                    titleColor:'#fff',
                    bodyColor:'rgba(255,255,255,.75)',
                    padding:12,
                    cornerRadius:10,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.datasetIndex === 0) {
                                label += '₱' + context.parsed.y.toLocaleString();
                            } else {
                                label += context.parsed.y + ' batches';
                            }
                            return label;
                        }
                    }
                }
            },
            scales:{
                x:{grid:{color:gc},ticks:{color:tc,font:{size:11}}},
                y:{grid:{color:gc},ticks:{color:tc,font:{size:11},callback:v=>'₱'+v.toLocaleString()}},
                y2:{display:false}
            },
            onHover: function(event, activeElements) {
                if (activeElements.length > 0) {
                    const datasetIndex = activeElements[0].datasetIndex;
                    const index = activeElements[0].index;
                    const value = this.data.datasets[datasetIndex].data[index];
                    
                    if (datasetIndex === 0) {
                        valueEl.textContent = '₱' + value.toLocaleString();
                        valueEl.style.color = '#0f4c81';
                        valueEl.style.transform = 'scale(1.05)';
                    } else {
                        batchEl.textContent = value;
                        batchEl.style.color = '#8b5cf6';
                        batchEl.style.transform = 'scale(1.05)';
                    }
                } else {
                    valueEl.textContent = originalValue;
                    batchEl.textContent = originalBatch;
                    valueEl.style.color = '';
                    batchEl.style.color = '';
                    valueEl.style.transform = '';
                    batchEl.style.transform = '';
                }
            }
        }
    });
    
    ctx.style.opacity = '0';
});
</script>
