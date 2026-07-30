<?php
require_once __DIR__ . '/../includes/session.php';
Auth::require(['admin', 'editor']);
$pageTitle = 'Page Content';
$activeNav = 'content';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::csrfCheck($_POST['csrf'] ?? null)) {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_page_content') {
        $slug = $_POST['page_slug'] ?? '';
        if (in_array($slug, ['home', 'about', 'working-together'], true)) {
            foreach ($_POST['blocks'] as $key => $value) {
                Database::query(
                    "INSERT INTO page_content (page_slug, section_key, content)
                     VALUES (?, ?, ?)
                     ON DUPLICATE KEY UPDATE content = VALUES(content), updated_at = NOW()",
                    [$slug, $key, trim($value)]
                );
            }
            $msg = ucwords(str_replace('-', ' ', $slug)) . ' page content saved.';
        }
    }
}

function pageBlocks(string $slug): array {
    $rows = Database::fetchAll(
        "SELECT section_key, content FROM page_content WHERE page_slug = ?",
        [$slug]
    );
    return array_column($rows, 'content', 'section_key');
}

$homeBlocks = pageBlocks('home');
$aboutBlocks = pageBlocks('about');
$wtBlocks = pageBlocks('working-together');

$activeTab = in_array($_GET['tab'] ?? '', ['home', 'about', 'working-together'])
           ? $_GET['tab']
           : 'home';

include __DIR__ . '/_layout_top.php';
?>

<div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
  <div>
    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase;
                letter-spacing:0.07em; color:var(--hzc-accent-dark); margin-bottom:0.2rem;">
      Editable site content
    </div>
    <h2 class="fw-bold mb-0">Page Content</h2>
  </div>
</div>

<?php if ($msg): ?>
<div class="alert alert-success mb-4">
  <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>

<!-- ── Tabs ──────────────────────────────────────────────────── -->
<ul class="nav nav-tabs mb-4" style="border-bottom:2px solid var(--hzc-border);">
  <?php
  $tabs = [
    'home'              => ['bi-house',       'Homepage'],
    'about'             => ['bi-info-circle', 'About Page'],
    'working-together'  => ['bi-diagram-3',   'Working Together'],
  ];
  foreach ($tabs as $tabKey => [$icon, $label]):
  ?>
  <li class="nav-item">
    <a href="?tab=<?= $tabKey ?>"
       class="nav-link <?= $activeTab === $tabKey ? 'active fw-semibold' : '' ?>"
       style="font-size:0.85rem;">
      <i class="bi <?= $icon ?> me-1"></i><?= $label ?>
    </a>
  </li>
  <?php endforeach; ?>
</ul>

<!-- ════════════════════════════════════════════════════════════
     TAB: HOMEPAGE
═════════════════════════════════════════════════════════════ -->
<?php if ($activeTab === 'home'): ?>

<?php $h = fn(string $key, string $fallback = '') => htmlspecialchars($homeBlocks[$key] ?? $fallback); ?>

