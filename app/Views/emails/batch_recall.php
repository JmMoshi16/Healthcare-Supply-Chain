<div class="alert-box alert-danger">
    <h2 style="margin-top: 0;">🚨 URGENT: Batch Recall Notice</h2>
    <p><strong>IMMEDIATE ACTION REQUIRED</strong></p>
    <p>The following batch has been marked for recall. Please take immediate action to remove it from circulation.</p>
</div>

<h3>Recalled Batch Details:</h3>
<table>
    <tr>
        <th>Medicine Name:</th>
        <td><strong><?= htmlspecialchars($batch['medicine_name']) ?></strong></td>
    </tr>
    <tr>
        <th>Batch Number:</th>
        <td><strong style="color: #dc3545;"><?= htmlspecialchars($batch['batch_number']) ?></strong></td>
    </tr>
    <tr>
        <th>Supplier:</th>
        <td><?= htmlspecialchars($batch['supplier'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <th>Manufacturing Date:</th>
        <td><?= date('M d, Y', strtotime($batch['manufacturing_date'])) ?></td>
    </tr>
    <tr>
        <th>Expiry Date:</th>
        <td><?= date('M d, Y', strtotime($batch['expiry_date'])) ?></td>
    </tr>
    <tr>
        <th>Current Quantity:</th>
        <td><strong><?= number_format($batch['current_quantity']) ?> units</strong></td>
    </tr>
    <tr>
        <th>Recall Reason:</th>
        <td><?= htmlspecialchars($reason ?? 'Quality control issue') ?></td>
    </tr>
</table>

<div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 6px;">
    <h4 style="margin-top: 0;">⚠️ Required Actions:</h4>
    <ol>
        <li><strong>Immediately quarantine</strong> all units of this batch</li>
        <li><strong>Stop distribution</strong> to all departments</li>
        <li><strong>Notify all staff</strong> who may have received this batch</li>
        <li><strong>Document</strong> all actions taken</li>
        <li><strong>Contact supplier</strong> for return/disposal instructions</li>
        <li><strong>Update inventory</strong> system to reflect recall status</li>
    </ol>
</div>

<div style="margin-top: 20px;">
    <p><strong>Recall Initiated By:</strong> <?= htmlspecialchars($initiated_by ?? 'System Administrator') ?></p>
    <p><strong>Recall Date:</strong> <?= date('M d, Y H:i:s') ?></p>
</div>

<a href="<?= $_ENV['APP_URL'] ?>/batches/<?= $batch['id'] ?>" class="btn" style="background: #dc3545;">View Batch Details</a>
