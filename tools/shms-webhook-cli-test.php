<?php
/**
 * POST a minimal test payload to the configured Sheet webhook and print the raw HTTP body.
 * Run from project root: php tools/shms-webhook-cli-test.php
 *
 * If you see {"ok":true} but no Sheet row, SPREADSHEET_ID in Apps Script points to a different
 * file than the one you have open, or you are viewing Sheet1 instead of the Submissions tab.
 */
$root = dirname(__DIR__);
require_once $root . '/includes/init.php';
require_once $root . '/includes/contact-form.php';

if (!function_exists('curl_init')) {
    fwrite(STDERR, "cURL is not enabled.\n");
    exit(1);
}

$url = shms_contact_sheet_webhook_url();
$secret = shms_contact_sheet_webhook_secret();
if ($url === '' || strlen($secret) < 12) {
    fwrite(STDERR, "Set sheet_webhook_url and sheet_webhook_secret (12+ chars) in includes/config.php\n");
    exit(1);
}

$payload = array(
    'secret' => $secret,
    'mail_to' => shms_contact_mail_to(),
    'timestamp_utc' => gmdate('c'),
    'first_name' => 'CLI',
    'last_name' => 'Test',
    'email' => 'test@example.com',
    'topic_key' => 'general',
    'topic_label' => 'General enquiry',
    'role_key' => '',
    'role_label' => '',
    'urgency_key' => 'normal',
    'urgency_label' => 'Standard (we reply within a few working days)',
    'subject' => '[SHMS diagnostic] Webhook CLI test — not a visitor',
    'message' => 'This row was created by running: php tools/shms-webhook-cli-test.php' . "\n\n"
        . 'If you see this in the Sheet and got this e-mail, the webhook and SPREADSHEET_ID are correct. '
        . 'Submit the real contact form on contact.php to test visitor flow.',
    'ip' => '127.0.0.1',
    'user_agent' => 'shms-webhook-cli-test.php',
);

$json = json_encode($payload, JSON_UNESCAPED_UNICODE);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 45);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
shms_contact_curl_set_ca_bundle($ch);

$raw = curl_exec($ch);
$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errno = curl_errno($ch);
curl_close($ch);

echo "HTTP status: {$code}\n";
if ($errno !== 0) {
    $errstr = function_exists('curl_strerror') ? curl_strerror($errno) : '';
    echo "cURL error {$errno}" . ($errstr !== '' ? " ({$errstr})" : '') . "\n";
    if ($errno === 60) {
        echo "\nSSL verify failed. The project includes tools/cacert.pem — if missing, download:\n";
        echo "  https://curl.se/ca/cacert.pem\n";
        echo "Or set curl.cainfo in php.ini to that file path, or sheet_webhook_cainfo in config.php.\n";
    }
    exit(1);
}
echo "Body:\n{$raw}\n";
