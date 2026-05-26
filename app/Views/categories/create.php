<?php use App\Core\View; View::layout('app'); $title = 'Add Category'; ?>

<a href="/categories" class="back-link"><i class="bi bi-arrow-left"></i> Back to Categories</a>

<div class="page-header" style="margin-bottom:1.75rem;">
    <div>
        <h1 class="page-title">Add New Category</h1>
        <p class="page-subtitle">Define a new category to classify medicines</p>
    </div>
</div>

<?php if ($errors = flash('errors')): ?>
<div class="pg-alert pg-alert-error" style="margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem; background:#fef2f2; color:#dc2626; padding:0.85rem 1.1rem; border-radius:10px; border:1px solid #fecaca; font-size:0.875rem;">
    <i class="bi bi-exclamation-circle-fill"></i>
    <div><?php foreach ($errors as $msgs): foreach ($msgs as $msg): ?><div><?= esc($msg) ?></div><?php endforeach; endforeach; ?></div>
</div>
<?php endif; ?>

<div class="pf-form-layout">
    <div class="pf-form-main" style="flex: 1;">
        <div class="card" style="background:#fff; border:1px solid var(--border); border-radius:16px; overflow:hidden;">
            <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                <span class="card-title" style="font-weight:700; color:var(--text-heading);">Category Information</span>
                <span style="font-size:.72rem;color:var(--text-muted);">* Required field</span>
            </div>
            <div style="padding:1.5rem;">
                <form method="POST" action="/categories">
                    <?= csrf_field() ?>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label" style="display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.85rem; color:var(--text-body);">Category Name <span class="pf-required" style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" class="form-input" placeholder="e.g. Analgesics, Antibiotics" value="<?= esc(old('name')) ?>" required style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:8px; font-size:0.9rem; background:#fff; color:var(--text-body);" autofocus>
                    </div>

                    <div class="pf-form-actions" style="display:flex; gap:0.75rem; align-items:center; border-top: 1px solid var(--border); padding-top:1.25rem; margin-top:1.5rem;">
                        <button type="submit" class="btn-create-order" style="border:none; background:linear-gradient(135deg,#0f4c81,#0ea5e9); color:#fff; padding:0.65rem 1.25rem; border-radius:8px; cursor:pointer; font-weight:600; font-size:0.85rem; display:inline-flex; align-items:center; gap:0.35rem;"><i class="bi bi-check-lg"></i> Create Category</button>
                        <a href="/categories" class="btn btn-ghost" style="border:1px solid var(--border); background:#fff; color:var(--text-body); padding:0.65rem 1.25rem; border-radius:8px; text-decoration:none; font-weight:600; font-size:0.85rem; display:inline-flex; align-items:center;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="pf-form-side" style="width: 320px; flex-shrink: 0;">
        <div class="card" style="background:#fff; border:1px solid var(--border); border-radius:16px; overflow:hidden;">
            <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                <span class="card-title" style="font-weight:700; color:var(--text-heading);">Quick Info</span>
                <i class="bi bi-info-circle" style="color:var(--accent-orange);"></i>
            </div>
            <div style="padding:1.25rem;">
                <ul class="pf-tips-list" style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:0.75rem; font-size:0.825rem; color:var(--text-muted); line-height:1.4;">
                    <li><i class="bi bi-lightbulb" style="color:var(--accent-orange); margin-right:0.35rem;"></i> Try to make category names plural and descriptive (e.g. <strong>Antibiotics</strong> instead of <strong>Antibiotic</strong>).</li>
                    <li><i class="bi bi-shield-check" style="color:#22c55e; margin-right:0.35rem;"></i> Category names must be unique to keep database records clean.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
