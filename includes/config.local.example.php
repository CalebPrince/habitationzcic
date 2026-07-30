<?php
// includes/config.local.php — server-local secrets and overrides.
// Copy this file to config.local.php and fill in your real values.
// config.local.php is gitignored and must NEVER be committed.
// It is loaded by config.php before any defaults are applied,
// so anything defined here wins.

// ── Database ──────────────────────────────────────────────────
define('DB_HOST', '');          // e.g. db5xxxxxxxxx.hosting-data.io
define('DB_NAME', '');          // e.g. dbsXXXXXXXX
define('DB_USER', '');          // your DB username
define('DB_PASS', '');          // your DB password

// ── Site identity & contact details ──────────────────────────
// define('SITE_NAME', 'Habitationz CIC');
// define('SITE_EMAIL', 'info@habitationzcic.co.uk');
// define('SITE_PHONE', '+44 0000 000000');
// define('SITE_WHATSAPP', 'https://wa.me/440000000000');
// define('SITE_ADDRESS', 'Your registered office address, United Kingdom');

// ── Email (SMTP) ──────────────────────────────────────────────
define('SMTP_PASS', '');        // password for the SITE_EMAIL mailbox

// ── Optional overrides ────────────────────────────────────────
// define('BASE_URL', '/habitationzcic');   // if running in a subfolder
// define('SMTP_HOST', 'smtp.gmail.com');
// define('SMTP_PORT', 587);
