<?php
/**
 * Shared bootstrap: path helpers for subdirectory deployment on MNNIT hosting.
 * Targets PHP 5.2.7+ (e.g. PHP 5.5 on institute servers). Avoids PHP 7-only syntax.
 */
if (defined('SHMS_INIT_LOADED')) {
    return;
}
define('SHMS_INIT_LOADED', true);

if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50207) {
    if (!headers_sent()) {
        header('Content-Type: text/plain; charset=UTF-8', true, 500);
    }
    echo 'SHMS-2026 requires PHP 5.2.7 or newer. This server reports PHP ' . PHP_VERSION . ".\n";
    exit(1);
}

if (!function_exists('http_response_code')) {
    /**
     * Polyfill for PHP &lt; 5.4.
     *
     * @param int|null $code
     * @return int
     */
    function http_response_code($code = null)
    {
        static $current = 200;
        if ($code === null) {
            return $current;
        }
        $current = (int) $code;
        $messages = array(
            200 => 'OK',
            204 => 'No Content',
            400 => 'Bad Request',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        );
        $msg = isset($messages[$current]) ? $messages[$current] : 'OK';
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
        header($protocol . ' ' . $current . ' ' . $msg);
        return $current;
    }
}

if (!function_exists('json_last_error_msg')) {
    /**
     * Polyfill for PHP &lt; 5.5 (native json_last_error_msg added in 5.5.0).
     */
    function json_last_error_msg()
    {
        $code = json_last_error();
        $map = array(
            JSON_ERROR_NONE => 'No error',
        );
        if (defined('JSON_ERROR_DEPTH')) {
            $map[JSON_ERROR_DEPTH] = 'Maximum stack depth exceeded';
        }
        if (defined('JSON_ERROR_STATE_MISMATCH')) {
            $map[JSON_ERROR_STATE_MISMATCH] = 'Underflow or the modes mismatch';
        }
        if (defined('JSON_ERROR_CTRL_CHAR')) {
            $map[JSON_ERROR_CTRL_CHAR] = 'Unexpected control character found';
        }
        if (defined('JSON_ERROR_SYNTAX')) {
            $map[JSON_ERROR_SYNTAX] = 'Syntax error, malformed JSON';
        }
        if (defined('JSON_ERROR_UTF8')) {
            $map[JSON_ERROR_UTF8] = 'Malformed UTF-8 characters';
        }
        if (isset($map[$code])) {
            return $map[$code];
        }
        return 'JSON error code ' . $code;
    }
}

/**
 * @param mixed $value
 * @return string
 */
function shms_json_encode($value)
{
    $json = json_encode($value);
    if ($json === false) {
        throw new RuntimeException('json_encode failed: ' . json_last_error_msg());
    }
    return $json;
}

/**
 * Web path prefix for this app (e.g. "/shms2026" when not at document root).
 *
 * @return string
 */
function shms_web_base()
{
    $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/index.php';
    $dir = str_replace('\\', '/', dirname($script));
    if ($dir === '/' || $dir === '.') {
        return '';
    }
    return rtrim($dir, '/');
}

/**
 * Load includes/config.php at most once per request (avoids PHP returning true on second require).
 *
 * @return array|null Full config array, or null if file missing / invalid.
 */
function shms_app_config()
{
    static $cached = null;
    static $done = false;
    if ($done) {
        return $cached;
    }
    $done = true;
    $path = dirname(__FILE__) . '/config.php';
    if (!is_readable($path)) {
        $cached = null;
        return $cached;
    }
    $cfg = require $path;
    if (!is_array($cfg)) {
        $cached = null;
        return $cached;
    }
    $cached = $cfg;
    return $cached;
}

/**
 * Published Google Form URL for registration payment / proof submission (optional).
 *
 * @return string HTTPS URL or empty
 */
function shms_registration_payment_form_url()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['registration']) || !is_array($cfg['registration'])) {
        return '';
    }
    $u = isset($cfg['registration']['payment_form_url']) ? trim((string) $cfg['registration']['payment_form_url']) : '';
    if ($u === '' || stripos($u, 'http') !== 0) {
        return '';
    }
    return $u;
}

