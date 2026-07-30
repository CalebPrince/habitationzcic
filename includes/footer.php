<section class="py-5">
  <div class="container">
    <div class="footer-cta-card">
      <div>
        <h3 class="fw-bold mb-1">Have a question, or want to refer someone?</h3>
        <p>Our team typically replies within one business day.</p>
      </div>
      <a href="<?= BASE_URL ?>/contact.php" class="btn btn-hzc-accent flex-shrink-0">
        <i class="bi bi-arrow-right-circle me-1"></i> Get in Touch
      </a>
    </div>
  </div>
</section>

<footer class="site-footer pb-4">
  <div class="container">
    <div class="row g-4">

      <!-- Brand -->
      <div class="col-md-3">
        <img src="<?= BASE_URL ?>/assets/img/logo-white.svg" alt="<?= SITE_NAME ?>"
             style="height:40px; margin-bottom:1rem;">
        <p style="font-size:0.85rem; line-height:1.7; color:rgba(255,255,255,0.6);">
          Safe, supported housing across the UK — delivered in partnership with
          regulated care providers, with independence and dignity at the centre.
        </p>
        <div class="d-flex gap-2 mt-3">
          <a href="#" class="footer-social-btn" target="_blank" rel="noopener" aria-label="Facebook">
            <i class="bi bi-facebook"></i>
          </a>
          <a href="#" class="footer-social-btn" target="_blank" rel="noopener" aria-label="Instagram">
            <i class="bi bi-instagram"></i>
          </a>
          <a href="#" class="footer-social-btn" target="_blank" rel="noopener" aria-label="YouTube">
            <i class="bi bi-youtube"></i>
          </a>
          <a href="#" class="footer-social-btn" target="_blank" rel="noopener" aria-label="X">
            <i class="bi bi-twitter-x"></i>
          </a>
        </div>
      </div>

      <!-- Quick links -->
      <div class="col-md-3 offset-md-1">
        <h6>Quick Links</h6>
        <ul class="list-unstyled" style="font-size:0.87rem;">
          <li class="mb-2"><a href="<?= BASE_URL ?>/index.php">Home</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>/about.php">About Us</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>/services.php">Our Services</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>/working-together.php">Working Together</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>/contact.php">Contact Us</a></li>
        </ul>
      </div>

      <!-- Legal -->
      <div class="col-md-2">
        <h6>Legal</h6>
        <ul class="list-unstyled" style="font-size:0.87rem;">
          <li class="mb-2"><a href="<?= BASE_URL ?>/privacy.php">Privacy Policy</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>/terms.php">Terms of Use</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>/cookies.php">Cookie Policy</a></li>
        </ul>
      </div>

      <!-- Contact -->
      <div class="col-md-3">
        <h6>Get in Touch</h6>
        <ul class="list-unstyled" style="font-size:0.87rem;">
          <li class="mb-2" style="color:rgba(255,255,255,0.6);">
            <i class="bi bi-geo-alt me-2" style="color:var(--hzc-accent);"></i>
            <?= SITE_ADDRESS ?>
          </li>
          <li class="mb-2">
            <a href="tel:<?= preg_replace('/\s+/', '', SITE_PHONE) ?>">
              <i class="bi bi-telephone me-2" style="color:var(--hzc-accent);"></i>
              <?= SITE_PHONE ?>
            </a>
          </li>
          <li class="mb-2">
            <a href="mailto:<?= SITE_EMAIL ?>">
              <i class="bi bi-envelope me-2" style="color:var(--hzc-accent);"></i>
              <?= SITE_EMAIL ?>
            </a>
          </li>
        </ul>
      </div>

    </div>

    <hr style="border-color:rgba(255,255,255,0.14); margin:2.5rem 0 1.25rem;">

    <div class="d-flex flex-column flex-md-row justify-content-between
                align-items-center gap-2" style="font-size:0.8rem;">
      <p class="mb-0" style="color:rgba(255,255,255,0.45);">
        &copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.
      </p>
      <p class="mb-0" style="color:rgba(255,255,255,0.45);">
        <a href="<?= BASE_URL ?>/privacy.php" style="color:rgba(255,255,255,0.45);">Privacy Policy</a>
        &middot;
        <a href="<?= BASE_URL ?>/terms.php" style="color:rgba(255,255,255,0.45);">Terms of Use</a>
        &middot;
        <a href="<?= BASE_URL ?>/cookies.php" style="color:rgba(255,255,255,0.45);">Cookie Policy</a>
      </p>
    </div>

  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
