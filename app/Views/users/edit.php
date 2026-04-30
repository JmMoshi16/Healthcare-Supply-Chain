<?php use App\Core\View; View::layout('app'); $title = 'Edit User'; ?>

<a href="/users" class="back-link"><i class="bi bi-arrow-left"></i> Back to Users</a>

<div class="page-header" style="margin-bottom:1.75rem;">
    <div>
        <h1 class="page-title">Edit: <?= esc($user['fullname']) ?></h1>
        <p class="page-subtitle">Update user details and role assignment</p>
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
                <span class="card-title">User Details</span>
                <span class="badge badge-<?= $user['is_active'] ? 'success' : 'danger' ?>"><?= $user['is_active'] ? 'Active' : 'Inactive' ?></span>
            </div>
            <div style="padding:1.5rem;">
                <form method="POST" action="/users/<?= (int)$user['id'] ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">

                    <div class="pf-row">
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Full Name <span class="pf-required">*</span></label>
                                <input type="text" name="fullname" class="form-input" value="<?= esc($user['fullname']) ?>" required>
                            </div>
                        </div>
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Email <span class="pf-required">*</span></label>
                                <input type="email" name="email" class="form-input" value="<?= esc($user['email']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="pf-row">
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Password <span style="color:var(--text-muted);font-weight:400;">(leave blank to keep)</span></label>
                                <input type="password" name="password" class="form-input" placeholder="••••••••" minlength="8">
                            </div>
                        </div>
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Role <span class="pf-required">*</span></label>
                                <div class="sl-select-wrap">
                                    <i class="bi bi-shield-check sl-select-icon"></i>
                                    <select name="role" class="form-input sl-has-icon" required>
                                        <option value="superadmin" <?= $user['role'] === 'superadmin' ? 'selected' : '' ?>>Super Admin</option>
                                        <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                                        <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-input">
                            <option value="1" <?= $user['is_active'] ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= !$user['is_active'] ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="pf-form-actions">
                        <button type="submit" class="btn-create-order"><i class="bi bi-check-lg"></i> Update User</button>
                        <a href="/users" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="pf-form-side">
        <div class="card">
            <div class="card-header"><span class="card-title">Account Info</span></div>
            <div style="padding:1.25rem;">
                <div class="pshow-meta-grid">
                    <div class="pshow-meta-item">
                        <div class="pshow-meta-label">Current Role</div>
                        <div class="pshow-meta-value"><?= esc(ucfirst($user['role'])) ?></div>
                    </div>
                    <div class="pshow-meta-item">
                        <div class="pshow-meta-label">Status</div>
                        <div class="pshow-meta-value"><?= $user['is_active'] ? 'Active' : 'Inactive' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
