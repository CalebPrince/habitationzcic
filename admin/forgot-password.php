<?php
// admin/forgot-password.php — password reset for admin/editor users
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/mailer.php';

$step  = 'request';
$error = '';
$token = '';

// ── Step 1: process email submission ─────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email'] ?? '');
    $step  = 'sent'; // always show sent — avoids user enumeration

    $user = Database::fetchOne(
        "SELECT id, full_name, role FROM users
         WHERE email = ? AND status = 'active' AND role IN ('admin','editor')",
        [$email]
    );

    if ($user) {
        Database::query(
            "UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0",
            [$user['id']]
        );
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expires   = date('Y-m-d H:i:s', strtotime('+1 hour'));

        Database::query(
            "INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)",
            [$user['id'], $tokenHash, $expires]
        );

        $resetUrl = rtrim(BASE_URL, '/') . '/admin/forgot-password.php?token=' . $token;
        $safeResetUrl = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');
        $message = <<<HTML
<tr><td style="padding:24px 32px 8px;">
  <p style="margin:0;font-size:15px;color:#223838;line-height:1.7;">
    A password reset was requested for your admin account. Use the button below to choose a new password.
  </p>
</td></tr>
<tr><td style="padding:8px 32px 24px;">
  <a href="{$safeResetUrl}"
     style="display:inline-block;background:#DFC72B;color:#2E4C4E;text-decoration:none;padding:12px 24px;border-radius:8px;font-weight:700;font-size:14px;">
    Reset Password
  </a>
  <p style="margin:16px 0 0;font-size:13px;color:#8fa0b4;line-height:1.7;">
    If the button does not work, copy and paste this link into your browser:<br>
    {$safeResetUrl}
  </p>
</td></tr>
HTML;

        if (!sendMail(
            $email,
            $user['full_name'],
            'Reset your password — ' . SITE_NAME,
            emailTemplate('Password reset request', $message)
        )) {
            $_SESSION['_dev_reset_url'] = $resetUrl;
        }
    }
}

// ── Step 2: process new password ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'], $_POST['password'])) {
    $token       = $_POST['token'];
    $tokenHash   = hash('sha256', $token);
    $newPassword = $_POST['password'];
    $confirm     = $_POST['confirm'] ?? '';

    if (strlen($newPassword) < 8) {
        $step = 'reset'; $error = 'Password must be at least 8 characters.';
    } elseif ($newPassword !== $confirm) {
        $step = 'reset'; $error = 'Passwords do not match.';
    } else {
        $row = Database::fetchOne(
            "SELECT pr.id, pr.user_id FROM password_resets pr
             WHERE pr.token_hash = ? AND pr.used = 0 AND pr.expires_at > NOW()",
            [$tokenHash]
        );
        if (!$row) {
            $step = 'error';
            $error = 'This reset link is invalid or has expired. Please request a new one.';
        } else {
            Database::query(
                "UPDATE users SET password_hash = ? WHERE id = ?",
                [password_hash($newPassword, PASSWORD_BCRYPT), $row['user_id']]
            );
            Database::query(
                "UPDATE password_resets SET used = 1 WHERE id = ?",
                [$row['id']]
            );
            $step = 'done';
        }
    }
}

// ── Step: show reset form from URL token ─────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token     = $_GET['token'];
    $tokenHash = hash('sha256', $token);
    $row = Database::fetchOne(
        "SELECT id FROM password_resets
         WHERE token_hash = ? AND used = 0 AND expires_at > NOW()",
        [$tokenHash]
    );
    $step  = $row ? 'reset' : 'error';
    $error = $row ? '' : 'This reset link is invalid or has expired. Please request a new one.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reset Password | <?= SITE_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
