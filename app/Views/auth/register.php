<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create Account — HealthChain</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body {
    font-family: 'Inter', system-ui, sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f7ff;
    -webkit-font-smoothing: antialiased;
    padding: 1.5rem 1rem;
}
.auth-wrap {
    display: flex;
    width: 940px;
    max-width: 98vw;
    min-height: 620px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.12);
    overflow: hidden;
}
.auth-form-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 2.5rem 3.25rem;
    min-height: 620px;
}
.auth-brand { display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1.5rem; }
.auth-app-icon {
    width: 40px; height: 40px; border-radius: 11px;
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 12px rgba(14,165,233,0.35); flex-shrink: 0;
}
.auth-brand-name { font-size: 1rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
.auth-heading { font-size: 1.45rem; font-weight: 800; color: #0f172a; letter-spacing: -0.03em; margin-bottom: 0.2rem; }
.auth-subheading { font-size: 0.8rem; color: #64748b; margin-bottom: 1.35rem; }
.auth-tabs { display: flex; background: #f3f4f6; border-radius: 12px; padding: 4px; margin-bottom: 1.35rem; }
.auth-tab {
    flex: 1; padding: 0.52rem; border: none; border-radius: 9px;
    font-size: 0.84rem; font-weight: 600; cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
    background: transparent; color: #999;
    text-decoration: none; text-align: center; display: block;
}
.auth-tab.active { background: #fff; color: #1a1a1a; box-shadow: 0 1px 6px rgba(0,0,0,0.1); }
.auth-alert {
    display: flex; align-items: flex-start; gap: 0.6rem;
    padding: 0.75rem 1rem; border-radius: 10px;
    font-size: 0.82rem; font-weight: 500; margin-bottom: 1rem;
    animation: slideDown 0.2s ease;
}
@keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: none; } }
.auth-alert-err { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
.auth-alert i { margin-top: 0.05rem; flex-shrink: 0; }
.auth-field { margin-bottom: 0.85rem; }
.auth-label { font-size: 0.72rem; font-weight: 700; color: #0f172a; letter-spacing: 0.04em; margin-bottom: 0.35rem; display: block; }
.auth-input-wrap { position: relative; display: flex; align-items: center; }
.auth-input {
    width: 100%; padding: 0.7rem 2.6rem 0.7rem 0.95rem;
    border: 1.5px solid #e5e7eb; border-radius: 11px;
    font-size: 0.875rem; font-family: inherit;
    color: #0f172a; background: #fff; outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.auth-input::placeholder { color: #bbb; }
.auth-input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3.5px rgba(14,165,233,0.12); }
.auth-input-icon { position: absolute; right: 0.9rem; color: #bbb; font-size: 0.9rem; pointer-events: none; }
.auth-input-btn {
    position: absolute; right: 0.85rem;
    background: none; border: none; color: #bbb;
    cursor: pointer; font-size: 0.9rem; padding: 0.2rem;
    transition: color 0.12s; line-height: 1;
}
.auth-input-btn:hover { color: #555; }
.auth-hint { font-size: 0.7rem; color: #aaa; margin-top: 0.3rem; display: flex; align-items: center; gap: 0.3rem; }
/* Role cards */
.auth-role-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.6rem; margin-bottom: 0.85rem; }
.auth-role-card input[type="radio"] { display: none; }
.auth-role-card {
    border: 1.5px solid #e5e7eb; border-radius: 11px;
    padding: 0.85rem 0.5rem 0.75rem; text-align: center;
    cursor: pointer; transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
    user-select: none; background: #fff; display: block;
}
.auth-role-card:hover { border-color: #ddd; background: #fafafa; }
.auth-role-card.selected { border-color: #0ea5e9; background: #f0f9ff; box-shadow: 0 0 0 3px rgba(14,165,233,0.15); }
.auth-role-icon-wrap {
    width: 36px; height: 36px; border-radius: 10px;
    background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 0.45rem; font-size: 1.05rem; color: #888;
    transition: background 0.15s, color 0.15s;
}
.auth-role-card.selected .auth-role-icon-wrap { background: rgba(14,165,233,0.12); color: #0ea5e9; }
.auth-role-title { font-size: 0.76rem; font-weight: 700; color: #0f172a; display: block; line-height: 1.2; }
.auth-role-sub   { font-size: 0.62rem; color: #aaa; display: block; margin-top: 0.1rem; }
.auth-role-card.selected .auth-role-title { color: #0ea5e9; }
/* Verify field */
.auth-verify-wrap { overflow: hidden; max-height: 0; transition: max-height 0.28s cubic-bezier(0.4,0,0.2,1), opacity 0.25s; opacity: 0; }
.auth-verify-wrap.visible { max-height: 110px; opacity: 1; }
/* Password grid */
.auth-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
/* Strength */
.auth-pwd-strength { margin-top: 0.35rem; }
.auth-strength-bar { height: 3px; background: #e5e7eb; border-radius: 2px; overflow: hidden; margin-bottom: 0.2rem; }
.auth-strength-fill { height: 100%; border-radius: 2px; transition: width 0.3s, background 0.3s; }
.auth-strength-label { font-size: 0.65rem; font-weight: 600; }
/* Submit */
.auth-btn-primary {
    width: 100%; padding: 0.8rem;
    border: none; border-radius: 12px;
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: #fff; font-size: 0.9rem; font-weight: 700;
    cursor: pointer; letter-spacing: 0.01em;
    transition: transform 0.12s, box-shadow 0.15s;
    box-shadow: 0 4px 14px rgba(14,165,233,0.35);
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    margin-top: 0.5rem; margin-bottom: 1rem;
    position: relative; overflow: hidden;
}
.auth-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(14,165,233,0.45); }
.auth-btn-primary:active { transform: translateY(0); }
.auth-btn-primary::after {
    content: ''; position: absolute; top: 0; left: -100%;
    width: 60%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    transition: left 0.5s;
}
.auth-btn-primary:hover::after { left: 150%; }
.auth-terms { font-size: 0.72rem; color: #aaa; text-align: center; margin-bottom: 0.6rem; }
.auth-terms a { color: #0ea5e9; text-decoration: none; font-weight: 600; }
.auth-link-row { text-align: center; font-size: 0.8rem; color: #999; }
.auth-link-row a { color: #0ea5e9; font-weight: 600; text-decoration: none; }
.auth-link-row a:hover { text-decoration: underline; }
/* RIGHT visual */
.auth-visual-panel {
    width: 380px; flex-shrink: 0;
    background: #0f4c81;
    position: relative; overflow: hidden;
    display: flex; flex-direction: column;
    align-items: flex-start; justify-content: flex-end;
    padding: 2.5rem; min-height: 620px;
}
.auth-visual-panel::before {
    content: ''; position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 60% 80% at 70% 20%, rgba(14,165,233,0.45) 0%, transparent 65%),
        radial-gradient(ellipse 50% 60% at 20% 80%, rgba(20,184,166,0.3) 0%, transparent 60%),
        radial-gradient(ellipse 40% 50% at 80% 70%, rgba(2,132,199,0.35) 0%, transparent 55%);
}
.auth-visual-content { position: relative; z-index: 1; width: 100%; }
.auth-steps { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem; }
.auth-step { display: flex; align-items: flex-start; gap: 0.8rem; }
.auth-step-num {
    width: 26px; height: 26px; border-radius: 8px;
    background: rgba(14,165,233,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.7rem; font-weight: 800; color: #7dd3fc; flex-shrink: 0;
}
.auth-step-title { font-size: 0.82rem; font-weight: 700; color: #fff; line-height: 1.2; }
.auth-step-sub { font-size: 0.7rem; color: rgba(255,255,255,0.4); }
.auth-visual-title { font-size: 1.45rem; font-weight: 800; color: #fff; letter-spacing: -0.03em; line-height: 1.2; margin-bottom: 0.5rem; }
.auth-visual-sub { font-size: 0.78rem; color: rgba(255,255,255,0.45); line-height: 1.6; }
.auth-role-tags { display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap; }
.auth-role-tag { font-size: 0.62rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; padding: 0.22rem 0.6rem; border-radius: 5px; }
.rt-super { background: rgba(14,165,233,0.15);  color: #7dd3fc; border: 1px solid rgba(14,165,233,0.3); }
.rt-mgr   { background: rgba(20,184,166,0.12);  color: #5eead4; border: 1px solid rgba(20,184,166,0.25); }
.rt-staff { background: rgba(245,158,11,0.12); color: #fbbf24; border: 1px solid rgba(245,158,11,0.25); }
.auth-visual-copy {
    position: absolute; bottom: 1.25rem; left: 1.25rem; right: 1.25rem;
    background: rgba(255,255,255,0.05); backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.08); border-radius: 10px;
    padding: 0.65rem 1rem; font-size: 0.62rem; color: rgba(255,255,255,0.3);
    line-height: 1.5; z-index: 1;
}
@media (max-width: 700px) {
    .auth-visual-panel { display: none; }
    .auth-form-panel { padding: 2rem 1.75rem; min-height: auto; }
    .auth-wrap { border-radius: 18px; min-height: auto; }
    .auth-grid-2 { grid-template-columns: 1fr; }
    .auth-role-grid { grid-template-columns: 1fr 1fr; }
}
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* ── View Transitions: lock the visual panel, slide the form ── */
.auth-visual-panel { view-transition-name: auth-visual; }
.auth-form-panel   { view-transition-name: auth-form; }
.auth-wrap         { view-transition-name: auth-wrap; }

@keyframes slideInFromRight {
    from { opacity: 0; transform: translateX(32px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes slideOutToLeft {
    from { opacity: 1; transform: translateX(0); }
    to   { opacity: 0; transform: translateX(-32px); }
}
@keyframes slideInFromLeft {
    from { opacity: 0; transform: translateX(-32px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes slideOutToRight {
    from { opacity: 1; transform: translateX(0); }
    to   { opacity: 0; transform: translateX(32px); }
}

html[data-nav='to-register']::view-transition-old(auth-form) {
    animation: slideOutToLeft 0.28s cubic-bezier(0.4,0,0.2,1) both;
}
html[data-nav='to-register']::view-transition-new(auth-form) {
    animation: slideInFromRight 0.28s cubic-bezier(0.4,0,0.2,1) both;
}
html[data-nav='to-login']::view-transition-old(auth-form) {
    animation: slideOutToRight 0.28s cubic-bezier(0.4,0,0.2,1) both;
}
html[data-nav='to-login']::view-transition-new(auth-form) {
    animation: slideInFromLeft 0.28s cubic-bezier(0.4,0,0.2,1) both;
}
::view-transition-old(auth-visual),
::view-transition-new(auth-visual) {
    animation: none;
    mix-blend-mode: normal;
}
::view-transition-old(auth-wrap),
::view-transition-new(auth-wrap) {
    animation: none;
}
.auth-tab:hover:not(.active) {
    color: #64748b;
    background: rgba(0,0,0,0.04);
    border-radius: 9px;
}
</style>
</head>
<body>
<div class="auth-wrap">

    <!-- FORM -->
    <div class="auth-form-panel">

        <div class="auth-brand">
            <div class="auth-app-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" stroke="white" stroke-width="1.6"/>
                    <path d="M12 7v10M7 12h10" stroke="white" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </div>
            <span class="auth-brand-name">HealthChain</span>
        </div>

        <h1 class="auth-heading">Create Account</h1>
        <p class="auth-subheading">Join the platform and start managing healthcare supply</p>

        <div class="auth-tabs">
            <a href="/login" class="auth-tab">Sign In</a>
            <span class="auth-tab active">Sign Up</span>
        </div>

        <?php if ($error = flash('error')): ?>
        <div class="auth-alert auth-alert-err">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span><?= esc($error) ?></span>
        </div>
        <?php endif; ?>

        <form action="/register" method="POST" id="registerForm">
            <?= csrf_field() ?>

            <!-- Full Name -->
            <div class="auth-field">
                <label class="auth-label">Full Name</label>
                <div class="auth-input-wrap">
                    <input type="text" name="fullname" class="auth-input"
                           placeholder="Juan dela Cruz"
                           value="<?= esc(old('fullname')) ?>" required>
                    <i class="bi bi-person auth-input-icon"></i>
                </div>
            </div>

            <!-- Role cards -->
            <div class="auth-field">
                <label class="auth-label">Select Your Role</label>
                <div class="auth-role-grid">

                    <label class="auth-role-card" id="card-superadmin" onclick="selectRole(event,'superadmin')">
                        <input type="radio" name="role" value="superadmin" <?= old('role')==='superadmin'?'checked':'' ?>>
                        <div class="auth-role-icon-wrap"><i class="bi bi-shield-lock-fill"></i></div>
                        <span class="auth-role-title">Super Admin</span>
                        <span class="auth-role-sub">Full access</span>
                    </label>

                    <label class="auth-role-card" id="card-manager" onclick="selectRole(event,'manager')">
                        <input type="radio" name="role" value="manager" <?= old('role')==='manager'?'checked':'' ?>>
                        <div class="auth-role-icon-wrap"><i class="bi bi-bar-chart-line-fill"></i></div>
                        <span class="auth-role-title">Manager</span>
                        <span class="auth-role-sub">Inventory ops</span>
                    </label>

                    <label class="auth-role-card" id="card-staff" onclick="selectRole(event,'staff')">
                        <input type="radio" name="role" value="staff" <?= old('role')==='staff'?'checked':'' ?>>
                        <div class="auth-role-icon-wrap"><i class="bi bi-person-badge-fill"></i></div>
                        <span class="auth-role-title">Staff</span>
                        <span class="auth-role-sub">View only</span>
                    </label>

                </div>
            </div>

            <!-- Verification code (slides in on role select) -->
            <div class="auth-verify-wrap" id="verifyWrap">
                <div class="auth-field">
                    <label class="auth-label" id="verifyLabel">Verification Code</label>
                    <div class="auth-input-wrap">
                        <input type="text" name="verify_code" id="inputVerify" class="auth-input"
                               placeholder="" value="<?= esc(old('verify_code')) ?>">
                        <i class="bi bi-key-fill auth-input-icon"></i>
                    </div>
                    <div class="auth-hint" id="verifyHint">
                        <i class="bi bi-info-circle"></i>
                        <span id="verifyHintText"></span>
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="auth-field">
                <label class="auth-label">Email Address</label>
                <div class="auth-input-wrap">
                    <input type="email" name="email" id="inputEmail" class="auth-input"
                           placeholder="Select a role first"
                           value="<?= esc(old('email')) ?>" required>
                    <i class="bi bi-envelope auth-input-icon"></i>
                </div>
            </div>

            <!-- Passwords -->
            <div class="auth-grid-2">
                <div class="auth-field">
                    <label class="auth-label">Password</label>
                    <div class="auth-input-wrap">
                        <input type="password" name="password" id="regPwd" class="auth-input"
                               placeholder="Min. 8 chars" required oninput="checkStrength(this.value)">
                        <button type="button" class="auth-input-btn" onclick="togglePasswords()" title="Toggle Password Visibility">
                            <i class="bi bi-eye" id="pwdEyeIcon"></i>
                        </button>
                    </div>
                    <div class="auth-pwd-strength">
                        <div class="auth-strength-bar">
                            <div class="auth-strength-fill" id="strengthFill" style="width:0%;"></div>
                        </div>
                        <span class="auth-strength-label" id="strengthLabel" style="color:#bbb;"></span>
                    </div>
                </div>
                <div class="auth-field">
                    <label class="auth-label">Confirm Password</label>
                    <div class="auth-input-wrap">
                        <input type="password" name="password_confirm" id="regPwd2" class="auth-input"
                               placeholder="Repeat" required oninput="checkMatch()" style="padding-right: 0.95rem;">
                    </div>
                    <div class="auth-hint" id="matchHint" style="display:none;"></div>
                </div>
            </div>

            <p class="auth-terms">By creating an account you agree to our
                <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
            </p>

            <button type="submit" class="auth-btn-primary" id="registerBtn">
                <i class="bi bi-person-plus-fill"></i> Create Account
            </button>
        </form>

        <p class="auth-link-row">Already have an account? <a href="/login">Sign in →</a></p>
    </div>

    <!-- VISUAL -->
    <div class="auth-visual-panel">
        <div class="auth-visual-content">
            <div class="auth-steps">
                <div class="auth-step">
                    <div class="auth-step-num">1</div>
                    <div>
                        <div class="auth-step-title">Choose your role</div>
                        <div class="auth-step-sub">Admin, Manager, or Staff</div>
                    </div>
                </div>
                <div class="auth-step">
                    <div class="auth-step-num">2</div>
                    <div>
                        <div class="auth-step-title">Verify your identity</div>
                        <div class="auth-step-sub">Enter your assigned verification code</div>
                    </div>
                </div>
                <div class="auth-step">
                    <div class="auth-step-num">3</div>
                    <div>
                        <div class="auth-step-title">Access the platform</div>
                        <div class="auth-step-sub">Manage medicines, batches &amp; stock</div>
                    </div>
                </div>
            </div>
            <h2 class="auth-visual-title">Join the<br>Platform</h2>
            <p class="auth-visual-sub">Create your account to start managing<br>the Healthcare Supply Chain.</p>
            <div class="auth-role-tags">
                <span class="auth-role-tag rt-super">Super Admin</span>
                <span class="auth-role-tag rt-mgr">Manager</span>
                <span class="auth-role-tag rt-staff">Staff</span>
            </div>
        </div>
        <div class="auth-visual-copy">
            © <?= date('Y') ?> HealthChain. All rights reserved.
            Secure multi-role healthcare supply chain system.
        </div>
    </div>

</div>
<script>
const roleCfg = {
    superadmin: { ph:'admin@healthcare.com',   lbl:'Admin Secret Code', hint:'Enter the super admin registration code.',  vph:'e.g. HC@SUPERADMIN2024' },
    manager:    { ph:'manager@healthcare.com', lbl:'Manager ID',        hint:'Enter your assigned manager ID.',           vph:'e.g. MGR-001' },
    staff:      { ph:'staff@healthcare.com',   lbl:'Staff ID',          hint:'Enter your assigned staff ID.',             vph:'e.g. STF-0001' },
};

function selectRole(e, role) {
    document.querySelectorAll('.auth-role-card').forEach(c => c.classList.remove('selected'));
    const card = document.getElementById('card-' + role);
    card.classList.add('selected');
    card.querySelector('input[type="radio"]').checked = true;
    const cfg = roleCfg[role];
    document.getElementById('inputEmail').placeholder    = cfg.ph;
    document.getElementById('verifyLabel').textContent   = cfg.lbl;
    document.getElementById('inputVerify').placeholder   = cfg.vph;
    document.getElementById('verifyHintText').textContent = cfg.hint;
    document.getElementById('inputVerify').required = true;
    document.getElementById('verifyWrap').classList.add('visible');
}

(function() {
    const checked = document.querySelector('input[name="role"]:checked');
    if (checked) selectRole(new Event('click'), checked.value);
})();

function togglePasswords() {
    const p1 = document.getElementById('regPwd');
    const p2 = document.getElementById('regPwd2');
    const ico = document.getElementById('pwdEyeIcon');
    const newType = p1.type === 'password' ? 'text' : 'password';
    p1.type = newType;
    p2.type = newType;
    ico.className = newType === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function checkStrength(val) {
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    let score = 0;
    if (val.length >= 8)           score++;
    if (val.length >= 12)          score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { pct:'0%',   color:'#e5e7eb', txt:'' },
        { pct:'25%',  color:'#ef4444', txt:'Weak' },
        { pct:'50%',  color:'#f59e0b', txt:'Fair' },
        { pct:'75%',  color:'#3b82f6', txt:'Good' },
        { pct:'100%', color:'#22c55e', txt:'Strong' },
    ];
    const l = levels[Math.min(score, 4)];
    fill.style.width      = l.pct;
    fill.style.background = l.color;
    label.textContent     = l.txt;
    label.style.color     = l.color;
}

function checkMatch() {
    const p1   = document.getElementById('regPwd').value;
    const p2   = document.getElementById('regPwd2').value;
    const hint = document.getElementById('matchHint');
    if (!p2) { hint.style.display = 'none'; return; }
    hint.style.display = 'flex';
    hint.innerHTML = p1 === p2
        ? '<i class="bi bi-check-circle-fill" style="color:#22c55e;"></i><span style="color:#22c55e;">Passwords match</span>'
        : '<i class="bi bi-x-circle-fill" style="color:#ef4444;"></i><span style="color:#ef4444;">Passwords do not match</span>';
}

// Smooth tab navigation using View Transitions API
document.querySelectorAll('.auth-tab[href]').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        var href = this.href;
        var dir = href.includes('register') ? 'to-register' : 'to-login';
        if (document.startViewTransition) {
            document.documentElement.setAttribute('data-nav', dir);
            document.startViewTransition(function() {
                window.location.href = href;
                return new Promise(function(resolve) { setTimeout(resolve, 50); });
            });
        } else {
            window.location.href = href;
        }
    });
});

document.getElementById('registerForm').addEventListener('submit', function() {
    const btn = document.getElementById('registerBtn');
    btn.innerHTML = '<i class="bi bi-arrow-repeat" style="display:inline-block;animation:spin 0.8s linear infinite;"></i> Creating account...';
    btn.style.opacity = '0.85';
    btn.disabled = true;
});
</script>
</body>
</html>
