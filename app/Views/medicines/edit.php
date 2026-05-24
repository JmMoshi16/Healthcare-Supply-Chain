<?php use App\Core\View; View::layout('app'); $title = 'Edit Medicine'; ?>

<a href="/medicines" class="back-link"><i class="bi bi-arrow-left"></i> Back to Medicines</a>

<div class="page-header" style="margin-bottom:1.75rem;">
    <div>
        <h1 class="page-title">Edit: <?= esc($medicine['name']) ?></h1>
        <p class="page-subtitle">Update medicine details and manage batches</p>
    </div>
    <a href="/medicines/<?= (int)$medicine['id'] ?>" class="btn btn-ghost"><i class="bi bi-eye"></i> View</a>
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
                <span class="card-title">Medicine Details</span>
                <span class="badge badge-<?= $medicine['is_active'] ? 'success' : 'danger' ?>"><?= $medicine['is_active'] ? 'Active' : 'Inactive' ?></span>
            </div>
            <div style="padding:1.5rem;">
                <form method="POST" action="/medicines/<?= (int)$medicine['id'] ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">

                    <div class="pf-row">
                        <div class="pf-col-8">
                            <div class="form-group">
                                <label class="form-label">Medicine Name <span class="pf-required">*</span></label>
                                <input type="text" name="name" class="form-input" value="<?= esc($medicine['name']) ?>" required>
                            </div>
                        </div>
                        <div class="pf-col-4">
                            <div class="form-group">
                                <label class="form-label">Unit <span class="pf-required">*</span></label>
                                <select name="unit" class="form-input" required>
                                    <?php foreach (['tablet','capsule','syrup','injection','cream'] as $u): ?>
                                    <option value="<?= $u ?>" <?= $medicine['unit'] === $u ? 'selected' : '' ?>><?= ucfirst($u) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pf-row">
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Generic Name</label>
                                <input type="text" name="generic_name" class="form-input" value="<?= esc($medicine['generic_name'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Category <span class="pf-required">*</span></label>
                                <select name="category_id" class="form-input" required>
                                    <option value="">Select category</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $medicine['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pf-row">
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Status <span style="color:var(--text-muted);font-weight:400;">(Active = Visible, Inactive = Hidden)</span></label>
                                <select name="is_active" class="form-input" id="statusSelect" onchange="updateStatusPreview()">
                                    <option value="1" <?= $medicine['is_active'] ? 'selected' : '' ?>>✓ Active (Visible in listings)</option>
                                    <option value="0" <?= !$medicine['is_active'] ? 'selected' : '' ?>>✗ Inactive (Hidden from main view)</option>
                                </select>
                                <div class="form-hint" id="statusHint">
                                    <?= $medicine['is_active'] ? '✓ This medicine is currently visible' : '✗ This medicine is currently hidden' ?>
                                </div>
                            </div>
                        </div>
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Product Image</label>
                                <div class="pf-upload-zone <?= !empty($medicine['image']) ? 'has-image' : '' ?>" id="uploadZone" onclick="document.getElementById('imageInput').click()">
                                    <div class="pf-upload-inner" id="uploadInner" style="<?= !empty($medicine['image']) ? 'display:none;' : '' ?>">
                                        <i class="bi bi-cloud-arrow-up pf-upload-icon"></i>
                                        <span class="pf-upload-text">Click to replace</span>
                                        <span class="pf-upload-hint">JPG, PNG — max 2MB</span>
                                    </div>
                                    <?php if (!empty($medicine['image'])): ?>
                                    <img id="imagePreview" class="pf-upload-preview" src="/uploads/medicines/<?= esc($medicine['image']) ?>" alt="">
                                    <?php else: ?>
                                    <img id="imagePreview" class="pf-upload-preview" style="display:none;" alt="">
                                    <?php endif; ?>
                                </div>
                                <input type="file" id="imageInput" name="image" accept="image/*" style="display:none;" onchange="previewImage(this)">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-input form-textarea" rows="3"><?= esc($medicine['description'] ?? '') ?></textarea>
                    </div>

                    <div class="pf-form-actions">
                        <button type="submit" class="btn-create-order"><i class="bi bi-check-lg"></i> Update Medicine</button>
                        <a href="/medicines" class="btn btn-ghost">Cancel</a>
                        <button type="button" class="btn" style="background:#fef2f2;color:var(--danger);border:1px solid #fecaca;margin-left:auto;"
                                onclick="confirmDeleteMed()">
                            <i class="bi bi-trash3"></i> Delete
                        </button>
                    </div>
                </form>

<!-- Standalone delete form — completely outside the update form -->
<form id="deleteMedForm" action="/medicines/<?= (int)$medicine['id'] ?>" method="POST" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>
            </div>
        </div>
    </div>

    <!-- RIGHT: Batches panel -->
    <div class="pf-form-side">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Batches</span>
                <span class="badge badge-neutral"><?= count($medicine['batches'] ?? []) ?> batch<?= count($medicine['batches'] ?? []) !== 1 ? 'es' : '' ?></span>
            </div>
            <?php if (!empty($medicine['batches'])): ?>
            <div style="padding:.75rem 1.25rem;">
                <?php foreach ($medicine['batches'] as $b): ?>
                <div class="pv-variant-row">
                    <div class="pv-variant-info">
                        <div>
                            <div class="pv-variant-label"><?= esc($b['batch_number']) ?></div>
                            <div class="pv-variant-sub">Exp: <?= date('M d, Y', strtotime($b['expiry_date'])) ?> · Stock: <?= (int)$b['current_quantity'] ?></div>
                        </div>
                    </div>
                    <span class="badge badge-<?= $b['status'] === 'active' ? 'success' : 'danger' ?>"><?= esc($b['status']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="padding:2.5rem;text-align:center;">
                <i class="bi bi-box-seam" style="font-size:1.75rem;color:#ccc;display:block;margin-bottom:.5rem;"></i>
                <p style="font-size:.82rem;color:var(--text-muted);margin:0;">No batches yet.</p>
            </div>
            <?php endif; ?>
            <div style="padding:.85rem 1.25rem;border-top:1px solid var(--border);">
                <a href="/batches/create" class="btn-create-order" style="width:100%;justify-content:center;font-size:.8rem;">
                    <i class="bi bi-plus"></i> Add Batch
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input){
    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = e => {
            const p = document.getElementById('imagePreview'), i = document.getElementById('uploadInner');
            p.src = e.target.result; p.style.display = 'block'; i.style.display = 'none';
            document.getElementById('uploadZone').classList.add('has-image');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function confirmDeleteMed() {
    if (confirm('Delete this medicine permanently? This cannot be undone.')) {
        document.getElementById('deleteMedForm').submit();
    }
}

function updateStatusPreview() {
    const select = document.getElementById('statusSelect');
    const hint = document.getElementById('statusHint');
    const isActive = select.value === '1';
    
    hint.textContent = isActive 
        ? '✓ This medicine will be visible in listings' 
        : '✗ This medicine will be hidden from main view';
    hint.style.color = isActive ? '#15803d' : '#dc2626';
}
</script>
