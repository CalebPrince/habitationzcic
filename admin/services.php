<?php
require_once __DIR__ . '/../includes/session.php';
Auth::require(['admin', 'editor']);
$pageTitle = 'Services';
$activeNav = 'services';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::csrfCheck($_POST['csrf'] ?? null)) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        Database::query(
            "INSERT INTO services (title, description, icon, sort_order)
             VALUES (?, ?, ?, (SELECT m FROM (SELECT COALESCE(MAX(sort_order),0)+1 m FROM services) t))",
            [
                trim($_POST['title']),
                trim($_POST['description']),
                trim($_POST['icon'] ?? 'bi-house-heart'),
            ]
        );
        $msg = 'Service added.';

    } elseif ($action === 'edit') {
        Database::query(
            "UPDATE services SET title=?, description=?, icon=?, is_active=? WHERE id=?",
            [
                trim($_POST['title']),
                trim($_POST['description']),
                trim($_POST['icon'] ?? 'bi-house-heart'),
                isset($_POST['is_active']) ? 1 : 0,
                (int)$_POST['id'],
            ]
        );
        $msg = 'Service updated.';

    } elseif ($action === 'delete') {
        Database::query("DELETE FROM services WHERE id=?", [(int)$_POST['id']]);
        $msg = 'Service deleted.';
    }
}

$services = Database::fetchAll("SELECT * FROM services ORDER BY sort_order ASC");

include __DIR__ . '/_layout_top.php';
?>

<div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase;
                letter-spacing:0.07em; color:var(--hzc-accent-dark); margin-bottom:0.2rem;">
      <?= count($services) ?> total
    </div>
    <h2 class="fw-bold mb-0">Services</h2>
  </div>
  <button class="btn btn-hzc-accent btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="bi bi-plus-lg me-1"></i> Add service
  </button>
</div>

<?php if ($msg): ?>
<div class="alert alert-success mb-4">
  <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>

<div class="row g-4">
  <?php foreach ($services as $s): ?>
  <div class="col-md-6">
    <div class="admin-card h-100">
      <div class="admin-card-header">
        <div style="display:flex; align-items:center; gap:0.6rem;">
          <div class="icon-wrap" style="width:36px;height:36px;font-size:1rem;
               background:rgba(223,199,43,0.2); color:#8a761a; border-radius:10px;
               display:flex; align-items:center; justify-content:center;">
            <i class="bi <?= htmlspecialchars($s['icon']) ?>"></i>
          </div>
          <h5 style="font-size:0.88rem; margin:0;"><?= htmlspecialchars($s['title']) ?></h5>
        </div>
        <span class="status-badge <?= $s['is_active'] ? 'status-active' : 'status-suspended' ?>">
          <?= $s['is_active'] ? 'Visible' : 'Hidden' ?>
        </span>
      </div>
      <form method="post">
        <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id"     value="<?= $s['id'] ?>">
        <div class="row g-2">
          <div class="col-8">
            <label class="form-label">Title</label>
            <input class="form-control" name="title" value="<?= htmlspecialchars($s['title']) ?>" required>
          </div>
          <div class="col-4">
            <label class="form-label">Icon class</label>
            <input class="form-control" name="icon" value="<?= htmlspecialchars($s['icon']) ?>"
                   placeholder="bi-house-heart">
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea class="form-control" rows="3" name="description" required><?= htmlspecialchars($s['description']) ?></textarea>
          </div>
          <div class="col-12 d-flex align-items-center justify-content-between">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" name="is_active"
                     id="active<?= $s['id'] ?>" <?= $s['is_active'] ? 'checked' : '' ?>>
              <label class="form-check-label" for="active<?= $s['id'] ?>"
                     style="font-size:0.85rem; font-weight:400; text-transform:none; letter-spacing:0;">
                Show on website
              </label>
            </div>
          </div>
          <div class="col-12 mt-1 d-flex gap-2">
            <button class="btn btn-hzc-primary btn-sm px-3">Save changes</button>
            <button type="submit" form="delete<?= $s['id'] ?>" class="btn btn-outline-danger btn-sm px-3">Delete</button>
          </div>
        </div>
      </form>
      <form method="post" id="delete<?= $s['id'] ?>"
            onsubmit="return confirm('Delete this service?');" style="display:none;">
        <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id"     value="<?= $s['id'] ?>">
      </form>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($services)): ?>
  <div class="col-12">
    <div class="admin-card text-center py-5" style="color:var(--hzc-muted);">
      <i class="bi bi-house-heart fs-3 d-block mb-2"></i>
      No services yet — add one to get started.
    </div>
  </div>
  <?php endif; ?>
</div>

<div class="admin-card mt-4">
  <h6 class="fw-bold mb-2" style="font-size:0.85rem;">
    <i class="bi bi-info-circle me-2" style="color:var(--hzc-accent-dark);"></i>Bootstrap icon reference
  </h6>
  <p class="text-muted mb-0" style="font-size:0.82rem;">
    Use any icon name from
    <a href="https://icons.getbootstrap.com" target="_blank" rel="noopener">icons.getbootstrap.com</a>
    — e.g. <code>bi-house-heart</code>, <code>bi-people</code>, <code>bi-clipboard-check</code>.
  </p>
</div>

<!-- ── Add service modal ─────────────────────────────────── -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content" style="border-radius:var(--radius-md); overflow:hidden;">
      <div class="modal-header" style="border-bottom:1.5px solid var(--hzc-border);">
        <h5 class="modal-title">Add new service</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
        <input type="hidden" name="action" value="add">
        <div class="row g-3">
          <div class="col-8">
            <label class="form-label">Title</label>
            <input class="form-control" name="title" placeholder="e.g. Community Inclusion" required>
          </div>
          <div class="col-4">
            <label class="form-label">Icon class</label>
            <input class="form-control" name="icon" placeholder="bi-house-heart">
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea class="form-control" rows="3" name="description"
                      placeholder="Describe this service…" required></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="border-top:1.5px solid var(--hzc-border);">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-hzc-primary">Add service</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
