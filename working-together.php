<?php
require_once __DIR__ . '/includes/database.php';
$pageTitle = 'Working Together';

$rawBlocks = Database::fetchAll(
    "SELECT section_key, content FROM page_content WHERE page_slug = 'working-together'"
);
$pc = array_column($rawBlocks, 'content', 'section_key');
$b = fn(string $key, string $fallback = '') => htmlspecialchars($pc[$key] ?? $fallback);

include __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
  <div class="container banner-content">
    <div class="crumb-pill">
      <a href="<?= BASE_URL ?>/index.php">Home</a> <span class="sep">/</span> <span>Working Together</span>
    </div>
    <h1><?= $b('header_title', 'Working Together') ?></h1>
    <p><?= $b('header_sub', 'How we partner with local authorities, commissioners, and regulated care providers') ?></p>
  </div>
  <svg class="page-banner-wave" viewBox="0 0 1440 74" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M0,32 C240,74 480,0 720,18 C960,36 1200,74 1440,32 L1440,74 L0,74 Z" fill="#FBF8F1"></path>
  </svg>
</div>

<!-- ── Partnership model ───────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <span class="section-label"><?= $b('model_label', 'Our Partnership Model') ?></span>
        <h2 class="fw-bold mb-3"><?= $b('model_title', 'Housing and care, clearly separated') ?></h2>
        <p class="mb-3"><?= $b('model_body_1', SITE_NAME . ' provides accommodation only. All regulated care and support are delivered by regulated, registered partner organisations.') ?></p>
        <p class="mb-0"><?= $b('model_body_2', 'This clear separation means residents receive high-quality housing from us, and person-centred, regulated care from a specialist provider — each doing what they do best.') ?></p>
      </div>
      <div class="col-lg-6">
        <div class="row g-3">
          <?php
          $partners = [
            ['bi-bank', 'teal', 'Local Authorities',        'Refer eligible individuals with an assessed housing need.'],
            ['bi-shield-check', 'gold', 'CQC-Registered Providers', 'Deliver personal, clinical, and regulated care and support.'],
            ['bi-briefcase', 'teal', 'Commissioners',             'Coordinate placements and ensure services meet local need.'],
          ];
          foreach ($partners as [$icon, $tone, $title, $desc]):
          ?>
          <div class="col-12">
            <div class="mini-card d-flex align-items-start gap-3" style="flex-direction:row; padding:1.25rem 1.4rem;">
              <div class="mini-card-icon tone-<?= $tone ?> flex-shrink-0" style="margin-bottom:0;"><i class="bi <?= $icon ?>"></i></div>
              <div>
                <h6 class="fw-bold mb-1" style="font-size:0.92rem;"><?= $title ?></h6>
                <p class="text-muted mb-0" style="font-size:0.85rem;"><?= $desc ?></p>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── Referral eligibility ───────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label">Referral Eligibility</span>
      <h2 class="fw-bold">Who we can accept a referral for</h2>
    </div>
    <div class="row g-4 justify-content-center">
      <div class="col-lg-9">
        <div class="value-grid-wrap">
          <div class="row g-3">
            <?php
            $criteria = [
              ['bi-clipboard-check', 'An assessed housing need, confirmed by a local authority or commissioner'],
              ['bi-heart-pulse', 'A package of care or support already in place with a regulated provider'],
              ['bi-file-medical', 'Confirmation of any complex, clinical, or continuing healthcare needs'],
              ['bi-shield-check', 'Relevant risk assessments and support plan documentation'],
            ];
            foreach ($criteria as [$icon, $text]): ?>
            <div class="col-sm-6">
              <div class="value-chip">
                <div class="value-icon"><i class="bi <?= $icon ?>"></i></div>
                <div class="value-text"><?= htmlspecialchars($text) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── Social value ────────────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <div class="hero-photo-frame" style="box-shadow:var(--shadow-md);">
          <img src="<?= BASE_URL ?>/assets/img/hero-2.webp" alt="Community partnership meeting" style="height:340px;">
        </div>
      </div>
      <div class="col-lg-6">
        <span class="section-label">Financial Structure</span>
        <h2 class="fw-bold mb-3">Surplus reinvested, not extracted</h2>
        <p class="text-muted mb-4">
          As a Community Interest Company, we reinvest the majority of our surplus into
          property improvements, tenancy sustainment, and expanding access to supported
          housing — so our partnerships create lasting community value.
        </p>
        <a href="<?= BASE_URL ?>/contact.php" class="btn btn-hzc-primary">
          <i class="bi bi-diagram-3 me-1"></i> Start a Referral Conversation
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ── CTA ────────────────────────────────────────────────── -->
<section class="py-5 text-center">
  <div class="container">
    <h2 class="fw-bold mb-2">Ready to make a referral or start a partnership?</h2>
    <p class="text-muted mb-4 col-lg-5 mx-auto">
      Reach out to our team and we'll walk you through the referral process step by step.
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="<?= BASE_URL ?>/contact.php" class="btn btn-hzc-primary">Contact Us</a>
      <a href="<?= BASE_URL ?>/services.php" class="btn btn-hzc-accent">Our Services</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
