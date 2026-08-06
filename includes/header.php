<?php require_once __DIR__ . '/config.php'; ?>
<?php
// ── Active nav detection ──────────────────────────────────────
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// ── SEO meta defaults (pages override $metaDesc before including header) ──
$metaDesc = $metaDesc ?? match($currentPage) {
    'index'             => 'Habitationz CIC provides safe, supported housing across the UK, partnering with CQC-registered care providers to help residents live independently.',
    'about'             => 'Learn about Habitationz CIC — our mission, values, and commitment to safe, high-quality supported housing for vulnerable individuals.',
    'services'          => 'Explore Habitationz CIC\'s services: accommodation, tenancy support, care partnerships, community inclusion, and social value.',
    'working-together'  => 'How Habitationz CIC works with local authorities, commissioners, and CQC-registered care providers to deliver supported housing.',
    'contact'           => 'Get in touch with Habitationz CIC. Contact us by phone or email for referrals, partnership enquiries, or general questions.',
    default             => 'Habitationz CIC — safe, supported housing across the UK, delivered in partnership with regulated care providers.',
};

// ── OG image (pages can override $ogImage) ───────────────────
$ogImage = $ogImage ?? BASE_URL . '/assets/img/logo.svg';
$ogTitle = isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . SITE_NAME : SITE_NAME;
$pageUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'habitationzcic.co.uk') . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- ── Primary SEO ── -->
<title><?= $ogTitle ?></title>
<meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?= htmlspecialchars($pageUrl) ?>">

<!-- ── Open Graph (Facebook, WhatsApp, LinkedIn) ── -->
<meta property="og:type"        content="website">
<meta property="og:url"         content="<?= htmlspecialchars($pageUrl) ?>">
<meta property="og:title"       content="<?= $ogTitle ?>">
<meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>">
<meta property="og:image"       content="<?= htmlspecialchars($ogImage) ?>">
<meta property="og:site_name"   content="<?= SITE_NAME ?>">
<meta property="og:locale"      content="en_GB">

<!-- ── Twitter Card ── -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="<?= $ogTitle ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($metaDesc) ?>">
<meta name="twitter:image"       content="<?= htmlspecialchars($ogImage) ?>">

<!-- ── Favicon ── -->
<link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/img/favicon.svg">
<link rel="shortcut icon" href="<?= BASE_URL ?>/assets/img/favicon.svg">

<!-- ── Fonts & CSS ── -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,400;6..12,600;6..12,700;6..12,800;6..12,900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">
      <img src="<?= BASE_URL ?>/assets/img/logo.svg" alt="<?= SITE_NAME ?>" style="height:42px;">
    </a>
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <?php
        $navLinks = [
          'index'             => ['Home',              BASE_URL . '/index.php'],
          'about'             => ['About Us',           BASE_URL . '/about.php'],
          'services'          => ['Our Services',       BASE_URL . '/services.php'],
          'working-together'  => ['Working Together',   BASE_URL . '/working-together.php'],
          'contact'           => ['Contact Us',         BASE_URL . '/contact.php'],
        ];
        foreach ($navLinks as $page => [$label, $href]):
          $isActive = $currentPage === $page;
        ?>
        <li class="nav-item">
          <a class="nav-link <?= $isActive ? 'active' : '' ?>"
             href="<?= $href ?>"
             <?= $isActive ? 'aria-current="page"' : '' ?>>
            <?= $label ?>
          </a>
        </li>
        <?php endforeach; ?>
        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
          <a class="btn btn-hzc-accent" href="<?= BASE_URL ?>/contact.php">
            Make a Referral
          </a>
        </li>
        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
          <a class="nav-link px-2" href="<?= BASE_URL ?>/admin/login.php" title="Admin Login" aria-label="Admin Login">
            <i class="bi bi-person-circle fs-5"></i>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
