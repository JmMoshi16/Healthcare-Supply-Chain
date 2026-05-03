<?php use App\Core\View; View::layout('app'); $title = 'Batches'; ?>
<?php $totalCount = count($data ?? []); ?>

<link rel="stylesheet" href="/assets/css/batches-advanced.css?v=<?= time() ?>">
<script src="/assets/js/batches-advanced.js?v=<?= time() ?>" defer></script>

<div class="sl-header">
    <div>
        <h1 class="sl-title">Batches</h1>
        <p class="sl-subtitle">Track medicine batches with expiry and stock levels</p>
    </div>
    <?php if (can('batches.*')): ?>
    <a href="/batches/create" class="btn-create-order"><i class="bi bi-plus-lg"></i> Add Batch</a>
    <?php endif; ?>
</div>

<!-- Stat cards -->
<?php
$expiring = array_filter($data ?? [], fn($b) => (strtotime($b['expiry_date']) - time()) / 86400 <= 30 && (strtotime($b['expiry_date']) - time()) / 86400 > 0);
$expired  = array_filter($data ?? [], fn($b) => strtotime($b['expiry_date']) < time());
$lowStock = array_filter($data ?? [], fn($b) => $b['current_quantity'] < 10);
?>
<div class="rt-stats-row" style="margin-bottom:1.25rem;">
    <div class="stat-card stat-card-dark" style="border-radius:16px;">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-label">Total Batches</div>
                <div class="stat-card-value"><?= number_format($totalCount) ?></div>
                <div class="stat-card-sub">All tracked batches</div>
            </div>
            <i class="bi bi-boxes" style="font-size:1.25rem;opacity:.5;"></i>
        </div>
    </div>
    <div class="stat-card stat-card-orange" style="border-radius:16px;">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-label" style="color:rgba(255,255,255,.75);">Expiring Soon</div>
                <div class="stat-card-value" style="color:#fff;"><?= count($expiring) ?></div>
                <div class="stat-card-sub" style="color:rgba(255,255,255,.55);">Within 30 days</div>
            </div>
            <i class="bi bi-clock-history" style="font-size:1.25rem;color:rgba(255,255,255,.4);"></i>
        </div>
    </div>
    <div class="stat-card stat-card-light" style="border-radius:16px;">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-label">Expired</div>
                <div class="stat-card-value"><?= count($expired) ?></div>
                <div class="stat-card-sub">Need removal</div>
            </div>
            <i class="bi bi-exclamation-triangle" style="font-size:1.25rem;color:var(--danger);"></i>
        </div>
    </div>
    <div class="stat-card stat-card-light" style="border-radius:16px;">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-label">Low Stock</div>
                <div class="stat-card-value"><?= count($lowStock) ?></div>
                <div class="stat-card-sub">Below 10 units</div>
            </div>
            <i class="bi bi-box-seam" style="font-size:1.25rem;color:var(--warning);"></i>
        </div>
    </div>
</div>

<!-- Search toolbar -->
<div class="rt-toolbar" style="margin-bottom:1rem;">
    <div class="pg-search-wrap" style="max-width:360px;">
        <i class="bi bi-search pg-search-icon"></i>
        <input type="text" id="batchSearch" placeholder="Search by batch number or supplier…" class="pg-search-input" oninput="filterBatches(this.value)">
    </div>
    <div class="rt-filter-pills">
        <button class="pill-tab active" onclick="setFilter(this,'all')">All</button>
        <button class="pill-tab" onclick="setFilter(this,'active')">Active</button>
        <button class="pill-tab" onclick="setFilter(this,'expiring')">Expiring</button>
        <button class="pill-tab" onclick="setFilter(this,'expired')">Expired</button>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="smro-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Batch Number</th>
                    <th>Medicine</th>
                    <th>Expiry Date</th>
                    <th>Supplier</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <?php if (can('batches.*')): ?><th></th><?php endif; ?>
                </tr>
            </thead>
            <tbody id="batchTbody">
                <?php if (empty($data)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted);">
                        <i class="bi bi-box-seam" style="font-size:1.75rem;display:block;margin-bottom:.6rem;opacity:.25;"></i>
                        No batches found.
                        <?php if (can('batches.*')): ?>
                        <a href="/batches/create" style="color:var(--accent-orange);font-weight:600;text-decoration:none;display:block;margin-top:.4rem;">Add one →</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: foreach ($data as $b):
                    $daysLeft = (strtotime($b['expiry_date']) - time()) / 86400;
                    $filterTag = $daysLeft < 0 ? 'expired' : ($daysLeft <= 30 ? 'expiring' : 'active');
                ?>
                <tr data-filter="<?= $filterTag ?>" data-search="<?= strtolower(esc($b['batch_number']) . ' ' . esc($b['supplier'] ?? '')) ?>">
                    <td style="font-family:monospace;font-size:.75rem;color:var(--text-muted);"><?= (int)$b['id'] ?></td>
                    <td style="font-weight:600;color:var(--text-heading);"><?= esc($b['batch_number']) ?></td>
                    <td style="color:var(--text-muted);"><?= (int)$b['medicine_id'] ?></td>
                    <td>
                        <?php if ($daysLeft < 0): ?>
                            <span class="badge badge-danger">Expired <?= date('M d, Y', strtotime($b['expiry_date'])) ?></span>
                        <?php elseif ($daysLeft <= 30): ?>
                            <span class="badge badge-warning"><?= date('M d, Y', strtotime($b['expiry_date'])) ?> (<?= (int)$daysLeft ?>d)</span>
                        <?php else: ?>
                            <?= date('M d, Y', strtotime($b['expiry_date'])) ?>
                        <?php endif; ?>
                    </td>
                    <td style="color:var(--text-muted);"><?= esc($b['supplier'] ?? '—') ?></td>
                    <td>
                        <?php if ($b['current_quantity'] < 10): ?>
                            <span class="badge badge-danger"><?= (int)$b['current_quantity'] ?> Low</span>
                        <?php else: ?>
                            <span class="badge badge-success"><?= (int)$b['current_quantity'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-<?= $b['status'] === 'active' ? 'success' : 'danger' ?>"><?= esc($b['status']) ?></span></td>
                    <?php if (can('batches.*')): ?>
                    <td>
                        <div style="display:flex;gap:.4rem;">
                            <a href="/batches/<?= (int)$b['id'] ?>/edit" class="pgc-icon-btn" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/batches/<?= (int)$b['id'] ?>" onsubmit="return confirm('Delete this batch?')" style="display:contents;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="pgc-icon-btn" style="border-color:#fecaca;color:var(--danger);" title="Delete"><i class="bi bi-trash3"></i></button>
                            </form>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pagination['total_pages'] > 1): ?>
    <div style="padding:.85rem 1.25rem;border-top:1px solid var(--border);">
        <div class="pager-wrap" style="padding:0;">
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <a href="?page=<?= $i ?>" class="page-link <?= $i === $pagination['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
let currentFilter = 'all';
function filterBatches(q){ applyFilters(q, currentFilter); }
function setFilter(btn, f){ currentFilter = f; document.querySelectorAll('.pill-tab').forEach(b => b.classList.remove('active')); btn.classList.add('active'); applyFilters(document.getElementById('batchSearch').value, f); }
function applyFilters(q, f){
    const rows = document.querySelectorAll('#batchTbody tr[data-filter]');
    rows.forEach(r => {
        const mQ = !q || r.dataset.search.includes(q.toLowerCase());
        const mF = f === 'all' || r.dataset.filter === f;
        r.style.display = mQ && mF ? '' : 'none';
    });
}
</script>
