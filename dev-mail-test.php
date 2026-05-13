<?php
/**
 * Local trial email (PHP only). Blocked on non-localhost hosts.
 *
 * With npm run dev (PHP built-in server on 127.0.0.1:8080):
 *   http://127.0.0.1:8080/dev-mail-test.php
 * Optional recipient override:
 *   http://127.0.0.1:8080/dev-mail-test.php?to=you@gmail.com
 *
 * CLI:
 *   php dev-mail-test.php
 *   php dev-mail-test.php you@gmail.com
 */
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/contact-form.php';

/**
 * @param string $host HTTP_HOST value
 * @return bool
 */
function shms_dev_mail_local_host($host)
{
    $host = strtolower(trim((string) $host));
    if ($host === '') {
        return false;
    }
    if (preg_match('/^127\.0\.0\.1(:\d+)?$/', $host)) {
        return true;
    }
    if (preg_match('/^localhost(:\d+)?$/', $host)) {
        return true;
    }
    if (preg_match('/^\[::1\](:\d+)?$/', $host)) {
        return true;
    }
    return false;
}

$sapi = php_sapi_name();
$cli = ($sapi === 'cli');

if (!$cli) {
    $h = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    if (!shms_dev_mail_local_host($h)) {
        header('HTTP/1.0 403 Forbidden');
        header('Content-Type: text/plain; charset=UTF-8');
        echo "This trial script only runs on localhost (127.0.0.1 / [::1]).\n";
        exit(1);
    }
}

$smtpOn = shms_contact_smtp_settings() !== null;

$to = shms_contact_mail_to();

if ($cli) {
    if (isset($argv[1]) && trim((string) $argv[1]) !== '') {
        $alt = trim((string) $argv[1]);
        if (filter_var($alt, FILTER_VALIDATE_EMAIL)) {
            $to = $alt;
        }
    }
} else {
    if (isset($_GET['to']) && trim((string) $_GET['to']) !== '') {
        $alt = trim((string) $_GET['to']);
        if (filter_var($alt, FILTER_VALIDATE_EMAIL)) {
            $to = $alt;
        }
    }
}

$subject = '[SHMS-2026 dev] Trial email from localhost';
$body = "This is a trial message from the SHMS-2026 project.\r\n\r\n";
$body .= 'Transport: ' . ($smtpOn ? 'SMTP (includes/config.php)' : 'PHP mail()') . "\r\n";
$body .= 'PHP: ' . PHP_VERSION . "\r\n";
$body .= 'SAPI: ' . $sapi . "\r\n";
$body .= 'Time (UTC): ' . gmdate('c') . "\r\n";
if (!$cli && isset($_SERVER['HTTP_HOST'])) {
    $body .= 'HTTP_HOST: ' . $_SERVER['HTTP_HOST'] . "\r\n";
}
$body .= 'Recipient: ' . $to . "\r\n";
$body .= 'mail_from: ' . shms_contact_mail_from() . "\r\n";
$body .= "\r\nIf this arrived in your inbox, outbound mail is configured for this environment.\r\n";

$ok = shms_contact_send_simple($to, $subject, $body);

if ($cli) {
    if ($smtpOn) {
        echo "Transport: SMTP (includes/config.php)\n";
    } else {
        echo "Transport: PHP mail() — on Windows this often fails; add smtp_* to includes/config.php\n";
    }
    if ($ok) {
        echo "Send reported success for {$to}\n";
        echo "Check inbox/spam. If SMTP auth failed, verify smtp_user / smtp_pass (Google App Password).\n";
    } else {
        echo "Send failed for {$to}\n";
        if (!$smtpOn) {
            echo "Add SMTP settings to includes/config.php (see config.sample.php) or configure php.ini mail.\n";
        }
    }
    echo "Local copy: dev-captured-mail.log in the project root.\n";
    exit($ok ? 0 : 1);
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dev mail trial — SHMS-2026</title>
  <style>
    body { font-family: system-ui, sans-serif; max-width: 42rem; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
    code { background: #f1f5f9; padding: 0.1em 0.35em; border-radius: 4px; }
    .ok { color: #15803d; }
    .fail { color: #b91c1c; }
    pre { background: #0f172a; color: #e2e8f0; padding: 1rem; border-radius: 8px; overflow: auto; font-size: 0.85rem; }
  </style>
</head>
<body>
  <h1>PHP mail trial (localhost only)</h1>
  <p><strong>Transport:</strong> <?php echo $smtpOn ? 'SMTP (<code>includes/config.php</code>)' : 'PHP <code>mail()</code> — add <code>smtp_*</code> to <code>config.php</code> for real delivery on Windows'; ?>.</p>
  <p><strong>Result:</strong>
    <?php if ($ok) : ?>
      <span class="ok">Send succeeded</span> — check the recipient inbox (and spam). With SMTP, failures are usually wrong App Password or blocked sign-in.
    <?php else : ?>
      <span class="fail">Send failed</span> — <?php echo $smtpOn ? 'check SMTP credentials in <code>includes/config.php</code>.' : 'configure <code>smtp_*</code> in <code>includes/config.php</code> or <code>php.ini</code> mail.'; ?> See <code>dev-captured-mail.log</code>.
    <?php endif; ?>
  </p>
  <p><strong>To:</strong> <?php echo htmlspecialchars($to, ENT_QUOTES, 'UTF-8'); ?></p>
  <p><strong>From (header):</strong> <?php echo htmlspecialchars(shms_contact_mail_from(), ENT_QUOTES, 'UTF-8'); ?></p>
  <p><strong>Local copy:</strong> On localhost, each attempt is also appended to <code>dev-captured-mail.log</code>.</p>
  <p><strong>Real delivery on localhost:</strong> copy <code>includes/config.sample.php</code> to <code>includes/config.php</code> and set <code>smtp_host</code> (e.g. <code>smtp.gmail.com</code>), <code>smtp_port</code> <code>587</code>, <code>smtp_encryption</code> <code>tls</code>, <code>smtp_user</code>, and <code>smtp_pass</code> (Google <em>App Password</em> if 2FA is on). Optionally set <code>mail_to</code> to your own address while testing.</p>
  <p>Optional: <code>dev-mail-test.php?to=your.address@gmail.com</code> overrides the recipient for this page only.</p>
  <h2>Without SMTP in config.php</h2>
  <p>PHP <code>mail()</code> on Windows usually does not deliver. Use SMTP in <code>config.php</code> or configure <code>php.ini</code> <code>[mail function]</code>.</p>
  <p>CLI test: <code>php dev-mail-test.php you@gmail.com</code></p>
</body>
</html>
