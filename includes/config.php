<?php
// includes/config.php — central configuration loader
// Prefer a server-local override file for FTP deployments, then fall back to env vars.

if (file_exists(__DIR__ . '/config.local.php')) {
	require_once __DIR__ . '/config.local.php';
}

if (!defined('DB_HOST')) define('DB_HOST', getenv('HZC_DB_HOST') ?: '');
if (!defined('DB_NAME')) define('DB_NAME', getenv('HZC_DB_NAME') ?: '');
if (!defined('DB_USER')) define('DB_USER', getenv('HZC_DB_USER') ?: '');
if (!defined('DB_PASS')) define('DB_PASS', getenv('HZC_DB_PASS') ?: '');

if (!defined('BASE_URL')) define('BASE_URL', getenv('HZC_BASE_URL') ?: '');
if (!defined('SITE_NAME')) define('SITE_NAME', 'Habitationz CIC');
// TODO: replace these placeholders with your real contact details (or set them in config.local.php)
if (!defined('SITE_EMAIL')) define('SITE_EMAIL', 'info@habitationzcic.co.uk');
if (!defined('SITE_PHONE')) define('SITE_PHONE', '+44 0000 000000');
if (!defined('SITE_WHATSAPP')) define('SITE_WHATSAPP', 'https://wa.me/440000000000');
if (!defined('SITE_ADDRESS')) define('SITE_ADDRESS', 'Your registered office address, United Kingdom');

if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', __DIR__ . '/../uploads/');
if (!defined('UPLOAD_URL')) define('UPLOAD_URL', BASE_URL . '/uploads/');

// Brand colours
if (!defined('SITE_COLOUR_PRIMARY')) define('SITE_COLOUR_PRIMARY', '#2F5C5F');   // teal
if (!defined('SITE_COLOUR_ACCENT')) define('SITE_COLOUR_ACCENT',  '#DFC72B');   // gold
if (!defined('SITE_COLOUR_DARK')) define('SITE_COLOUR_DARK',    '#2E4C4E');   // dark teal (footer/sidebar)
if (!defined('SITE_COLOUR_SECONDARY')) define('SITE_COLOUR_SECONDARY', '#5C8F92'); // lighter teal
if (!defined('SITE_COLOUR_LIGHT')) define('SITE_COLOUR_LIGHT', '#E4EFEF');   // pale teal background

error_reporting(E_ALL);
ini_set('display_errors', '0'); // set to '1' only during local debugging
date_default_timezone_set('Europe/London');
