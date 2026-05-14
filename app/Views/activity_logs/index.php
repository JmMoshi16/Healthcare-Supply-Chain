<?php use App\Core\View; View::layout('app'); $title = 'Activity Logs'; ?>
<?php $totalCount = $pagination['total'] ?? count($logs ?? []); ?>

<link rel="stylesheet" href="/assets/css/batches-advanced.css?v=<?= time() ?>">
<style>
/* Custom modal styles for JSON view that fit the premium theme */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; transition: opacity 0.2s; backdrop-filter: blur(4px); }
.modal-overlay.active { display: flex; opacity: 1; }
.data-modal { background: #fff; width: 90%; max-width: 800px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); display: flex; flex-direction: column; max-height: 85vh; border: 1px solid var(--border); overflow: hidden; transform: translateY(20px); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-overlay.active .data-modal { transform: translateY(0); }
.data-modal-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
.data-modal-title { font-weight: 700; font-size: 1.1rem; color: var(--text-heading); display: flex; align-items: center; gap: 0.5rem; }
.btn-close-modal { background: #e2e8f0; border: none; font-size: 1rem; cursor: pointer; color: var(--text-muted); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.btn-close-modal:hover { background: #cbd5e1; color: var(--text-heading); }
.data-modal-body { padding: 1.5rem; overflow-y: auto; display: flex; gap: 1rem; background: #fff; }
.data-pane { flex: 1; background: #1e293b; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; display: flex; flex-direction: column; box-shadow: inset 0 2px 10px rgba(0,0,0,0.1); }
.data-pane-title { background: #0f172a; color: #94a3b8; padding: 0.6rem 1rem; font-weight: 600; font-size: 0.75rem; border-bottom: 1px solid #334155; text-transform: uppercase; letter-spacing: 0.05em; display: flex; justify-content: space-between; }
.data-pane pre { margin: 0; padding: 1.25rem; font-size: 0.8rem; overflow-x: auto; color: #e2e8f0; font-family: 'Consolas', 'Monaco', monospace; line-height: 1.5; flex: 1; }
.json-key { color: #38bdf8; }
.json-string { color: #a3e635; }
.json-number { color: #f472b6; }
.json-boolean { color: #fbbf24; font-weight: bold; }
.json-null { color: #94a3b8; font-style: italic; }

.log-action-create { background: #dcfce7; color: #166534; }
.log-action-update { background: #dbeafe; color: #1e40af; }
.log-action-delete { background: #fee2e2; color: #991b1b; }
</style>

<div class="sl-header">
    <div>
        <h1 class="sl-title" style="display:flex;align-items:center;gap:.6rem;">
            <i class="bi bi-clock-history" style="color:var(--accent-orange);font-size:1.4rem;"></i>
            Activity Logs
        </h1>
        <p class="sl-subtitle">Audit trail of all system actions and data changes</p>
    </div>
</div>

<div class="rt-toolbar" style="margin-bottom:1rem;">
    <div class="pg-search-wrap" style="max-width:360px;">
        <i class="bi bi-search pg-search-icon"></i>
        <input type="text" id="logSearch" placeholder="Search by user or entity…" class="pg-search-input" oninput="filterLogs(this.value)">
    </div>
    <div class="rt-filter-pills">
        <button class="pill-tab active" onclick="setFilter(this,'all')">All Actions</button>
        <button class="pill-tab" onclick="setFilter(this,'create')">Creates</button>
        <button class="pill-tab" onclick="setFilter(this,'update')">Updates</button>
        <button class="pill-tab" onclick="setFilter(this,'delete')">Deletes</button>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="smro-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>Record ID</th>
                    <th>IP Address</th>
                    <th style="text-align:right;">Data</th>
                </tr>
            </thead>
            <tbody id="logTbody">
                <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:4rem 2rem;color:var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:.75rem;opacity:.2;"></i>
                        <p style="font-size:.9rem;">No activity logs found.</p>
                    </td>
                </tr>
                <?php else: foreach ($logs as $log): 
                    $actionClass = 'log-action-update';
                    if ($log['action'] === 'create') $actionClass = 'log-action-create';
                    if ($log['action'] === 'delete') $actionClass = 'log-action-delete';
                ?>
                <tr data-filter="<?= esc($log['action']) ?>" data-search="<?= strtolower(esc($log['user_name'] ?? 'System') . ' ' . esc($log['model'])) ?>">
                    <td style="font-size:.8rem;color:var(--text-muted);"><i class="bi bi-calendar-event" style="margin-right:.3rem;opacity:.6;"></i><?= date('M d, Y g:i A', strtotime($log['timestamp'])) ?></td>
                    <td>
                        <?php if ($log['user_id']): ?>
                            <div style="display:flex;align-items:center;gap:.5rem;">
                                <div style="width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,#0f4c81,#0ea5e9);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;">
                                    <?= strtoupper(substr($log['user_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <strong style="color:var(--text-heading);font-size:.85rem;"><?= esc($log['user_name']) ?></strong>
                            </div>
                        <?php else: ?>
                            <span style="color:var(--text-muted);font-style:italic;font-size:.85rem;"><i class="bi bi-robot" style="margin-right:.3rem;"></i>System</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge <?= $actionClass ?>" style="padding:.3rem .6rem;border-radius:6px;font-size:.7rem;letter-spacing:.05em;"><?= esc($log['action']) ?></span></td>
                    <td style="font-weight:600;color:var(--text-heading);"><?= esc($log['model']) ?></td>
                    <td style="font-family:monospace;font-size:.8rem;color:var(--text-muted);">#<?= esc($log['model_id']) ?></td>
                    <td><span style="font-family:monospace;font-size:.75rem;color:var(--text-muted);background:#f8fafc;padding:.2rem .4rem;border-radius:4px;border:1px solid var(--border);"><?= esc($log['ip_address'] ?? 'N/A') ?></span></td>
                    <td style="text-align:right;">
                        <?php if ($log['old_data'] || $log['new_data']): ?>
                            <button class="pgc-action-btn" onclick="viewData(<?= htmlspecialchars(json_encode([
                                'old' => $log['old_data'] ? json_decode($log['old_data'], true) : null,
                                'new' => $log['new_data'] ? json_decode($log['new_data'], true) : null,
                                'title' => ucfirst($log['action']) . ' ' . $log['model'] . ' #' . $log['model_id'],
                                'icon' => $log['action'] === 'create' ? 'bi-plus-circle' : ($log['action'] === 'delete' ? 'bi-trash3' : 'bi-pencil-square'),
                                'color' => $log['action'] === 'create' ? '#16a34a' : ($log['action'] === 'delete' ? '#dc2626' : '#2563eb')
                            ])) ?>)" title="View Details" style="background:#f1f5f9;border:1px solid #e2e8f0;padding:.4rem .8rem;border-radius:8px;font-size:.8rem;display:inline-flex;align-items:center;gap:.4rem;">
                                <i class="bi bi-code-slash" style="color:var(--accent-orange);"></i> View
                            </button>
                        <?php else: ?>
                            <span style="color:var(--text-muted);font-size:.75rem;font-style:italic;">No data</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($pagination['total_pages']) && $pagination['total_pages'] > 1): ?>
    <div style="padding:.85rem 1.25rem;border-top:1px solid var(--border);">
        <div class="pager-wrap" style="padding:0;">
            <?php if ($pagination['current_page'] > 1): ?>
                <a href="?page=<?= $pagination['current_page'] - 1 ?>" class="page-link"><i class="bi bi-chevron-left"></i></a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <?php if ($i == 1 || $i == $pagination['total_pages'] || abs($i - $pagination['current_page']) <= 2): ?>
                    <a href="?page=<?= $i ?>" class="page-link <?= $i == $pagination['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
                <?php elseif (abs($i - $pagination['current_page']) == 3): ?>
                    <span class="page-link" style="border:none;background:none;pointer-events:none;">...</span>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                <a href="?page=<?= $pagination['current_page'] + 1 ?>" class="page-link"><i class="bi bi-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="modal-overlay" id="dataModalOverlay">
    <div class="data-modal">
        <div class="data-modal-header">
            <div class="data-modal-title" id="dataModalTitle"><i class="bi bi-file-earmark-code"></i> View Data</div>
            <button class="btn-close-modal" onclick="closeModal()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="data-modal-body">
            <div class="data-pane" id="paneOld" style="display:none;">
                <div class="data-pane-title">
                    <span><i class="bi bi-skip-backward" style="margin-right:.3rem;"></i> Previous State</span>
                    <span style="background:rgba(255,255,255,0.1);padding:2px 6px;border-radius:4px;">JSON</span>
                </div>
                <pre id="preOld"></pre>
            </div>
            <div class="data-pane" id="paneNew" style="display:none;">
                <div class="data-pane-title">
                    <span><i class="bi bi-skip-forward" style="margin-right:.3rem;"></i> New State</span>
                    <span style="background:rgba(255,255,255,0.1);padding:2px 6px;border-radius:4px;">JSON</span>
                </div>
                <pre id="preNew"></pre>
            </div>
        </div>
    </div>
</div>

<script>
// Syntax highlighting function for JSON
function syntaxHighlight(json) {
    if (typeof json != 'string') {
        json = JSON.stringify(json, undefined, 2);
    }
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'json-number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'json-key';
            } else {
                cls = 'json-string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'json-boolean';
        } else if (/null/.test(match)) {
            cls = 'json-null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}

function viewData(data) {
    const titleEl = document.getElementById('dataModalTitle');
    titleEl.innerHTML = `<i class="bi ${data.icon}" style="color:${data.color}"></i> ${data.title}`;
    
    const paneOld = document.getElementById('paneOld');
    const paneNew = document.getElementById('paneNew');
    
    if (data.old) {
        paneOld.style.display = 'flex';
        document.getElementById('preOld').innerHTML = syntaxHighlight(data.old);
    } else {
        paneOld.style.display = 'none';
    }
    
    if (data.new) {
        paneNew.style.display = 'flex';
        document.getElementById('preNew').innerHTML = syntaxHighlight(data.new);
    } else {
        paneNew.style.display = 'none';
    }
    
    // Auto-adjust layout if only one pane is visible
    if ((data.old && !data.new) || (!data.old && data.new)) {
        document.querySelector('.data-modal-body').style.flexDirection = 'column';
    } else {
        document.querySelector('.data-modal-body').style.flexDirection = 'row';
    }
    
    document.getElementById('dataModalOverlay').classList.add('active');
}

function closeModal() {
    document.getElementById('dataModalOverlay').classList.remove('active');
}

document.getElementById('dataModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Filtering and Searching
let currentFilter = 'all';
function filterLogs(q){ applyFilters(q, currentFilter); }
function setFilter(btn, f){ 
    currentFilter = f; 
    document.querySelectorAll('.pill-tab').forEach(b => b.classList.remove('active')); 
    btn.classList.add('active'); 
    applyFilters(document.getElementById('logSearch').value, f); 
}
function applyFilters(q, f){
    q = (q || '').toLowerCase();
    const rows = document.querySelectorAll('#logTbody tr[data-filter]');
    let visibleCount = 0;
    
    rows.forEach(r => {
        const mQ = !q || r.dataset.search.includes(q);
        const mF = f === 'all' || r.dataset.filter === f;
        const show = mQ && mF;
        
        r.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
}
</script>
