<?php use App\Core\View; View::layout('app'); $title = 'Stock Transactions'; ?>

<?php
$f        = $filters ?? [];
$sort     = $_GET['sort'] ?? 'created_at';
$dir      = $_GET['dir']  ?? 'DESC';
$nextDir  = $dir === 'DESC' ? 'ASC' : 'DESC';
$total    = $pagination['total'] ?? 0;
$inCount  = count(array_filter($data ?? [], fn($t) => $t['transaction_type'] === 'in'));
$outCount = count(array_filter($data ?? [], fn($t) => $t['transaction_type'] === 'out'));
$adjCount = count(array_filter($data ?? [], fn($t) => $t['transaction_type'] === 'adjustment'));

function sortLink(string $col, string $label, string $currentSort, string $currentDir): string {
    $d = ($currentSort === $col && $currentDir === 'DESC') ? 'ASC' : 'DESC';
    $icon = $currentSort === $col ? ($currentDir === 'ASC' ? '↑' : '↓') : '↕';
    $q = array_merge($_GET, ['sort' => $col, 'dir' => $d, 'page' => 1]);
    return '<a href="?' . http_build_query($q) . '" style="color:inherit;text-decoration:none;">' . $label . ' <span style="opacity:.5;font-size:.7rem;">' . $icon . '</span></a>';
}
?>

<div class="sl-header">
    <div>
        <h1 class="sl-title">Stock Transactions</h1>
        <p class="sl-subtitle">Full audit trail of all stock movements — <?= number_format($total) ?> total records</p>
    </div>
    <a href="/stocks/create" class="btn-create-order"><i class="bi bi-plus-lg"></i> New Transaction</a>
</div>

<!-- Stat counters -->
<div class="sl-stats-row" style="margin-bottom:1.25rem;">
    <div class="sl-balance-card">
        <div class="sl-balance-top">
            <div>
                <div class="sl-card-label">Total Transactions</div>
                <div class="sl-balance-value"><?= number_format($total) ?></div>
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
            <div class="sl-mini-value"><?= count($data ?? []) ?></div>
            <div class="sl-mini-change up"><i class="bi bi-list-ul"></i> records</div>
        </div>
    </div>
</div>

