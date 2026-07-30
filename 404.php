<?php
// 404.php — custom not found page
// For Apache: add "ErrorDocument 404 /404.php" to your .htaccess
http_response_code(404);
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Page Not Found';
include __DIR__ . '/includes/header.php';
?>

<section style="min-height:60vh; display:flex; align-items:center;">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-6 text-center">

        <div style="width:140px; height:140px; margin:0 auto 1.5rem; border-radius:50%;
                    background:var(--hzc-light-alt); display:flex; align-items:center; justify-content:center;
                    font-family:'Nunito Sans',sans-serif; font-size:2.6rem; font-weight:800;
                    color:var(--hzc-primary);">
          404
        </div>

        <h1 class="fw-bold mb-3" style="font-size:1.6rem;">
          Page not found
        </h1>
        <p class="text-muted mb-5" style="font-size:0.95rem; max-width:420px; margin:0 auto 2rem;">
          Sorry, we couldn't find the page you were looking for. It may have been moved,
          deleted, or the link might be incorrect.
        </p>

        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="<?= BASE_URL ?>/index.php" class="btn btn-hzc-primary">
            <i class="bi bi-house me-2"></i>Back to Home
          </a>
          <a href="<?= BASE_URL ?>/contact.php" class="btn btn-hzc-accent">
            Contact Us
          </a>
        </div>

        <div class="mt-5 pt-4" style="border-top:1.5px solid var(--hzc-border);">
          <p class="text-muted mb-3" style="font-size:0.85rem;">
            Or try one of these pages:
          </p>
          <div class="d-flex gap-3 justify-content-center flex-wrap" style="font-size:0.88rem;">
            <a href="<?= BASE_URL ?>/services.php" style="color:var(--hzc-accent-dark); text-decoration:none;">
              <i class="bi bi-house-heart me-1"></i>Services
            </a>
            <a href="<?= BASE_URL ?>/about.php" style="color:var(--hzc-accent-dark); text-decoration:none;">
              <i class="bi bi-info-circle me-1"></i>About Us
            </a>
            <a href="<?= BASE_URL ?>/working-together.php" style="color:var(--hzc-accent-dark); text-decoration:none;">
              <i class="bi bi-diagram-3 me-1"></i>Working Together
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
