<?php use App\Core\View; View::layout('app'); $title = 'Medicines'; ?>
<?php
$allCategories = array_unique(array_column($data ?? [], 'category'));
sort($allCategories);
$totalCount = count($data ?? []);
?>

<link rel="stylesheet" href="/assets/css/medicines-advanced.css">
<script src="/assets/js/medicines-advanced.js"></script>

<!-- Page header -->
<div class="pg-header">
    <div class="pg-header-left">
        <div class="pg-breadcrumb">
            <span>Medicines</span>
            <i class="bi bi-chevron-right"></i>
            <span class="pg-breadcrumb-active">All Medicines</span>
        </div>
        <div class="pg-header-meta">Showing all <strong><?= number_format($totalCount) ?></strong> medicine<?= $totalCount !== 1 ? 's' : '' ?></div>
    </div>
    <?php if (can('medicines.*')): ?>
    <a href="/medicines/create" class="btn-create-order"><i class="bi bi-plus"></i> Add Medicine</a>
    <?php endif; ?>
</div>

<div class="pg-layout">

    <!-- LEFT FILTER SIDEBAR -->
    <aside class="pg-filters" id="pgFiltersSidebar">
        <div class="pf-section">
            <button class="pf-dropdown-toggle" onclick="togglePFSection('catBody','catToggle')">
                <span>Category</span>
                <i class="bi bi-chevron-down pf-toggle-icon" id="catToggleIcon"></i>
            </button>
            <div class="pf-dropdown-body" id="catBody">
                <label class="pf-radio checked">
                    <input type="radio" name="pf_category" value="" checked onchange="applyFilters()">
                    <span class="pf-radio-dot"></span> All Categories
                </label>
                <?php foreach ($allCategories as $cat): ?>
                <label class="pf-radio">
                    <input type="radio" name="pf_category" value="<?= esc($cat) ?>" onchange="applyFilters()">
                    <span class="pf-radio-dot"></span> <?= esc(ucfirst($cat)) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="pf-section">
            <button class="pf-dropdown-toggle" onclick="togglePFSection('statusBody','statusToggle')">
                <span>Status</span>
                <i class="bi bi-chevron-down pf-toggle-icon" id="statusToggleIcon"></i>
            </button>
            <div class="pf-dropdown-body" id="statusBody">
                <label class="pf-radio checked">
                    <input type="radio" name="pf_status" value="" checked onchange="applyFilters()">
                    <span class="pf-radio-dot"></span> All Status
                </label>
                <label class="pf-radio">
                    <input type="radio" name="pf_status" value="active" onchange="applyFilters()">
                    <span class="pf-radio-dot"></span> Active <span class="pf-count" id="activeCount"></span>
                </label>
                <label class="pf-radio">
                    <input type="radio" name="pf_status" value="inactive" onchange="applyFilters()">
                    <span class="pf-radio-dot"></span> Inactive <span class="pf-count" id="inactiveCount"></span>
                </label>
            </div>
        </div>
        <button class="pf-apply-btn" onclick="applyFilters()">Apply</button>
    </aside>

    <!-- RIGHT: search + grid -->
    <div class="pg-right">
        <div class="pg-toolbar">
            <div class="pg-search-wrap" id="pgSearchWrap">
                <i class="bi bi-search pg-search-icon"></i>
                <input type="text" id="pgSearchInput" placeholder="Search medicines by name or category…" class="pg-search-input" oninput="pgSearchHandler(this.value)" autocomplete="off">
                <button class="pg-search-clear" id="pgSearchClear" onclick="clearPgSearch()"><i class="bi bi-x-lg"></i></button>
                <span class="pg-search-kbd">/</span>
            </div>
            <button class="pg-filter-btn" onclick="toggleSidebar()"><i class="bi bi-sliders2"></i> Filter</button>
        </div>

        <?php if (empty($data)): ?>
        <div class="pg-empty">
            <i class="bi bi-capsule"></i>
            <p>No medicines found.</p>
            <?php if (can('medicines.*')): ?>
            <a href="/medicines/create" class="btn-create-order" style="font-size:.82rem;"><i class="bi bi-plus"></i> Add first medicine</a>
            <?php endif; ?>
        </div>
        <?php else: ?>

        <div class="pg-grid" id="pgGrid">
            <?php foreach ($data as $m): ?>
            <div class="pgc"
                 data-medicine-id="<?= (int)$m['id'] ?>"
                 data-name="<?= strtolower(esc($m['name'])) ?>"
                 data-cat="<?= strtolower(esc($m['category'])) ?>"
                 data-status="<?= $m['is_active'] ? 'active' : 'inactive' ?>">

                <div class="pgc-img-wrap">
                    <?php if (!empty($m['image'])): ?>
                        <img src="/uploads/medicines/<?= esc($m['image']) ?>" class="pgc-img" alt="<?= esc($m['name']) ?>">
                    <?php else: ?>
                        <div class="pgc-img pgc-img-placeholder"><i class="bi bi-capsule"></i></div>
                    <?php endif; ?>

                    <div class="pgc-actions">
                        <a href="/medicines/<?= (int)$m['id'] ?>" class="pgc-action-btn" title="View"><i class="bi bi-eye"></i></a>
                        <?php if (can('medicines.*')): ?>
                        <a href="/medicines/<?= (int)$m['id'] ?>/edit" class="pgc-action-btn" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="/medicines/<?= (int)$m['id'] ?>" method="POST" onsubmit="return confirmDelete(event,'<?= esc($m['name']) ?>')" style="display:contents;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="pgc-action-btn pgc-action-danger" title="Delete"><i class="bi bi-trash3"></i></button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <div class="pgc-status-badge <?= $m['is_active'] ? 'pgc-badge-active' : 'pgc-badge-inactive' ?>">
                        <?php if (can('medicines.*')): ?>
                        <button class="status-toggle-btn" onclick="toggleStatus(<?= (int)$m['id'] ?>, <?= $m['is_active'] ? 'true' : 'false' ?>)" title="Click to toggle status">
                            <?= $m['is_active'] ? 'Active' : 'Inactive' ?>
                        </button>
                        <?php else: ?>
                        <?= $m['is_active'] ? 'Active' : 'Inactive' ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pgc-body">
                    <div class="pgc-category"><?= esc(ucfirst($m['category'])) ?></div>
                    <div class="pgc-name" title="<?= esc($m['name']) ?>"><?= esc($m['name']) ?></div>
                    <div class="pgc-footer">
                        <span style="font-size:.78rem;color:var(--text-muted);"><?= esc($m['unit']) ?></span>
                        <div class="pgc-icon-group">
                            <a href="/medicines/<?= (int)$m['id'] ?>" class="pgc-icon-btn" title="View"><i class="bi bi-eye"></i></a>
                            <?php if (can('medicines.*')): ?>
                            <a href="/medicines/<?= (int)$m['id'] ?>/edit" class="pgc-icon-btn" title="Edit"><i class="bi bi-pencil"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="pg-empty" id="pgNoResults" style="display:none;">
            <i class="bi bi-search"></i><p>No medicines match your search.</p>
        </div>

        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pager-wrap">
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <a href="?page=<?= $i ?>" class="page-link <?= $i === $pagination['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- DELETE MODAL -->
<div class="pg-modal-overlay" id="deleteModal" style="display:none;">
    <div class="pg-modal">
        <div class="pg-modal-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <h3 class="pg-modal-title">Delete Medicine?</h3>
        <p class="pg-modal-body">You're about to delete <strong id="deleteItemName"></strong>. This cannot be undone.</p>
        <div class="pg-modal-actions">
            <button class="pg-modal-btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button class="pg-modal-btn-confirm" id="deleteConfirmBtn">Delete</button>
        </div>
    </div>
