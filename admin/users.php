<?php
// admin/users.php — full user management (admin only)
require_once __DIR__ . '/../includes/session.php';
Auth::require(['admin']);
$pageTitle = 'Users';
$activeNav = 'users';
$msg  = '';
$type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::csrfCheck($_POST['csrf'] ?? null)) {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    // ── Create ────────────────────────────────────────────────
    if ($action === 'create') {
        $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        try {
            Database::query(
                "INSERT INTO users (full_name, email, password_hash, role) VALUES (?,?,?,?)",
                [
                    trim($_POST['full_name']),
                    trim($_POST['email']),
                    $hash,
                    $_POST['role'],
                ]
            );
            $msg = 'Account created successfully.';
        } catch (PDOException $e) {
            $msg  = 'Could not create account — that email may already be in use.';
            $type = 'danger';
        }

    // ── Edit name / email / role ──────────────────────────────
    } elseif ($action === 'edit' && $id > 0) {
        if ($_POST['role'] !== 'admin') {
            $adminCount = Database::fetchOne(
                "SELECT COUNT(*) c FROM users WHERE role='admin' AND id != ?", [$id]
            )['c'];
            if ($adminCount < 1) {
                $msg  = 'Cannot change role — at least one admin must always exist.';
                $type = 'danger';
                goto render;
            }
        }
        try {
            Database::query(
                "UPDATE users SET full_name=?, email=?, role=? WHERE id=?",
                [trim($_POST['full_name']), trim($_POST['email']), $_POST['role'], $id]
            );
            $msg = 'Account updated.';
        } catch (PDOException $e) {
            $msg  = 'Could not update — that email may already be in use.';
            $type = 'danger';
        }

    // ── Reset password ────────────────────────────────────────
    } elseif ($action === 'reset_password' && $id > 0) {
        $pw = trim($_POST['new_password'] ?? '');
        if (strlen($pw) < 8) {
            $msg  = 'Password must be at least 8 characters.';
            $type = 'danger';
        } else {
            Database::query(
                "UPDATE users SET password_hash=? WHERE id=?",
                [password_hash($pw, PASSWORD_BCRYPT), $id]
            );
            $msg = 'Password reset successfully.';
        }

    // ── Toggle active / suspended ─────────────────────────────
    } elseif ($action === 'toggle_status' && $id > 0) {
        $u = Database::fetchOne("SELECT status, role FROM users WHERE id=?", [$id]);
        if ($u['role'] === 'admin' && $u['status'] === 'active') {
            $activeAdmins = Database::fetchOne(
                "SELECT COUNT(*) c FROM users WHERE role='admin' AND status='active' AND id != ?",
                [$id]
            )['c'];
            if ($activeAdmins < 1) {
                $msg  = 'Cannot suspend the last active admin account.';
                $type = 'danger';
                goto render;
            }
        }
        $new = $u['status'] === 'active' ? 'suspended' : 'active';
        Database::query("UPDATE users SET status=? WHERE id=?", [$new, $id]);
        $msg = 'Account ' . ($new === 'active' ? 'reactivated' : 'suspended') . '.';

    // ── Delete ────────────────────────────────────────────────
    } elseif ($action === 'delete' && $id > 0) {
        $u = Database::fetchOne("SELECT role FROM users WHERE id=?", [$id]);
        if ($u['role'] === 'admin') {
            $adminCount = Database::fetchOne(
                "SELECT COUNT(*) c FROM users WHERE role='admin' AND id != ?", [$id]
            )['c'];
            if ($adminCount < 1) {
                $msg  = 'Cannot delete the last admin account.';
                $type = 'danger';
                goto render;
            }
        }
        Database::query("DELETE FROM users WHERE id=?", [$id]);
        $msg = 'Account deleted.';
    }
}

render:

$filterRole = $_GET['role'] ?? '';
$search     = trim($_GET['q'] ?? '');
$page       = max(1, (int)($_GET['page'] ?? 1));
$perPage    = 20;
$offset     = ($page - 1) * $perPage;

$where  = ['1=1'];
$params = [];

if ($filterRole) {
    $where[]  = 'role = ?';
    $params[] = $filterRole;
}
if ($search) {
    $where[]  = '(full_name LIKE ? OR email LIKE ?)';
    $s = "%$search%";
    $params[] = $s; $params[] = $s;
}

