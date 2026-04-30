<?php use App\Core\View; View::layout('app'); $title = 'Stock Transactions'; ?>
<?php $totalCount = count($data ?? []); ?>

<div class="sl-header">
    <div>
        <h1 class="sl-title">Stock Transactions</h1>
        <p class="sl-subtitle">Full audit trail of all stock movements</p>
    </div>
    <a href="/stocks/create" class="btn-create-order"><i class="bi bi-plus-lg"></i> New Transaction</a>
</div>

<!-- Stat counters -->
<?php
$inCount  = count(array_filter($data ?? [], fn($t) => $t['transaction_type'] === 'in'));
$outCount = count(array_filter($data ?? [], fn($t) => $t['transaction_type'] === 'out'));
$adjCount = count(array_filter($data ?? [], fn($t) => $t['transaction_type'] === 'adjustment'));
?>
<div class="sl-stats-row" style="margin-bottom:1.25rem;">
    <div class="sl-balance-card">
        <div class="sl-balance-top">
            <div>
                <div class="sl-card-label">Total Transactions</div>
                <div class="sl-balance-value"><?= number_format($totalCount) ?></div>
                <div class="sl-balance-change up"><i class="bi bi-arrow-up-short"></i> all time</div>
            </div>
        </div>
        <div class="sl-status-counters" style="margin-top:1rem;border-top:1px solid rgba(255,255,255,.1);padding-top:.75rem;">
            <div class="sl-counter-item">
                <div class="sl-counter-value" style="font-size:1.25rem;"><?= str_pad($inCount, 2, '0', STR_PAD_LEFT) ?></div>
                <div class="sl-counter-label">Stock In</div>
            </div>
            <div class="sl-counter-item">
                <div class="sl-counter-value" style="font-size:1.25rem;"><?= str_pad($outCount, 2, '0', STR_PAD_LEFT) ?></div>
                <div class="sl-counter-label">Stock Out</div>
            </div>
            <div class="sl-counter-item">
                <div class="sl-counter-value" style="font-size:1.25rem;"><?= str_pad($adjCount, 2, '0', STR_PAD_LEFT) ?></div>
                <div class="sl-counter-label">Adjustments</div>
            </div>
        </div>
    </div>
    <div class="sl-mini-stats">
        <div class="sl-mini-card" style="background:var(--success-light,#dcfce7);">
            <div class="sl-mini-label">Stock In</div>
            <div class="sl-mini-value" style="color:var(--success);"><?= $inCount ?></div>
            <div class="sl-mini-change up"><i class="bi bi-arrow-up-short"></i> received</div>
        </div>
        <div class="sl-mini-card" style="background:#fee2e2;">
            <div class="sl-mini-label">Stock Out</div>
            <div class="sl-mini-value" style="color:var(--danger);"><?= $outCount ?></div>
            <div class="sl-mini-change down"><i class="bi bi-arrow-down-short"></i> dispensed</div>
        </div>
        <div class="sl-mini-card">
            <div class="sl-mini-label">Adjustments</div>
            <div class="sl-mini-value"><?= $adjCount ?></div>
            <div class="sl-mini-change up"><i class="bi bi-sliders"></i> corrected</div>
        </div>
        <div class="sl-mini-card">
            <div class="sl-mini-label">This Page</div>
            <div class="sl-mini-value"><?= $totalCount ?></div>
            <div class="sl-mini-change up"><i class="bi bi-list-ul"></i> records</div>
        </div>
    </div>
</div>

<!-- Search + filter toolbar -->
<div class="rt-toolbar" style="margin-bottom:1rem;">
    <div class="pg-search-wrap" style="max-width:360px;">
        <i class="bi bi-search pg-search-icon"></i>
        <input type="text" id="txSearch" placeholder="Search by batch or reason…" class="pg-search-input" oninput="filterTx(this.value)">
    </div>
    <div class="rt-filter-pills">
        <button class="pill-tab active" onclick="setTxFilter(this,'all')">All</button>
        <button class="pill-tab" onclick="setTxFilter(this,'in')">Stock In</button>
        <button class="pill-tab" onclick="setTxFilter(this,'out')">Stock Out</button>
        <button class="pill-tab" onclick="setTxFilter(this,'adjustment')">Adjustment</button>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="smro-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Batch</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Reason</th>
                    <th>By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody id="txTbody">
                <?php if (empty($data)): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted);">
                        <i class="bi bi-arrow-left-right" style="font-size:1.75rem;display:block;margin-bottom:.6rem;opacity:.25;"></i>
                        No transactions yet.
                        <a href="/stocks/create" style="color:var(--accent-orange);font-weight:600;text-decoration:none;display:block;margin-top:.4rem;">Record one →</a>
                    </td>
                </tr>
                <?php else: foreach ($data as $s):
                    $typeClass = $s['transaction_type'] === 'in' ? 'sl-badge-green' : ($s['transaction_type'] === 'out' ? 'sl-badge-red' : 'sl-badge-orange');
                ?>
                <tr data-type="<?= esc($s['transaction_type']) ?>" data-search="<?= strtolower(esc($s['batch_id'] . ' ' . ($s['reason'] ?? ''))) ?>">
                    <td style="font-family:monospace;font-size:.75rem;color:var(--text-muted);"><?= sprintf('%04d', $s['id']) ?></td>
                    <td style="font-weight:600;color:var(--text-heading);"><?= (int)$s['batch_id'] ?></td>
                    <td><span class="sl-status-badge <?= $typeClass ?>"><?= strtoupper($s['transaction_type']) ?></span></td>
                    <td style="font-weight:700;color:var(--text-heading);"><?= (int)$s['quantity'] ?></td>
                    <td style="color:var(--text-muted);"><?= esc($s['reason'] ?? '—') ?></td>
                    <td style="color:var(--text-muted);font-size:.78rem;"><?= esc($s['performed_by_name'] ?? '—') ?></td>
                    <td style="font-size:.75rem;color:var(--text-muted);"><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
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
let currentTxFilter = 'all';
function filterTx(q){ applyTxFilters(q, currentTxFilter); }
function setTxFilter(btn, f){ currentTxFilter = f; document.querySelectorAll('.pill-tab').forEach(b => b.classList.remove('active')); btn.classList.add('active'); applyTxFilters(document.getElementById('txSearch').value, f); }
function applyTxFilters(q, f){
    document.querySelectorAll('#txTbody tr[data-type]').forEach(r => {
        const mQ = !q || r.dataset.search.includes(q.toLowerCase());
        const mF = f === 'all' || r.dataset.type === f;
        r.style.display = mQ && mF ? '' : 'none';
    });
}
</script>
