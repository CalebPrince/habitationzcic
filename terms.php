<?php
require_once __DIR__ . '/includes/database.php';
$pageTitle = 'Terms of Use';
$metaDesc  = SITE_NAME . ' terms of use for this website.';
include __DIR__ . '/includes/header.php';
?>

<div class="page-banner" style="padding:3rem 0 4.5rem;">
  <div class="container banner-content">
    <div class="crumb-pill">
      <a href="<?= BASE_URL ?>/index.php">Home</a> <span class="sep">/</span> <span>Terms of Use</span>
    </div>
    <h1 style="font-size:1.9rem;">Terms of Use</h1>
    <p>Last updated: <?= date('d F Y') ?></p>
  </div>
  <svg class="page-banner-wave" viewBox="0 0 1440 74" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M0,32 C240,74 480,0 720,18 C960,36 1200,74 1440,32 L1440,74 L0,74 Z" fill="#FBF8F1"></path>
  </svg>
</div>

<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="panel-card p-4 p-lg-5">

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            1. Acceptance of terms
          </h4>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            By accessing and using this website, you accept and agree to be bound by these
            Terms of Use. If you do not agree to these terms, please do not use this website.
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            2. Use of this website
          </h4>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            This website is provided for general information about <?= SITE_NAME ?> and our
            supported housing services. It is not a substitute for professional advice, and
            nothing on this site constitutes a formal referral or offer of accommodation until
            confirmed directly by our team.
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            3. No direct care provision
          </h4>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            <?= SITE_NAME ?> provides accommodation only. Regulated care and support are
            delivered by separate, regulated, registered partner organisations. References to
            "care" or "support" on this website describe services delivered by those partners,
            not by <?= SITE_NAME ?> directly.
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            4. Intellectual property
          </h4>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            All content on this website, including text, graphics, logos, and images, is the
            property of <?= SITE_NAME ?> unless otherwise stated, and may not be reproduced
            without permission.
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            5. Limitation of liability
          </h4>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            While we make every effort to keep information on this website accurate and up to
            date, we make no warranties about the completeness or accuracy of this information
            and will not be liable for any losses arising from its use.
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            6. Changes to these terms
          </h4>
          <p class="text-muted mb-0" style="font-size:0.9rem; line-height:1.8;">
            We may update these Terms of Use from time to time. Continued use of this website
            after changes are posted constitutes acceptance of the updated terms.
          </p>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