/**
 * Apps Script Web App URL for the embedded registration + receipt upload form (optional).
 * Deploy from includes/registration-webapp/ (Code.gs + Registration.html). Use the /exec URL.
 *
 * @return string HTTPS URL or empty
 */
function shms_registration_webapp_url()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['registration']) || !is_array($cfg['registration'])) {
        return '';
    }
    $u = isset($cfg['registration']['webapp_url']) ? trim((string) $cfg['registration']['webapp_url']) : '';
    if ($u === '' || stripos($u, 'http') !== 0) {
        return '';
    }
    return $u;
}

/**
 * Apps Script Web App /exec URL used for server-side JSON POST (registration + receipt).
 * Uses registration.webhook_url if set, otherwise registration.webapp_url.
 *
 * @return string HTTPS URL or empty
 */
function shms_registration_post_url()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['registration']) || !is_array($cfg['registration'])) {
        return '';
    }
    $reg = $cfg['registration'];
    $u = isset($reg['webhook_url']) ? trim((string) $reg['webhook_url']) : '';
    if ($u === '' || stripos($u, 'http') !== 0) {
        $u = isset($reg['webapp_url']) ? trim((string) $reg['webapp_url']) : '';
    }
    if ($u === '' || stripos($u, 'http') !== 0) {
        return '';
    }
    return $u;
}

/**
 * True when the embedded PHP form on registration.php should be shown (server-side POST).
 * Not used when registration.webapp_url is set — visitors use the Google-hosted form link instead.
 *
 * @return bool
 */
function shms_registration_php_form_enabled()
{
    if (shms_registration_webapp_url() !== '') {
        return false;
    }
    return shms_registration_post_url() !== '';
}

/**
 * True when the “submit registration” link (Web App form in a new window) is available.
 *
 * @return bool
 */
function shms_registration_webapp_popup_enabled()
{
    return shms_registration_webapp_url() !== '';
}

/**
 * Shared secret sent in JSON as submitSecret; must match CONFIG.SUBMIT_SECRET in Code.gs (empty on both = no check).
 *
 * @return string
 */
function shms_registration_submit_secret()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['registration']) || !is_array($cfg['registration'])) {
        return '';
    }
    return trim((string) (isset($cfg['registration']['submit_secret']) ? $cfg['registration']['submit_secret'] : ''));
}

/**
 * When true, the embedded registration form POSTs JSON from the visitor’s browser to registration.webhook_url
 * (text/plain body, same as contact sheet mode). Use when the web server cannot reach script.google.com.
 * Set use_browser_post => false to force server-side registration-submit.php only.
 *
 * @return bool
 */
function shms_registration_use_browser_post()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['registration']) || !is_array($cfg['registration'])) {
        return false;
    }
    $reg = $cfg['registration'];
    if (!array_key_exists('use_browser_post', $reg)) {
        return false;
    }
    return !empty($reg['use_browser_post']);
}

/**
 * Relative URL for ?reg=ok after successful browser-side registration POST.
 *
 * @return string
 */
function shms_registration_ok_redirect_path()
{
    $wb = shms_web_base();
    return $wb !== '' ? $wb . '/registration.php?reg=ok' : 'registration.php?reg=ok';
}

/**
 * Relative path to registration.php (no query) for links from the popup.
 *
 * @return string
 */
function shms_registration_page_url_path()
{
    $wb = shms_web_base();
    return $wb !== '' ? $wb . '/registration.php' : 'registration.php';
}

/**
 * Relative path to the registration form popup page (full form in a new window).
 *
 * @return string
 */
function shms_registration_popup_url_path()
{
    $wb = shms_web_base();
    return $wb !== '' ? $wb . '/registration-form-popup.php' : 'registration-form-popup.php';
}

/**
 * Success URL inside the popup when opener cannot be updated (e.g. blocked).
 *
 * @return string
 */
function shms_registration_popup_self_ok_path()
{
    $wb = shms_web_base();
    return $wb !== '' ? $wb . '/registration-form-popup.php?reg=ok' : 'registration-form-popup.php?reg=ok';
}

/**
 * Relative path to the printable registration receipt page.
 *
 * @return string
 */
function shms_registration_receipt_page_path()
{
    $wb = shms_web_base();
    return $wb !== '' ? $wb . '/registration-receipt.php' : 'registration-receipt.php';
}

