<?php
require_once __DIR__ . '/../includes/session.php';
Auth::require(['admin', 'editor']);
$pageTitle = 'Enquiries';
$activeNav = 'enquiries';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::csrfCheck($_POST['csrf'] ?? null)) {
    $action = $_POST['action'] ?? 'update_status';
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'update_status') {
        $allowed = ['new', 'read', 'responded'];
        $status  = in_array($_POST['status'] ?? '', $allowed, true) ? $_POST['status'] : 'new';
        Database::query(
            "UPDATE enquiries SET status = ? WHERE id = ?",
            [$status, $id]
        );
    } elseif ($action === 'save_note' && $id > 0) {
        $note = trim($_POST['admin_note'] ?? '');
        $newStatus = !empty($note) ? 'responded' : null;
        if ($newStatus) {
            Database::query(
                "UPDATE enquiries SET admin_note = ?, status = ? WHERE id = ?",
                [$note, $newStatus, $id]
            );
        } else {
            Database::query(
                "UPDATE enquiries SET admin_note = ? WHERE id = ?",
                [$note, $id]
            );
        }
        $msg = 'Note saved.';
    }
}

$filterStatus = $_GET['status'] ?? '';
$filterSql    = $filterStatus ? "WHERE status = ?" : '';
$filterParams = $filterStatus ? [$filterStatus] : [];

$enquiries = Database::fetchAll(
    "SELECT * FROM enquiries $filterSql ORDER BY submitted_at DESC",
    $filterParams
);

$counts   = Database::fetchAll("SELECT status, COUNT(*) c FROM enquiries GROUP BY status");
$countMap = array_column($counts, 'c', 'status');

include __DIR__ . '/_layout_top.php';
?>

<div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase;
                letter-spacing:0.07em; color:var(--hzc-accent-dark); margin-bottom:0.2rem;">
      <?= count($enquiries) ?> <?= $filterStatus ?: 'total' ?>
    </div>
    <h2 class="fw-bold mb-0">Enquiries</h2>
  </div>

  <div class="d-flex gap-2 flex-wrap">
    <?php
    $statuses = ['' => 'All', 'new' => 'New', 'read' => 'Read', 'responded' => 'Responded'];
    foreach ($statuses as $val => $label):
      $active = $filterStatus === $val;
    ?>
    <a href="?status=<?= urlencode($val) ?>"
       class="btn btn-sm <?= $active ? 'btn-hzc-primary' : 'btn-outline-secondary' ?> px-3">
      <?= $label ?>
      <?php if ($val && isset($countMap[$val])): ?>
        <span style="margin-left:4px; opacity:0.75;">(<?= $countMap[$val] ?>)</span>
      <?php endif; ?>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php if ($msg): ?>
<div class="alert alert-success mb-4">
  <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>

<div class="admin-table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th>From</th>
        <th>Contact</th>
        <th>Enquiry type</th>
        <th>Message</th>
        <th>Status</th>
        <th>Date</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($enquiries as $e): ?>
      <tr>
        <td>
          <div style="font-weight:600; color:var(--hzc-primary);">
            <?= htmlspecialchars($e['full_name']) ?>
          </div>
        </td>
        <td style="font-size:0.83rem;">
          <div><?= htmlspecialchars($e['email']) ?></div>
          <?php if ($e['phone']): ?>
          <div style="color:var(--hzc-muted);"><?= htmlspecialchars($e['phone']) ?></div>
          <?php endif; ?>
        </td>
        <td style="font-size:0.83rem; color:var(--hzc-muted);">
          <?= htmlspecialchars($e['subject'] ?? '—') ?>
        </td>
        <td style="font-size:0.83rem; max-width:200px;">
          <?= htmlspecialchars(mb_strimwidth($e['message'], 0, 90, '…')) ?>
        </td>
        <td>
          <form method="post">
            <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id"     value="<?= $e['id'] ?>">
            <select name="status" class="form-select form-select-sm"
                    style="min-width:120px; font-size:0.8rem;"
                    onchange="this.form.submit()">
              <?php foreach (['new','read','responded'] as $st): ?>
              <option value="<?= $st ?>" <?= $e['status'] === $st ? 'selected' : '' ?>>
                <?= ucfirst($st) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </form>
        </td>
        <td style="color:var(--hzc-muted); font-size:0.83rem; white-space:nowrap;">
          <?= date('d M Y', strtotime($e['submitted_at'])) ?>
        </td>
        <td>
          <button class="btn btn-outline-secondary btn-sm px-2" style="font-size:0.78rem;"
                  data-bs-toggle="modal"
                  data-bs-target="#enquiryModal"
                  onclick="openModal(<?= htmlspecialchars(json_encode([
                    'id'      => $e['id'],
                    'name'    => $e['full_name'],
                    'email'   => $e['email'],
                    'phone'   => $e['phone'],
                    'subject' => $e['subject'],
                    'message' => $e['message'],
                    'note'    => $e['admin_note'] ?? '',
                    'date'    => date('d M Y H:i', strtotime($e['submitted_at'])),
                  ])) ?>)">
            <i class="bi bi-eye"></i> View
          </button>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($enquiries)): ?>
    <tr>
      <td colspan="7" class="text-center py-5" style="color:var(--hzc-muted);">
        <i class="bi bi-chat-square-dots fs-3 d-block mb-2"></i>
        No enquiries <?= $filterStatus ? "with status \"$filterStatus\"" : 'yet' ?>.
      </td>
    </tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- ── Enquiry detail + notes modal ──────────────────────── -->
