<div class="alert-box alert-info">
    <h2 style="margin-top: 0;">📊 <?= $reportType === 'daily' ? 'Daily' : 'Weekly' ?> Inventory Report</h2>
    <p>Report Period: <strong><?= $startDate ?></strong> to <strong><?= $endDate ?></strong></p>
</div>

<h3>📈 Summary Statistics:</h3>
<table>
    <tr>
        <th>Total Medicines:</th>
        <td><strong><?= number_format($stats['total_medicines']) ?></strong></td>
    </tr>
    <tr>
        <th>Active Batches:</th>
        <td><strong><?= number_format($stats['active_batches']) ?></strong></td>
    </tr>
    <tr>
        <th>Total Stock Value:</th>
        <td><strong>$<?= number_format($stats['total_value'], 2) ?></strong></td>
    </tr>
    <tr>
        <th>Stock Transactions:</th>
        <td><?= number_format($stats['transactions']) ?></td>
    </tr>
    <tr>
        <th>Stock In:</th>
        <td style="color: #28a745;">+<?= number_format($stats['stock_in']) ?> units</td>
    </tr>
    <tr>
        <th>Stock Out:</th>
        <td style="color: #dc3545;">-<?= number_format($stats['stock_out']) ?> units</td>
    </tr>
</table>

<?php if (!empty($expiringBatches)): ?>
<h3>⚠️ Expiring Soon (Next 30 Days):</h3>
<table>
    <thead>
        <tr>
            <th>Medicine</th>
            <th>Batch</th>
            <th>Expiry Date</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (array_slice($expiringBatches, 0, 5) as $batch): ?>
        <tr>
            <td><?= htmlspecialchars($batch['medicine_name']) ?></td>
            <td><?= htmlspecialchars($batch['batch_number']) ?></td>
            <td><?= date('M d, Y', strtotime($batch['expiry_date'])) ?></td>
            <td><?= number_format($batch['current_quantity']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php if (count($expiringBatches) > 5): ?>
    <p><em>... and <?= count($expiringBatches) - 5 ?> more</em></p>
<?php endif; ?>
<?php endif; ?>

<?php if (!empty($lowStockMedicines)): ?>
<h3>🔴 Low Stock Medicines:</h3>
<table>
    <thead>
        <tr>
            <th>Medicine</th>
            <th>Category</th>
            <th>Current Stock</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (array_slice($lowStockMedicines, 0, 5) as $medicine): ?>
        <tr>
            <td><?= htmlspecialchars($medicine['name']) ?></td>
            <td><?= htmlspecialchars($medicine['category']) ?></td>
            <td><?= number_format($medicine['total_stock']) ?></td>
            <td>
                <?php if ($medicine['total_stock'] == 0): ?>
                    <span style="color: #dc3545;">OUT OF STOCK</span>
                <?php else: ?>
                    <span style="color: #ffc107;">LOW</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php if (count($lowStockMedicines) > 5): ?>
    <p><em>... and <?= count($lowStockMedicines) - 5 ?> more</em></p>
<?php endif; ?>
<?php endif; ?>

<h3>📦 Top 5 Most Active Medicines:</h3>
<table>
    <thead>
        <tr>
            <th>Medicine</th>
            <th>Transactions</th>
            <th>Total Movement</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($topMedicines as $medicine): ?>
        <tr>
            <td><?= htmlspecialchars($medicine['name']) ?></td>
            <td><?= number_format($medicine['transaction_count']) ?></td>
            <td><?= number_format($medicine['total_quantity']) ?> units</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="<?= $_ENV['APP_URL'] ?>/dashboard" class="btn">View Full Dashboard</a>

<div style="margin-top: 20px; font-size: 12px; color: #6c757d;">
    <p>This is an automated <?= $reportType ?> report. Next report will be sent on <?= $nextReportDate ?>.</p>
</div>