<form method="post">
  <input type="hidden" name="csrf"      value="<?= Auth::csrfToken() ?>">
  <input type="hidden" name="action"    value="save_page_content">
  <input type="hidden" name="page_slug" value="home">

  <div class="admin-card mb-4">
    <div class="admin-card-header mb-3">
      <h5><i class="bi bi-stars me-2" style="color:var(--hzc-accent-dark);"></i>Hero Section</h5>
      <span class="status-badge status-active">Appears first on homepage</span>
    </div>
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label">Badge text</label>
        <input class="form-control" name="blocks[hero_badge]"
               value="<?= $h('hero_badge', 'Supported housing across the UK') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Hero headline</label>
        <input class="form-control" name="blocks[hero_title]"
               value="<?= $h('hero_title', 'Safe housing. Real independence.') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Hero body text</label>
        <textarea class="form-control" rows="4" name="blocks[hero_body]"><?= $h('hero_body') ?></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label">Primary CTA button label</label>
        <input class="form-control" name="blocks[hero_cta_primary]"
               value="<?= $h('hero_cta_primary', 'Our Services') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Secondary CTA button label</label>
        <input class="form-control" name="blocks[hero_cta_secondary]"
               value="<?= $h('hero_cta_secondary', 'Make a Referral') ?>">
      </div>
    </div>
  </div>

  <div class="admin-card mb-4">
    <div class="admin-card-header mb-3">
      <h5><i class="bi bi-house-heart me-2" style="color:var(--hzc-accent-dark);"></i>How We Serve You Section</h5>
    </div>
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Section label</label>
        <input class="form-control" name="blocks[how_label]"
               value="<?= $h('how_label', 'How We Serve You') ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Section headline</label>
        <input class="form-control" name="blocks[how_title]"
               value="<?= $h('how_title', 'Housing that supports independence') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Section body text</label>
        <textarea class="form-control" rows="3" name="blocks[how_body]"><?= $h('how_body') ?></textarea>
      </div>
    </div>
  </div>

  <div class="admin-card mb-4">
    <div class="admin-card-header mb-3">
      <h5><i class="bi bi-heart me-2" style="color:var(--hzc-accent-dark);"></i>Our Approach Section</h5>
    </div>
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Section label</label>
        <input class="form-control" name="blocks[approach_label]"
               value="<?= $h('approach_label', 'Our Approach') ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Section headline</label>
        <input class="form-control" name="blocks[approach_title]"
               value="<?= $h('approach_title', 'Accommodation only — care by our partners') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Body text</label>
        <textarea class="form-control" rows="3" name="blocks[approach_body]"><?= $h('approach_body') ?></textarea>
      </div>
    </div>
  </div>

  <button class="btn btn-hzc-primary px-4">
    <i class="bi bi-floppy me-2"></i>Save Homepage Content
  </button>
</form>

<!-- ════════════════════════════════════════════════════════════
     TAB: ABOUT PAGE
═════════════════════════════════════════════════════════════ -->
<?php elseif ($activeTab === 'about'): ?>

<?php $a = fn(string $key, string $fallback = '') => htmlspecialchars($aboutBlocks[$key] ?? $fallback); ?>

<form method="post">
  <input type="hidden" name="csrf"      value="<?= Auth::csrfToken() ?>">
  <input type="hidden" name="action"    value="save_page_content">
  <input type="hidden" name="page_slug" value="about">

  <div class="admin-card mb-4">
    <div class="admin-card-header mb-3">
      <h5><i class="bi bi-layout-text-window me-2" style="color:var(--hzc-accent-dark);"></i>Page Header</h5>
    </div>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Page title (H1)</label>
        <input class="form-control" name="blocks[header_title]"
               value="<?= $a('header_title', 'About ' . SITE_NAME) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Subtitle</label>
        <input class="form-control" name="blocks[header_sub]"
               value="<?= $a('header_sub', 'Safe, supported housing built around independence, inclusion, and community') ?>">
      </div>
    </div>
  </div>

  <div class="admin-card mb-4">
    <div class="admin-card-header mb-3">
      <h5><i class="bi bi-people me-2" style="color:var(--hzc-accent-dark);"></i>Who We Are Section</h5>
    </div>
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Section label</label>
        <input class="form-control" name="blocks[who_label]"
               value="<?= $a('who_label', 'Who We Are') ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Section headline</label>
        <input class="form-control" name="blocks[who_title]"
               value="<?= $a('who_title', 'More than just a landlord') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Paragraph 1</label>
        <textarea class="form-control" rows="3" name="blocks[who_body_1]"><?= $a('who_body_1') ?></textarea>
      </div>
      <div class="col-12">
        <label class="form-label">Paragraph 2</label>
        <textarea class="form-control" rows="3" name="blocks[who_body_2]"><?= $a('who_body_2') ?></textarea>
      </div>
      <div class="col-12">
        <label class="form-label">Paragraph 3</label>
        <textarea class="form-control" rows="3" name="blocks[who_body_3]"><?= $a('who_body_3') ?></textarea>
      </div>
    </div>
  </div>

  <div class="admin-card mb-4">
    <div class="admin-card-header mb-3">
      <h5><i class="bi bi-bullseye me-2" style="color:var(--hzc-accent-dark);"></i>Vision &amp; Mission</h5>
    </div>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Vision title</label>
        <input class="form-control" name="blocks[vision_title]"
               value="<?= $a('vision_title', 'Our Vision') ?>">
        <label class="form-label mt-2">Vision body</label>
        <textarea class="form-control" rows="4" name="blocks[vision_body]"><?= $a('vision_body') ?></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label">Mission title</label>
        <input class="form-control" name="blocks[mission_title]"
               value="<?= $a('mission_title', 'Our Mission') ?>">
        <label class="form-label mt-2">Mission body</label>
        <textarea class="form-control" rows="4" name="blocks[mission_body]"><?= $a('mission_body') ?></textarea>
      </div>
    </div>
  </div>

  <div class="admin-card mb-4">
    <div class="admin-card-header mb-3">
      <h5><i class="bi bi-building me-2" style="color:var(--hzc-accent-dark);"></i>C.I.C. / Social Value Section</h5>
    </div>
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Section label</label>
        <input class="form-control" name="blocks[cic_label]"
               value="<?= $a('cic_label', 'Community Interest Company') ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Section headline</label>
        <input class="form-control" name="blocks[cic_title]"
               value="<?= $a('cic_title', 'Profit with a purpose') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Paragraph 1</label>
        <textarea class="form-control" rows="3" name="blocks[cic_body_1]"><?= $a('cic_body_1') ?></textarea>
      </div>
      <div class="col-12">
        <label class="form-label">Paragraph 2</label>
        <textarea class="form-control" rows="2" name="blocks[cic_body_2]"><?= $a('cic_body_2') ?></textarea>
      </div>
    </div>
  </div>

  <div class="admin-card mb-4">
    <div class="admin-card-header mb-3">
      <h5><i class="bi bi-megaphone me-2" style="color:var(--hzc-accent-dark);"></i>Bottom CTA</h5>
    </div>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">CTA headline</label>
        <input class="form-control" name="blocks[cta_title]"
               value="<?= $a('cta_title', 'Ready to find out more?') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">CTA subtitle</label>
        <input class="form-control" name="blocks[cta_sub]"
               value="<?= $a('cta_sub', 'Whether you\'re a local authority, a care provider, or looking to refer someone, our team is here to help.') ?>">
      </div>
    </div>
  </div>

  <button class="btn btn-hzc-primary px-4">
    <i class="bi bi-floppy me-2"></i>Save About Page Content
  </button>
