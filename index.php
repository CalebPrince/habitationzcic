<?php
require_once __DIR__ . '/includes/database.php';
$pageTitle = 'Home';
$services  = Database::fetchAll("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC");
$faqs      = Database::fetchAll("SELECT * FROM faqs WHERE is_active = 1 ORDER BY sort_order ASC");

$rawBlocks = Database::fetchAll(
    "SELECT section_key, content FROM page_content WHERE page_slug = 'home'"
);
$pc = array_column($rawBlocks, 'content', 'section_key');
$b = fn(string $key, string $fallback = '') => htmlspecialchars($pc[$key] ?? $fallback);

$pillarTones = ['teal', 'gold'];
$pillarIcons = ['bi-house-door', 'bi-clipboard-check', 'bi-people', 'bi-diagram-3', 'bi-graph-up-arrow'];

include __DIR__ . '/includes/header.php';
?>

<!-- ── Hero ──────────────────────────────────────────────── -->
<section class="hero-warm">
  <div class="container">
    <div class="row align-items-center g-5 pb-5">
      <div class="col-lg-6" style="position:relative; z-index:2;">
        <span class="section-label"><?= $b('hero_badge', 'Supported housing across the UK') ?></span>
        <h1 class="display-5 mb-3" style="line-height:1.2;">
          <?= $b('hero_title', 'Safe housing. Real independence.') ?>
        </h1>
        <p class="lead mb-4" style="color:var(--hzc-muted);">
          <?= $b('hero_body', SITE_NAME . ' provides safe, high-quality supported housing, helping residents build independence, inclusion, and lasting community wellbeing — in partnership with regulated care providers.') ?>
        </p>
        <div class="d-flex flex-wrap gap-3">
          <a href="<?= BASE_URL ?>/services.php" class="btn btn-hzc-primary">
            <?= $b('hero_cta_primary', 'Our Services') ?> <i class="bi bi-arrow-right-short ms-1"></i>
          </a>
          <a href="<?= BASE_URL ?>/contact.php" class="btn btn-hzc-accent">
            <i class="bi bi-telephone me-1"></i><?= $b('hero_cta_secondary', 'Make a Referral') ?>
          </a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="hero-photo-frame">
          <img src="<?= BASE_URL ?>/assets/img/hero-1.webp" alt="A resident's supported housing home">
        </div>
        <div class="hero-caption-chip">
          <div class="chip-icon"><i class="bi bi-house-heart"></i></div>
          <div class="chip-text">Accommodation only —<br>care delivered by partners</div>
        </div>
      </div>
    </div>
  </div>
  <svg class="wave-divider" viewBox="0 0 1440 74" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M0,32 C240,74 480,0 720,18 C960,36 1200,74 1440,32 L1440,74 L0,74 Z" fill="#FBF8F1"></path>
  </svg>
</section>

<!-- ── How We Serve You — card grid ─────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label"><?= $b('how_label', 'How We Serve You') ?></span>
      <h2 class="fw-bold mb-0" style="font-size:2rem;">
        <?= $b('how_title', 'Housing that supports independence') ?>
      </h2>
      <p class="text-muted mt-3 mx-auto" style="max-width:560px;">
        <?= $b('how_body', 'We provide safe, high-quality accommodation and work alongside regulated care providers, local authorities, and commissioners to help residents thrive.') ?>
      </p>
    </div>

    <div class="row g-4">
      <?php foreach ($services as $i => $s):
        $tone = $pillarTones[$i % 2];
        $icon = $s['icon'] ?: ($pillarIcons[$i % count($pillarIcons)]);
      ?>
      <div class="col-md-6 col-lg-4">
        <div class="mini-card h-100">
          <div class="mini-card-icon tone-<?= $tone ?>">
            <i class="bi <?= htmlspecialchars($icon) ?>"></i>
          </div>
          <h5 class="fw-bold mb-2" style="font-size:1rem;"><?= htmlspecialchars($s['title']) ?></h5>
          <p class="text-muted mb-0" style="font-size:0.88rem;"><?= htmlspecialchars($s['description']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
      <a href="<?= BASE_URL ?>/services.php" class="btn btn-hzc-primary">
        View All Services
      </a>
    </div>
  </div>
</section>

<!-- ── Our Approach — value grid ─────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-5">
        <span class="section-label"><?= $b('approach_label', 'Our Approach') ?></span>
        <h2 class="fw-bold mb-3"><?= $b('approach_title', 'Accommodation only — care by our partners') ?></h2>
        <p class="text-muted mb-4">
          <?= $b('approach_body', SITE_NAME . ' provides accommodation only. All regulated care and support are delivered by regulated, registered partner organisations — keeping housing and care clearly separated, and both delivered to the highest standard.') ?>
        </p>
        <a href="<?= BASE_URL ?>/working-together.php" class="btn btn-hzc-primary">
          Working Together <i class="bi bi-arrow-right-short ms-1"></i>
        </a>
      </div>
      <div class="col-lg-7">
        <div class="value-grid-wrap">
          <div class="row g-3">
            <?php
            $values = [
              ['bi-shield-check', 'Safe, high-quality housing meeting statutory standards'],
              ['bi-people', 'Partnerships with CQC-registered care providers only'],
              ['bi-wallet2', 'Tenancy support, budgeting help, and advocacy'],
              ['bi-arrow-repeat', 'Surpluses reinvested into homes and communities'],
              ['bi-geo-alt', 'Coverage across the UK'],
              ['bi-heart', 'Independence and dignity at the centre'],
            ];
            foreach ($values as $v): [$icon, $text] = $v; ?>
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

<!-- ── FAQ ─────────────────────────────────────────────────── -->
<?php if (!empty($faqs)): ?>
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label">Frequently Asked Questions</span>
      <h2 class="fw-bold mb-0">Common questions, answered</h2>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="accordion faq-accordion" id="faqAccordion">
          <?php foreach ($faqs as $i => $f): ?>
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button <?= $i === 0 ? '' : 'collapsed' ?>" type="button"
                      data-bs-toggle="collapse" data-bs-target="#faq<?= $f['id'] ?>"
                      aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
                <?= htmlspecialchars($f['question']) ?>
              </button>
            </h2>
            <div id="faq<?= $f['id'] ?>"
                 class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>"
                 data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <?= nl2br(htmlspecialchars($f['answer'])) ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── CTA band ────────────────────────────────────────────── -->
<section class="py-5">
  <div class="container">
    <div class="cta-organic">
      <div class="row g-4">
        <div class="col-md-6 cta-col">
          <div class="cta-icon"><i class="bi bi-diagram-3"></i></div>
          <h4 class="fw-bold mb-2">Refer a client</h4>
          <p class="mb-4">Local authorities and commissioners can refer individuals with an assessed housing need and an existing care package.</p>
          <a href="<?= BASE_URL ?>/working-together.php" class="btn btn-hzc-accent">
            Working Together
          </a>
        </div>
        <div class="col-md-6 cta-col cta-divider ps-md-5">
          <div class="cta-icon"><i class="bi bi-chat-heart"></i></div>
          <h4 class="fw-bold mb-2">Have a question?</h4>
          <p class="mb-4">Get in touch today and our team will be happy to help with any query about our housing and services.</p>
          <a href="<?= BASE_URL ?>/contact.php" class="btn btn-hzc-accent">
            Contact Us
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
