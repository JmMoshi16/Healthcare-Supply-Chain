<?php use App\Core\View; View::layout('app'); $title = 'Add User'; ?>

<a href="/users" class="back-link"><i class="bi bi-arrow-left"></i> Back to Users</a>

<div class="page-header" style="margin-bottom:1.75rem;">
    <div>
        <h1 class="page-title">Add New User</h1>
        <p class="page-subtitle">Create a new team member account with role assignment</p>
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
                <span class="card-title">User Information</span>
                <span style="font-size:.72rem;color:var(--text-muted);">* Required fields</span>
            </div>
            <div style="padding:1.5rem;">
                <form method="POST" action="/users">
                    <?= csrf_field() ?>

                    <div class="pf-row">
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Full Name <span class="pf-required">*</span></label>
                                <input type="text" name="fullname" class="form-input" placeholder="e.g. Juan dela Cruz" value="<?= esc(old('fullname')) ?>" required>
                            </div>
                        </div>
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Email <span class="pf-required">*</span></label>
                                <input type="email" name="email" class="form-input" placeholder="user@healthcare.com" value="<?= esc(old('email')) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="pf-row">
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Password <span class="pf-required">*</span></label>
                                <input type="password" name="password" class="form-input" placeholder="Min. 8 characters" required minlength="8">
                            </div>
                        </div>
                        <div class="pf-col-6">
                            <div class="form-group">
                                <label class="form-label">Role <span class="pf-required">*</span></label>
                                <div class="sl-select-wrap">
                                    <i class="bi bi-shield-check sl-select-icon"></i>
                                    <select name="role" class="form-input sl-has-icon" required>
                                        <option value="">Select role...</option>
                                        <option value="superadmin" <?= old('role') === 'superadmin' ? 'selected' : '' ?>>Super Admin</option>
                                        <option value="manager" <?= old('role') === 'manager' ? 'selected' : '' ?>>Manager</option>
                                        <option value="staff" <?= old('role') === 'staff' ? 'selected' : '' ?>>Staff</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pf-form-actions">
                        <button type="submit" class="btn-create-order"><i class="bi bi-check-lg"></i> Create User</button>
                        <a href="/users" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="pf-form-side">
        <div class="card">
            <div class="card-header"><span class="card-title">Role Access</span><i class="bi bi-shield-lock" style="color:var(--accent-orange);"></i></div>
            <div style="padding:1.25rem;">
                <ul class="pf-tips-list">
                    <li><i class="bi bi-shield-fill-check" style="color:var(--accent-orange);"></i> <strong>SuperAdmin</strong> — Full access to all modules</li>
                    <li><i class="bi bi-shield-fill" style="color:#3b82f6;"></i> <strong>Manager</strong> — Medicines, batches, stocks</li>
                    <li><i class="bi bi-shield" style="color:#22c55e;"></i> <strong>Staff</strong> — Read-only access</li>
                </ul>
            </div>
        </div>
    </div>
</div>
