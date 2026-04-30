<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Healthcare Supply Chain') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/premium.css">
</head>
<body style="background:var(--bg-body,#f0f2f5);display:flex;align-items:center;justify-content:center;min-height:100vh;">
    <div style="width:100%;max-width:420px;padding:1.5rem;">
        <?php if ($error = flash('error')): ?>
            <div style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:0.85rem 1.1rem;border-radius:10px;margin-bottom:1.25rem;font-size:0.84rem;">
                <i class="bi bi-exclamation-circle-fill" style="margin-right:0.5rem;"></i><?= esc($error) ?>
            </div>
        <?php endif; ?>
        <?= $content ?>
    </div>
</body>
</html>
