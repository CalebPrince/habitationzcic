<?php
// admin/_layout_top.php — shared admin shell
$adminUser = Auth::user();
$initials  = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', $adminUser['full_name']), 0, 2)));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> | <?= SITE_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body style="background:var(--hzc-light);">

<div class="admin-layout">

  <!-- ── Sidebar ── -->
  <aside class="admin-sidebar">
    <div class="sidebar-logo">
      <img src="../assets/img/logo-white.svg" alt="<?= SITE_NAME ?>">
    </div>

    <nav class="py-2 flex-grow-1">
      <div class="sidebar-section-label">Management</div>

      <a href="dashboard.php" class="<?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
      <a href="enquiries.php" class="<?= ($activeNav ?? '') === 'enquiries' ? 'active' : '' ?>">
        <i class="bi bi-chat-dots"></i> Enquiries
      </a>

      <div class="sidebar-section-label mt-2">Content</div>
      <a href="services.php" class="<?= ($activeNav ?? '') === 'services' ? 'active' : '' ?>">
        <i class="bi bi-house-heart"></i> Services
      </a>
      <a href="faqs.php" class="<?= ($activeNav ?? '') === 'faqs' ? 'active' : '' ?>">
        <i class="bi bi-question-circle"></i> FAQs
      </a>
      <a href="content.php" class="<?= ($activeNav ?? '') === 'content' ? 'active' : '' ?>">
        <i class="bi bi-pencil-square"></i> Page Content
      </a>

      <div class="sidebar-section-label mt-2">Administration</div>
      <a href="users.php" class="<?= ($activeNav ?? '') === 'users' ? 'active' : '' ?>">
        <i class="bi bi-people"></i> Users
      </a>
      <a href="activity-log.php" class="<?= ($activeNav ?? '') === 'activity' ? 'active' : '' ?>">
        <i class="bi bi-clock-history"></i> Activity Log
      </a>

      <div class="sidebar-section-label mt-2">Site</div>
      <a href="../index.php" target="_blank">
        <i class="bi bi-box-arrow-up-right"></i> View Website
      </a>
    </nav>

    <div class="sidebar-footer">
      <div style="padding:0 0.75rem 0.5rem; font-size:0.75rem; color:rgba(255,255,255,0.35);">
        Signed in as <strong style="color:rgba(255,255,255,0.6);"><?= htmlspecialchars($adminUser['full_name']) ?></strong>
      </div>
      <a href="logout.php">
        <i class="bi bi-box-arrow-right"></i> Sign out
      </a>
    </div>
  </aside>

  <!-- ── Main ── -->
  <div class="admin-main">

    <!-- Top bar -->
    <div class="admin-topbar">
      <div class="page-title-bar">
        <?= htmlspecialchars($pageTitle ?? 'Admin') ?>
      </div>
      <div class="user-chip">
        <div class="avatar"><?= $initials ?></div>
        <span><?= htmlspecialchars($adminUser['full_name']) ?></span>
        <a href="logout.php" class="btn btn-outline-secondary btn-sm ms-2"
           style="font-size:0.75rem; padding:0.2rem 0.65rem;">Sign out</a>
      </div>
    </div>

    <!-- Page content begins -->
    <div class="admin-content">
