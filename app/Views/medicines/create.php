<?php use App\Core\View; View::layout('app'); $title = 'Add Medicine'; ?>

<a href="/medicines" class="back-link"><i class="bi bi-arrow-left"></i> Back to Medicines</a>

<div class="page-header" style="margin-bottom:1.75rem;">
    <div>
        <h1 class="page-title">Add New Medicine</h1>
        <p class="page-subtitle">Fill in the details below to add a new medicine to inventory</p>
    </div>
</div>

<?php if ($errors = flash('errors')): ?>
<div class="pg-alert pg-alert-error" style="margin-bottom:1.25rem;">
    <i class="bi bi-exclamation-circle-fill"></i>
    <div><?php foreach ($errors as $msgs): foreach ($msgs as $msg): ?><div><?= esc($msg) ?></div><?php endforeach; endforeach; ?></div>
</div>
<?php endif; ?>

<div class="pf-form-layout">

    <!-- LEFT: Main form -->
    <div class="pf-form-main">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Medicine Information</span>
                <span style="font-size:.72rem;color:var(--text-muted);">* Required fields</span>
            </div>
            <div style="padding:1.5rem;">
                <form method="POST" action="/medicines" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="pf-row">
                        <div class="pf-col-8">
                            <div class="form-group">
                                <label class="form-label">Medicine Name <span class="pf-required">*</span></label>
                                <input type="text" name="name" class="form-input" placeholder="e.g. Paracetamol 500mg" value="<?= esc(old('name')) ?>" required>
                            </div>
                        </div>
                        <div class="pf-col-4">
                            <div class="form-group">
                                <label class="form-label">Unit <span class="pf-required">*</span></label>
                                <select name="unit" class="form-input" required>
                                    <option value="">Select unit</option>
                                    <?php foreach (['tablet','capsule','syrup','injection','cream'] as $u): ?>
                                    <option value="<?= $u ?>" <?= old('unit') === $u ? 'selected' : '' ?>><?= ucfirst($u) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pf-row">
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Generic Name</label>
                                <input type="text" name="generic_name" class="form-input" placeholder="e.g. Acetaminophen" value="<?= esc(old('generic_name')) ?>">
                            </div>
                        </div>
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Category <span class="pf-required">*</span></label>
                                <input type="text" name="category" class="form-input" placeholder="e.g. Analgesic" value="<?= esc(old('category')) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="pf-row">
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Product Image</label>
                                <div class="pf-upload-zone" id="uploadZone" onclick="document.getElementById('imageInput').click()">
                                    <div class="pf-upload-inner" id="uploadInner">
                                        <i class="bi bi-cloud-arrow-up pf-upload-icon"></i>
                                        <span class="pf-upload-text">Click to upload</span>
                                        <span class="pf-upload-hint">JPG, PNG — max 2MB</span>
                                    </div>
                                    <img id="imagePreview" class="pf-upload-preview" style="display:none;" alt="Preview">
                                </div>
                                <input type="file" id="imageInput" name="image" accept="image/*" style="display:none;" onchange="previewImage(this)">
                            </div>
                        </div>
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-input form-textarea" rows="5" placeholder="Optional description..."><?= esc(old('description')) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pf-form-actions">
                        <button type="submit" class="btn-create-order"><i class="bi bi-check-lg"></i> Save Medicine</button>
                        <a href="/medicines" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT: Tips -->
    <div class="pf-form-side">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Quick Tips</span>
                <i class="bi bi-lightbulb" style="color:var(--accent-orange);"></i>
            </div>
            <div style="padding:1.25rem;">
                <ul class="pf-tips-list">
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Use the full brand name for clarity</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Add batches after saving the medicine</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Generic name helps with search</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Category groups medicines in filters</li>
                    <li><i class="bi bi-check-circle-fill" style="color:var(--accent-orange);"></i> Square images look best in the grid</li>
                </ul>
            </div>
        </div>
        <div class="card" style="margin-top:1rem;">
            <div class="card-header"><span class="card-title">Status</span></div>
            <div style="padding:1.25rem;">
                <div class="pf-status-toggle">
                    <div>
                        <div style="font-size:.85rem;font-weight:600;color:var(--text-heading);">Active by default</div>
                        <div style="font-size:.75rem;color:var(--text-muted);margin-top:.2rem;">New medicines are set to Active</div>
                    </div>
                    <div class="pf-toggle-pill active"><div class="pf-toggle-knob"></div></div>
                </div>
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
const zone = document.getElementById('uploadZone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('pf-drag-over'); });
zone.addEventListener('dragleave', () => zone.classList.remove('pf-drag-over'));
zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('pf-drag-over');
    const file = e.dataTransfer.files[0];
    if(file && file.type.startsWith('image/')){ const dt = new DataTransfer(); dt.items.add(file); document.getElementById('imageInput').files = dt.files; previewImage(document.getElementById('imageInput')); }
});
</script>
