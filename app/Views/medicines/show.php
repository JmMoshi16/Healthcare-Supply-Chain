<?php use App\Core\View; View::layout('app'); $title = esc($medicine['name']); ?>

<a href="/medicines" class="back-link"><i class="bi bi-arrow-left"></i> Back to Medicines</a>

<div class="page-header" style="margin-bottom:1.75rem;">
    <div>
        <h1 class="page-title"><?= esc($medicine['name']) ?></h1>
        <p class="page-subtitle">
            <span class="badge badge-neutral"><?= esc(ucfirst($medicine['category'])) ?></span>
            &nbsp;·&nbsp; <?= esc($medicine['unit']) ?>
        </p>
    </div>
    <div style="display:flex;gap:.6rem;">
        <?php if (can('medicines.*')): ?>
        <a href="/medicines/<?= (int)$medicine['id'] ?>/edit" class="btn-create-order"><i class="bi bi-pencil"></i> Edit</a>
        <?php endif; ?>
    </div>
</div>

<div class="pf-form-layout">

    <!-- LEFT: Image + info -->
    <div style="flex:0 0 340px;min-width:0;">
        <div class="card">
            <?php if (!empty($medicine['image'])): ?>
                <img src="/uploads/medicines/<?= esc($medicine['image']) ?>" style="width:100%;aspect-ratio:1/1;object-fit:cover;display:block;border-radius:16px 16px 0 0;" alt="">
            <?php else: ?>
                <div style="width:100%;aspect-ratio:1/1;background:var(--bg-surface);display:flex;align-items:center;justify-content:center;border-radius:16px 16px 0 0;flex-direction:column;gap:.5rem;">
                    <i class="bi bi-capsule" style="font-size:3rem;color:#ccc;"></i>
                    <span style="font-size:.75rem;color:var(--text-muted);">No image</span>
                </div>
            <?php endif; ?>

            <div style="padding:1.35rem;">
                <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:1.25rem;">
                    <div>
                        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:.2rem;">Total Stock</div>
                        <div style="font-size:2rem;font-weight:800;color:var(--text-heading);letter-spacing:-.03em;line-height:1;">
                            <?= number_format((int)$medicine['total_stock']) ?>
                        </div>
                    </div>
                    <span class="badge badge-<?= $medicine['is_active'] ? 'success' : 'danger' ?>" style="font-size:.78rem;padding:.3rem .8rem;">
                        <?= $medicine['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </div>

                <div class="pshow-meta-grid">
                    <div class="pshow-meta-item">
                        <div class="pshow-meta-label">Generic Name</div>
                        <div class="pshow-meta-value"><?= esc($medicine['generic_name'] ?? '—') ?></div>
                    </div>
                    <div class="pshow-meta-item">
                        <div class="pshow-meta-label">Unit</div>
                        <div class="pshow-meta-value"><?= esc($medicine['unit']) ?></div>
                    </div>
                    <div class="pshow-meta-item">
                        <div class="pshow-meta-label">Category</div>
                        <div class="pshow-meta-value"><?= esc(ucfirst($medicine['category'])) ?></div>
                    </div>
                    <div class="pshow-meta-item">
                        <div class="pshow-meta-label">Batches</div>
                        <div class="pshow-meta-value"><?= count($medicine['batches'] ?? []) ?></div>
                    </div>
                </div>

                <?php if (!empty($medicine['description'])): ?>
                <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);">
                    <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:.5rem;">Description</div>
                    <p style="font-size:.85rem;color:var(--text-body);line-height:1.75;margin:0;"><?= nl2br(esc($medicine['description'])) ?></p>
                </div>
                <?php endif; ?>

                <div style="margin-top:1.5rem;display:flex;flex-direction:column;gap:.6rem;">
                    <?php if (can('medicines.*')): ?>
                    <a href="/medicines/<?= (int)$medicine['id'] ?>/edit" class="btn-create-order" style="justify-content:center;gap:.5rem;">
                        <i class="bi bi-pencil-square"></i> Edit Medicine
                    </a>
                    <?php endif; ?>
                    <a href="/stocks/create" class="btn btn-ghost" style="justify-content:center;">
                        <i class="bi bi-arrow-left-right"></i> Record Transaction
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Batches table -->
    <div style="flex:1;min-width:0;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Batches</span>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span style="font-size:.78rem;color:var(--text-muted);"><?= count($medicine['batches'] ?? []) ?> batch<?= count($medicine['batches'] ?? []) !== 1 ? 'es' : '' ?></span>
                    <?php if (can('batches.*')): ?>
                    <a href="/batches/create" class="btn btn-ghost btn-sm"><i class="bi bi-plus"></i> Add Batch</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($medicine['batches'])): ?>
            <div style="padding:4rem;text-align:center;">
                <i class="bi bi-box-seam" style="font-size:2rem;color:#ccc;display:block;margin-bottom:.75rem;"></i>
                <p style="font-size:.85rem;color:var(--text-muted);margin:0 0 1rem;">No batches added yet.</p>
                <?php if (can('batches.*')): ?>
                <a href="/batches/create" class="btn-create-order" style="font-size:.8rem;"><i class="bi bi-plus"></i> Add First Batch</a>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="table-wrapper">
                <table class="smro-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Batch Number</th>
                            <th>Expiry Date</th>
                            <th>Supplier</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicine['batches'] as $i => $b):
                            $daysLeft = (strtotime($b['expiry_date']) - time()) / 86400;
                        ?>
                        <tr>
                            <td style="font-family:monospace;font-size:.75rem;color:var(--text-muted);"><?= sprintf('%02d', $i + 1) ?></td>
                            <td style="font-weight:600;color:var(--text-heading);"><?= esc($b['batch_number']) ?></td>
                            <td>
                                <?php if ($daysLeft < 0): ?>
                                    <span class="badge badge-danger">Expired</span>
                                <?php elseif ($daysLeft <= 30): ?>
                                    <span class="badge badge-warning"><?= date('M d, Y', strtotime($b['expiry_date'])) ?></span>
                                <?php else: ?>
                                    <span style="font-size:.83rem;"><?= date('M d, Y', strtotime($b['expiry_date'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="color:var(--text-muted);"><?= esc($b['supplier'] ?? '—') ?></td>
                            <td>
                                <?php if ($b['current_quantity'] < 10): ?>
                                    <span class="badge badge-danger"><i class="bi bi-exclamation-triangle" style="font-size:.65rem;"></i> <?= (int)$b['current_quantity'] ?> Low</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><i class="bi bi-check" style="font-size:.7rem;"></i> <?= (int)$b['current_quantity'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-<?= $b['status'] === 'active' ? 'success' : 'danger' ?>"><?= esc($b['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding:.85rem 1.25rem;background:var(--bg-surface);border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:.8rem;color:var(--text-muted);">
                    Total stock: <strong style="color:var(--text-heading);"><?= number_format((int)$medicine['total_stock']) ?> units</strong>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
