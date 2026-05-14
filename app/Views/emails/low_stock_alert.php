<div class="alert-box alert-danger">
    <h2 style="margin-top: 0;">🔴 Low Stock Alert</h2>
    <p><strong><?= count($medicines) ?></strong> medicine(s) are running low on stock and need immediate restocking.</p>
</div>

<h3>Low Stock Medicines:</h3>
<table>
    <thead>
        <tr>
            <th>Medicine</th>
            <th>Category</th>
            <th>Current Stock</th>
            <th>Threshold</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($medicines as $medicine): ?>
        <tr>
            <td><strong><?= htmlspecialchars($medicine['name']) ?></strong></td>
            <td><?= htmlspecialchars($medicine['category']) ?></td>
            <td><?= number_format($medicine['total_stock']) ?> units</td>
            <td><?= $threshold ?> units</td>
            <td>
                <?php if ($medicine['total_stock'] == 0): ?>
                    <span style="color: #dc3545; font-weight: bold;">OUT OF STOCK</span>
                <?php else: ?>
                    <span style="color: #ffc107; font-weight: bold;">LOW STOCK</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="margin-top: 20px;">
    <h4>Immediate Actions Required:</h4>
    <ul>
        <li>Place urgent orders with suppliers</li>
        <li>Check alternative suppliers for faster delivery</li>
        <li>Notify pharmacy and medical staff</li>
        <li>Review consumption patterns</li>
    </ul>
</div>

<a href="<?= $_ENV['APP_URL'] ?>/medicines" class="btn">View Inventory</a>
