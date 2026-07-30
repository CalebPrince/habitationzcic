<?php
require_once __DIR__ . '/../includes/session.php';
Auth::require(['admin', 'editor']);
$pageTitle = 'FAQs';
$activeNav = 'faqs';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::csrfCheck($_POST['csrf'] ?? null)) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        Database::query(
            "INSERT INTO faqs (question, answer, sort_order)
             VALUES (?, ?, (SELECT m FROM (SELECT COALESCE(MAX(sort_order),0)+1 m FROM faqs) t))",
            [trim($_POST['question']), trim($_POST['answer'])]
        );
        $msg = 'FAQ added.';

    } elseif ($action === 'edit') {
        Database::query(
            "UPDATE faqs SET question=?, answer=?, is_active=? WHERE id=?",
            [
                trim($_POST['question']),
                trim($_POST['answer']),
                isset($_POST['is_active']) ? 1 : 0,
                (int)$_POST['id'],
            ]
        );
        $msg = 'FAQ updated.';

    } elseif ($action === 'delete') {
        Database::query("DELETE FROM faqs WHERE id=?", [(int)$_POST['id']]);
        $msg = 'FAQ deleted.';

    } elseif ($action === 'move' && in_array($_POST['dir'] ?? '', ['up', 'down'], true)) {
        $id  = (int)$_POST['id'];
        $dir = $_POST['dir'];
        $current = Database::fetchOne("SELECT id, sort_order FROM faqs WHERE id=?", [$id]);
        if ($current) {
            $neighbor = $dir === 'up'
                ? Database::fetchOne("SELECT id, sort_order FROM faqs WHERE sort_order < ? ORDER BY sort_order DESC LIMIT 1", [$current['sort_order']])
                : Database::fetchOne("SELECT id, sort_order FROM faqs WHERE sort_order > ? ORDER BY sort_order ASC LIMIT 1", [$current['sort_order']]);
            if ($neighbor) {
                Database::query("UPDATE faqs SET sort_order=? WHERE id=?", [$neighbor['sort_order'], $current['id']]);
                Database::query("UPDATE faqs SET sort_order=? WHERE id=?", [$current['sort_order'], $neighbor['id']]);
            }
        }
    }
}

$faqs = Database::fetchAll("SELECT * FROM faqs ORDER BY sort_order ASC");

include __DIR__ . '/_layout_top.php';
?>

<div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase;
                letter-spacing:0.07em; color:var(--hzc-accent-dark); margin-bottom:0.2rem;">
      <?= count($faqs) ?> total
    </div>
    <h2 class="fw-bold mb-0">FAQs</h2>
  </div>
  <button class="btn btn-hzc-accent btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addFaqModal">
    <i class="bi bi-plus-lg me-1"></i> Add FAQ
  </button>
</div>

<?php if ($msg): ?>
<div class="alert alert-success mb-4">
  <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>

<div class="d-flex flex-column gap-3">
  <?php foreach ($faqs as $i => $f): ?>
  <div class="admin-card">
    <div class="admin-card-header">
      <div style="display:flex; align-items:center; gap:0.6rem; min-width:0;">
        <div class="d-flex flex-column">
          <form method="post" class="d-inline">
            <input type="hidden" name="csrf" value="<?= Auth::csrfToken() ?>">
            <input type="hidden" name="action" value="move">
            <input type="hidden" name="dir" value="up">
            <input type="hidden" name="id" value="<?= $f['id'] ?>">
            <button class="btn btn-sm p-0 border-0" style="line-height:1; color:var(--hzc-muted);" <?= $i === 0 ? 'disabled' : '' ?>>
              <i class="bi bi-caret-up-fill"></i>
            </button>
          </form>
          <form method="post" class="d-inline">
            <input type="hidden" name="csrf" value="<?= Auth::csrfToken() ?>">
            <input type="hidden" name="action" value="move">
            <input type="hidden" name="dir" value="down">
            <input type="hidden" name="id" value="<?= $f['id'] ?>">
            <button class="btn btn-sm p-0 border-0" style="line-height:1; color:var(--hzc-muted);" <?= $i === count($faqs) - 1 ? 'disabled' : '' ?>>
              <i class="bi bi-caret-down-fill"></i>
            </button>
          </form>
        </div>
        <h5 style="font-size:0.88rem; margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
          <?= htmlspecialchars($f['question']) ?>
        </h5>
      </div>
      <span class="status-badge <?= $f['is_active'] ? 'status-active' : 'status-suspended' ?>">
        <?= $f['is_active'] ? 'Visible' : 'Hidden' ?>
      </span>
    </div>
    <form method="post">
      <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id"     value="<?= $f['id'] ?>">
      <div class="row g-2">
        <div class="col-12">
          <label class="form-label">Question</label>
          <input class="form-control" name="question" value="<?= htmlspecialchars($f['question']) ?>" required>
        </div>
        <div class="col-12">
          <label class="form-label">Answer</label>
          <textarea class="form-control" rows="3" name="answer" required><?= htmlspecialchars($f['answer']) ?></textarea>
        </div>
        <div class="col-12 d-flex align-items-center justify-content-between">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" name="is_active"
                   id="faqActive<?= $f['id'] ?>" <?= $f['is_active'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="faqActive<?= $f['id'] ?>"
                   style="font-size:0.85rem; font-weight:400; text-transform:none; letter-spacing:0;">
              Show on website
            </label>
          </div>
        </div>
        <div class="col-12 mt-1 d-flex gap-2">
          <button class="btn btn-hzc-primary btn-sm px-3">Save changes</button>
          <button type="submit" form="deleteFaq<?= $f['id'] ?>" class="btn btn-outline-danger btn-sm px-3">Delete</button>
        </div>
      </div>
    </form>
    <form method="post" id="deleteFaq<?= $f['id'] ?>"
          onsubmit="return confirm('Delete this FAQ?');" style="display:none;">
      <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id"     value="<?= $f['id'] ?>">
    </form>
  </div>
  <?php endforeach; ?>
  <?php if (empty($faqs)): ?>
  <div class="admin-card text-center py-5" style="color:var(--hzc-muted);">
    <i class="bi bi-question-circle fs-3 d-block mb-2"></i>
    No FAQs yet — add one to get started.
  </div>
  <?php endif; ?>
</div>

<!-- ── Add FAQ modal ─────────────────────────────────────── -->
<div class="modal fade" id="addFaqModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content" style="border-radius:var(--radius-md); overflow:hidden;">
      <div class="modal-header" style="border-bottom:1.5px solid var(--hzc-border);">
        <h5 class="modal-title">Add new FAQ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
        <input type="hidden" name="action" value="add">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Question</label>
            <input class="form-control" name="question" placeholder="e.g. How are referrals assessed?" required>
          </div>
          <div class="col-12">
            <label class="form-label">Answer</label>
            <textarea class="form-control" rows="3" name="answer" placeholder="Write the answer…" required></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="border-top:1.5px solid var(--hzc-border);">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-hzc-primary">Add FAQ</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
