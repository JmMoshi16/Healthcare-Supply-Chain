<?php use App\Core\View; View::layout('app'); $title = 'New Stock Transaction'; ?>

<a href="/stocks" class="back-link"><i class="bi bi-arrow-left"></i> Back to Transactions</a>

<div class="page-header" style="margin-bottom:1.75rem;">
    <div>
        <h1 class="page-title">Record Transaction</h1>
        <p class="page-subtitle">Select a batch, choose transaction type, and enter quantity</p>
    </div>
</div>

<?php if ($errors = flash('errors')): ?>
<div class="pg-alert pg-alert-error" style="margin-bottom:1.25rem;" id="alertBox">
    <i class="bi bi-exclamation-circle-fill"></i>
    <div><?php foreach ($errors as $msgs): foreach ($msgs as $msg): ?><div><?= esc($msg) ?></div><?php endforeach; endforeach; ?></div>
</div>
<?php endif; ?>

<div class="pf-form-layout">

    <!-- LEFT: Form -->
    <div class="pf-form-main">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Transaction Details</span>
                <span style="font-size:.72rem;color:var(--text-muted);">* Required fields</span>
            </div>
            <div style="padding:1.5rem;">
                <form method="POST" action="/stocks" id="txForm">
                    <?= csrf_field() ?>

                    <!-- Step 1: Batch -->
                    <div class="sl-form-step">
                        <div class="sl-step-num">1</div>
                        <div class="sl-step-body">
                            <div class="form-group">
                                <label class="form-label">Select Batch <span class="pf-required">*</span></label>
                                <div class="sl-select-wrap">
                                    <i class="bi bi-box-seam sl-select-icon"></i>
                                    <select name="batch_id" id="batchSelect" class="form-input sl-has-icon" required>
                                        <option value="">Choose a batch...</option>
                                        <?php foreach ($batches as $b): ?>
                                        <option value="<?= (int)$b['id'] ?>"
                                            data-stock="<?= (int)$b['current_quantity'] ?>"
                                            <?= old('batch_id') == $b['id'] ? 'selected' : '' ?>>
                                            <?= esc($b['batch_number']) ?> (Stock: <?= (int)$b['current_quantity'] ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div id="stockInfo" class="form-hint" style="margin-top:.4rem;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Type -->
                    <div class="sl-form-step">
                        <div class="sl-step-num">2</div>
                        <div class="sl-step-body">
                            <div class="form-group">
                                <label class="form-label">Transaction Type <span class="pf-required">*</span></label>
                                <div class="sl-variant-chips" style="margin-top:.5rem;">
                                    <div class="sl-variant-chip" onclick="selectType('in')">📥 Stock In</div>
                                    <div class="sl-variant-chip" onclick="selectType('out')">📤 Stock Out</div>
                                    <div class="sl-variant-chip" onclick="selectType('adjustment')">🔧 Adjustment</div>
                                </div>
                                <div class="sl-select-wrap" style="margin-top:.75rem;">
                                    <i class="bi bi-arrow-left-right sl-select-icon"></i>
                                    <select name="transaction_type" id="typeSelect" class="form-input sl-has-icon" required onchange="syncTypeChips(this.value)">
                                        <option value="">Select type...</option>
                                        <option value="in" <?= old('transaction_type') === 'in' ? 'selected' : '' ?>>Stock In</option>
                                        <option value="out" <?= old('transaction_type') === 'out' ? 'selected' : '' ?>>Stock Out</option>
                                        <option value="adjustment" <?= old('transaction_type') === 'adjustment' ? 'selected' : '' ?>>Adjustment</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Quantity + Reason -->
                    <div class="sl-form-step" style="border-bottom:none;margin-bottom:0;padding-bottom:0;">
                        <div class="sl-step-num">3</div>
                        <div class="sl-step-body">
                            <div class="pf-row">
                                <div class="pf-col-6">
                                    <div class="form-group">
                                        <label class="form-label">Quantity <span class="pf-required">*</span></label>
                                        <div class="sl-qty-wrap">
                                            <button type="button" class="sl-qty-btn" id="qtyMinus"><i class="bi bi-dash"></i></button>
                                            <input type="number" name="quantity" id="quantityInput" class="form-input sl-qty-input" value="<?= esc(old('quantity', 1)) ?>" min="1" required>
                                            <button type="button" class="sl-qty-btn" id="qtyPlus"><i class="bi bi-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="pf-col-6">
                                    <div class="form-group">
                                        <label class="form-label">Reason</label>
                                        <input type="text" name="reason" class="form-input" placeholder="e.g. Received from supplier" value="<?= esc(old('reason')) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sl-submit-row">
                        <div>
                            <div class="sl-total-label">Current Stock</div>
                            <div class="sl-total-value" id="currentStock" style="color:var(--text-heading);">—</div>
                        </div>
                        <div style="display:flex;gap:.75rem;align-items:center;">
                            <a href="/stocks" class="btn btn-ghost">Cancel</a>
                            <button type="submit" class="btn-create-order" style="padding:.7rem 1.5rem;font-size:.9rem;">
                                <i class="bi bi-check-lg"></i> Record Transaction
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT: Summary + Tips -->
    <div class="pf-form-side">
        <div class="card sl-summary-card">
            <div class="card-header">
                <span class="card-title">Transaction Summary</span>
                <i class="bi bi-arrow-left-right" style="color:var(--accent-orange);"></i>
            </div>
            <div style="padding:1.25rem;">
                <div class="sl-summary-item">
                    <span class="sl-summary-label">Batch</span>
                    <span class="sl-summary-val" id="sumBatch">—</span>
                </div>
                <div class="sl-summary-item">
                    <span class="sl-summary-label">Type</span>
                    <span class="sl-summary-val" id="sumType">—</span>
                </div>
                <div class="sl-summary-item">
                    <span class="sl-summary-label">Quantity</span>
                    <span class="sl-summary-val" id="sumQty">—</span>
                </div>
                <div class="sl-summary-divider"></div>
                <div class="sl-summary-item sl-summary-total">
                    <span>Current Stock</span>
                    <span id="sumStock">—</span>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top:1rem;">
            <div class="card-header">
                <span class="card-title">Transaction Guide</span>
                <i class="bi bi-info-circle" style="color:var(--text-muted);"></i>
            </div>
            <div style="padding:1.25rem;">
                <ul class="pf-tips-list">
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Stock In — adds to current quantity</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Stock Out — deducts from current quantity</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Adjustment — corrects stock discrepancies</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> All transactions are logged with your user ID</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Cannot exceed available stock on Stock Out</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
const batchSelect = document.getElementById('batchSelect');
const typeSelect  = document.getElementById('typeSelect');
const qtyInput    = document.getElementById('quantityInput');

batchSelect.addEventListener('change', function(){
    const opt = this.selectedOptions[0];
    const stock = opt?.dataset.stock || '—';
    document.getElementById('stockInfo').textContent = opt?.value ? `Current stock: ${stock} units` : '';
    document.getElementById('currentStock').textContent = opt?.value ? stock : '—';
    document.getElementById('sumBatch').textContent = opt?.value ? opt.textContent.split('(')[0].trim() : '—';
    document.getElementById('sumStock').textContent = opt?.value ? stock : '—';
});

typeSelect.addEventListener('change', function(){ syncTypeChips(this.value); updateSummary(); });
qtyInput.addEventListener('input', updateSummary);

function syncTypeChips(val){
    document.querySelectorAll('.sl-variant-chip').forEach(c => c.classList.toggle('active', c.textContent.toLowerCase().includes(val)));
}
function selectType(val){
    typeSelect.value = val;
    syncTypeChips(val);
    updateSummary();
}
function updateSummary(){
    document.getElementById('sumType').textContent = typeSelect.value || '—';
    document.getElementById('sumQty').textContent  = qtyInput.value || '—';
}

document.getElementById('qtyMinus').addEventListener('click', () => { const v = parseInt(qtyInput.value)||1; if(v>1){ qtyInput.value=v-1; updateSummary(); } });
document.getElementById('qtyPlus').addEventListener('click',  () => { qtyInput.value = (parseInt(qtyInput.value)||1)+1; updateSummary(); });
</script>
