<div class="alert-box alert-warning">
    <h2 style="margin-top: 0;">⚠️ Medicine Expiry Alert</h2>
    <p><strong><?= count($batches) ?></strong> medicine batch(es) are expiring soon and require your attention.</p>
</div>

<h3>Expiring Batches:</h3>
<table>
    <thead>
        <tr>
            <th>Medicine</th>
            <th>Batch Number</th>
            <th>Expiry Date</th>
            <th>Quantity</th>
            <th>Days Left</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($batches as $batch): ?>
        <tr>
            <td><strong><?= htmlspecialchars($batch['medicine_name']) ?></strong></td>
            <td><?= htmlspecialchars($batch['batch_number']) ?></td>
            <td><?= date('M d, Y', strtotime($batch['expiry_date'])) ?></td>
            <td><?= number_format($batch['current_quantity']) ?> units</td>
            <td>
                <?php 
                $daysLeft = floor((strtotime($batch['expiry_date']) - time()) / 86400);
                $color = $daysLeft <= 7 ? '#dc3545' : ($daysLeft <= 15 ? '#ffc107' : '#fd7e14');
                ?>
                <span style="color: <?= $color ?>; font-weight: bold;">
                    <?= $daysLeft ?> days
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="margin-top: 20px;">
    <h4>Recommended Actions:</h4>
    <ul>
        <li>Review and prioritize distribution of expiring medicines</li>
        <li>Contact suppliers for replacement stock</li>
        <li>Update inventory records</li>
        <li>Notify relevant departments</li>
    </ul>
</div>

<a href="<?= $_ENV['APP_URL'] ?>/batches" class="btn">View All Batches</a>
