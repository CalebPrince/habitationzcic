<?php
// contact.php — public contact page with enquiry type
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/mailer.php';
$pageTitle = 'Contact';
$success   = false;
$error     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::csrfCheck($_POST['csrf'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } else {
        $name    = trim($_POST['full_name'] ?? '');
        $email   = trim($_POST['email']     ?? '');
        $phone   = trim($_POST['phone']     ?? '');
        $subject = trim($_POST['subject']   ?? '');
        $message = trim($_POST['message']   ?? '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '' || $subject === '') {
            $error = 'Please fill in your name, email, enquiry type, and message.';
        } else {
            Database::query(
                "INSERT INTO enquiries (full_name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)",
                [$name, $email, $phone, $subject, $message]
            );

            $adminContent  = emailRow('Enquiry type', htmlspecialchars($subject));
            $adminContent .= emailRow('Name', htmlspecialchars($name));
            $adminContent .= emailRow('Email', htmlspecialchars($email));
            $adminContent .= emailRow('Phone', htmlspecialchars($phone ?: 'Not provided'));
            $adminContent .= emailMessage('Message', $message);
            $adminContent .= '
            <tr><td style="padding:0 32px 32px;">
              <a href="' . rtrim(BASE_URL, '/') . '/admin/enquiries.php"
                 style="display:inline-block;background:' . SITE_COLOUR_ACCENT . ';color:' . SITE_COLOUR_DARK . ';
                        text-decoration:none;padding:12px 24px;border-radius:8px;
                        font-weight:700;font-size:14px;">
                Review in Admin Panel →
              </a>
            </td></tr>';

            sendMail(
                ADMIN_EMAIL,
                SITE_NAME,
                'New Enquiry: ' . $subject . ' — ' . $name,
                emailTemplate('New enquiry received', $adminContent)
            );

            $replyContent  = '
            <tr><td style="padding:24px 32px 8px;">
              <p style="margin:0;font-size:15px;color:#223838;line-height:1.7;">
                Hi ' . htmlspecialchars($name) . ',
              </p>
              <p style="margin:12px 0 0;font-size:15px;color:#223838;line-height:1.7;">
                Thank you for contacting ' . SITE_NAME . '. We have received your enquiry and will
                get back to you within one business day.
              </p>
            </td></tr>';
            $replyContent .= emailRow('Enquiry type', htmlspecialchars($subject));
            $replyContent .= emailMessage('Your message', $message);

            sendMail(
                $email,
                $name,
                'We received your enquiry — ' . SITE_NAME,
                emailTemplate('Thanks for getting in touch', $replyContent)
            );

            $success = true;
        }
    }
}

include __DIR__ . '/includes/header.php';

$subjects = [
    'General enquiry',
    'Referral from a local authority',
    'Referral from a care provider',
    'Partnership / commissioning enquiry',
    'Existing resident',
    'Other',
];
$csrf = Auth::csrfToken();
?>

<!-- ── Page banner ────────────────────────────────────────── -->
<div class="page-banner">
  <div class="container banner-content">
    <div class="crumb-pill">
      <a href="<?= BASE_URL ?>/index.php">Home</a> <span class="sep">/</span> <span>Contact</span>
    </div>
    <h1>Get in Touch</h1>
    <p>We typically respond within one business day</p>
  </div>
  <svg class="page-banner-wave" viewBox="0 0 1440 74" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M0,32 C240,74 480,0 720,18 C960,36 1200,74 1440,32 L1440,74 L0,74 Z" fill="#FBF8F1"></path>
  </svg>
</div>

<section class="py-5">
  <div class="container">
    <div class="row g-5">

      <!-- Contact info -->
      <div class="col-lg-4">
        <div class="contact-info-card mb-4">
          <h5 class="fw-bold mb-4">Contact details</h5>
          <div class="ci-row">
            <div class="ci-icon"><i class="bi bi-geo-alt-fill"></i></div>
            <span><?= SITE_ADDRESS ?></span>
          </div>
          <div class="ci-row">
            <div class="ci-icon"><i class="bi bi-telephone-fill"></i></div>
            <a href="tel:<?= preg_replace('/\s+/', '', SITE_PHONE) ?>"
               style="color:rgba(255,255,255,0.85); text-decoration:none;">
              <?= SITE_PHONE ?>
            </a>
          </div>
          <div class="ci-row">
            <div class="ci-icon"><i class="bi bi-envelope-fill"></i></div>
            <a href="mailto:<?= SITE_EMAIL ?>"
               style="color:rgba(255,255,255,0.85); text-decoration:none;">
              <?= SITE_EMAIL ?>
            </a>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="col-lg-8">
        <?php if ($success): ?>
        <div class="text-center py-5 panel-card">
          <div style="width:64px; height:64px; border-radius:50%;
                      background:rgba(25,135,84,0.1); color:#198754; font-size:1.8rem;
                      display:flex; align-items:center; justify-content:center;
                      margin:0 auto 1.25rem;">
            <i class="bi bi-check-circle-fill"></i>
          </div>
          <h3 class="fw-bold mb-2">Message sent</h3>
          <p class="text-muted mb-4">
            Thanks for getting in touch — we'll reply within one business day.
          </p>
          <a href="<?= BASE_URL ?>/index.php" class="btn btn-hzc-primary">
            Back to Home
          </a>
        </div>

        <?php else: ?>
        <div class="panel-card">
          <h5 class="mb-4">
            <i class="bi bi-envelope me-2" style="color:var(--hzc-accent-dark);"></i>
            Send us a message
          </h5>

          <?php if ($error): ?>
          <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
          </div>
          <?php endif; ?>

          <form method="post" novalidate>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
            <div class="row g-3">

              <!-- Enquiry type -->
              <div class="col-12">
                <label for="subject" class="form-label">
                  Enquiry type <span style="color:#dc3545;">*</span>
                </label>
                <select id="subject" name="subject" class="form-select" required>
                  <option value="" disabled <?= empty($_POST['subject']) ? 'selected' : '' ?>>
                    Select what your enquiry is about…
                  </option>
                  <?php foreach ($subjects as $s): ?>
                  <option value="<?= htmlspecialchars($s) ?>"
                    <?= ($_POST['subject'] ?? '') === $s ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label for="full_name" class="form-label">
                  Full name <span style="color:#dc3545;">*</span>
                </label>
                <input type="text" id="full_name" name="full_name" class="form-control"
                       required placeholder="Jane Smith"
                       value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">
                  Email address <span style="color:#dc3545;">*</span>
                </label>
                <input type="email" id="email" name="email" class="form-control"
                       required placeholder="jane@example.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label for="phone" class="form-label">
                  Phone number
                  <span style="font-weight:400; text-transform:none;
                               font-size:0.78rem; color:var(--hzc-muted);">(optional)</span>
                </label>
                <input type="tel" id="phone" name="phone" class="form-control"
                       placeholder="07xxx xxxxxx"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label for="message" class="form-label">
                  Message <span style="color:#dc3545;">*</span>
                </label>
                <textarea id="message" name="message" rows="5" class="form-control"
                          required placeholder="Tell us how we can help…"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
              </div>
              <div class="col-12 pt-1">
                <button type="submit" class="btn btn-hzc-primary px-4">
                  Send Message <i class="bi bi-arrow-right-short ms-1"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
