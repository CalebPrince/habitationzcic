<?php
require_once __DIR__ . '/includes/database.php';
$pageTitle = 'About Us';

$rawBlocks = Database::fetchAll(
    "SELECT section_key, content FROM page_content WHERE page_slug = 'about'"
);
$pc = array_column($rawBlocks, 'content', 'section_key');
$b = fn(string $key, string $fallback = '') => htmlspecialchars($pc[$key] ?? $fallback);

include __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
  <div class="container banner-content">
    <div class="crumb-pill">
      <a href="<?= BASE_URL ?>/index.php">Home</a> <span class="sep">/</span> <span>About Us</span>
    </div>
    <h1><?= $b('header_title', 'About ' . SITE_NAME) ?></h1>
    <p><?= $b('header_sub', 'Safe, supported housing built around independence, inclusion, and community') ?></p>
  </div>
  <svg class="page-banner-wave" viewBox="0 0 1440 74" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M0,32 C240,74 480,0 720,18 C960,36 1200,74 1440,32 L1440,74 L0,74 Z" fill="#FBF8F1"></path>
  </svg>
</div>

<!-- ── Who we are ─────────────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <span class="section-label"><?= $b('who_label', 'Who We Are') ?></span>
        <h2 class="fw-bold mb-3"><?= $b('who_title', 'More than just a landlord') ?></h2>
        <p class="mb-3"><?= $b('who_body_1', SITE_NAME . ' provides safe, supported housing, promoting independence, inclusion, and lasting community wellbeing for the people we house.') ?></p>
        <p class="mb-3"><?= $b('who_body_2', 'We support young people and adults, including older persons, with an assessed housing need and a package of care or support already in place — including those with learning disabilities, mental health needs, physical disabilities, care leavers, and people with complex or continuing healthcare requirements.') ?></p>
        <p class="mb-0"><?= $b('who_body_3', 'We provide accommodation only. All regulated care and support are delivered by regulated, registered partner organisations, keeping housing and care clearly separated.') ?></p>
      </div>
      <div class="col-lg-6">
        <div class="hero-photo-frame" style="box-shadow:var(--shadow-md);">
          <img src="<?= BASE_URL ?>/assets/img/hero-3.webp" alt="Supported housing property" style="height:380px;">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── Vision & Mission ───────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label">What Drives Us</span>
      <h2 class="fw-bold">Our vision &amp; mission</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="mini-card">
          <div class="mini-card-icon tone-teal"><i class="bi bi-eye"></i></div>
          <h4 class="fw-bold mb-3" style="font-size:1.05rem;">
            <?= $b('vision_title', 'Our Vision') ?>
          </h4>
          <p class="text-muted mb-0" style="line-height:1.75;">
            <?= $b('vision_body', 'A future where everyone has access to safe, stable housing and the support they need to live independently and be part of their community.') ?>
          </p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="mini-card">
          <div class="mini-card-icon tone-gold"><i class="bi bi-bullseye"></i></div>
          <h4 class="fw-bold mb-3" style="font-size:1.05rem;">
            <?= $b('mission_title', 'Our Mission') ?>
          </h4>
          <p class="text-muted mb-0" style="line-height:1.75;">
            <?= $b('mission_body', 'To provide safe, high-quality supported housing in partnership with regulated care providers, reinvesting our surplus into better homes and stronger communities.') ?>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── Who we support ─────────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label">Eligibility</span>
      <h2 class="fw-bold">Who we support</h2>
    </div>
    <div class="row g-4">
      <?php
      $population = [
        ['bi-person-heart',    'teal', 'Learning Disabilities',      'Supported housing tailored to individuals with learning disabilities, referred with an existing care package.'],
        ['bi-heart-pulse',     'gold', 'Mental Health Needs',        'Safe, stable accommodation for people managing mental health conditions, alongside their regulated care team.'],
        ['bi-person-wheelchair', 'teal', 'Physical Disabilities',    'Accessible, high-quality housing for adults and older persons with physical disabilities.'],
        ['bi-signpost-2',      'gold', 'Care Leavers',               'Housing support that helps care leavers build independence with the right wraparound support in place.'],
      ];
      foreach ($population as [$icon, $tone, $title, $desc]):
      ?>
      <div class="col-md-6 col-lg-3">
        <div class="mini-card">
          <div class="mini-card-icon tone-<?= $tone ?>"><i class="bi <?= $icon ?>"></i></div>
          <h5 class="fw-bold mb-2" style="font-size:0.98rem;"><?= $title ?></h5>
          <p class="text-muted mb-0" style="font-size:0.87rem;"><?= $desc ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Social value ────────────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-5">
        <div class="stat-pill-row">
          <div class="stat-pill">
            <span class="stat-num">C.I.C.</span>
            <span class="stat-label">Community Interest Company</span>
          </div>
          <div class="stat-pill">
            <span class="stat-num">0%</span>
            <span class="stat-label">Direct care delivery</span>
          </div>
          <div class="stat-pill">
            <span class="stat-num">UK</span>
            <span class="stat-label">National coverage</span>
          </div>
        </div>
      </div>
      <div class="col-lg-7">
        <span class="section-label"><?= $b('cic_label', 'Community Interest Company') ?></span>
        <h2 class="fw-bold mb-3"><?= $b('cic_title', 'Profit with a purpose') ?></h2>
        <p class="text-muted mb-3"><?= $b('cic_body_1', 'As a Community Interest Company, ' . SITE_NAME . ' exists to benefit the community, not shareholders.') ?></p>
        <p class="text-muted mb-4"><?= $b('cic_body_2', 'We reinvest the majority of our surplus into property improvements, tenancy sustainment, and expanding access to supported housing across the UK.') ?></p>
        <a href="<?= BASE_URL ?>/working-together.php" class="btn btn-hzc-primary">
          <i class="bi bi-diagram-3 me-1"></i> How We Work Together
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ── CTA ────────────────────────────────────────────────── -->
<section class="py-5 text-center">
  <div class="container">
    <h2 class="fw-bold mb-2"><?= $b('cta_title', 'Ready to find out more?') ?></h2>
    <p class="text-muted mb-4 col-lg-5 mx-auto">
      <?= $b('cta_sub', 'Whether you\'re a local authority, a care provider, or looking to refer someone, our team is here to help.') ?>
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="<?= BASE_URL ?>/contact.php" class="btn btn-hzc-primary">Get in Touch</a>
      <a href="<?= BASE_URL ?>/services.php" class="btn btn-hzc-accent">Our Services</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
