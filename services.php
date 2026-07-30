<?php
require_once __DIR__ . '/includes/database.php';
$pageTitle = 'Our Services';
$services  = Database::fetchAll("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC");
$pillarTones = ['teal', 'gold'];
include __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
  <div class="container banner-content">
    <div class="crumb-pill">
      <a href="<?= BASE_URL ?>/index.php">Home</a> <span class="sep">/</span> <span>Our Services</span>
    </div>
    <h1>Our Services</h1>
    <p>Safe accommodation, tenancy support, and community inclusion — delivered in partnership with regulated care providers</p>
  </div>
  <svg class="page-banner-wave" viewBox="0 0 1440 74" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M0,32 C240,74 480,0 720,18 C960,36 1200,74 1440,32 L1440,74 L0,74 Z" fill="#FBF8F1"></path>
  </svg>
</div>

<!-- ── Services grid ────────────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <?php foreach ($services as $i => $s): $tone = $pillarTones[$i % 2]; ?>
      <div class="col-md-6">
        <div class="mini-card h-100">
          <div class="mini-card-icon tone-<?= $tone ?>" style="width:64px; height:64px; font-size:1.7rem;">
            <i class="bi <?= htmlspecialchars($s['icon']) ?>"></i>
          </div>
          <h4 class="fw-bold mb-2"><?= htmlspecialchars($s['title']) ?></h4>
          <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($s['description'])) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Standards ─────────────────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <div class="hero-photo-frame" style="box-shadow:var(--shadow-md);">
          <img src="<?= BASE_URL ?>/assets/img/hero-4.webp" alt="Well-maintained supported housing" style="height:340px;">
        </div>
      </div>
      <div class="col-lg-6">
        <span class="section-label">Housing Standards</span>
        <h2 class="fw-bold mb-3">Quality you can rely on</h2>
        <p class="text-muted mb-4">
          Every property meets the Housing Act 2004 and REACH standards, and is regularly
          reviewed to ensure it remains safe, well-maintained, and fit for purpose.
        </p>
        <div class="d-flex flex-wrap gap-3">
          <a href="<?= BASE_URL ?>/working-together.php" class="btn btn-hzc-primary">
            Working Together <i class="bi bi-arrow-right-short ms-1"></i>
          </a>
          <a href="<?= BASE_URL ?>/contact.php" class="btn btn-hzc-accent">
            Make a Referral
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CTA ────────────────────────────────────────────────── -->
<section class="py-5 text-center">
  <div class="container">
    <h2 class="fw-bold mb-2">Want to know more about a specific service?</h2>
    <p class="text-muted mb-4 col-lg-5 mx-auto">
      Get in touch and our team will talk you through eligibility, referral routes, and next steps.
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="<?= BASE_URL ?>/contact.php" class="btn btn-hzc-primary">Contact Us</a>
      <a href="<?= BASE_URL ?>/about.php" class="btn btn-hzc-accent">About Us</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
