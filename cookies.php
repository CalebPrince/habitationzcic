<?php
require_once __DIR__ . '/includes/database.php';
$pageTitle = 'Cookie Policy';
$metaDesc  = SITE_NAME . ' cookie policy — how we use cookies on this website.';
include __DIR__ . '/includes/header.php';
?>

<div class="page-banner" style="padding:3rem 0 4.5rem;">
  <div class="container banner-content">
    <div class="crumb-pill">
      <a href="<?= BASE_URL ?>/index.php">Home</a> <span class="sep">/</span> <span>Cookie Policy</span>
    </div>
    <h1 style="font-size:1.9rem;">Cookie Policy</h1>
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
            1. What are cookies?
          </h4>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            Cookies are small text files placed on your device when you visit a website.
            They help the website function correctly and can also be used to remember your
            preferences and understand how visitors use the site.
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            2. How we use cookies
          </h4>
          <p class="text-muted mb-2" style="font-size:0.9rem;">
            <?= SITE_NAME ?> uses cookies for:
          </p>
          <ul class="text-muted mb-4" style="font-size:0.9rem; line-height:1.9;">
            <li><strong>Essential cookies</strong> — required for core site functionality, such as session management and CSRF protection on forms</li>
            <li><strong>Analytics cookies</strong> — help us understand how visitors use our website so we can improve it (only where enabled)</li>
          </ul>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            3. Managing cookies
          </h4>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            Most web browsers allow you to control cookies through their settings. You can
            usually find these settings in the "options" or "preferences" menu of your browser.
            Please note that disabling essential cookies may affect the functionality of this
            website.
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            4. More information
          </h4>
          <p class="text-muted mb-0" style="font-size:0.9rem; line-height:1.8;">
            For more information about how we handle your personal data, please see our
            <a href="<?= BASE_URL ?>/privacy.php">Privacy Policy</a>. If you have any questions,
            contact us at <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>.
          </p>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
