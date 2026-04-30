<?php
// Fix register.php
$f = 'app/Views/auth/register.php';
$c = file_get_contents($f);
$c = str_replace([
    'background: #f0f2f5;',
    'background: linear-gradient(135deg, #1a1a1a, #444);',
    'box-shadow: 0 4px 12px rgba(0,0,0,0.25); flex-shrink: 0;',
    ".auth-brand-name { font-size: 1rem; font-weight: 800; color: #1a1a1a;",
    ".auth-heading { font-size: 1.45rem; font-weight: 800; color: #1a1a1a;",
    ".auth-subheading { font-size: 0.8rem; color: #999;",
    '.auth-input:focus { border-color: #1a1a1a; box-shadow: 0 0 0 3.5px rgba(26,26,26,0.1); }',
    '.auth-role-card.selected { border-color: #1a1a1a; background: #f9f9f9; box-shadow: 0 0 0 3px rgba(26,26,26,0.08); }',
    '.auth-role-card.selected .auth-role-icon-wrap { background: rgba(26,26,26,0.1); color: #1a1a1a; }',
    ".auth-role-title { font-size: 0.76rem; font-weight: 700; color: #1a1a1a;",
    ".auth-role-card.selected .auth-role-title { color: #1a1a1a; }",
    'background: linear-gradient(135deg, #1a1a1a, #333);',
    'box-shadow: 0 4px 14px rgba(0,0,0,0.25);',
    '.auth-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.35); }',
    ".auth-terms a { color: #1a1a1a;",
    ".auth-link-row a { color: #1a1a1a;",
    "background: #1a1a1a;\n    position: relative; overflow: hidden;\n    display: flex; flex-direction: column;\n    align-items: flex-start; justify-content: flex-end;\n    padding: 2.5rem; min-height: 620px;",
    "radial-gradient(ellipse 60% 80% at 70% 20%, rgba(34,197,94,0.35) 0%, transparent 65%),\n        radial-gradient(ellipse 50% 60% at 20% 80%, rgba(34,197,94,0.18) 0%, transparent 60%),\n        radial-gradient(ellipse 40% 50% at 80% 70%, rgba(16,185,129,0.3) 0%, transparent 55%);",
    'background: rgba(34,197,94,0.2);',
    'font-size: 0.7rem; font-weight: 800; color: #4ade80;',
    '.rt-super { background: rgba(34,197,94,0.15);  color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }',
    '.rt-mgr   { background: rgba(59,130,246,0.12); color: #60a5fa; border: 1px solid rgba(59,130,246,0.25); }',
    ".auth-label { font-size: 0.72rem; font-weight: 700; color: #1a1a1a;",
    "color: #1a1a1a; background: #fff; outline: none;",
], [
    'background: #f0f7ff;',
    'background: linear-gradient(135deg, #0ea5e9, #0284c7);',
    'box-shadow: 0 4px 12px rgba(14,165,233,0.35); flex-shrink: 0;',
    ".auth-brand-name { font-size: 1rem; font-weight: 800; color: #0f172a;",
    ".auth-heading { font-size: 1.45rem; font-weight: 800; color: #0f172a;",
    ".auth-subheading { font-size: 0.8rem; color: #64748b;",
    '.auth-input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3.5px rgba(14,165,233,0.12); }',
    '.auth-role-card.selected { border-color: #0ea5e9; background: #f0f9ff; box-shadow: 0 0 0 3px rgba(14,165,233,0.15); }',
    '.auth-role-card.selected .auth-role-icon-wrap { background: rgba(14,165,233,0.12); color: #0ea5e9; }',
    ".auth-role-title { font-size: 0.76rem; font-weight: 700; color: #0f172a;",
    ".auth-role-card.selected .auth-role-title { color: #0ea5e9; }",
    'background: linear-gradient(135deg, #0ea5e9, #0284c7);',
    'box-shadow: 0 4px 14px rgba(14,165,233,0.35);',
    '.auth-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(14,165,233,0.45); }',
    ".auth-terms a { color: #0ea5e9;",
    ".auth-link-row a { color: #0ea5e9;",
    "background: #0f4c81;\n    position: relative; overflow: hidden;\n    display: flex; flex-direction: column;\n    align-items: flex-start; justify-content: flex-end;\n    padding: 2.5rem; min-height: 620px;",
    "radial-gradient(ellipse 60% 80% at 70% 20%, rgba(14,165,233,0.45) 0%, transparent 65%),\n        radial-gradient(ellipse 50% 60% at 20% 80%, rgba(20,184,166,0.3) 0%, transparent 60%),\n        radial-gradient(ellipse 40% 50% at 80% 70%, rgba(2,132,199,0.35) 0%, transparent 55%);",
    'background: rgba(14,165,233,0.2);',
    'font-size: 0.7rem; font-weight: 800; color: #7dd3fc;',
    '.rt-super { background: rgba(14,165,233,0.15);  color: #7dd3fc; border: 1px solid rgba(14,165,233,0.3); }',
    '.rt-mgr   { background: rgba(20,184,166,0.12);  color: #5eead4; border: 1px solid rgba(20,184,166,0.25); }',
    ".auth-label { font-size: 0.72rem; font-weight: 700; color: #0f172a;",
    "color: #0f172a; background: #fff; outline: none;",
], $c);
file_put_contents($f, $c);
echo "register.php done\n";

// Fix dashboard/index.php
$f = 'app/Views/dashboard/index.php';
$c = file_get_contents($f);
$c = str_replace([
    "'#e5e7eb','#e5e7eb','#f0481c','#e5e7eb','#e5e7eb','#e5e7eb'",
    "color:var(--accent-orange);font-weight:600",
], [
    "'#dbeafe','#dbeafe','#0ea5e9','#dbeafe','#dbeafe','#dbeafe'",
    "color:var(--accent-primary);font-weight:600",
], $c);
file_put_contents($f, $c);
echo "dashboard/index.php done\n";

// Fix users/index.php — check for any hardcoded orange
$f = 'app/Views/users/index.php';
$c = file_get_contents($f);
$c = str_replace(['#f0481c','#d63d15','rgba(240,72,28'], ['#0ea5e9','#0284c7','rgba(14,165,233'], $c);
file_put_contents($f, $c);
echo "users/index.php done\n";

// Final check on premium.css
$f = 'public/assets/css/premium.css';
$c = file_get_contents($f);
// remaining `#1a1a1a` in dark badge and generic refs
$c = str_replace('.sb-badge-dark   { background: #1a1a1a; color: #fff; }', '.sb-badge-dark   { background: #0f4c81; color: #fff; }', $c);
$c = str_replace('background: #1a1a1a;', 'background: #0f4c81;', $c);
file_put_contents($f, $c);
echo "premium.css remaining refs done\n";

echo "\nAll done!\n";
