<?php use App\Core\View; View::layout('app'); $title = 'Reorder List'; ?>

<div class="pg-header">
    <div class="pg-header-left">
        <h1 class="page-title">Reorder Management</h1>
        <p class="page-subtitle">Medicines below their minimum stock threshold.</p>
    </div>
    <div>
        <button class="btn-create-order" onclick="generatePO()"><i class="bi bi-file-earmark-text"></i> Generate Purchase Order</button>
    </div>
</div>

<div class="card" style="margin-top: 1.5rem;">
    <?php if (empty($lowStockItems)): ?>
        <div style="padding: 4rem; text-align: center;">
            <i class="bi bi-check-circle" style="font-size: 3rem; color: #10b981; margin-bottom: 1rem; display: inline-block;"></i>
            <h3 style="margin-bottom: 0.5rem; color: var(--text-heading);">Inventory is Healthy</h3>
            <p style="color: var(--text-muted);">No medicines are currently below their minimum stock levels.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="smro-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="selectAll" onchange="toggleAll(this)"></th>
                        <th>Medicine</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Min Stock</th>
                        <th>Deficit</th>
                        <th>Suggested Order</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lowStockItems as $item): 
                        $current = (int)$item['total_stock'];
                        $min = (int)$item['minimum_stock'];
                        $deficit = (int)$item['deficit'];
                        $suggested = (int)$item['suggested_order'];
                        
                        $isCritical = $current == 0;
                        $statusClass = $isCritical ? 'badge-danger' : 'badge-warning';
                        $statusText = $isCritical ? 'Critical (Out)' : 'Warning (Low)';
                    ?>
                    <tr>
                        <td><input type="checkbox" class="po-checkbox" value="<?= $item['id'] ?>"></td>
                        <td style="font-weight: 600; color: var(--text-heading);">
                            <a href="/medicines/<?= $item['id'] ?>" style="color: inherit; text-decoration: none;">
                                <?= esc($item['name']) ?>
                            </a>
                        </td>
                        <td style="color: var(--text-muted);"><?= esc($item['category']) ?></td>
                        <td>
                            <strong style="color: <?= $isCritical ? 'var(--danger)' : 'var(--warning-dark)' ?>;">
                                <?= number_format($current) ?>
                            </strong>
                        </td>
                        <td><?= number_format($min) ?></td>
                        <td>
                            <span style="color: var(--danger); font-weight: 600;">-<?= number_format($deficit) ?></span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="number" class="form-input" style="width: 80px; padding: 0.25rem 0.5rem; height: auto;" value="<?= $suggested ?>">
                                <span style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($item['unit']) ?>s</span>
                            </div>
                        </td>
                        <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleAll(source) {
    checkboxes = document.querySelectorAll('.po-checkbox');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}

function generatePO() {
    const selected = document.querySelectorAll('.po-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select at least one medicine to generate a purchase order.');
        return;
    }
    
    // In a real app, this would submit to a controller to generate a PDF or record.
    alert(`Generating Purchase Order for ${selected.length} items...\n(Mock action successful)`);
}
</script>