$whereStr = implode(' AND ', $where);

$total      = Database::fetchOne("SELECT COUNT(*) c FROM users WHERE $whereStr", $params)['c'];
$totalPages = (int)ceil($total / $perPage);

$users = Database::fetchAll(
    "SELECT * FROM users WHERE $whereStr ORDER BY role ASC, created_at DESC
     LIMIT $perPage OFFSET $offset",
    $params
);

$counts = Database::fetchAll("SELECT role, status, COUNT(*) c FROM users GROUP BY role, status");
$countMap = [];
foreach ($counts as $c) {
    $countMap[$c['role']][$c['status']] = $c['c'];
}

include __DIR__ . '/_layout_top.php';
?>

<div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase;
                letter-spacing:0.07em; color:var(--hzc-accent-dark); margin-bottom:0.2rem;">
      <?= $total ?> <?= $filterRole ?: 'total users' ?>
    </div>
    <h2 class="fw-bold mb-0">Users</h2>
  </div>
  <div class="d-flex gap-2 flex-wrap align-items-center">
    <form method="get" class="d-flex gap-2">
      <?php if ($filterRole): ?>
      <input type="hidden" name="role" value="<?= htmlspecialchars($filterRole) ?>">
      <?php endif; ?>
      <div style="position:relative;">
        <i class="bi bi-search" style="position:absolute; left:0.7rem; top:50%;
           transform:translateY(-50%); color:var(--hzc-muted); font-size:0.85rem;"></i>
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
               class="form-control form-control-sm"
               style="padding-left:2rem; min-width:200px; font-size:0.83rem;"
               placeholder="Search name or email…">
      </div>
      <button class="btn btn-outline-secondary btn-sm px-3">Search</button>
      <?php if ($search): ?>
      <a href="?role=<?= urlencode($filterRole) ?>"
         class="btn btn-outline-danger btn-sm px-2"><i class="bi bi-x"></i></a>
      <?php endif; ?>
    </form>
    <button class="btn btn-hzc-accent btn-sm px-3"
            data-bs-toggle="modal" data-bs-target="#userModal"
            onclick="openCreate()">
      <i class="bi bi-plus-lg me-1"></i> New user
    </button>
  </div>
</div>

<!-- Role filter tabs -->
<div class="d-flex gap-2 flex-wrap mb-4">
  <?php
  $roles = ['' => 'All', 'admin' => 'Admins', 'editor' => 'Editors'];
  foreach ($roles as $val => $label):
    $qs = http_build_query(['role' => $val, 'q' => $search]);
  ?>
  <a href="?<?= $qs ?>"
     class="btn btn-sm <?= $filterRole === $val ? 'btn-hzc-primary' : 'btn-outline-secondary' ?> px-3">
    <?= $label ?>
    <?php if ($val && isset($countMap[$val])): ?>
    <span style="margin-left:4px; opacity:0.75;">
      (<?= array_sum($countMap[$val]) ?>)
    </span>
    <?php endif; ?>
  </a>
  <?php endforeach; ?>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $type ?> mb-4">
  <i class="bi bi-<?= $type === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
  <?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>

