<?php use App\Core\View; View::layout('app'); $title = 'Users'; ?>
<?php
$accessMap = ['superadmin' => 'Full Access', 'manager' => 'Limited Admin', 'staff' => 'Read-Only'];
$currentUserId = auth()['id'] ?? 0;
?>

<style>
.tm-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;}
.tm-toggle{display:flex;background:#f3f4f6;border-radius:10px;padding:3px;gap:2px;}
.tm-toggle-btn{padding:.45rem 1.1rem;border-radius:8px;border:none;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .15s;color:var(--text-muted);background:transparent;}
.tm-toggle-btn.active{background:#fff;color:var(--text-heading);box-shadow:0 1px 4px rgba(0,0,0,.1);}
.tm-toolbar{display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap;}
.tm-role-filter{display:flex;gap:.4rem;flex-wrap:wrap;}
.tm-role-pill{padding:.35rem .85rem;border-radius:20px;border:1px solid var(--border);background:#fff;color:var(--text-muted);font-size:.78rem;font-weight:600;cursor:pointer;transition:all .14s;}
.tm-role-pill:hover{border-color:#ccc;color:var(--text-heading);}
.tm-role-pill.active{background:var(--accent-orange);border-color:var(--accent-orange);color:#fff;}
.tm-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;}
@media(max-width:1100px){.tm-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:780px){.tm-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:500px){.tm-grid{grid-template-columns:1fr;}}
.tm-card{background:#fff;border:1px solid var(--border);border-radius:16px;overflow:hidden;transition:box-shadow .18s,transform .18s;display:flex;flex-direction:column;}
.tm-card:hover{box-shadow:0 8px 28px rgba(0,0,0,.08);transform:translateY(-2px);}
.tm-card-top{display:flex;align-items:center;justify-content:space-between;padding:1rem 1rem 0;}
.tm-card-logo{width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#0f4c81,#0ea5e9);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.tm-card-logo i{color:#fff;font-size:.9rem;}
.tm-card-body{padding:.75rem 1rem 1rem;display:flex;flex-direction:column;align-items:center;text-align:center;flex:1;}
.tm-avatar-wrap{position:relative;margin-bottom:.75rem;}
.tm-avatar{width:64px;height:64px;border-radius:50%;background:#e5e7eb;color:#6b7280;font-size:1.35rem;font-weight:700;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.1);}
.tm-status-dot{position:absolute;bottom:2px;right:2px;width:12px;height:12px;border-radius:50%;border:2px solid #fff;}
.tm-status-dot.active{background:#22c55e;}
.tm-status-dot.inactive{background:#d1d5db;}
.tm-name{font-size:.9rem;font-weight:700;color:var(--text-heading);margin-bottom:.15rem;width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.tm-email{font-size:.72rem;color:var(--text-muted);margin-bottom:.85rem;width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.tm-meta{display:grid;grid-template-columns:1fr 1fr;gap:.35rem;width:100%;margin-bottom:1rem;}
.tm-meta-item{text-align:left;}
.tm-meta-label{font-size:.6rem;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);font-weight:600;margin-bottom:.15rem;}
.tm-meta-value{font-size:.75rem;font-weight:700;display:flex;align-items:center;gap:.25rem;}
.tm-meta-value.role-val{color:#3b82f6;}
.tm-meta-value.access-val{color:#22c55e;}
.tm-card-actions{display:grid;grid-template-columns:1fr 1fr;gap:0;border-top:1px solid var(--border);}
.tm-action-btn{display:flex;align-items:center;justify-content:center;gap:.4rem;padding:.7rem;font-size:.78rem;font-weight:600;background:none;border:none;cursor:pointer;transition:background .12s;color:var(--text-body);}
.tm-action-btn:first-child{border-right:1px solid var(--border);}
.tm-action-btn:hover{background:var(--bg-surface);color:var(--text-heading);}
.tm-action-btn.danger:hover{background:#fef2f2;color:var(--danger);}
</style>

<div class="tm-header">
    <div>
        <h1 class="sl-title" style="display:flex;align-items:center;gap:.5rem;">
            <i class="bi bi-people-fill" style="color:var(--accent-orange);font-size:1.1rem;"></i>
            Team Members
        </h1>
        <p class="sl-subtitle">HealthChain · <?= count($data) ?> member<?= count($data) != 1 ? 's' : '' ?> total</p>
    </div>
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div class="tm-toggle">
            <button class="tm-toggle-btn active" id="toggleAll" onclick="setToggle('all')">All</button>
            <button class="tm-toggle-btn" id="toggleMine" onclick="setToggle('mine')">My Role</button>
        </div>
        <a href="/users/create" class="btn-create-order"><i class="bi bi-person-plus-fill"></i> Add User</a>
    </div>
</div>

<div class="tm-toolbar">
    <div class="pg-search-wrap" style="max-width:260px;flex:1;">
        <i class="bi bi-search pg-search-icon"></i>
        <input type="text" id="tmSearch" class="pg-search-input" placeholder="Search members..." oninput="applyFilters()">
    </div>
    <div class="tm-role-filter">
        <button class="tm-role-pill active" data-role="all" onclick="setRoleFilter(this,'all')">All <span style="opacity:.6;font-weight:400;">(<?= count($data) ?>)</span></button>
        <?php
        $roleCounts = [];
        foreach ($data as $u) { $r = $u['role'] ?? 'staff'; $roleCounts[$r] = ($roleCounts[$r] ?? 0) + 1; }
        foreach (array_unique(array_column($data, 'role')) as $rn):
        ?>
        <button class="tm-role-pill" data-role="<?= esc($rn) ?>" onclick="setRoleFilter(this,'<?= esc($rn) ?>')">
            <?= esc(ucfirst($rn)) ?> <span style="opacity:.6;font-weight:400;">(<?= $roleCounts[$rn] ?? 0 ?>)</span>
        </button>
        <?php endforeach; ?>
    </div>
</div>

<div class="tm-grid" id="tmGrid">
    <?php if (empty($data)): ?>
    <div style="grid-column:1/-1;padding:4rem 2rem;text-align:center;color:var(--text-muted);">
        <i class="bi bi-people" style="font-size:3rem;opacity:.2;display:block;margin-bottom:.75rem;"></i>
        <p style="font-size:.9rem;">No users found.</p>
    </div>
    <?php else: foreach ($data as $u):
        $uid      = $u['id'] ?? 0;
        $uRole    = $u['role'] ?? 'staff';
        $uAccess  = $accessMap[$uRole] ?? 'Read-Only';
        $isActive = (int)($u['is_active'] ?? 1);
        $initial  = strtoupper(substr($u['fullname'] ?? 'U', 0, 1));
        $isMe     = ($uid == $currentUserId);
    ?>
    <div class="tm-card"
         data-role="<?= esc($uRole) ?>"
         data-name="<?= strtolower(esc($u['fullname'] ?? '')) ?>"
         data-active="<?= $isActive ?>"
         data-uid="<?= $uid ?>"
         data-me="<?= $isMe ? '1' : '0' ?>">

        <div class="tm-card-top">
            <div class="tm-card-logo"><i class="bi bi-capsule"></i></div>
            <?php if (!$isMe): ?>
            <form method="POST" action="/users/<?= $uid ?>" onsubmit="return confirm('Delete this user?')" style="display:inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" style="background:none;border:none;color:var(--text-muted);cursor:pointer;padding:.2rem .4rem;border-radius:6px;font-size:.85rem;" title="Delete">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
            </form>
            <?php endif; ?>
        </div>

        <div class="tm-card-body">
            <div class="tm-avatar-wrap">
                <div class="tm-avatar"><?= $initial ?></div>
                <div class="tm-status-dot <?= $isActive ? 'active' : 'inactive' ?>"></div>
            </div>
            <div class="tm-name"><?= esc($u['fullname'] ?? '—') ?></div>
            <div class="tm-email"><?= esc($u['email'] ?? '') ?></div>
            <div class="tm-meta">
                <div class="tm-meta-item">
                    <div class="tm-meta-label">Role</div>
                    <div class="tm-meta-value role-val"><i class="bi bi-circle-fill" style="font-size:.35rem;"></i> <?= esc(ucfirst($uRole)) ?></div>
                </div>
                <div class="tm-meta-item">
                    <div class="tm-meta-label">Access</div>
                    <div class="tm-meta-value access-val"><i class="bi bi-bar-chart-fill" style="font-size:.65rem;"></i> <?= $uAccess ?></div>
                </div>
            </div>
        </div>

        <div class="tm-card-actions">
            <a href="/users/<?= $uid ?>/edit" class="tm-action-btn"><i class="bi bi-pencil"></i> Edit</a>
            <?php if (!$isMe): ?>
            <form method="POST" action="/users/<?= $uid ?>" onsubmit="return confirm('Delete this user?')" style="display:contents;">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="tm-action-btn danger"><i class="bi bi-trash3"></i> Delete</button>
            </form>
            <?php else: ?>
            <span class="tm-action-btn" style="opacity:.4;cursor:default;"><i class="bi bi-lock"></i> You</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; endif; ?>
</div>

<div id="tmNoResults" style="display:none;padding:3rem;text-align:center;color:var(--text-muted);">
    <i class="bi bi-search" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:.5rem;"></i>
    <p style="font-size:.9rem;">No members match your search.</p>
</div>

<script>
let currentToggle = 'all', currentRole = 'all';
function setToggle(val){ currentToggle = val; document.getElementById('toggleAll').classList.toggle('active', val==='all'); document.getElementById('toggleMine').classList.toggle('active', val==='mine'); applyFilters(); }
function setRoleFilter(btn, role){ currentRole = role; document.querySelectorAll('.tm-role-pill').forEach(b => b.classList.remove('active')); btn.classList.add('active'); applyFilters(); }
function applyFilters(){
    const q = document.getElementById('tmSearch').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.tm-card');
    let visible = 0;
    cards.forEach(card => {
        const show = (currentToggle==='all' || card.dataset.me==='1') && (currentRole==='all' || card.dataset.role===currentRole) && (!q || card.dataset.name.includes(q) || card.textContent.toLowerCase().includes(q));
        card.style.display = show ? '' : 'none';
        if(show) visible++;
    });
    document.getElementById('tmNoResults').style.display = visible === 0 ? 'block' : 'none';
}
</script>
