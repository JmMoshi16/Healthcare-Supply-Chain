<?php use App\Core\View; View::layout('app'); $title = 'Dashboard'; ?>

<?php
$totalMeds      = (int)($stats['total_medicines'] ?? 0);
$activeMeds     = (int)($stats['active_medicines'] ?? 0);
$inactiveMeds   = (int)($stats['inactive_medicines'] ?? 0);
$totalBatch     = (int)($stats['total_batches'] ?? 0);
$activeBatch    = (int)($stats['active_batches'] ?? 0);
$expiredBatch   = (int)($stats['expired_batches'] ?? 0);
$expiringItems  = $stats['expiring_soon'] ?? [];
$expiringCount  = (int)($stats['expiring_count'] ?? 0);
$lowStockItems  = $stats['low_stock'] ?? [];
$lowStockCount  = (int)($stats['low_stock_count'] ?? 0);
$totalUsers     = (int)($stats['total_users'] ?? 0);
$recentTx       = $stats['recent_transactions'] ?? [];
$todayTx        = (int)($stats['today_transactions'] ?? 0);
$stockValue     = (float)($stats['total_stock_value'] ?? 0);
?>

<!-- PAGE HEADER -->
<div class="dash-header">
    <div>
        <h1 class="dash-title">Dashboard</h1>
        <p class="dash-subtitle">Healthcare Supply Chain — Real-time Overview</p>
    </div>
    <div class="dash-header-actions">
        <button class="btn-icon" onclick="refreshDashboard()" title="Refresh">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
        <?php if (can('medicines.*')): ?>
        <a href="/medicines/create" class="btn-create-order">
            <i class="bi bi-plus-lg"></i> Add Medicine
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- STAT CARDS ROW -->
<div class="dash-stats-grid">
    <!-- Total Medicines Card -->
    <a href="/medicines" class="dash-stat-card dash-card-clickable">
        <div class="dash-stat-icon dash-icon-blue">
            <i class="bi bi-capsule"></i>
        </div>
        <div class="dash-stat-content">
            <div class="dash-stat-label">Total Medicines</div>
            <div class="dash-stat-value"><?= number_format($totalMeds) ?></div>
            <div class="dash-stat-meta">
                <span class="dash-badge dash-badge-success"><?= $activeMeds ?> active</span>
                <?php if ($inactiveMeds > 0): ?>
                <span class="dash-badge dash-badge-neutral"><?= $inactiveMeds ?> inactive</span>
                <?php endif; ?>
            </div>
        </div>
        <i class="bi bi-arrow-right dash-card-arrow"></i>
    </a>

    <!-- Total Batches Card -->
    <a href="/batches" class="dash-stat-card dash-card-clickable">
        <div class="dash-stat-icon dash-icon-purple">
            <i class="bi bi-box-seam"></i>
        </div>
        <div class="dash-stat-content">
            <div class="dash-stat-label">Total Batches</div>
            <div class="dash-stat-value"><?= number_format($totalBatch) ?></div>
            <div class="dash-stat-meta">
                <span class="dash-badge dash-badge-success"><?= $activeBatch ?> active</span>
                <?php if ($expiredBatch > 0): ?>
                <span class="dash-badge dash-badge-danger"><?= $expiredBatch ?> expired</span>
                <?php endif; ?>
            </div>
        </div>
        <i class="bi bi-arrow-right dash-card-arrow"></i>
    </a>

    <!-- Expiring Soon Card -->
    <div class="dash-stat-card <?= $expiringCount > 0 ? 'dash-card-warning dash-card-clickable' : '' ?>" 
         <?= $expiringCount > 0 ? 'onclick="showExpiringModal()"' : '' ?>>
        <div class="dash-stat-icon dash-icon-orange">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="dash-stat-content">
            <div class="dash-stat-label">Expiring Soon</div>
            <div class="dash-stat-value"><?= number_format($expiringCount) ?></div>
            <div class="dash-stat-meta">
                <span class="dash-badge dash-badge-warning">Within 30 days</span>
            </div>
        </div>
        <?php if ($expiringCount > 0): ?>
        <i class="bi bi-arrow-right dash-card-arrow"></i>
        <?php endif; ?>
    </div>

    <!-- Low Stock Card -->
    <div class="dash-stat-card <?= $lowStockCount > 0 ? 'dash-card-danger dash-card-clickable' : '' ?>"
         <?= $lowStockCount > 0 ? 'onclick="showLowStockModal()"' : '' ?>>
        <div class="dash-stat-icon dash-icon-red">
            <i class="bi bi-arrow-down-circle"></i>
        </div>
        <div class="dash-stat-content">
            <div class="dash-stat-label">Low Stock</div>
            <div class="dash-stat-value"><?= number_format($lowStockCount) ?></div>
            <div class="dash-stat-meta">
                <span class="dash-badge dash-badge-danger">Need restock</span>
            </div>
        </div>
        <?php if ($lowStockCount > 0): ?>
        <i class="bi bi-arrow-right dash-card-arrow"></i>
        <?php endif; ?>
    </div>
