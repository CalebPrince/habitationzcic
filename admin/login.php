<?php
// admin/login.php — administrator/editor access
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';

if (Auth::check() && in_array(Auth::role(), ['admin', 'editor'], true)) {
    header('Location: dashboard.php');
    exit;
}
if (Auth::check()) {
    Auth::logout();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!Auth::csrfCheck($_POST['csrf_token'] ?? null)) {
    $error = 'Your session expired. Please try again.';
  } else {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (Auth::attempt($email, $password) && in_array(Auth::role(), ['admin', 'editor'], true)) {
      header('Location: dashboard.php');
      exit;
    }

    $error = 'Invalid credentials or insufficient permissions.';
    if (isset($_SESSION['user_id'])) Auth::logout();
  }
}
$csrf = Auth::csrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login | <?= SITE_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
<style>
  body {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--hzc-dark);
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .login-card {
    background: #fff;
    border-radius: 18px;
    padding: 2.75rem 2.5rem;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.28);
  }
  .login-card h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--hzc-primary);
    margin-bottom: 0.2rem;
  }
  .login-card .sub {
    font-size: 0.82rem;
    color: var(--hzc-muted);
    margin-bottom: 1.75rem;
  }
  .login-card .form-label {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--hzc-primary);
    margin-bottom: 0.3rem;
  }
  .login-card .form-control {
    border: 1.5px solid var(--hzc-border);
    border-radius: 10px;
    font-size: 0.9rem;
    padding: 0.6rem 0.85rem;
    transition: border-color 0.15s, box-shadow 0.15s;
  }
  .login-card .form-control:focus {
    border-color: var(--hzc-accent);
    box-shadow: 0 0 0 3px rgba(223,199,43,0.25);
    outline: none;
  }
  .btn-login {
    background: var(--hzc-primary);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 0.7rem;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 0.9rem;
    width: 100%;
    transition: background 0.15s;
  }
  .btn-login:hover { background: var(--hzc-primary-dark); color: #fff; }
  .admin-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: rgba(47,92,95,0.09);
    color: var(--hzc-primary);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    border-radius: 50px;
    padding: 0.28rem 0.8rem;
    margin-bottom: 1.25rem;
  }
  .alert-err {
    background: rgba(220,53,69,0.08);
    border: 1.5px solid rgba(220,53,69,0.2);
    border-radius: 10px;
    color: #a02030;
    font-size: 0.84rem;
    padding: 0.7rem 0.9rem;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .forgot-link {
    font-size: 0.78rem;
    color: var(--hzc-muted);
    text-decoration: none;
  }
  .forgot-link:hover { color: var(--hzc-accent-dark); }
</style>
</head>
<body>

<div class="login-card">
  <div class="text-center mb-4">
    <img src="../assets/img/logo.svg" alt="<?= SITE_NAME ?>" style="height:36px;">
  </div>

  <div class="admin-badge">
    <i class="bi bi-shield-lock"></i> Admin Panel
  </div>

  <h1>Sign in</h1>
  <p class="sub">Administrator &amp; editor access only</p>

  <?php if ($error): ?>
  <div class="alert-err" role="alert">
    <i class="bi bi-exclamation-circle-fill"></i>
    <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <form method="post" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" id="email" name="email" class="form-control"
             placeholder="admin@habitationzcic.co.uk"
             autocomplete="email" required autofocus>
    </div>
    <div class="mb-4">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <label for="password" class="form-label mb-0">Password</label>
        <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
      </div>
      <input type="password" id="password" name="password" class="form-control"
             placeholder="••••••••"
             autocomplete="current-password" required>
    </div>
    <button type="submit" class="btn-login">Sign in</button>
  </form>

  <p class="text-center mt-3 mb-0" style="font-size:0.8rem;">
    <a href="../index.php" style="color:var(--hzc-muted); text-decoration:none;">
      <i class="bi bi-arrow-left me-1"></i>Back to website
    </a>
  </p>
</div>

</body>
</html>
