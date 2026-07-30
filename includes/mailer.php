<?php
// includes/mailer.php — central email helper using PHPMailer
// Requires PHPMailer installed via Composer or manual include
// Install: composer require phpmailer/phpmailer
// Or manually upload PHPMailer src/ folder to includes/PHPMailer/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ── Load PHPMailer ────────────────────────────────────────────
// Try Composer autoload first, fall back to manual include
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';
}

// ── SMTP Configuration ────────────────────────────────────────
// config.local.php may define these first; otherwise fall back to
// environment variables, then defaults.
if (!defined('SMTP_HOST'))      define('SMTP_HOST',      getenv('HZC_SMTP_HOST') ?: 'smtp.gmail.com');
if (!defined('SMTP_PORT'))      define('SMTP_PORT',      (int) (getenv('HZC_SMTP_PORT') ?: 587));
if (!defined('SMTP_USER'))      define('SMTP_USER',      getenv('HZC_SMTP_USER') ?: SITE_EMAIL);
if (!defined('SMTP_PASS'))      define('SMTP_PASS',      getenv('HZC_SMTP_PASS') ?: '');
if (!defined('SMTP_FROM'))      define('SMTP_FROM',      SITE_EMAIL);
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', SITE_NAME);
if (!defined('ADMIN_EMAIL'))    define('ADMIN_EMAIL',    SITE_EMAIL);   // where admin notifications go

/**
 * Send an email via PHPMailer SMTP.
 *
 * @param string $toEmail    Recipient email
 * @param string $toName     Recipient name
 * @param string $subject    Email subject
 * @param string $htmlBody   HTML body content
 * @param string $plainBody  Plain text fallback
 * @return bool              True on success, false on failure
 */
function sendMail(
    string $toEmail,
    string $toName,
    string $subject,
    string $htmlBody,
    string $plainBody = ''
): bool {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host        = SMTP_HOST;
        $mail->SMTPAuth    = true;
        $mail->Username    = SMTP_USER;
        $mail->Password    = SMTP_PASS;
        $mail->SMTPSecure  = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port        = SMTP_PORT;
        $mail->CharSet     = 'UTF-8';

        // Sender
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addReplyTo(SMTP_FROM, SMTP_FROM_NAME);

        // Recipient
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $plainBody ?: strip_tags($htmlBody);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error silently — don't expose to user
        error_log('Mailer error: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Build a branded HTML email wrapper around body content.
 *
 * @param string $title   Email heading
 * @param string $content Inner HTML content rows
 * @return string         Full HTML email
 */
function emailTemplate(string $title, string $content): string {
    $siteName = SITE_NAME;
    $accent   = SITE_COLOUR_ACCENT;
    $primary  = SITE_COLOUR_PRIMARY;
    $year     = date('Y');

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$title}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:32px 16px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0"
           style="background:#ffffff;border-radius:12px;overflow:hidden;
                  box-shadow:0 4px 24px rgba(0,0,0,0.08);max-width:600px;width:100%;">

      <!-- Header -->
      <tr>
        <td style="background:{$primary};padding:28px 32px;">
          <h1 style="margin:0;font-size:22px;font-weight:700;color:#ffffff;
                     letter-spacing:-0.3px;">{$siteName}</h1>
        </td>
      </tr>

      <!-- Accent bar -->
      <tr>
        <td style="background:{$accent};height:4px;font-size:0;line-height:0;">&nbsp;</td>
      </tr>

      <!-- Title -->
      <tr>
        <td style="padding:32px 32px 0;">
          <h2 style="margin:0;font-size:20px;font-weight:700;color:{$primary};">{$title}</h2>
        </td>
      </tr>

      <!-- Body content -->
      {$content}

      <!-- Footer -->
      <tr>
        <td style="padding:24px 32px;border-top:1px solid #e8ecf0;margin-top:8px;">
          <p style="margin:0;font-size:12px;color:#8fa0b4;">
            &copy; {$year} {$siteName}. This email was sent automatically — please do not reply directly.
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
}

/**
 * Build a single data row for inside an email template.
 */
function emailRow(string $label, string $value): string {
    return <<<HTML
<tr>
  <td style="padding:6px 32px;">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td width="140" style="font-size:12px;font-weight:700;text-transform:uppercase;
                                letter-spacing:0.05em;color:#8fa0b4;padding:8px 0;
                                vertical-align:top;">{$label}</td>
        <td style="font-size:14px;color:#2F3C4C;padding:8px 0;
                   vertical-align:top;">{$value}</td>
      </tr>
    </table>
  </td>
</tr>
HTML;
}

/**
 * Build a message block row for inside an email template.
 */
function emailMessage(string $label, string $message): string {
    $escaped = nl2br(htmlspecialchars($message));
    return <<<HTML
<tr>
  <td style="padding:6px 32px 24px;">
    <p style="margin:0 0 6px;font-size:12px;font-weight:700;text-transform:uppercase;
              letter-spacing:0.05em;color:#8fa0b4;">{$label}</p>
    <div style="background:#f4f6f9;border-radius:8px;padding:16px;font-size:14px;
                color:#2F3C4C;line-height:1.7;">{$escaped}</div>
  </td>
</tr>
HTML;
}
