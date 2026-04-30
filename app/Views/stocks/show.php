<?php require_once __DIR__ . '/../layouts/app.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Stock Transaction Details</h1>
        <a href="<?= base_url('stocks') ?>" class="btn btn-secondary">Back to Transactions</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Transaction #<?= esc($stock['id']) ?></h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Batch ID:</th>
                    <td><?= esc($stock['batch_id']) ?></td>
                </tr>
                <tr>
                    <th>Transaction Type:</th>
                    <td><span class="badge badge-<?= $stock['transaction_type'] === 'in' ? 'success' : 'warning' ?>"><?= strtoupper(esc($stock['transaction_type'])) ?></span></td>
                </tr>
                <tr>
                    <th>Quantity:</th>
                    <td><?= esc($stock['quantity']) ?></td>
                </tr>
                <tr>
                    <th>Reason:</th>
                    <td><?= esc($stock['reason'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Performed By:</th>
                    <td><?= esc($stock['performed_by']) ?></td>
                </tr>
                <tr>
                    <th>Date:</th>
                    <td><?= format_date($stock['created_at'], 'M d, Y H:i:s') ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
