<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign In — HealthChain</title>
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
    width: 1060px;
    max-width: 98vw;
    min-height: 700px;
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
    padding: 3rem 3.75rem;
    min-height: 700px;
}
.auth-brand { display: flex; align-items: center; gap: 0.65rem; margin-bottom: 2rem; }
.auth-app-icon {
    width: 40px; height: 40px; border-radius: 11px;
    background: linear-gradient(135deg, #0ea5e9, #0369a1);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 12px rgba(14,165,233,0.3); flex-shrink: 0;
}
.auth-brand-name { font-size: 1rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
.auth-heading { font-size: 1.55rem; font-weight: 800; color: #0f172a; letter-spacing: -0.03em; margin-bottom: 0.25rem; }
.auth-subheading { font-size: 0.82rem; color: #64748b; margin-bottom: 1.75rem; }
.auth-tabs { display: flex; background: #f3f4f6; border-radius: 12px; padding: 4px; margin-bottom: 1.75rem; }
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
    padding: 0.8rem 1rem; border-radius: 10px;
    font-size: 0.82rem; font-weight: 500; margin-bottom: 1.25rem;
    animation: slideDown 0.2s ease;
}
@keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: none; } }
.auth-alert-err { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
.auth-alert-ok  { background: #ecfdf5; border: 1px solid #bbf7d0; color: #15803d; }
.auth-alert i { margin-top: 0.05rem; flex-shrink: 0; }
.auth-field { margin-bottom: 1rem; }
.auth-label { font-size: 0.72rem; font-weight: 700; color: #1a1a1a; letter-spacing: 0.04em; margin-bottom: 0.4rem; display: block; }
.auth-input-wrap { position: relative; display: flex; align-items: center; }
.auth-input {
    width: 100%;
    padding: 0.75rem 2.75rem 0.75rem 1rem;
    border: 1.5px solid #e5e7eb; border-radius: 11px;
    font-size: 0.875rem; font-family: inherit;
    color: #1a1a1a; background: #fff; outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.auth-input::placeholder { color: #bbb; }
.auth-input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3.5px rgba(14,165,233,0.12); }
.auth-input-icon { position: absolute; right: 0.9rem; color: #bbb; font-size: 0.95rem; pointer-events: none; }
.auth-input-btn {
    position: absolute; right: 0.85rem;
    background: none; border: none; color: #bbb;
    cursor: pointer; font-size: 0.95rem; padding: 0.2rem;
    transition: color 0.12s; line-height: 1;
}
.auth-input-btn:hover { color: #555; }
.auth-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
.auth-remember { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; user-select: none; }
.auth-checkbox {
    width: 17px; height: 17px; border-radius: 5px;
    border: 1.5px solid #e5e7eb; background: #fff;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.15s; flex-shrink: 0;
}
.auth-checkbox.checked { background: #0ea5e9; border-color: #0ea5e9; }
.auth-checkbox.checked::after {
    content: ''; width: 9px; height: 5px;
    border-left: 2px solid #fff; border-bottom: 2px solid #fff;
    transform: rotate(-45deg); margin-top: -2px;
}
.auth-remember-label { font-size: 0.8rem; color: #475569; font-weight: 500; }
.auth-forgot { font-size: 0.8rem; color: #0ea5e9; font-weight: 600; text-decoration: none; }
.auth-forgot:hover { text-decoration: underline; }
.auth-btn-primary {
    width: 100%; padding: 0.82rem;
    border: none; border-radius: 12px;
    background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
    color: #fff; font-size: 0.9rem; font-weight: 700;
    cursor: pointer; letter-spacing: 0.01em;
    transition: transform 0.12s, box-shadow 0.15s;
    box-shadow: 0 4px 16px rgba(14,165,233,0.3);
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    margin-bottom: 1.5rem;
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
.auth-link-row { text-align: center; font-size: 0.8rem; color: #64748b; }
.auth-link-row a { color: #0ea5e9; font-weight: 600; text-decoration: none; }
.auth-link-row a:hover { text-decoration: underline; }
/* RIGHT visual panel */
.auth-visual-panel {
    width: 440px; flex-shrink: 0;
    background: linear-gradient(145deg, #0c3a6b 0%, #0f4c81 50%, #0a5a9a 100%);
    position: relative; overflow: hidden;
    display: flex; flex-direction: column;
    align-items: flex-start; justify-content: flex-end;
    padding: 3rem;
    min-height: 700px;
}
.auth-visual-panel::before {
    content: ''; position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 65% 55% at 75% 15%, rgba(14,165,233,0.22) 0%, transparent 60%),
        radial-gradient(ellipse 45% 65% at 15% 85%, rgba(2,132,199,0.18) 0%, transparent 55%);
}
.auth-visual-content { position: relative; z-index: 1; width: 100%; }
.auth-stat-pills { display: flex; flex-direction: column; gap: 0.65rem; margin-bottom: 2rem; }
.auth-stat-pill {
    display: inline-flex; align-items: center; gap: 0.65rem;
    background: rgba(255,255,255,0.08); backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px; padding: 0.65rem 1rem;
    animation: floatUp 0.5s ease backwards;
}
.auth-stat-pill:nth-child(2) { animation-delay: 0.1s; }
.auth-stat-pill:nth-child(3) { animation-delay: 0.2s; }
@keyframes floatUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
.auth-stat-icon {
    width: 30px; height: 30px; border-radius: 8px;
    background: rgba(14,165,233,0.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.9rem; color: #7dd3fc; flex-shrink: 0;
}
.auth-stat-text .val { font-size: 0.85rem; font-weight: 700; color: #fff; line-height: 1.2; }
.auth-stat-text .lbl { font-size: 0.65rem; color: rgba(255,255,255,0.45); }
.auth-visual-title { font-size: 1.5rem; font-weight: 800; color: #fff; letter-spacing: -0.03em; line-height: 1.2; margin-bottom: 0.5rem; }
.auth-visual-sub { font-size: 0.78rem; color: rgba(255,255,255,0.45); line-height: 1.6; }
.auth-role-tags { display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap; }
.auth-role-tag { font-size: 0.62rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; padding: 0.22rem 0.6rem; border-radius: 5px; }
.rt-super { background: rgba(14,165,233,0.15);  color: #7dd3fc; border: 1px solid rgba(14,165,233,0.3); }
.rt-mgr   { background: rgba(20,184,166,0.12);  color: #5eead4; border: 1px solid rgba(20,184,166,0.25); }
.rt-staff { background: rgba(245,158,11,0.12);  color: #fbbf24; border: 1px solid rgba(245,158,11,0.25); }
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

/* Going to register (Sign Up clicked) */
html[data-nav='to-register']::view-transition-old(auth-form) {
    animation: slideOutToLeft 0.28s cubic-bezier(0.4,0,0.2,1) both;
}
html[data-nav='to-register']::view-transition-new(auth-form) {
    animation: slideInFromRight 0.28s cubic-bezier(0.4,0,0.2,1) both;
}
/* Going to login (Sign In clicked) */
html[data-nav='to-login']::view-transition-old(auth-form) {
    animation: slideOutToRight 0.28s cubic-bezier(0.4,0,0.2,1) both;
}
html[data-nav='to-login']::view-transition-new(auth-form) {
    animation: slideInFromLeft 0.28s cubic-bezier(0.4,0,0.2,1) both;
}
/* Visual panel: no animation — stays perfectly in place */
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

        <h1 class="auth-heading">Welcome Back!</h1>
        <p class="auth-subheading">Sign in to your account to continue</p>

        <div class="auth-tabs">
            <span class="auth-tab active">Sign In</span>
            <a href="/register" class="auth-tab">Sign Up</a>
        </div>

        <?php if ($error = flash('error')): ?>
        <div class="auth-alert auth-alert-err">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span><?= esc($error) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($success = flash('success')): ?>
        <div class="auth-alert auth-alert-ok">
            <i class="bi bi-check-circle-fill"></i>
            <span><?= esc($success) ?></span>
        </div>
        <?php endif; ?>

        <form action="/login" method="POST" autocomplete="off" id="loginForm">
            <?= csrf_field() ?>

            <div class="auth-field">
                <label class="auth-label">Email Address</label>
                <div class="auth-input-wrap">
                    <input type="email" name="email" class="auth-input"
                           placeholder="you@healthcare.com"
                           value="<?= esc(old('email')) ?>" required autocomplete="username">
                    <i class="bi bi-envelope auth-input-icon"></i>
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-label">Password</label>
                <div class="auth-input-wrap">
                    <input type="password" name="password" id="loginPassword" class="auth-input"
                           placeholder="••••••••" required style="padding-right:2.75rem;">
                    <button type="button" class="auth-input-btn" onclick="togglePwd('loginPassword','loginEye')">
                        <i class="bi bi-eye" id="loginEye"></i>
                    </button>
                </div>
            </div>

            <div class="auth-row">
                <div class="auth-remember" onclick="toggleRemember()">
                    <div class="auth-checkbox" id="rememberBox"></div>
                    <span class="auth-remember-label">Remember me</span>
                </div>
                <a href="#" class="auth-forgot">Forgot Password?</a>
            </div>

            <button type="submit" class="auth-btn-primary" id="loginBtn">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>
        </form>

        <p class="auth-link-row">Don't have an account? <a href="/register">Create one →</a></p>
    </div>

    <!-- VISUAL -->
    <div class="auth-visual-panel">
        <div class="auth-visual-content">
            <div class="auth-stat-pills">
                <div class="auth-stat-pill">
                    <div class="auth-stat-icon"><i class="bi bi-capsule-pill"></i></div>
                    <div class="auth-stat-text">
                        <div class="val">Medicine Tracking</div>
                        <div class="lbl">Full inventory management</div>
                    </div>
                </div>
                <div class="auth-stat-pill">
                    <div class="auth-stat-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    <div class="auth-stat-text">
                        <div class="val">Role-Based Access</div>
                        <div class="lbl">Secure multi-user system</div>
                    </div>
                </div>
                <div class="auth-stat-pill">
                    <div class="auth-stat-icon"><i class="bi bi-clock-history"></i></div>
                    <div class="auth-stat-text">
                        <div class="val">Expiry Alerts</div>
                        <div class="lbl">Auto 30-day notifications</div>
                    </div>
                </div>
            </div>
            <h2 class="auth-visual-title">Healthcare<br>Supply Chain</h2>
            <p class="auth-visual-sub">Manage medicines, batches, and stock<br>from one powerful platform.</p>
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
function togglePwd(inputId, iconId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(iconId);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    ico.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
let remembered = false;
function toggleRemember() {
    remembered = !remembered;
    document.getElementById('rememberBox').classList.toggle('checked', remembered);
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

document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.innerHTML = '<i class="bi bi-arrow-repeat" style="display:inline-block;animation:spin 0.8s linear infinite;"></i> Signing in...';
    btn.style.opacity = '0.85';
    btn.disabled = true;
});
</script>
</body>
</html>
