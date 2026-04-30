<?php
$f = __DIR__ . '/public/assets/css/premium.css';
$css = file_get_contents($f);

$replacements = [
    // Comment header
    "ThreadHub — Retail Inventory Design System\n   Modeled after the reference dashboard image" =>
    "HealthChain — Healthcare Supply Chain System\n   Professional Medical Color System",

    // :root light palette
    '--bg-body:        #f0f2f5'     => '--bg-body:        #f0f7ff',
    '--bg-surface:     #f7f8fa'     => '--bg-surface:     #f5faff',
    '--border:         #ebebeb'     => '--border:         #dbeafe',
    '--text-heading:   #1a1a1a'     => '--text-heading:   #0f172a',
    '--text-body:      #555'        => '--text-body:      #475569',
    '--text-muted:     #999'        => '--text-muted:     #94a3b8',
    '--accent-orange:  #f0481c'     => "--accent-orange:  #0ea5e9;   /* compat alias */\n    --accent-primary: #0ea5e9;\n    --accent-teal:    #14b8a6",
    '--accent-dark:    #1a1a1a'     => '--accent-dark:    #0f4c81',
    '--success:        #22c55e'     => '--success:        #10b981',

    // Sidebar light tokens
    '--sb-rail-bg:       #1a1a1a'   => '--sb-rail-bg:       #0f4c81',
    '--sb-rail-sep:      rgba(255,255,255,0.1)' => '--sb-rail-sep:      rgba(255,255,255,0.12)',
    '--sb-panel-border:  #ebebeb'   => '--sb-panel-border:  #dbeafe',
    '--sb-item-hover:    #f4f4f4'   => '--sb-item-hover:    #eff6ff',
    '--sb-item-active:   #1a1a1a'   => '--sb-item-active:   #0f4c81',
    '--sb-item-text:     #555'      => '--sb-item-text:     #475569',
    '--sb-section-text:  #bbb'      => '--sb-section-text:  #93c5fd',
    '--sb-header-border: #f0f0f0'   => '--sb-header-border: #eff6ff',
    '--sb-user-border:   #ebebeb'   => '--sb-user-border:   #dbeafe',
    '--sb-switch-bg:     #f7f8fa'   => '--sb-switch-bg:     #f5faff',
    '--sb-switch-border: #ebebeb'   => '--sb-switch-border: #dbeafe',
    '--sb-switch-text:   #888'      => '--sb-switch-text:   #64748b',

    // Dark mode tokens
    '--bg-body:        #0f0f0f'     => '--bg-body:        #060f1e',
    '--bg-sidebar:     #1a1a1a'     => '--bg-sidebar:     #0a1628',
    '--bg-card:        #1e1e1e'     => '--bg-card:        #0d1f35',
    '--bg-surface:     #252525'     => '--bg-surface:     #112240',
    '--border:         #2e2e2e'     => '--border:         #1e3a5f',
    '--text-heading:   #f0f0f0'     => '--text-heading:   #e2f0ff',
    '--text-body:      #aaa'        => '--text-body:      #93bbdb',
    '--text-muted:     #666'        => '--text-muted:     #4d7a9e',
    '--accent-orange:  #f0481c'     => "--accent-orange:  #38bdf8;\n    --accent-primary: #38bdf8;\n    --accent-teal:    #2dd4bf",
    '--accent-dark:    #f0f0f0'     => '--accent-dark:    #e2f0ff',
    '--sb-rail-bg:       #111111'   => '--sb-rail-bg:       #071022',
    '--sb-rail-sep:      rgba(255,255,255,0.07)' => '--sb-rail-sep:      rgba(255,255,255,0.08)',
    '--sb-panel-bg:      #1a1a1a'   => '--sb-panel-bg:      #0a1628',
    '--sb-panel-border:  #2a2a2a'   => '--sb-panel-border:  #1e3a5f',
    '--sb-item-hover:    #252525'   => '--sb-item-hover:    #112240',
    '--sb-item-active:   #f0f0f0'   => '--sb-item-active:   #e2f0ff',
    '--sb-item-text:     #888'      => '--sb-item-text:     #64a0c8',
    '--sb-item-active-text: #111'   => '--sb-item-active-text: #071022',
    '--sb-section-text:  #444'      => '--sb-section-text:  #1e3a5f',
    '--sb-header-border: #242424'   => '--sb-header-border: #0d1f35',
    '--sb-user-border:   #2a2a2a'   => '--sb-user-border:   #1e3a5f',
    '--sb-switch-bg:     #222'      => '--sb-switch-bg:     #0d1f35',
    '--sb-switch-border: #2e2e2e'   => '--sb-switch-border: #1e3a5f',
    '--sb-switch-text:   #666'      => '--sb-switch-text:   #4d7a9e',

    // Logos & gradients
    'linear-gradient(135deg, #f0481c 0%, #d63d15 100%)' => 'linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%)',
    'rgba(240,72,28,0.4)'           => 'rgba(14,165,233,0.4)',
    'linear-gradient(135deg, #f0481c, #d63d15)' => 'linear-gradient(135deg, #0ea5e9, #0284c7)',
    'rgba(240,72,28,0.35)'          => 'rgba(14,165,233,0.35)',
    'linear-gradient(135deg, #1a1a1a, #444)' => 'linear-gradient(135deg, #0f4c81, #0ea5e9)',

    // Buttons
    '.btn-dark:hover { background: #333; color: #fff; }' => '.btn-dark:hover { background: #0c3d6b; color: #fff; }',
    '.btn-primary { background: var(--accent-orange)' => '.btn-primary { background: var(--accent-primary)',
    '.btn-primary:hover { background: #d63d15' => '.btn-primary:hover { background: #0284c7',
    'active-tab { background: var(--accent-orange); color: #fff; border-color: var(--accent-orange)' =>
    'active-tab { background: var(--accent-primary); color: #fff; border-color: var(--accent-primary)',

    // Stat cards
    '.stat-card-orange { background: var(--accent-orange)' => '.stat-card-orange { background: linear-gradient(135deg, #0ea5e9, #0284c7)',

    // Badges
    '.badge-success { background: #dcfce7; color: #15803d; }' => '.badge-success { background: #d1fae5; color: #065f46; }',
    '.badge-orange { background: #fff3ef; color: var(--accent-orange); }' => ".badge-orange { background: #e0f2fe; color: var(--accent-primary); }\n.badge-primary { background: #dbeafe; color: #1d4ed8; }\n.badge-teal   { background: #ccfbf1; color: #0f766e; }",

    // Notification badge
    "background: var(--accent-orange);\n    border-radius: 50%;" => "background: var(--accent-primary);\n    border-radius: 50%;",

    // Form focus
    'border-color: var(--accent-orange)' => 'border-color: var(--accent-primary)',
    'rgba(240,72,28,0.12)'          => 'rgba(14,165,233,0.12)',

    // Bar chart highlight
    '.bar-profit.highlight { background: var(--accent-orange)' => '.bar-profit.highlight { background: var(--accent-primary)',

    // Clean btn primary
    '.clean-btn-primary:hover { background: #d63d15' => '.clean-btn-primary:hover { background: #0284c7',
    'background: var(--accent-orange); color: #fff;
    border: none; border-radius: 8px;' => 'background: var(--accent-primary); color: #fff;
    border: none; border-radius: 8px;',
];

foreach ($replacements as $old => $new) {
    $css = str_replace($old, $new, $css);
}

file_put_contents($f, $css);
echo 'Done. File size: ' . strlen($css) . " bytes\n";
echo "Verified navy sidebar: " . (strpos($css, '#0f4c81') !== false ? 'YES' : 'NO') . "\n";
echo "Verified sky blue accent: " . (strpos($css, '#0ea5e9') !== false ? 'YES' : 'NO') . "\n";