</div>

<!-- SECONDARY STATS -->
<div class="dash-secondary-stats">
    <div class="dash-info-card">
        <i class="bi bi-currency-dollar"></i>
        <div>
            <div class="dash-info-label">Total Stock Value</div>
            <div class="dash-info-value">₱<?= number_format($stockValue, 2) ?></div>
        </div>
    </div>
    
    <div class="dash-info-card">
        <i class="bi bi-arrow-left-right"></i>
        <div>
            <div class="dash-info-label">Today's Transactions</div>
            <div class="dash-info-value"><?= $todayTx ?></div>
        </div>
    </div>
    
    <?php if (has_role('superadmin')): ?>
    <a href="/users" class="dash-info-card dash-card-clickable">
        <i class="bi bi-people"></i>
        <div>
            <div class="dash-info-label">Total Users</div>
            <div class="dash-info-value"><?= $totalUsers ?></div>
        </div>
        <i class="bi bi-arrow-right" style="margin-left:auto;font-size:0.9rem;color:var(--text-muted);"></i>
    </a>
    <?php endif; ?>
</div>

<!-- MAIN CONTENT ROW -->
<div class="dash-content-row">
    <!-- Quick Actions -->
    <div class="dash-panel">
        <div class="dash-panel-header">
            <div>
                <h3 class="dash-panel-title">Quick Actions</h3>
                <p class="dash-panel-subtitle">Common tasks</p>
            </div>
        </div>
        <div class="dash-quick-actions">
            <?php if (can('medicines.*')): ?>
            <a href="/medicines/create" class="dash-action-btn">
                <i class="bi bi-capsule"></i>
                <span>Add Medicine</span>
            </a>
            <a href="/batches/create" class="dash-action-btn">
                <i class="bi bi-box-seam"></i>
                <span>Add Batch</span>
            </a>
            <a href="/stocks/create" class="dash-action-btn">
                <i class="bi bi-arrow-left-right"></i>
                <span>Stock Transaction</span>
            </a>
            <?php endif; ?>
            <a href="/medicines" class="dash-action-btn">
                <i class="bi bi-list-ul"></i>
                <span>View Medicines</span>
            </a>
            <a href="/batches" class="dash-action-btn">
                <i class="bi bi-boxes"></i>
                <span>View Batches</span>
            </a>
            <a href="/stocks" class="dash-action-btn">
                <i class="bi bi-clock-history"></i>
                <span>View Transactions</span>
            </a>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="dash-panel dash-panel-large">
        <div class="dash-panel-header">
            <div>
                <h3 class="dash-panel-title">Recent Transactions</h3>
                <p class="dash-panel-subtitle">Latest stock movements</p>
            </div>
            <a href="/stocks" class="dash-view-all">View all <i class="bi bi-arrow-right"></i></a>
        </div>
        
        <?php if (empty($recentTx)): ?>
        <div class="dash-empty">
            <i class="bi bi-arrow-left-right"></i>
            <p>No transactions yet</p>
            <a href="/stocks/create" class="btn btn-ghost">Record Transaction</a>
        </div>
        <?php else: ?>
        <div class="dash-table-wrapper">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Batch</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Performed By</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentTx as $tx):
                        $typeClass = $tx['transaction_type'] === 'in' ? 'success' : ($tx['transaction_type'] === 'out' ? 'danger' : 'warning');
                    ?>
                    <tr>
                        <td class="dash-table-primary"><?= esc($tx['medicine_name'] ?? '—') ?></td>
                        <td><span class="dash-badge dash-badge-neutral"><?= esc($tx['batch_number'] ?? '—') ?></span></td>
                        <td><span class="dash-badge dash-badge-<?= $typeClass ?>"><?= strtoupper($tx['transaction_type']) ?></span></td>
                        <td class="dash-table-number"><?= number_format((int)$tx['quantity']) ?></td>
                        <td class="dash-table-muted"><?= esc($tx['performed_by_name'] ?? '—') ?></td>
                        <td class="dash-table-date"><?= date('M d, H:i', strtotime($tx['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- EXPIRING MEDICINES MODAL -->
<div class="dash-modal-overlay" id="expiringModal" style="display:none;">
    <div class="dash-modal">
        <div class="dash-modal-header">
            <h3>Expiring Medicines (30 Days)</h3>
            <button onclick="closeModal('expiringModal')" class="dash-modal-close">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="dash-modal-body">
            <?php if (empty($expiringItems)): ?>
            <div class="dash-empty">
                <i class="bi bi-check-circle"></i>
                <p>No medicines expiring soon</p>
            </div>
            <?php else: ?>
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Batch Number</th>
                        <th>Expiry Date</th>
                        <th>Stock</th>
                        <th>Days Left</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expiringItems as $item):
                        $daysLeft = max(0, floor((strtotime($item['expiry_date']) - time()) / 86400));
                        $urgency = $daysLeft <= 7 ? 'danger' : ($daysLeft <= 15 ? 'warning' : 'neutral');
                    ?>
                    <tr>
                        <td class="dash-table-primary"><?= esc($item['medicine_name']) ?></td>
                        <td><?= esc($item['batch_number']) ?></td>
                        <td><?= date('M d, Y', strtotime($item['expiry_date'])) ?></td>
                        <td class="dash-table-number"><?= number_format((int)$item['current_quantity']) ?></td>
                        <td><span class="dash-badge dash-badge-<?= $urgency ?>"><?= $daysLeft ?> days</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- LOW STOCK MODAL -->
<div class="dash-modal-overlay" id="lowStockModal" style="display:none;">
    <div class="dash-modal">
        <div class="dash-modal-header">
            <h3>Low Stock Medicines</h3>
            <button onclick="closeModal('lowStockModal')" class="dash-modal-close">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="dash-modal-body">
            <?php if (empty($lowStockItems)): ?>
            <div class="dash-empty">
                <i class="bi bi-check-circle"></i>
                <p>All medicines have sufficient stock</p>
            </div>
            <?php else: ?>
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Category</th>
                        <th>Total Stock</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lowStockItems as $item):
                        $stock = (int)$item['total_stock'];
                        $urgency = $stock == 0 ? 'danger' : ($stock <= 5 ? 'warning' : 'neutral');
                    ?>
                    <tr>
                        <td class="dash-table-primary"><?= esc($item['name']) ?></td>
                        <td><?= esc($item['category']) ?></td>
                        <td class="dash-table-number"><?= number_format($stock) ?></td>
                        <td><span class="dash-badge dash-badge-<?= $urgency ?>"><?= $stock == 0 ? 'Out of stock' : 'Low' ?></span></td>
                        <td><a href="/batches/create" class="dash-link">Add Batch →</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showExpiringModal() {
    document.getElementById('expiringModal').style.display = 'flex';
}

function showLowStockModal() {
    document.getElementById('lowStockModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function refreshDashboard() {
    location.reload();
}

// Close modals on outside click
document.querySelectorAll('.dash-modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            overlay.style.display = 'none';
        }
    });
});

// Auto-refresh every 5 minutes
setTimeout(() => location.reload(), 300000);
</script>