<div class="admin-table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Joined</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u):
      $words    = array_filter(explode(' ', $u['full_name']));
      $initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice($words, 0, 2)));
      $isMe     = $u['id'] === Auth::user()['id'];
    ?>
    <tr>
      <td>
        <div style="display:flex; align-items:center; gap:0.65rem;">
          <div style="width:32px; height:32px; border-radius:50%; flex-shrink:0;
                      background:<?= $u['role']==='admin' ? 'rgba(47,92,95,0.12)' : 'rgba(223,199,43,0.2)' ?>;
                      color:<?= $u['role']==='admin' ? 'var(--hzc-primary)' : '#8a761a' ?>;
                      font-size:0.72rem; font-weight:700;
                      display:flex; align-items:center; justify-content:center;">
            <?= $initials ?>
          </div>
          <div>
            <span style="font-weight:600; color:var(--hzc-primary);">
              <?= htmlspecialchars($u['full_name']) ?>
            </span>
            <?php if ($isMe): ?>
            <span style="font-size:0.65rem; font-weight:700; background:rgba(223,199,43,0.2);
                         color:#8a761a; border-radius:50px;
                         padding:0.1rem 0.5rem; margin-left:0.4rem;">You</span>
            <?php endif; ?>
          </div>
        </div>
      </td>
      <td style="font-size:0.85rem;"><?= htmlspecialchars($u['email']) ?></td>
      <td>
        <span class="status-badge"
              style="background:<?= $u['role']==='admin' ? 'rgba(47,92,95,0.1)' : 'rgba(223,199,43,0.2)' ?>;
                     color:<?= $u['role']==='admin' ? 'var(--hzc-primary)' : '#8a761a' ?>;">
          <?= ucfirst($u['role']) ?>
        </span>
      </td>
      <td>
        <span class="status-badge status-<?= $u['status'] ?>">
          <?= ucfirst($u['status']) ?>
        </span>
      </td>
      <td style="color:var(--hzc-muted); font-size:0.83rem; white-space:nowrap;">
        <?= date('d M Y', strtotime($u['created_at'])) ?>
      </td>
      <td class="text-end">
        <div class="d-inline-flex gap-1">
          <button class="btn btn-outline-secondary btn-sm px-2" style="font-size:0.78rem;"
                  onclick="openEdit(<?= htmlspecialchars(json_encode([
                    'id'        => $u['id'],
                    'full_name' => $u['full_name'],
                    'email'     => $u['email'],
                    'role'      => $u['role'],
                  ])) ?>)"
                  data-bs-toggle="modal" data-bs-target="#userModal">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-outline-secondary btn-sm px-2" style="font-size:0.78rem;"
                  onclick="openReset(<?= $u['id'] ?>, <?= htmlspecialchars(json_encode($u['full_name'])) ?>)"
                  data-bs-toggle="modal" data-bs-target="#resetModal">
            <i class="bi bi-key"></i>
          </button>
          <form method="post" class="d-inline">
            <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="id"     value="<?= $u['id'] ?>">
            <button class="btn btn-sm px-2"
                    style="font-size:0.78rem; border:1.5px solid var(--hzc-border);
                           background:transparent;
                           color:<?= $u['status']==='active' ? '#fd7e14' : '#198754' ?>;"
                    title="<?= $u['status']==='active' ? 'Suspend' : 'Reactivate' ?>">
              <i class="bi bi-<?= $u['status']==='active' ? 'pause-circle' : 'play-circle' ?>"></i>
            </button>
          </form>
          <?php if (!$isMe): ?>
          <form method="post" class="d-inline"
                onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($u['full_name'])) ?>? This cannot be undone.');">
            <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id"     value="<?= $u['id'] ?>">
            <button class="btn btn-outline-danger btn-sm px-2" style="font-size:0.78rem;">
              <i class="bi bi-trash"></i>
            </button>
          </form>
          <?php endif; ?>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($users)): ?>
    <tr>
      <td colspan="6" class="text-center py-5" style="color:var(--hzc-muted);">
        <i class="bi bi-people fs-3 d-block mb-2"></i>
        No users found.
      </td>
    </tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if ($totalPages > 1): ?>
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
  <div style="font-size:0.82rem; color:var(--hzc-muted);">
    Showing <?= $offset + 1 ?>–<?= min($offset + $perPage, $total) ?> of <?= $total ?>
  </div>
  <div class="d-flex gap-1">
    <?php for ($p = 1; $p <= $totalPages; $p++):
      $qs = http_build_query(['role' => $filterRole, 'q' => $search, 'page' => $p]);
    ?>
    <a href="?<?= $qs ?>"
       class="btn btn-sm <?= $p === $page ? 'btn-hzc-primary' : 'btn-outline-secondary' ?>"
       style="min-width:36px;"><?= $p ?></a>
    <?php endfor; ?>
  </div>
</div>
<?php endif; ?>