</form>

<!-- ════════════════════════════════════════════════════════════
     TAB: WORKING TOGETHER
═════════════════════════════════════════════════════════════ -->
<?php elseif ($activeTab === 'working-together'): ?>

<?php $w = fn(string $key, string $fallback = '') => htmlspecialchars($wtBlocks[$key] ?? $fallback); ?>

<form method="post">
  <input type="hidden" name="csrf"      value="<?= Auth::csrfToken() ?>">
  <input type="hidden" name="action"    value="save_page_content">
  <input type="hidden" name="page_slug" value="working-together">

  <div class="admin-card mb-4">
    <div class="admin-card-header mb-3">
      <h5><i class="bi bi-layout-text-window me-2" style="color:var(--hzc-accent-dark);"></i>Page Header</h5>
    </div>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Page title (H1)</label>
        <input class="form-control" name="blocks[header_title]"
               value="<?= $w('header_title', 'Working Together') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Subtitle</label>
        <input class="form-control" name="blocks[header_sub]"
               value="<?= $w('header_sub', 'How we partner with local authorities, commissioners, and regulated care providers') ?>">
      </div>
    </div>
  </div>

  <div class="admin-card mb-4">
    <div class="admin-card-header mb-3">
      <h5><i class="bi bi-diagram-3 me-2" style="color:var(--hzc-accent-dark);"></i>Partnership Model Section</h5>
    </div>
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Section label</label>
        <input class="form-control" name="blocks[model_label]"
               value="<?= $w('model_label', 'Our Partnership Model') ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Section headline</label>
        <input class="form-control" name="blocks[model_title]"
               value="<?= $w('model_title', 'Housing and care, clearly separated') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Paragraph 1</label>
        <textarea class="form-control" rows="3" name="blocks[model_body_1]"><?= $w('model_body_1') ?></textarea>
      </div>
      <div class="col-12">
        <label class="form-label">Paragraph 2</label>
        <textarea class="form-control" rows="3" name="blocks[model_body_2]"><?= $w('model_body_2') ?></textarea>
      </div>
    </div>
  </div>

  <button class="btn btn-hzc-primary px-4">
    <i class="bi bi-floppy me-2"></i>Save Working Together Content
  </button>
</form>

<?php endif; ?>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