</div>

<script>
// Count medicines on page load
window.addEventListener('DOMContentLoaded', function() {
    updateCounts();
});

function updateCounts() {
    const cards = document.querySelectorAll('.pgc');
    let activeCount = 0;
    let inactiveCount = 0;
    
    cards.forEach(c => {
        if(c.dataset.status === 'active') activeCount++;
        if(c.dataset.status === 'inactive') inactiveCount++;
    });
    
    const activeEl = document.getElementById('activeCount');
    const inactiveEl = document.getElementById('inactiveCount');
    
    if(activeEl) activeEl.textContent = `(${activeCount})`;
    if(inactiveEl) inactiveEl.textContent = `(${inactiveCount})`;
    
    console.log('Medicine counts:', { active: activeCount, inactive: inactiveCount });
}

function toggleSidebar(){ document.getElementById('pgFiltersSidebar').classList.toggle('pg-sidebar-open'); }
function togglePFSection(bodyId, toggleId){
    const body = document.getElementById(bodyId);
    const icon = document.getElementById(toggleId+'Icon');
    body.classList.toggle('pf-collapsed');
    icon.style.transform = body.classList.contains('pf-collapsed') ? 'rotate(-90deg)' : 'rotate(0deg)';
}
function pgSearchHandler(q){ liveSearch(q); document.getElementById('pgSearchWrap').classList.toggle('has-value', q.length > 0); }
function clearPgSearch(){ const i=document.getElementById('pgSearchInput'); i.value=''; document.getElementById('pgSearchWrap').classList.remove('has-value'); liveSearch(''); i.focus(); }
function liveSearch(query){
    const q = query.toLowerCase().trim();
    const cards = document.querySelectorAll('.pgc');
    let visible = 0;
    cards.forEach(c => { const show = !q || c.dataset.name.includes(q) || c.dataset.cat.includes(q); c.style.display = show ? '' : 'none'; if(show) visible++; });
    document.getElementById('pgNoResults').style.display = visible === 0 ? 'flex' : 'none';
}
function applyFilters(){
    const cat    = document.querySelector('input[name="pf_category"]:checked')?.value || '';
    const status = document.querySelector('input[name="pf_status"]:checked')?.value || '';
    const cards  = document.querySelectorAll('.pgc');
    let visible  = 0;
    
    cards.forEach(c => {
        const matchCat = !cat || c.dataset.cat === cat.toLowerCase();
        const matchStatus = !status || c.dataset.status === status;
        const show = matchCat && matchStatus;
        
        c.style.display = show ? '' : 'none';
        if(show) visible++;
    });
    
    document.getElementById('pgNoResults').style.display = visible === 0 ? 'flex' : 'none';
    
    // Show filter feedback
    if(status) {
        showToast(`Showing ${visible} ${status} medicine${visible !== 1 ? 's' : ''}`, 'success');
    }
}
document.querySelectorAll('input[type=radio]').forEach(r => r.addEventListener('change', () => {
    document.querySelectorAll(`input[name="${r.name}"]`).forEach(s => s.closest('.pf-radio')?.classList.toggle('checked', s.checked));
}));
document.addEventListener('keydown', e => { if(e.key==='/' && document.activeElement?.tagName?.toLowerCase() !== 'input'){ e.preventDefault(); document.getElementById('pgSearchInput')?.focus(); } });
let pendingForm = null;
function confirmDelete(e, name){ e.preventDefault(); pendingForm = e.target.closest('form'); document.getElementById('deleteItemName').textContent = name; document.getElementById('deleteModal').style.display = 'flex'; return false; }
document.getElementById('deleteConfirmBtn').addEventListener('click', () => { if(pendingForm) pendingForm.submit(); });
function closeDeleteModal(){ document.getElementById('deleteModal').style.display = 'none'; pendingForm = null; }
document.getElementById('deleteModal').addEventListener('click', e => { if(e.target === e.currentTarget) closeDeleteModal(); });

// Toggle medicine status
function toggleStatus(id, currentStatus) {
    const action = currentStatus ? 'deactivate' : 'activate';
    if(!confirm(`Are you sure you want to ${action} this medicine?`)) return;
    
    fetch(`/api/medicines/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('input[name="csrf_token"]')?.value || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            // Update the card without full reload
            const card = document.querySelector(`[data-medicine-id="${id}"]`);
            if(card) {
                const badge = card.querySelector('.pgc-status-badge');
                const btn = card.querySelector('.status-toggle-btn');
                const newStatus = data.is_active;
                
                // Update badge
                badge.className = `pgc-status-badge ${newStatus ? 'pgc-badge-active' : 'pgc-badge-inactive'}`;
                btn.textContent = newStatus ? 'Active' : 'Inactive';
                btn.onclick = () => toggleStatus(id, newStatus);
                
                // Update data attribute
                card.dataset.status = newStatus ? 'active' : 'inactive';
                
                // Show success message
                showToast(`Medicine ${newStatus ? 'activated' : 'deactivated'} successfully`, 'success');
            }
        } else {
            showToast('Failed to update status: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(err => {
        showToast('Error updating status. Please try again.', 'error');
        console.error(err);
    });
}

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
