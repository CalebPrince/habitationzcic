<?php
require_once __DIR__ . '/../includes/session.php';
Auth::require(['admin']);
$pageTitle = 'Activity Log';
$activeNav = 'activity';

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 30;
$offset  = ($page - 1) * $perPage;

$total = Database::fetchOne("SELECT COUNT(*) c FROM admin_log")['c'];
$totalPages = (int)ceil($total / $perPage);

$logs = Database::fetchAll(
    "SELECT l.*, u.full_name, u.role
     FROM admin_log l
     LEFT JOIN users u ON u.id = l.user_id
     ORDER BY l.created_at DESC
     LIMIT $perPage OFFSET $offset"
);

include __DIR__ . '/_layout_top.php';
?>

<div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase;
                letter-spacing:0.07em; color:var(--hzc-accent-dark); margin-bottom:0.2rem;">
      <?= $total ?> total entries
    </div>
    <h2 class="fw-bold mb-0">Activity Log</h2>
  </div>
</div>

<div class="admin-table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th>Admin</th>
        <th>Action</th>
        <th>Area</th>
        <th>Detail</th>
        <th>When</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($logs as $l): ?>
    <tr>
      <td>
        <div style="font-weight:600; font-size:0.88rem; color:var(--hzc-primary);">
          <?= htmlspecialchars($l['full_name'] ?? 'System') ?>
        </div>
        <div style="font-size:0.75rem; color:var(--hzc-muted);">
          <?= ucfirst($l['role'] ?? '') ?>
        </div>
      </td>
      <td>
        <span class="status-badge"
              style="background:rgba(47,92,95,0.1); color:var(--hzc-primary);">
          <?= htmlspecialchars(str_replace('_', ' ', $l['action'])) ?>
        </span>
      </td>
      <td style="font-size:0.85rem; color:var(--hzc-muted);">
        <?= htmlspecialchars(str_replace('_', ' ', ucfirst($l['target_table'] ?? ''))) ?>
        <?php if ($l['target_id']): ?>
        <span style="opacity:0.5;"> #<?= $l['target_id'] ?></span>
        <?php endif; ?>
      </td>
      <td style="font-size:0.85rem; max-width:280px;">
        <?= htmlspecialchars($l['detail'] ?? '') ?>
      </td>
      <td style="color:var(--hzc-muted); font-size:0.83rem; white-space:nowrap;">
        <?= date('d M Y H:i', strtotime($l['created_at'])) ?>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($logs)): ?>
    <tr>
      <td colspan="5" class="text-center py-5" style="color:var(--hzc-muted);">
        <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
        No activity logged yet.
      </td>
    </tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if ($totalPages > 1): ?>
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
  <div style="font-size:0.82rem; color:var(--hzc-muted);">
    Showing <?= $offset+1 ?>–<?= min($offset+$perPage,$total) ?> of <?= $total ?>
  </div>
  <div class="d-flex gap-1">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="?page=<?= $p ?>"
       class="btn btn-sm <?= $p===$page ? 'btn-hzc-primary' : 'btn-outline-secondary' ?>"
       style="min-width:36px;"><?= $p ?></a>
    <?php endfor; ?>
  </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