<!-- Filter Panel -->
<form method="GET" action="/stocks" id="filterForm">
<div class="card" style="margin-bottom:1rem;padding:1.25rem;">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.75rem;align-items:end;">
        <div>
            <label style="font-size:.72rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:.3rem;">Search</label>
            <div style="position:relative;">
                <i class="bi bi-search" style="position:absolute;left:.65rem;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.8rem;"></i>
                <input type="text" name="search" value="<?= esc($f['search'] ?? '') ?>" placeholder="Batch, reason, recipient…" class="form-input" style="padding-left:2rem;height:36px;font-size:.82rem;">
            </div>
        </div>
        <div>
            <label style="font-size:.72rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:.3rem;">Type</label>
            <select name="type" class="form-input" style="height:36px;font-size:.82rem;">
                <option value="">All Types</option>
                <option value="in"         <?= ($f['type'] ?? '') === 'in'         ? 'selected' : '' ?>>Stock In</option>
                <option value="out"        <?= ($f['type'] ?? '') === 'out'        ? 'selected' : '' ?>>Stock Out</option>
                <option value="adjustment" <?= ($f['type'] ?? '') === 'adjustment' ? 'selected' : '' ?>>Adjustment</option>
            </select>
        </div>
        <div>
            <label style="font-size:.72rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:.3rem;">Reason Code</label>
            <select name="reason_code" class="form-input" style="height:36px;font-size:.82rem;">
                <option value="">All Reasons</option>
                <?php foreach (['purchase','dispensing','return','damaged','expired','adjustment','transfer'] as $rc): ?>
                <option value="<?= $rc ?>" <?= ($f['reason_code'] ?? '') === $rc ? 'selected' : '' ?>><?= ucfirst($rc) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="font-size:.72rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:.3rem;">Ward / Dept</label>
            <input type="text" name="ward" value="<?= esc($f['ward'] ?? '') ?>" placeholder="e.g. ICU, Pharmacy…" class="form-input" style="height:36px;font-size:.82rem;">
        </div>
        <div>
            <label style="font-size:.72rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:.3rem;">Performed By</label>
            <select name="performed_by" class="form-input" style="height:36px;font-size:.82rem;">
                <option value="">All Users</option>
                <?php foreach ($users ?? [] as $u): ?>
                <option value="<?= (int)$u['id'] ?>" <?= ($f['performed_by'] ?? '') == $u['id'] ? 'selected' : '' ?>><?= esc($u['fullname']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="font-size:.72rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:.3rem;">Date From</label>
            <input type="date" name="date_from" value="<?= esc($f['date_from'] ?? '') ?>" class="form-input" style="height:36px;font-size:.82rem;">
        </div>
        <div>
            <label style="font-size:.72rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:.3rem;">Date To</label>
            <input type="date" name="date_to" value="<?= esc($f['date_to'] ?? '') ?>" class="form-input" style="height:36px;font-size:.82rem;">
        </div>
        <div style="display:flex;gap:.5rem;">
            <button type="submit" class="btn-create-order" style="height:36px;padding:0 1rem;font-size:.82rem;flex:1;"><i class="bi bi-funnel"></i> Filter</button>
            <a href="/stocks" class="btn btn-ghost" style="height:36px;padding:0 .75rem;font-size:.82rem;display:flex;align-items:center;"><i class="bi bi-x-lg"></i></a>
        </div>
    </div>
    <input type="hidden" name="sort" value="<?= esc($sort) ?>">
    <input type="hidden" name="dir"  value="<?= esc($dir) ?>">
</div>
</form>

<div class="card">
    <div class="table-wrapper">
        <table class="smro-table">
            <thead>
                <tr>
                    <th><?= sortLink('id', 'ID', $sort, $dir) ?></th>
                    <th>Medicine / Batch</th>
                    <th><?= sortLink('transaction_type', 'Type', $sort, $dir) ?></th>
                    <th><?= sortLink('reason_code', 'Reason Code', $sort, $dir) ?></th>
                    <th><?= sortLink('quantity', 'Qty', $sort, $dir) ?></th>
                    <th>Recipient / Ward</th>
                    <th>Ref #</th>
                    <th>Notes</th>
                    <th>By</th>
                    <th><?= sortLink('created_at', 'Date', $sort, $dir) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                <tr>
                    <td colspan="10" style="text-align:center;padding:3rem;color:var(--text-muted);">
                        <i class="bi bi-arrow-left-right" style="font-size:1.75rem;display:block;margin-bottom:.6rem;opacity:.25;"></i>
                        No transactions match your filters.
                        <a href="/stocks" style="color:var(--accent-orange);font-weight:600;text-decoration:none;display:block;margin-top:.4rem;">Clear filters →</a>
                    </td>
                </tr>
                <?php else: foreach ($data as $s):
                    $typeClass = $s['transaction_type'] === 'in' ? 'sl-badge-green' : ($s['transaction_type'] === 'out' ? 'sl-badge-red' : 'sl-badge-orange');
                    $rcColors  = ['purchase'=>'#dcfce7','dispensing'=>'#fee2e2','return'=>'#fef9c3','damaged'=>'#fce7f3','expired'=>'#f3f4f6','adjustment'=>'#e0f2fe','transfer'=>'#ede9fe'];
                    $rcColor   = $rcColors[$s['reason_code'] ?? ''] ?? '#f3f4f6';
                ?>
                <tr>
                    <td style="font-family:monospace;font-size:.75rem;color:var(--text-muted);"><?= sprintf('%04d', $s['id']) ?></td>
                    <td>
                        <div style="font-weight:600;color:var(--text-heading);font-size:.82rem;"><?= esc($s['medicine_name']) ?></div>
                        <div style="font-size:.72rem;color:var(--text-muted);"><?= esc($s['batch_number']) ?></div>
                    </td>
                    <td><span class="sl-status-badge <?= $typeClass ?>"><?= strtoupper($s['transaction_type']) ?></span></td>
                    <td>
                        <?php if ($s['reason_code']): ?>
                        <span style="background:<?= $rcColor ?>;padding:.2rem .55rem;border-radius:999px;font-size:.72rem;font-weight:600;"><?= ucfirst($s['reason_code']) ?></span>
                        <?php else: ?>
                        <span style="color:var(--text-muted);font-size:.75rem;">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:700;color:var(--text-heading);"><?= number_format((int)$s['quantity']) ?></td>
                    <td>
                        <div style="font-size:.82rem;"><?= esc($s['recipient'] ?? '—') ?></div>
                        <?php if ($s['ward_department']): ?>
                        <div style="font-size:.72rem;color:var(--text-muted);"><?= esc($s['ward_department']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="font-family:monospace;font-size:.75rem;color:var(--text-muted);"><?= esc($s['reference_number'] ?? '—') ?></td>
                    <td style="color:var(--text-muted);font-size:.78rem;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= esc($s['reason'] ?? '') ?>"><?= esc($s['reason'] ?? '—') ?></td>
                    <td style="color:var(--text-muted);font-size:.78rem;"><?= esc($s['performed_by_name'] ?? '—') ?></td>
                    <td style="font-size:.75rem;color:var(--text-muted);white-space:nowrap;"><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
    <div style="padding:.85rem 1.25rem;border-top:1px solid var(--border);">
        <div class="pager-wrap" style="padding:0;">
            <?php
            $baseQuery = array_merge($_GET, ['sort' => $sort, 'dir' => $dir]);
            for ($i = 1; $i <= $pagination['total_pages']; $i++):
                $q = array_merge($baseQuery, ['page' => $i]);
            ?>
            <a href="?<?= http_build_query($q) ?>" class="page-link <?= $i === $pagination['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
