<?php use App\Core\View; View::layout('app'); $title = 'Edit Batch'; ?>

<a href="/batches" class="back-link"><i class="bi bi-arrow-left"></i> Back to Batches</a>

<div class="page-header" style="margin-bottom:1.75rem;">
    <div>
        <h1 class="page-title">Edit Batch: <?= esc($batch['batch_number']) ?></h1>
        <p class="page-subtitle">Update batch details, pricing and status</p>
    </div>
</div>

<?php if ($errors = flash('errors')): ?>
<div class="pg-alert pg-alert-error" style="margin-bottom:1.25rem;">
    <i class="bi bi-exclamation-circle-fill"></i>
    <div><?php foreach ($errors as $msgs): foreach ($msgs as $msg): ?><div><?= esc($msg) ?></div><?php endforeach; endforeach; ?></div>
</div>
<?php endif; ?>

<div class="pf-form-layout">
    <div class="pf-form-main">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Batch Details</span>
                <span class="badge badge-<?= $batch['status'] === 'active' ? 'success' : 'danger' ?>"><?= esc($batch['status']) ?></span>
            </div>
            <div style="padding:1.5rem;">
                <form method="POST" action="/batches/<?= (int)$batch['id'] ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">

                    <div class="pf-row">
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Medicine <span class="pf-required">*</span></label>
                                <select name="medicine_id" class="form-input" required>
                                    <?php foreach ($medicines as $med): ?>
                                    <option value="<?= (int)$med['id'] ?>" <?= $batch['medicine_id'] == $med['id'] ? 'selected' : '' ?>><?= esc($med['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Batch Number <span class="pf-required">*</span></label>
                                <input type="text" name="batch_number" class="form-input" value="<?= esc($batch['batch_number']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="pf-row">
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Manufacturing Date <span class="pf-required">*</span></label>
                                <input type="date" name="manufacturing_date" class="form-input" value="<?= esc($batch['manufacturing_date']) ?>" required>
                            </div>
                        </div>
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Expiry Date <span class="pf-required">*</span></label>
                                <input type="date" name="expiry_date" class="form-input" value="<?= esc($batch['expiry_date']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="pf-row">
                        <div class="pf-col-4">
                            <div class="form-group">
                                <label class="form-label">Purchase Price <span class="pf-required">*</span></label>
                                <div class="pf-input-prefix">
                                    <span class="pf-prefix-symbol">₱</span>
                                    <input type="number" step="0.01" name="purchase_price" class="form-input pf-has-prefix" value="<?= esc($batch['purchase_price']) ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="pf-col-4">
                            <div class="form-group">
                                <label class="form-label">Selling Price <span class="pf-required">*</span></label>
                                <div class="pf-input-prefix">
                                    <span class="pf-prefix-symbol">₱</span>
                                    <input type="number" step="0.01" name="selling_price" class="form-input pf-has-prefix" value="<?= esc($batch['selling_price']) ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="pf-col-4">
                            <div class="form-group">
                                <label class="form-label">Supplier</label>
                                <input type="text" name="supplier" class="form-input" value="<?= esc($batch['supplier'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input">
                            <?php foreach (['active','expired','recalled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $batch['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="pf-form-actions">
                        <button type="submit" class="btn-create-order"><i class="bi bi-check-lg"></i> Update Batch</button>
                        <a href="/batches" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="pf-form-side">
        <div class="card">
            <div class="card-header"><span class="card-title">Batch Info</span></div>
            <div style="padding:1.25rem;">
                <div class="pshow-meta-grid">
                    <div class="pshow-meta-item">
                        <div class="pshow-meta-label">Current Stock</div>
                        <div class="pshow-meta-value"><?= (int)$batch['current_quantity'] ?> units</div>
                    </div>
                    <div class="pshow-meta-item">
                        <div class="pshow-meta-label">Initial Qty</div>
                        <div class="pshow-meta-value"><?= (int)$batch['initial_quantity'] ?> units</div>
                    </div>
                </div>
                <div style="margin-top:1rem;">
                    <a href="/stocks/create" class="btn-create-order" style="width:100%;justify-content:center;font-size:.8rem;">
                        <i class="bi bi-arrow-left-right"></i> Record Transaction
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
