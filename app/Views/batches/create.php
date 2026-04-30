<?php use App\Core\View; View::layout('app'); $title = 'Add Batch'; ?>

<a href="/batches" class="back-link"><i class="bi bi-arrow-left"></i> Back to Batches</a>

<div class="page-header" style="margin-bottom:1.75rem;">
    <div>
        <h1 class="page-title">Add New Batch</h1>
        <p class="page-subtitle">Track a new medicine batch with expiry and pricing details</p>
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
                <span class="card-title">Batch Information</span>
                <span style="font-size:.72rem;color:var(--text-muted);">* Required fields</span>
            </div>
            <div style="padding:1.5rem;">
                <form method="POST" action="/batches">
                    <?= csrf_field() ?>

                    <!-- Step 1: Medicine -->
                    <div class="sl-form-step">
                        <div class="sl-step-num">1</div>
                        <div class="sl-step-body">
                            <div class="form-group">
                                <label class="form-label">Medicine <span class="pf-required">*</span></label>
                                <div class="sl-select-wrap">
                                    <i class="bi bi-capsule sl-select-icon"></i>
                                    <select name="medicine_id" class="form-input sl-has-icon" required>
                                        <option value="">Choose a medicine...</option>
                                        <?php foreach ($medicines as $med): ?>
                                        <option value="<?= (int)$med['id'] ?>" <?= old('medicine_id') == $med['id'] ? 'selected' : '' ?>><?= esc($med['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Batch details -->
                    <div class="sl-form-step">
                        <div class="sl-step-num">2</div>
                        <div class="sl-step-body">
                            <div class="pf-row">
                                <div class="pf-col-6">
                                    <div class="form-group">
                                        <label class="form-label">Batch Number <span class="pf-required">*</span></label>
                                        <input type="text" name="batch_number" class="form-input" placeholder="e.g. BATCH-2025-001" value="<?= esc(old('batch_number')) ?>" required>
                                    </div>
                                </div>
                                <div class="pf-col-6">
                                    <div class="form-group">
                                        <label class="form-label">Supplier</label>
                                        <input type="text" name="supplier" class="form-input" placeholder="e.g. PharmaCorp" value="<?= esc(old('supplier')) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="pf-row">
                                <div class="pf-col-6">
                                    <div class="form-group">
                                        <label class="form-label">Manufacturing Date <span class="pf-required">*</span></label>
                                        <input type="date" name="manufacturing_date" class="form-input" value="<?= esc(old('manufacturing_date')) ?>" required>
                                    </div>
                                </div>
                                <div class="pf-col-6">
                                    <div class="form-group">
                                        <label class="form-label">Expiry Date <span class="pf-required">*</span></label>
                                        <input type="date" name="expiry_date" class="form-input" value="<?= esc(old('expiry_date')) ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Pricing & Quantity -->
                    <div class="sl-form-step" style="border-bottom:none;margin-bottom:0;padding-bottom:0;">
                        <div class="sl-step-num">3</div>
                        <div class="sl-step-body">
                            <div class="pf-row">
                                <div class="pf-col-4">
                                    <div class="form-group">
                                        <label class="form-label">Purchase Price <span class="pf-required">*</span></label>
                                        <div class="pf-input-prefix">
                                            <span class="pf-prefix-symbol">₱</span>
                                            <input type="number" step="0.01" name="purchase_price" class="form-input pf-has-prefix" placeholder="0.00" value="<?= esc(old('purchase_price')) ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="pf-col-4">
                                    <div class="form-group">
                                        <label class="form-label">Selling Price <span class="pf-required">*</span></label>
                                        <div class="pf-input-prefix">
                                            <span class="pf-prefix-symbol">₱</span>
                                            <input type="number" step="0.01" name="selling_price" class="form-input pf-has-prefix" placeholder="0.00" value="<?= esc(old('selling_price')) ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="pf-col-4">
                                    <div class="form-group">
                                        <label class="form-label">Initial Quantity <span class="pf-required">*</span></label>
                                        <div class="sl-qty-wrap">
                                            <button type="button" class="sl-qty-btn" onclick="adjustQty(-1)"><i class="bi bi-dash"></i></button>
                                            <input type="number" name="initial_quantity" id="qtyInput" class="form-input sl-qty-input" value="<?= esc(old('initial_quantity', 1)) ?>" min="1" required>
                                            <button type="button" class="sl-qty-btn" onclick="adjustQty(1)"><i class="bi bi-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pf-form-actions">
                        <button type="submit" class="btn-create-order"><i class="bi bi-check-lg"></i> Save Batch</button>
                        <a href="/batches" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT: Tips -->
    <div class="pf-form-side">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Batch Tips</span>
                <i class="bi bi-lightbulb" style="color:var(--accent-orange);"></i>
            </div>
            <div style="padding:1.25rem;">
                <ul class="pf-tips-list">
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Batch numbers must be unique per medicine</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Expiry alerts trigger 30 days before expiry</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Initial quantity sets the opening stock</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Use stock transactions to adjust later</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Selling price is used for stock-out calculations</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function adjustQty(delta){ const i = document.getElementById('qtyInput'); const v = parseInt(i.value)||1; if(v+delta >= 1) i.value = v+delta; }
</script>