<style>
  body {
    min-height:100vh; display:flex; align-items:center; justify-content:center;
    background:var(--hzc-dark); font-family:'Plus Jakarta Sans',sans-serif;
  }
  .reset-card {
    background:#fff; border-radius:18px; padding:2.75rem 2.5rem;
    width:100%; max-width:420px; box-shadow:0 8px 40px rgba(0,0,0,0.28);
  }
  .reset-card h1 {
    font-family:'Plus Jakarta Sans',sans-serif; font-size:1.2rem; font-weight:700;
    color:var(--hzc-primary); margin-bottom:0.2rem;
  }
  .reset-card .sub { font-size:0.82rem; color:var(--hzc-muted); margin-bottom:1.75rem; }
  .reset-card .form-label {
    font-size:0.78rem; font-weight:700; text-transform:uppercase;
    letter-spacing:0.05em; color:var(--hzc-primary); margin-bottom:0.3rem;
  }
  .reset-card .form-control {
    border:1.5px solid var(--hzc-border); border-radius:10px;
    font-size:0.9rem; padding:0.6rem 0.85rem;
    transition:border-color 0.15s, box-shadow 0.15s;
  }
  .reset-card .form-control:focus {
    border-color:var(--hzc-accent);
    box-shadow:0 0 0 3px rgba(223,199,43,0.25); outline:none;
  }
  .btn-reset {
    background:var(--hzc-primary); color:#fff; border:none;
    border-radius:10px; padding:0.7rem;
    font-family:'Plus Jakarta Sans',sans-serif; font-weight:600;
    font-size:0.9rem; width:100%; transition:background 0.15s;
  }
  .btn-reset:hover { background:var(--hzc-primary-dark); color:#fff; }
  .alert-err {
    background:rgba(220,53,69,0.08); border:1.5px solid rgba(220,53,69,0.2);
    border-radius:10px; color:#a02030; font-size:0.84rem;
    padding:0.7rem 0.9rem; margin-bottom:1.25rem;
    display:flex; align-items:center; gap:0.5rem;
  }
  .alert-ok {
    background:rgba(25,135,84,0.08); border:1.5px solid rgba(25,135,84,0.2);
    border-radius:10px; color:#146c43; font-size:0.84rem;
    padding:0.9rem; margin-bottom:1.25rem;
  }
  .dev-box {
    background:rgba(223,199,43,0.12); border:1.5px solid rgba(223,199,43,0.35);
    border-radius:10px; padding:0.85rem; margin-top:1rem;
    font-size:0.78rem; word-break:break-all;
  }
  .strength-bar  { height:4px; border-radius:2px; margin-top:6px; background:var(--hzc-border); }
  .strength-fill { height:100%; border-radius:2px; transition:width 0.2s, background 0.2s; }
</style>
</head>
<body>
<div class="reset-card">
  <div class="text-center mb-4">
    <img src="../assets/img/logo.svg" alt="<?= SITE_NAME ?>" style="height:34px;">
  </div>

  <?php if ($step === 'request'): ?>
  <h1>Forgot password?</h1>
  <p class="sub">Enter your admin email and we'll send a reset link.</p>
  <form method="post" novalidate>
    <div class="mb-4">
      <label for="email" class="form-label">Email address</label>
      <input type="email" id="email" name="email" class="form-control"
             placeholder="admin@habitationzcic.co.uk"
             autocomplete="email" required autofocus>
    </div>
    <button type="submit" class="btn-reset">Send reset link</button>
  </form>

  <?php elseif ($step === 'sent'): ?>
  <div class="alert-ok">
    <i class="bi bi-envelope-check me-2"></i>
    <strong>Check your inbox.</strong><br>
    If that email is registered, a reset link has been sent. It expires in 1 hour.
  </div>
  <?php if (!empty($_SESSION['_dev_reset_url'])): ?>
  <div class="dev-box">
    <strong style="color:var(--hzc-accent-dark);">⚠ Dev mode — link not emailed yet:</strong><br>
    <a href="<?= htmlspecialchars($_SESSION['_dev_reset_url']) ?>"
       style="color:var(--hzc-primary);">
      <?= htmlspecialchars($_SESSION['_dev_reset_url']) ?>
    </a>
  </div>
  <?php unset($_SESSION['_dev_reset_url']); endif; ?>

  <?php elseif ($step === 'reset'): ?>
  <h1>Choose a new password</h1>
  <p class="sub">Pick something strong — at least 8 characters.</p>
  <?php if ($error): ?>
  <div class="alert-err"><i class="bi bi-exclamation-circle-fill"></i><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post" novalidate>
    <input type="hidden" name="token"
           value="<?= htmlspecialchars($token ?: ($_GET['token'] ?? '')) ?>">
    <div class="mb-3">
      <label for="password" class="form-label">New password</label>
      <input type="password" id="password" name="password" class="form-control"
             placeholder="Min. 8 characters" autocomplete="new-password" required
             oninput="checkStrength(this.value)">
      <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
      <div id="strengthLabel" style="font-size:0.72rem; color:var(--hzc-muted); margin-top:3px;"></div>
    </div>
    <div class="mb-4">
      <label for="confirm" class="form-label">Confirm password</label>
      <input type="password" id="confirm" name="confirm" class="form-control"
             placeholder="Repeat new password" autocomplete="new-password" required>
    </div>
    <button type="submit" class="btn-reset">Set new password</button>
  </form>

  <?php elseif ($step === 'done'): ?>
  <div class="alert-ok">
    <i class="bi bi-check-circle-fill me-2"></i>
    <strong>Password updated!</strong> You can now sign in with your new password.
  </div>
  <a href="login.php" class="btn-reset d-block text-center text-decoration-none">
    Go to sign in
  </a>

  <?php elseif ($step === 'error'): ?>
  <div class="alert-err">
    <i class="bi bi-exclamation-circle-fill"></i><?= htmlspecialchars($error) ?>
  </div>
  <a href="forgot-password.php" class="btn-reset d-block text-center text-decoration-none">
    Request a new link
  </a>
  <?php endif; ?>

  <p class="text-center mt-4 mb-0" style="font-size:0.8rem;">
    <a href="login.php" style="color:var(--hzc-muted); text-decoration:none;">
      <i class="bi bi-arrow-left me-1"></i>Back to sign in
    </a>
  </p>
</div>

<script>
function checkStrength(val) {
  const fill  = document.getElementById('strengthFill');
  const label = document.getElementById('strengthLabel');
  let score = 0;
  if (val.length >= 8)           score++;
  if (val.length >= 12)          score++;
  if (/[A-Z]/.test(val))         score++;
  if (/[0-9]/.test(val))         score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const levels = [
    { pct:'20%', bg:'#dc3545', lbl:'Very weak' },
    { pct:'40%', bg:'#fd7e14', lbl:'Weak' },
    { pct:'60%', bg:'#ffc107', lbl:'Fair' },
    { pct:'80%', bg:'#20c997', lbl:'Good' },
    { pct:'100%', bg:'#198754', lbl:'Strong' },
  ];
  const l = levels[Math.min(score, 4)];
  fill.style.width      = l.pct;
  fill.style.background = l.bg;
  label.textContent     = l.lbl;
  label.style.color     = l.bg;
}
</script>
</body>
</html>