/**
 * Relative path to download receipt as PDF (session).
 *
 * @return string
 */
function shms_registration_receipt_pdf_path()
{
    $wb = shms_web_base();
    return $wb !== '' ? $wb . '/registration-receipt-pdf.php' : 'registration-receipt-pdf.php';
}

/**
 * POST bridge: stores browser-submitted receipt JSON in session then redirects to registration-receipt.php.
 *
 * @return string
 */
function shms_registration_receipt_bridge_path()
{
    $wb = shms_web_base();
    return $wb !== '' ? $wb . '/registration-receipt-bridge.php' : 'registration-receipt-bridge.php';
}

/**
 * One-time style token (same session as registration form) for registration-receipt-bridge.php.
 *
 * @return string
 */
function shms_registration_receipt_bridge_token()
{
    shms_registration_session_start();
    if (empty($_SESSION['shms_receipt_bridge_token']) || !is_string($_SESSION['shms_receipt_bridge_token'])) {
        $hex = '';
        if (function_exists('openssl_random_pseudo_bytes')) {
            $raw = @openssl_random_pseudo_bytes(16);
            if ($raw !== false && strlen($raw) === 16) {
                $hex = bin2hex($raw);
            }
        }
        if ($hex === '') {
            $hex = '';
            for ($i = 0; $i < 16; $i++) {
                $hex .= sprintf('%02x', mt_rand(0, 255));
            }
        }
        $_SESSION['shms_receipt_bridge_token'] = $hex;
    }
    return $_SESSION['shms_receipt_bridge_token'];
}

/**
 * Start PHP session for registration receipt (server-side submit path).
 *
 * @return void
 */
function shms_registration_session_start()
{
    if (session_id() == '') {
        @session_start();
    }
}

/**
 * When registration.webapp_url is set, also show the PHP form on registration.php (POST via server file_get_contents to the same /exec URL).
 * Enable with <code>'php_form_alongside_webapp' => true</code> under <code>registration</code> in config when the Google-hosted form returns HTML instead of JSON.
 *
 * @return bool
 */
function shms_registration_php_form_alongside_webapp()
{
    if (shms_registration_webapp_url() === '') {
        return false;
    }
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['registration']) || !is_array($cfg['registration'])) {
        return false;
    }
    if (empty($cfg['registration']['php_form_alongside_webapp'])) {
        return false;
    }
    return shms_registration_post_url() !== '';
}

/**
 * Google Fonts (UI + headings) — preconnect + stylesheet. Display=swap for CLS.
 *
 * @return string HTML
 */
function shms_head_typography()
{
    $out = '';
    $out .= "<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">\n";
    $out .= "<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>\n";
    $out .= "<link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&amp;family=Outfit:wght@400..800&amp;display=swap\">\n";
    return $out;
}

/**
 * Emit <head> resource hints: site typography + optional Flag Counter badge.
 * - Fonts: preconnect + stylesheet (all pages)
 * - Flag counter: preconnect / preload when enabled
 *
 * @return string HTML (typography always; flag block may be empty)
 */
function shms_head_resource_hints()
{
    $out = shms_head_typography();

    require_once dirname(__FILE__) . '/flag-counter.php';
    $s = shms_flagcounter_settings();
    if (!$s['show'] || !isset($s['img_src']) || !is_string($s['img_src']) || trim($s['img_src']) === '') {
        return $out;
    }
    $img = trim($s['img_src']);

    // Basic allowlist: only emit for the official host.
    if (stripos($img, 'https://s01.flagcounter.com/') !== 0) {
        return $out;
    }

    $imgEsc = htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
    $out .= "<link rel=\"preconnect\" href=\"https://s01.flagcounter.com\" crossorigin>\n";
    $out .= "<link rel=\"preconnect\" href=\"https://info.flagcounter.com\" crossorigin>\n";
    $out .= "<link rel=\"dns-prefetch\" href=\"//s01.flagcounter.com\">\n";
    $out .= "<link rel=\"dns-prefetch\" href=\"//info.flagcounter.com\">\n";
    $out .= "<link rel=\"preload\" as=\"image\" href=\"" . $imgEsc . "\" fetchpriority=\"high\">\n";
    return $out;
}

require_once dirname(__FILE__) . '/theme.php';