<!-- ── Create / Edit user modal ──────────────────────────── -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content" style="border-radius:var(--radius-md); overflow:hidden;">
      <div class="modal-header" style="border-bottom:1.5px solid var(--hzc-border);">
        <h5 class="modal-title" id="userModalTitle">Create user</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
        <input type="hidden" name="action" id="userAction" value="create">
        <input type="hidden" name="id"     id="userId">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Full name</label>
            <input class="form-control" name="full_name" id="uFullName"
                   placeholder="Jane Smith" required>
          </div>
          <div class="col-12">
            <label class="form-label">Email address</label>
            <input type="email" class="form-control" name="email" id="uEmail"
                   placeholder="jane@example.com" required>
          </div>
          <div class="col-12" id="passwordRow">
            <label class="form-label">
              Password
              <span style="font-weight:400; text-transform:none; font-size:0.78rem;
                           color:var(--hzc-muted);" id="passwordHint">
                (min. 8 characters)
              </span>
            </label>
            <div style="position:relative;">
              <input type="password" class="form-control" name="password" id="uPassword"
                     placeholder="••••••••" minlength="8" autocomplete="new-password">
              <button type="button"
                      onclick="togglePw('uPassword', this)"
                      style="position:absolute; right:0.6rem; top:50%; transform:translateY(-50%);
                             background:none; border:none; color:var(--hzc-muted); cursor:pointer;">
                <i class="bi bi-eye" id="uPasswordEye"></i>
              </button>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label">Role</label>
            <select class="form-select" name="role" id="uRole">
              <option value="editor">Editor</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="border-top:1.5px solid var(--hzc-border);">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-hzc-primary" id="userSaveBtn">Create user</button>
      </div>
    </form>
  </div>
</div>

<!-- ── Reset password modal ───────────────────────────────── -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content" style="border-radius:var(--radius-md); overflow:hidden;">
      <div class="modal-header" style="border-bottom:1.5px solid var(--hzc-border);">
        <h5 class="modal-title">Reset password — <span id="resetName"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" name="csrf"   value="<?= Auth::csrfToken() ?>">
        <input type="hidden" name="action" value="reset_password">
        <input type="hidden" name="id"     id="resetId">
        <label class="form-label">New password</label>
        <div style="position:relative;">
          <input type="password" class="form-control" name="new_password" id="resetPw"
                 placeholder="Min. 8 characters" minlength="8"
                 autocomplete="new-password" required>
          <button type="button"
                  onclick="togglePw('resetPw', this)"
                  style="position:absolute; right:0.6rem; top:50%; transform:translateY(-50%);
                         background:none; border:none; color:var(--hzc-muted); cursor:pointer;">
            <i class="bi bi-eye"></i>
          </button>
        </div>
        <div style="font-size:0.78rem; color:var(--hzc-muted); margin-top:0.35rem;">
          The user will need to use this password on their next sign in.
        </div>
      </div>
      <div class="modal-footer" style="border-top:1.5px solid var(--hzc-border);">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-hzc-primary">Set new password</button>
      </div>
    </form>
  </div>
</div>

<script>
function openCreate() {
  document.getElementById('userModalTitle').textContent = 'Create user';
  document.getElementById('userSaveBtn').textContent    = 'Create user';
  document.getElementById('userAction').value = 'create';
  document.getElementById('userId').value     = '';
  document.getElementById('uFullName').value  = '';
  document.getElementById('uEmail').value     = '';
  document.getElementById('uPassword').value  = '';
  document.getElementById('uRole').value      = 'editor';
  document.getElementById('passwordRow').style.display = '';
  document.getElementById('uPassword').required = true;
}
function openEdit(u) {
  document.getElementById('userModalTitle').textContent = 'Edit user';
  document.getElementById('userSaveBtn').textContent    = 'Save changes';
  document.getElementById('userAction').value = 'edit';
  document.getElementById('userId').value     = u.id;
  document.getElementById('uFullName').value  = u.full_name;
  document.getElementById('uEmail').value     = u.email;
  document.getElementById('uPassword').value  = '';
  document.getElementById('uRole').value      = u.role;
  document.getElementById('passwordRow').style.display = 'none';
  document.getElementById('uPassword').required = false;
}
function openReset(id, name) {
  document.getElementById('resetId').value      = id;
  document.getElementById('resetName').textContent = name;
  document.getElementById('resetPw').value      = '';
}
function togglePw(inputId, btn) {
  var input = document.getElementById(inputId);
  var icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'bi bi-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'bi bi-eye';
  }
}
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
