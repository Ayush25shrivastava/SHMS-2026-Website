<?php
/**
 * POST handler for registration + receipt (pure PHP → Apps Script). Shows a wait screen while processing, then redirects.
 */
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/registration-php-submit.php';

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registration.php', true, 302);
    exit;
}

@ini_set('zlib.output_compression', '0');
while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: text/html; charset=UTF-8');
http_response_code(200);

$waitTitle = 'Submitting registration…';
echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>' . htmlspecialchars($waitTitle, ENT_QUOTES, 'UTF-8') . '</title>';
echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
echo '<style>body{font-family:system-ui,Segoe UI,sans-serif;background:#f1f5f9;color:#0f172a;margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem;text-align:center;} .box{max-width:28rem;padding:2rem;background:#fff;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 8px 24px rgba(15,23,42,.08);} h1{font-size:1.15rem;margin:0 0 .75rem;} p{margin:0;font-size:.95rem;color:#475569;line-height:1.5;}</style></head><body>';
echo '<div class="box"><h1>Submitting your registration</h1><p>Please wait — uploading your receipt and confirming with the registration service. Do not close this page.</p></div>';
echo str_repeat("\n", 64);
flush();
if (function_exists('ob_flush')) {
    @ob_flush();
}
flush();

$r = shms_registration_process_submission();

$dest = 'registration.php?reg=err&e=unknown';
if (!empty($r['ok'])) {
    shms_registration_session_start();
    $api = isset($r['api']) && is_array($r['api']) ? $r['api'] : array();
    $_SESSION['shms2026_registration_receipt'] = shms_registration_build_receipt_data($_POST, $api);
    $dest = shms_registration_receipt_page_path();
} else {
    $e = isset($r['error']) ? (string) $r['error'] : 'unknown';
    $dest = 'registration.php?reg=err&e=' . rawurlencode($e);
}

$destEsc = htmlspecialchars($dest, ENT_QUOTES, 'UTF-8');
echo '<p style="margin:2rem 1rem 1rem;text-align:center;font-size:.9rem"><a href="' . $destEsc . '">Continue to next step</a></p>';
echo '<meta http-equiv="refresh" content="0;url=' . $destEsc . '">';
echo '</body></html>';
exit;
