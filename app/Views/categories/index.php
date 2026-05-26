<?php use App\Core\View; View::layout('app'); $title = 'Categories'; ?>
<?php $totalCount = count($data ?? []); ?>

<div class="pg-header">
    <div class="pg-header-left">
        <div class="pg-breadcrumb">
            <span>Categories</span>
            <i class="bi bi-chevron-right"></i>
            <span class="pg-breadcrumb-active">All Categories</span>
        </div>
        <div class="pg-header-meta">Showing all <strong><?= number_format($totalCount) ?></strong> categories</div>
    </div>
    <?php if (can('categories.*')): ?>
    <a href="/categories/create" class="btn-create-order"><i class="bi bi-plus"></i> Add Category</a>
    <?php endif; ?>
</div>

<div class="rt-toolbar" style="margin-bottom:1rem; display:flex; justify-content:space-between; align-items:center;">
    <div class="pg-search-wrap" style="max-width:360px; flex:1;">
        <i class="bi bi-search pg-search-icon"></i>
        <input type="text" id="catSearch" placeholder="Search categories by name…" class="pg-search-input" oninput="filterCategories(this.value)">
    </div>
</div>

<div class="card" style="background:#fff; border:1px solid var(--border); border-radius:16px; overflow:hidden;">
    <div class="table-wrapper">
        <table class="smro-table">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Category Name</th>
                    <th style="text-align: center; width: 150px;">Associated Medicines</th>
                    <th style="width: 200px;">Created At</th>
                    <?php if (can('categories.*')): ?>
                    <th style="text-align: right; width: 160px; padding-right: 1.5rem;">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody id="catTbody">
                <?php if (empty($data)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;padding:3rem;color:var(--text-muted);">
                        <i class="bi bi-tags" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.25;"></i>
                        No categories found.
                        <?php if (can('categories.*')): ?>
                        <a href="/categories/create" style="color:var(--accent-orange);font-weight:600;text-decoration:none;display:block;margin-top:.4rem;">Create one →</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: foreach ($data as $c): ?>
                <tr data-search="<?= strtolower(esc($c['name'])) ?>">
                    <td style="font-family:monospace;font-size:.75rem;color:var(--text-muted);"><?= sprintf('%03d', $c['id']) ?></td>
                    <td style="font-weight:600;color:var(--text-heading);"><?= esc($c['name']) ?></td>
                    <td style="text-align: center;">
                        <span style="font-weight: 700; background: var(--bg-surface); padding: 0.25rem 0.65rem; border-radius: 99px; font-size: 0.8rem; border: 1px solid var(--border);">
                            <?= (int)$c['medicines_count'] ?>
                        </span>
                    </td>
                    <td style="font-size:.75rem;color:var(--text-muted);"><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></td>
                    <?php if (can('categories.*')): ?>
                    <td style="text-align: right; padding-right: 1.5rem;">
                        <div style="display:flex; justify-content:flex-end; gap:.5rem;">
                            <a href="/categories/<?= (int)$c['id'] ?>/edit" class="pgc-action-btn" title="Edit" style="border: 1px solid var(--border); padding: 0.35rem 0.55rem; border-radius: 8px; font-size: 0.8rem; background: #fff; color: var(--text-body); text-decoration: none; display:inline-flex; align-items:center; gap:0.25rem;"><i class="bi bi-pencil"></i> Edit</a>
                            
                            <?php if ((int)$c['medicines_count'] === 0): ?>
                            <form action="/categories/<?= (int)$c['id'] ?>" method="POST" onsubmit="return confirmDelete(event, '<?= esc($c['name']) ?>')" style="display:contents;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="pgc-action-btn pgc-action-danger" title="Delete" style="border: 1px solid #fee2e2; padding: 0.35rem 0.55rem; border-radius: 8px; font-size: 0.8rem; background: #fef2f2; color: #dc2626; cursor:pointer; display:inline-flex; align-items:center; gap:0.25rem;"><i class="bi bi-trash3"></i> Delete</button>
                            </form>
                            <?php else: ?>
                            <button class="pgc-action-btn" title="Cannot delete: has associated medicines" disabled style="opacity: 0.4; border: 1px solid var(--border); padding: 0.35rem 0.55rem; border-radius: 8px; font-size: 0.8rem; background: var(--bg-surface); color: var(--text-muted); cursor: not-allowed; display:inline-flex; align-items:center; gap:0.25rem;"><i class="bi bi-trash3"></i> Delete</button>
                            <?php endif; ?>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($pagination['total_pages']) && $pagination['total_pages'] > 1): ?>
    <div style="padding:.85rem 1.25rem;border-top:1px solid var(--border);">
        <div class="pager-wrap" style="padding:0; margin:0; display:flex; gap:0.35rem;">
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <a href="?page=<?= $i ?>" class="page-link <?= $i === $pagination['current_page'] ? 'active' : '' ?>" style="text-decoration:none; padding: 0.4rem 0.8rem; border-radius: 8px; border: 1px solid var(--border); font-size: 0.8rem; color: var(--text-body);"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="pg-modal-overlay" id="deleteModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:999; align-items:center; justify-content:center;">
    <div class="pg-modal" style="background:#fff; border-radius:16px; padding:1.75rem; width:380px; max-width:90vw; text-align:center; box-shadow:0 8px 32px rgba(0,0,0,.15);">
        <div class="pg-modal-icon" style="font-size:2.5rem; color:#dc2626; margin-bottom:0.75rem;"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <h3 class="pg-modal-title" style="font-size:1.15rem; font-weight:700; color:var(--text-heading); margin-bottom:0.5rem;">Delete Category?</h3>
        <p class="pg-modal-body" style="font-size:0.875rem; color:var(--text-muted); margin-bottom:1.5rem;">You're about to delete <strong id="deleteItemName" style="color:var(--text-heading);"></strong>. This cannot be undone.</p>
        <div class="pg-modal-actions" style="display:flex; gap:0.75rem; justify-content:center;">
            <button class="pg-modal-btn-cancel" onclick="closeDeleteModal()" style="flex:1; border:1px solid var(--border); background:#fff; padding:0.6rem; border-radius:8px; cursor:pointer; font-weight:600; font-size:0.85rem; color:var(--text-body);">Cancel</button>
            <button class="pg-modal-btn-confirm" id="deleteConfirmBtn" style="flex:1; border:none; background:#dc2626; color:#fff; padding:0.6rem; border-radius:8px; cursor:pointer; font-weight:600; font-size:0.85rem;">Delete</button>
        </div>
    </div>
</div>

<script>
function filterCategories(q) {
    const term = q.toLowerCase().trim();
    document.querySelectorAll('#catTbody tr').forEach(r => {
        if (!r.dataset.search) return;
        const match = !term || r.dataset.search.includes(term);
        r.style.display = match ? '' : 'none';
    });
}

let pendingForm = null;
function confirmDelete(e, name) {
    e.preventDefault();
    pendingForm = e.target.closest('form');
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
    return false;
}
document.getElementById('deleteConfirmBtn').addEventListener('click', () => { if (pendingForm) pendingForm.submit(); });
function closeDeleteModal() { document.getElementById('deleteModal').style.display = 'none'; pendingForm = null; }
document.getElementById('deleteModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeDeleteModal(); });
</script>
