<?php
require_once __DIR__ . '/includes/database.php';
$pageTitle = 'Privacy Policy';
$metaDesc  = SITE_NAME . ' privacy policy — how we collect, use, and protect your personal data in accordance with UK GDPR.';
include __DIR__ . '/includes/header.php';
?>

<div class="page-banner" style="padding:3rem 0 4.5rem;">
  <div class="container banner-content">
    <div class="crumb-pill">
      <a href="<?= BASE_URL ?>/index.php">Home</a> <span class="sep">/</span> <span>Privacy Policy</span>
    </div>
    <h1 style="font-size:1.9rem;">Privacy Policy</h1>
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

          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            <?= SITE_NAME ?> (&ldquo;we&rdquo;, &ldquo;us&rdquo;, &ldquo;our&rdquo;) is committed
            to protecting and respecting your privacy. This policy sets out how we collect, use,
            and protect any personal information you give us when you use this website or our
            services. We are registered with the Information Commissioner&rsquo;s Office (ICO).
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            1. Who we are
          </h4>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            <?= SITE_NAME ?> is a Community Interest Company providing supported housing,
            registered in the United Kingdom. Our registered address is <?= SITE_ADDRESS ?>.
            You can contact our data controller at <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>.
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            2. What information we collect
          </h4>
          <p class="text-muted mb-2" style="font-size:0.9rem;">We may collect the following:</p>
          <ul class="text-muted mb-4" style="font-size:0.9rem; line-height:1.9;">
            <li>Your name, email address, and phone number when you contact us or submit a form</li>
            <li>Information relating to a referral, including assessed housing need</li>
            <li>Technical data such as IP address, browser type, and pages visited</li>
          </ul>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            3. How we use your information
          </h4>
          <ul class="text-muted mb-4" style="font-size:0.9rem; line-height:1.9;">
            <li>To respond to your enquiries and process referrals</li>
            <li>To send confirmation emails when you submit a form</li>
            <li>To liaise with local authorities, commissioners, and regulated care partners</li>
            <li>To comply with our legal and regulatory obligations as a housing provider</li>
            <li>To improve our website and services</li>
          </ul>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            4. Legal basis for processing
          </h4>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            We process your personal data under the following lawful bases under UK GDPR:
            <strong>Legitimate interests</strong> (responding to enquiries),
            <strong>Contractual necessity</strong> (managing a tenancy),
            <strong>Legal obligation</strong> (regulatory compliance), and
            <strong>Consent</strong> (where you have explicitly given it).
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            5. Who we share your data with
          </h4>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            We do not sell your personal data. We may share data with: trusted IT service
            providers who host our website and email (under strict data processing agreements),
            regulatory bodies where legally required, and our registered care partners involved
            in delivering a resident's support.
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            6. How long we keep your data
          </h4>
          <ul class="text-muted mb-4" style="font-size:0.9rem; line-height:1.9;">
            <li>Enquiry and contact form data: up to 2 years</li>
            <li>Referral and tenancy records: in accordance with regulatory requirements</li>
          </ul>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            7. Your rights
          </h4>
          <p class="text-muted mb-2" style="font-size:0.9rem;">Under UK GDPR you have the right to:</p>
          <ul class="text-muted mb-4" style="font-size:0.9rem; line-height:1.9;">
            <li>Access the personal data we hold about you</li>
            <li>Request correction of inaccurate data</li>
            <li>Request deletion of your data (&ldquo;right to be forgotten&rdquo;)</li>
            <li>Object to or restrict how we process your data</li>
            <li>Lodge a complaint with the ICO at <a href="https://ico.org.uk" target="_blank" rel="noopener">ico.org.uk</a></li>
          </ul>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            To exercise any of these rights, please contact us at
            <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>.
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            8. Cookies
          </h4>
          <p class="text-muted mb-4" style="font-size:0.9rem; line-height:1.8;">
            We use essential cookies to make our website work. For full details please see our
            <a href="<?= BASE_URL ?>/cookies.php">Cookie Policy</a>.
          </p>

          <h4 class="fw-bold mb-3" style="font-size:1rem; color:var(--hzc-primary);">
            9. Changes to this policy
          </h4>
          <p class="text-muted mb-0" style="font-size:0.9rem; line-height:1.8;">
            We may update this policy from time to time. The date at the top of this page
            reflects the most recent revision. Continued use of our website after any changes
            constitutes acceptance of the updated policy.
          </p>

        </div>

        <div class="text-center mt-4">
          <a href="<?= BASE_URL ?>/contact.php" class="btn btn-hzc-primary px-4">
            Contact Us With Any Questions
          </a>
        </div>

      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
