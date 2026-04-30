<?php use App\Core\View; View::layout('app'); $title = 'Batch Details'; ?>

<a href="/batches" class="back-link"><i class="bi bi-arrow-left"></i> Back to Batches</a>

<div class="page-header" style="margin-bottom:1.75rem;">
    <div>
        <h1 class="page-title">Batch #<?= esc($batch['batch_number']) ?></h1>
        <p class="page-subtitle">Batch information and stock details</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Batch Details</span>
        <span class="badge badge-<?= $batch['status'] === 'active' ? 'success' : 'danger' ?>"><?= esc($batch['status']) ?></span>
    </div>
    <div style="padding:1.5rem;">
        <div class="table-wrapper">
            <table class="smro-table">
                <tr>
                    <th>Medicine ID:</th>
                    <td><?= esc($batch['medicine_id']) ?></td>
                </tr>
                <tr>
                    <th>Batch Number:</th>
                    <td><?= esc($batch['batch_number']) ?></td>
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
                    <th>Supplier:</th>
                    <td><?= esc($batch['supplier'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Purchase Price:</th>
                    <td>₱<?= number_format($batch['purchase_price'], 2) ?></td>
                </tr>
                <tr>
                    <th>Selling Price:</th>
                    <td>₱<?= number_format($batch['selling_price'], 2) ?></td>
                </tr>
                <tr>
                    <th>Initial Quantity:</th>
                    <td><?= esc($batch['initial_quantity']) ?></td>
                </tr>
                <tr>
                    <th>Current Quantity:</th>
                    <td><?= esc($batch['current_quantity']) ?></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td><span class="badge badge-<?= $batch['status'] === 'active' ? 'success' : 'danger' ?>"><?= esc($batch['status']) ?></span></td>
                </tr>
            </table>
        </div>
    </div>
</div>