<div class="modal fade" id="enquiryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border-radius:var(--radius-md); overflow:hidden;">

      <div class="modal-header" style="border-bottom:1.5px solid var(--hzc-border);">
        <div>
          <h5 class="modal-title mb-0" id="modalName">Enquiry</h5>
          <div style="font-size:0.78rem; color:var(--hzc-muted); margin-top:2px;">
            <span id="modalEmail"></span>
            <span id="modalPhone" class="ms-2"></span>
            <span id="modalDate" class="ms-2"></span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <div class="row g-3">
          <!-- Left: message -->
          <div class="col-md-7">
            <label style="font-size:0.73rem; font-weight:700; text-transform:uppercase;
                          letter-spacing:0.06em; color:var(--hzc-primary); display:block; margin-bottom:0.4rem;">
              Message from sender
            </label>
            <div id="modalSubject" style="font-size:0.8rem; color:var(--hzc-muted); margin-bottom:0.5rem;"></div>
            <div id="modalMessage"
                 style="font-size:0.88rem; line-height:1.75; white-space:pre-wrap;
                        background:var(--hzc-light); border-radius:var(--radius-sm);
                        padding:1rem; border:1px solid var(--hzc-border);
                        min-height:120px;"></div>
          </div>

          <!-- Right: admin note -->
          <div class="col-md-5">
            <form method="post" id="noteForm">
              <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
              <input type="hidden" name="action" value="save_note">
              <input type="hidden" name="id"     id="modalId">

              <label for="adminNote"
                     style="font-size:0.73rem; font-weight:700; text-transform:uppercase;
                            letter-spacing:0.06em; color:var(--hzc-primary); display:block; margin-bottom:0.4rem;">
                Internal note / reply log
              </label>
              <textarea id="adminNote" name="admin_note" class="form-control" rows="7"
                        placeholder="Add a private note — what action was taken, what was said, next steps…"
                        style="font-size:0.85rem; resize:vertical;"></textarea>
              <div style="font-size:0.72rem; color:var(--hzc-muted); margin-top:0.35rem;">
                <i class="bi bi-lock me-1"></i>Visible to admins only. Saving auto-sets status to Responded.
              </div>
            </form>
          </div>
        </div>

        <!-- Quick action buttons -->
        <div class="d-flex gap-2 flex-wrap mt-3 pt-3"
             style="border-top:1px solid var(--hzc-border);">
          <a id="modalEmailLink" href="#" class="btn btn-hzc-primary btn-sm px-3">
            <i class="bi bi-envelope me-1"></i> Reply by email
          </a>
          <a id="modalCall" href="#" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-telephone me-1"></i> Call
          </a>
        </div>
      </div>

      <div class="modal-footer" style="border-top:1.5px solid var(--hzc-border);">
        <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-hzc-primary btn-sm px-4" onclick="document.getElementById('noteForm').submit();">
          <i class="bi bi-floppy me-1"></i> Save note
        </button>
      </div>

    </div>
  </div>
</div>

<script>
function openModal(data) {
  document.getElementById('modalName').textContent    = data.name;
  document.getElementById('modalEmail').textContent   = data.email;
  document.getElementById('modalPhone').textContent   = data.phone || '';
  document.getElementById('modalDate').textContent    = data.date;
  document.getElementById('modalSubject').textContent = data.subject ? ('Enquiry type: ' + data.subject) : '';
  document.getElementById('modalMessage').textContent = data.message;
  document.getElementById('adminNote').value          = data.note || '';
  document.getElementById('modalId').value            = data.id;

  document.getElementById('modalEmailLink').href =
    'mailto:' + data.email + '?subject=Re: Your enquiry to <?= SITE_NAME ?>';
  document.getElementById('modalCall').href =
    data.phone ? 'tel:' + data.phone.replace(/\s/g, '') : '#';
}
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
