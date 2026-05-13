<?php
/**
 * CLI-only: POST a minimal registration payload to registration.webhook_url and print the result.
 * Use to diagnose firewall / TLS / proxy issues (same path as registration-submit.php → Google).
 *
 *   php tools/registration-transport-test.php
 */
if (php_sapi_name() !== 'cli') {
    header('HTTP/1.0 403 Forbidden');
    echo 'CLI only.';
    exit(1);
}

$root = dirname(__DIR__);
require_once $root . '/includes/init.php';
require_once $root . '/includes/registration-php-submit.php';

$url = shms_registration_post_url();
echo "POST URL: " . ($url !== '' ? $url : '(empty — set registration.webhook_url)') . "\n";
echo 'use_curl first: ' . (shms_registration_use_curl_first() ? 'yes' : 'no') . "\n";
echo 'openssl+allow_url_fopen: ' . (extension_loaded('openssl') && ini_get('allow_url_fopen') ? 'yes' : 'no') . "\n";
echo 'curl_init: ' . (function_exists('curl_init') ? 'yes' : 'no') . "\n";
echo 'insecure_ssl: ' . (shms_registration_insecure_ssl_allowed() ? 'yes' : 'no') . "\n";
$px = shms_registration_outbound_proxy_url();
echo 'outbound_proxy: ' . ($px !== '' ? $px : '(none)') . "\n";
$ca = shms_registration_ssl_ca_bundle_path();
echo 'CA bundle: ' . ($ca !== '' ? $ca : '(none — may use php.ini openssl.cafile)') . "\n\n";

if ($url === '') {
    exit(1);
}

$payload = array(
    'firstName' => 'Transport',
    'lastName' => 'Test',
    'email' => 'transport-test@example.com',
    'phone' => '0',
    'category' => 'Students / Research Scholars',
    'transactionId' => 'cli-test-' . gmdate('YmdHis') . '-' . mt_rand(1000, 9999),
    'paymentChannel' => 'UPI',
    'affiliation' => '',
    'notes' => 'registration-transport-test.php',
    'fileName' => 'ping.pdf',
    'mimeType' => 'application/pdf',
    'fileBase64' => base64_encode('%PDF-1.4 minimal test'),
);
$sec = shms_registration_submit_secret();
if ($sec !== '') {
    $payload['submitSecret'] = $sec;
}

$r = shms_registration_post_json_to_apps_script($payload);
echo shms_json_encode($r) . "\n";
exit(empty($r['ok']) ? 1 : 0);
