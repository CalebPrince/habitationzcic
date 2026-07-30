<?php
// admin/dashboard.php
require_once __DIR__ . '/../includes/session.php';
Auth::require(['admin', 'editor']);
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

// ── Stat counts ───────────────────────────────────────────────
$enqNewCount   = Database::fetchOne("SELECT COUNT(*) c FROM enquiries WHERE status='new'")['c'];
$enqTotalCount = Database::fetchOne("SELECT COUNT(*) c FROM enquiries")['c'];
$serviceCount  = Database::fetchOne("SELECT COUNT(*) c FROM services WHERE is_active=1")['c'];
$faqCount      = Database::fetchOne("SELECT COUNT(*) c FROM faqs WHERE is_active=1")['c'];

// ── This week counts (Mon 00:00 to now) ──────────────────────
$weekStart = date('Y-m-d', strtotime('monday this week'));
$enqWeek   = Database::fetchOne(
    "SELECT COUNT(*) c FROM enquiries WHERE submitted_at >= ?", [$weekStart]
)['c'];

// ── Recent activity ───────────────────────────────────────────
$recentEnqs = Database::fetchAll(
    "SELECT full_name, status, subject, submitted_at, LEFT(message, 80) AS preview
     FROM enquiries ORDER BY submitted_at DESC LIMIT 6"
);

include __DIR__ . '/_layout_top.php';
?>

<!-- ── Stat cards ──────────────────────────────────────────── -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat-card">
      <div class="stat-icon icon-gold"><i class="bi bi-chat-dots"></i></div>
      <div class="stat-val"><?= $enqNewCount ?></div>
      <div class="stat-lbl">New enquiries</div>
      <span class="stat-badge" style="background:rgba(223,199,43,0.2); color:#8a761a;">
        <?= $enqWeek > 0 ? "+$enqWeek this week" : 'No new this week' ?>
      </span>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat-card">
      <div class="stat-icon icon-teal"><i class="bi bi-inbox"></i></div>
      <div class="stat-val"><?= $enqTotalCount ?></div>
      <div class="stat-lbl">Total enquiries</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat-card">
      <div class="stat-icon icon-green"><i class="bi bi-house-heart"></i></div>
      <div class="stat-val"><?= $serviceCount ?></div>
      <div class="stat-lbl">Visible services</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat-card">
      <div class="stat-icon icon-blue"><i class="bi bi-question-circle"></i></div>
      <div class="stat-val"><?= $faqCount ?></div>
      <div class="stat-lbl">Visible FAQs</div>
    </div>
  </div>
</div>

<!-- ── Recent enquiries ─────────────────────────────────────── -->
<div class="admin-card mb-4">
  <div class="admin-card-header">
    <h5><i class="bi bi-chat-dots me-2" style="color:var(--hzc-accent-dark);"></i>Recent Enquiries</h5>
    <a href="enquiries.php" style="font-size:0.8rem; font-weight:600; color:var(--hzc-accent-dark); text-decoration:none;">
      View all <i class="bi bi-arrow-right-short"></i>
    </a>
  </div>
  <?php if (empty($recentEnqs)): ?>
  <p class="text-muted" style="font-size:0.85rem;">No enquiries yet.</p>
  <?php else: ?>
  <div style="display:flex; flex-direction:column; gap:0.75rem;">
    <?php foreach ($recentEnqs as $e): ?>
    <div style="display:flex; align-items:flex-start; justify-content:space-between;
                gap:0.75rem; padding-bottom:0.75rem; border-bottom:1px solid var(--hzc-border);">
      <div style="min-width:0;">
        <div style="font-weight:600; font-size:0.88rem; color:var(--hzc-primary);">
          <?= htmlspecialchars($e['full_name']) ?>
          <?php if ($e['subject']): ?>
          <span style="font-weight:400; color:var(--hzc-muted); font-size:0.78rem;">
            &middot; <?= htmlspecialchars($e['subject']) ?>
          </span>
          <?php endif; ?>
        </div>
        <div style="font-size:0.78rem; color:var(--hzc-muted);">
          <?= htmlspecialchars($e['preview']) ?>…
        </div>
      </div>
      <span class="status-badge status-<?= $e['status'] ?>"><?= ucfirst($e['status']) ?></span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ── Quick actions ───────────────────────────────────────── -->
<div class="admin-card">
  <div class="admin-card-header">
    <h5>Quick actions</h5>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="enquiries.php" class="btn btn-hzc-primary btn-sm px-3">
      <i class="bi bi-chat-dots me-1"></i> Review enquiries
    </a>
    <a href="services.php" class="btn btn-outline-secondary btn-sm px-3">Manage services</a>
    <a href="faqs.php" class="btn btn-outline-secondary btn-sm px-3">Manage FAQs</a>
    <a href="content.php" class="btn btn-outline-secondary btn-sm px-3">Edit page content</a>
    <a href="users.php" class="btn btn-outline-secondary btn-sm px-3">Manage users</a>
  </div>
</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
