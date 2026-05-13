<?php
/**
 * Contact form handler:
 *  - form_backend php: mail via optional SMTP (config) or PHP mail().
 *  - form_backend sheet: POST JSON via PHP cURL when available and contact.sheet_webhook_use_php_curl is true;
 *    if curl_init is missing, or sheet_webhook_use_php_curl is false, the visitor’s browser POSTs JSON after
 *    server-side validation (fetch). Redeploy Apps Script so browser_token may match WEBHOOK_SECRET.
 *  - form_backend sheet_browser: always use browser fetch (never PHP cURL). Optional sheet_browser_token;
 *    if unset, sheet_webhook_secret is used as browser_token (same Script rules as sheet fallback).
 *  - form_backend google: embed only; no PHP submit (see contact-form-block.php).
 * Target inbox: shms2026@mnnit.ac.in (override contact.mail_to).
 * Requires init.php loaded first.
 */

if (!defined('SHMS_INIT_LOADED')) {
    require_once dirname(__FILE__) . '/init.php';
}
require_once dirname(__FILE__) . '/contact-smtp.php';

define('SHMS_CONTACT_MAIL_TO_DEFAULT', 'shms2026@mnnit.ac.in');

/**
 * @return string
 */
function shms_contact_mail_to()
{
    $cfg = shms_app_config();
    if (is_array($cfg) && isset($cfg['contact']) && is_array($cfg['contact']) && isset($cfg['contact']['mail_to'])) {
        $e = trim((string) $cfg['contact']['mail_to']);
        if ($e !== '' && function_exists('filter_var') && filter_var($e, FILTER_VALIDATE_EMAIL)) {
            return $e;
        }
    }
    return SHMS_CONTACT_MAIL_TO_DEFAULT;
}

/**
 * @return string 'php'|'google'|'sheet'|'sheet_browser'
 */
function shms_contact_form_backend()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['contact']) || !is_array($cfg['contact'])) {
        return 'php';
    }
    $b = isset($cfg['contact']['form_backend']) ? strtolower(trim((string) $cfg['contact']['form_backend'])) : 'php';
    if ($b === 'google') {
        return 'google';
    }
    if ($b === 'sheet_browser') {
        return 'sheet_browser';
    }
    if ($b === 'sheet') {
        return 'sheet';
    }
    return 'php';
}

/**
 * Google Form embed URL (https://docs.google.com/forms/.../viewform?embedded=true).
 *
 * @return string empty if unset or invalid
 */
function shms_contact_google_form_embed_url()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['contact']) || !is_array($cfg['contact'])) {
        return '';
    }
    $u = isset($cfg['contact']['google_form_embed_url']) ? trim((string) $cfg['contact']['google_form_embed_url']) : '';
    if ($u === '') {
        return '';
    }
    if (stripos($u, 'https://docs.google.com/forms/') !== 0) {
        return '';
    }
    if (strpos($u, 'embedded=true') === false) {
        $u .= (strpos($u, '?') !== false ? '&' : '?') . 'embedded=true';
    }
    return $u;
}

/**
 * @return bool
 */
function shms_contact_uses_google_form()
{
    return shms_contact_form_backend() === 'google' && shms_contact_google_form_embed_url() !== '';
}

/**
 * Apps Script web app URL (Deploy → Web app → copy URL ending in /exec).
 *
 * @return string empty if unset or invalid
 */
function shms_contact_sheet_webhook_url()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['contact']) || !is_array($cfg['contact'])) {
        return '';
    }
    $u = isset($cfg['contact']['sheet_webhook_url']) ? trim((string) $cfg['contact']['sheet_webhook_url']) : '';
    if ($u === '') {
        return '';
    }
    $parts = @parse_url($u);
    if (!is_array($parts) || empty($parts['scheme']) || strtolower((string) $parts['scheme']) !== 'https') {
        return '';
    }
    if (empty($parts['host']) || strtolower((string) $parts['host']) !== 'script.google.com') {
        return '';
    }
    $path = isset($parts['path']) ? (string) $parts['path'] : '';
    if (!preg_match('#^/macros/s/[A-Za-z0-9_-]+/(exec|dev)$#', $path)) {
        return '';
    }
    return $u;
}

/**
 * Shared secret (must match Script property WEBHOOK_SECRET). Min. 12 characters recommended.
 *
 * @return string
 */
function shms_contact_sheet_webhook_secret()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['contact']) || !is_array($cfg['contact'])) {
        return '';
    }
    return trim((string) (isset($cfg['contact']['sheet_webhook_secret']) ? $cfg['contact']['sheet_webhook_secret'] : ''));
}

/**
 * @return bool
 */
function shms_contact_sheet_curl_available()
{
    return function_exists('curl_init');
}

/**
 * When false with form_backend sheet, skip PHP cURL and deliver via the visitor’s browser (same as no cURL).
 * Use if the server hits cURL error 28 (timeout) to script.google.com after cURL was enabled or firewall changed.
 *
 * @return bool
 */
function shms_contact_sheet_webhook_use_php_curl()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['contact']) || !is_array($cfg['contact'])) {
        return true;
    }
    if (array_key_exists('sheet_webhook_use_php_curl', $cfg['contact'])) {
        return !empty($cfg['contact']['sheet_webhook_use_php_curl']);
    }
    return true;
}

/**
 * @return bool
 */
function shms_contact_sheet_webhook_ready()
{
    return shms_contact_sheet_webhook_url() !== '' && strlen(shms_contact_sheet_webhook_secret()) >= 12;
}

/**
 * @return bool
 */
function shms_contact_uses_sheet_webhook()
{
    return shms_contact_form_backend() === 'sheet' && shms_contact_sheet_webhook_ready();
}

/**
 * Optional separate token for form_backend sheet_browser. If empty, sheet_webhook_secret is used (MNNIT / no cURL).
 *
 * @return string
 */
function shms_contact_sheet_browser_token()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['contact']) || !is_array($cfg['contact'])) {
        return '';
    }
    return trim((string) (isset($cfg['contact']['sheet_browser_token']) ? $cfg['contact']['sheet_browser_token'] : ''));
}

/**
 * Token embedded in browser JSON (fetch). Prefer sheet_browser_token; else sheet_webhook_secret for institute hosts without cURL.
 *
 * @return string
 */
function shms_contact_sheet_browser_token_for_payload()
{
    $t = shms_contact_sheet_browser_token();
    if (strlen($t) >= 12) {
        return $t;
    }
    $s = shms_contact_sheet_webhook_secret();
    if (strlen($s) >= 12) {
        return $s;
    }
    return '';
}

/**
 * Deliver Sheet webhook via visitor browser (fetch) — no PHP cURL on the server.
 * True when: (1) form_backend sheet_browser and URL + token OK, or (2) form_backend sheet and (cURL missing
 * or sheet_webhook_use_php_curl is false), URL + webhook secret OK.
 *
 * @return bool
 */
function shms_contact_sheet_delivery_via_browser()
{
    if (shms_contact_sheet_webhook_url() === '') {
        return false;
    }
    if (strlen(shms_contact_sheet_browser_token_for_payload()) < 12) {
        return false;
    }
    $b = shms_contact_form_backend();
    if ($b === 'sheet_browser') {
        return true;
    }
    if ($b === 'sheet' && (!shms_contact_sheet_curl_available() || !shms_contact_sheet_webhook_use_php_curl())) {
        return true;
    }
    return false;
}

/**
 * @return bool
 */
function shms_contact_sheet_browser_ready()
{
    return shms_contact_sheet_webhook_url() !== '' && strlen(shms_contact_sheet_browser_token_for_payload()) >= 12;
}

/**
 * True when the contact form will use the visitor’s browser to POST to Apps Script (sheet_browser, or sheet without cURL).
 *
 * @return bool
 */
function shms_contact_uses_sheet_browser()
{
    return shms_contact_sheet_delivery_via_browser();
}

/**
 * Point cURL at a CA certificate bundle (fixes SSL error 60 on Windows when php.ini has no curl.cainfo).
 * Uses contact.sheet_webhook_cainfo when set and readable; otherwise tools/cacert.pem in the project root.
 *
 * @param resource $ch cURL handle
 * @return void
 */
function shms_contact_curl_set_ca_bundle($ch)
{
    $path = '';
    $cfg = shms_app_config();
    if (is_array($cfg) && isset($cfg['contact']) && is_array($cfg['contact']) && isset($cfg['contact']['sheet_webhook_cainfo'])) {
        $path = trim((string) $cfg['contact']['sheet_webhook_cainfo']);
    }
    if ($path === '' || !is_readable($path)) {
        $path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'cacert.pem';
    }
    if (is_readable($path)) {
        @curl_setopt($ch, CURLOPT_CAINFO, $path);
    }
}

/**
 * Build payload fields for Apps Script (sheet and sheet_browser modes).
 *
 * @param array $v validated values from shms_contact_validate_and_collect
 * @return array
 */
function shms_contact_sheet_webhook_payload_array(array $v)
{
    $cats = shms_contact_categories();
    $roles = shms_contact_roles();
    $urg = shms_contact_urgency();
    $catLabel = isset($cats[$v['contact_category']]) ? $cats[$v['contact_category']] : $v['contact_category'];
    $roleLabel = ($v['contact_role'] !== '' && isset($roles[$v['contact_role']])) ? $roles[$v['contact_role']] : '';
    $urgLabel = isset($urg[$v['contact_urgency']]) ? $urg[$v['contact_urgency']] : $v['contact_urgency'];

    return array(
        'mail_to' => shms_contact_mail_to(),
        'timestamp_utc' => gmdate('c'),
        'first_name' => $v['first_name'],
        'last_name' => $v['last_name'],
        'email' => $v['email'],
        'topic_key' => $v['contact_category'],
        'topic_label' => $catLabel,
        'role_key' => $v['contact_role'],
        'role_label' => $roleLabel,
        'urgency_key' => $v['contact_urgency'],
        'urgency_label' => $urgLabel,
        'subject' => $v['subject_line'],
        'message' => $v['message'],
        'ip' => isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '',
        'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '',
    );
}

/**
 * POST submission to Google Apps Script (append Sheet row + send email).
 *
 * @param array $v validated values from shms_contact_validate_and_collect
 * @return bool
 */
function shms_contact_post_sheet_webhook(array $v)
{
    $GLOBALS['shms_sheet_webhook_diag'] = '';
    $GLOBALS['shms_sheet_webhook_curl_errno'] = 0;

    if (!function_exists('curl_init')) {
        $GLOBALS['shms_sheet_webhook_diag'] = 'no_curl';
        return false;
    }
    $url = shms_contact_sheet_webhook_url();
    $secret = shms_contact_sheet_webhook_secret();
    if ($url === '' || strlen($secret) < 12) {
        $GLOBALS['shms_sheet_webhook_diag'] = 'bad_config';
        return false;
    }

    $payload = shms_contact_sheet_webhook_payload_array($v);
    $payload['secret'] = $secret;

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    $ch = curl_init($url);
    if ($ch === false) {
        $GLOBALS['shms_sheet_webhook_diag'] = 'curl_init_failed';
        return false;
    }
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 25);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    shms_contact_curl_set_ca_bundle($ch);

    $raw = curl_exec($ch);
    $errno = (int) curl_errno($ch);
    curl_close($ch);

    if ($errno !== 0 || $raw === false) {
        $GLOBALS['shms_sheet_webhook_diag'] = 'curl_exec';
        $GLOBALS['shms_sheet_webhook_curl_errno'] = $errno;
        return false;
    }
    if ($raw === '') {
        $GLOBALS['shms_sheet_webhook_diag'] = 'empty_body';
        return false;
    }

    $cfgLog = shms_app_config();
    if (is_array($cfgLog) && isset($cfgLog['contact']) && is_array($cfgLog['contact'])) {
        $logPath = isset($cfgLog['contact']['sheet_webhook_debug_log'])
            ? trim((string) $cfgLog['contact']['sheet_webhook_debug_log'])
            : '';
        if ($logPath !== '') {
            $line = gmdate('c') . ' ' . strlen($raw) . " bytes\n" . $raw . "\n---\n";
            @file_put_contents($logPath, $line, FILE_APPEND | LOCK_EX);
        }
    }

    $dec = json_decode($raw, true);
    if (!is_array($dec)) {
        $GLOBALS['shms_sheet_webhook_diag'] = 'bad_json';
        return false;
    }
    if (empty($dec['ok'])) {
        $GLOBALS['shms_sheet_webhook_diag'] = 'rejected';
        return false;
    }

    return true;
}

/**
 * @param array $v
 * @return bool
 */
function shms_contact_dispatch_submission(array $v)
{
    if (shms_contact_form_backend() === 'sheet') {
        if (shms_contact_sheet_delivery_via_browser()) {
            return false;
        }
        return shms_contact_post_sheet_webhook($v);
    }
    if (shms_contact_form_backend() === 'sheet_browser') {
        return false;
    }
    return shms_contact_send_mail($v);
}

/**
 * Visible From: address. With SMTP (Gmail/Workspace), defaults to the authenticated mailbox.
 *
 * @return string
 */
function shms_contact_mail_from()
{
    $cfg = shms_app_config();
    $smtp = shms_contact_smtp_settings();
    if ($smtp !== null) {
        if (is_array($cfg) && isset($cfg['contact']) && is_array($cfg['contact']) && isset($cfg['contact']['smtp_from'])) {
            $e = trim((string) $cfg['contact']['smtp_from']);
            if ($e !== '' && function_exists('filter_var') && filter_var($e, FILTER_VALIDATE_EMAIL)) {
                return $e;
            }
        }
        return $smtp['user'];
    }

    if (is_array($cfg) && isset($cfg['contact']) && is_array($cfg['contact']) && isset($cfg['contact']['mail_from'])) {
        $e = trim((string) $cfg['contact']['mail_from']);
        if ($e !== '' && function_exists('filter_var') && filter_var($e, FILTER_VALIDATE_EMAIL)) {
            return $e;
        }
    }

    $host = '';
    if (isset($_SERVER['HTTP_HOST']) && is_string($_SERVER['HTTP_HOST'])) {
        $host = strtolower(trim($_SERVER['HTTP_HOST']));
        if (preg_match('/^([a-z0-9.-]+)(:\d+)?$/', $host, $m)) {
            $host = $m[1];
        } else {
            $host = '';
        }
    }
    if ($host === '' && isset($_SERVER['SERVER_NAME']) && is_string($_SERVER['SERVER_NAME'])) {
        $sn = strtolower(trim($_SERVER['SERVER_NAME']));
        if (preg_match('/^[a-z0-9.-]+$/', $sn)) {
            $host = $sn;
        }
    }
    if ($host === '' || $host === 'localhost' || preg_match('/^\d+\.\d+\.\d+\.\d+$/', $host)) {
        $host = 'localhost';
    }

    return 'noreply@' . $host;
}

/**
 * Sendmail envelope sender (-f). Set in config if your host requires it (often an @mnnit.ac.in mailbox).
 *
 * @return string empty or e.g. "-fwebform@mnnit.ac.in"
 */
function shms_contact_mail_envelope_param()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['contact']) || !is_array($cfg['contact']) || !isset($cfg['contact']['mail_envelope_from'])) {
        return '';
    }
    $e = trim((string) $cfg['contact']['mail_envelope_from']);
    if ($e === '' || !function_exists('filter_var') || !filter_var($e, FILTER_VALIDATE_EMAIL)) {
        return '';
    }
    return '-f' . $e;
}

/**
 * Append one line if contact.mail_log_path is set in config (server-side debugging).
 *
 * @param string $to
 * @param bool $ok
 * @param string $note
 * @return void
 */
function shms_contact_mail_log($to, $ok, $note = '')
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['contact']) || !is_array($cfg['contact']) || !isset($cfg['contact']['mail_log_path'])) {
        return;
    }
    $path = trim((string) $cfg['contact']['mail_log_path']);
    if ($path === '') {
        return;
    }
    $line = gmdate('c') . "\t" . ($ok ? 'OK' : 'FAIL') . "\t" . $to . "\t" . str_replace(array("\r", "\n", "\t"), ' ', $note) . "\n";
    @file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
}

/**
 * True when running under CLI or PHP dev server on loopback (npm run dev).
 * Used only to append a copy of outbound mail to a local file — never on production hostnames.
 *
 * @return bool
 */
function shms_contact_is_local_dev()
{
    if (php_sapi_name() === 'cli') {
        return true;
    }
    if (!isset($_SERVER['HTTP_HOST']) || !is_string($_SERVER['HTTP_HOST'])) {
        return false;
    }
    $h = strtolower(trim($_SERVER['HTTP_HOST']));
    if (preg_match('/^127\.0\.0\.1(:\d+)?$/', $h)) {
        return true;
    }
    if (preg_match('/^localhost(:\d+)?$/', $h)) {
        return true;
    }
    if (preg_match('/^\[::1\](:\d+)?$/', $h)) {
        return true;
    }
    return false;
}

/**
 * On localhost/CLI, append full message to project-root dev-captured-mail.log (real inbox not required).
 *
 * @param string $to
 * @param string $subjectPlain
 * @param string $body
 * @param bool $mailReturned
 * @return void
 */
function shms_contact_local_mail_capture($to, $subjectPlain, $body, $mailReturned)
{
    if (!shms_contact_is_local_dev()) {
        return;
    }
    $root = dirname(dirname(__FILE__));
    $path = $root . DIRECTORY_SEPARATOR . 'dev-captured-mail.log';
    $sep = str_repeat('=', 78) . "\r\n";
    $block = $sep;
    $block .= gmdate('c') . ' | send ' . ($mailReturned ? 'OK' : 'FAIL') . " | To: {$to}\r\n";
    $block .= 'From header would be: ' . shms_contact_mail_from() . "\r\n";
    $block .= 'Subject: ' . str_replace(array("\r", "\n"), ' ', (string) $subjectPlain) . "\r\n";
    $block .= str_repeat('-', 78) . "\r\n";
    $block .= (string) $body . "\r\n\r\n";
    @file_put_contents($path, $block, FILE_APPEND | LOCK_EX);
}

/**
 * Send via SMTP (if configured in config.php) or PHP mail().
 *
 * @param string $to
 * @param string $subjectPlain for logs / local capture
 * @param string $subjectHeader value for Subject (may be MIME-encoded for mail())
 * @param string $body
 * @param string $replyTo
 * @return bool
 */
function shms_contact_dispatch_mail($to, $subjectPlain, $subjectHeader, $body, $replyTo)
{
    $fromAddr = shms_contact_mail_from();

    if (shms_contact_smtp_settings() !== null) {
        $smtpErr = '';
        $sent = shms_contact_smtp_send($to, $subjectHeader, $body, $fromAddr, $replyTo, $smtpErr);
        $note = 'smtp:' . $fromAddr;
        if (!$sent && $smtpErr !== '') {
            $note .= ':' . substr(preg_replace('/\s+/', ' ', $smtpErr), 0, 200);
        }
        shms_contact_mail_log($to, $sent, $note);
        shms_contact_local_mail_capture($to, $subjectPlain, $body, $sent);
        return $sent;
    }

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= 'From: SHMS-2026 Web <' . $fromAddr . ">\r\n";
    $rt = trim((string) $replyTo);
    if ($rt !== '' && function_exists('filter_var') && filter_var($rt, FILTER_VALIDATE_EMAIL)) {
        $headers .= 'Reply-To: ' . $rt . "\r\n";
    }
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    $extra = shms_contact_mail_envelope_param();
    $sent = $extra !== ''
        ? @mail($to, $subjectHeader, $body, $headers, $extra)
        : @mail($to, $subjectHeader, $body, $headers);

    shms_contact_mail_log($to, $sent, $fromAddr);
    shms_contact_local_mail_capture($to, $subjectPlain, $body, $sent);

    return $sent;
}

/**
 * @return void
 */
function shms_contact_session_start()
{
    if (session_id() == '') {
        @session_start();
    }
}

/**
 * @return string
 */
function shms_contact_csrf_token()
{
    shms_contact_session_start();
    if (!isset($_SESSION['shms_csrf_contact']) || !is_string($_SESSION['shms_csrf_contact']) || $_SESSION['shms_csrf_contact'] === '') {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $b = @openssl_random_pseudo_bytes(16);
            if ($b !== false && strlen($b) === 16) {
                $_SESSION['shms_csrf_contact'] = bin2hex($b);
            }
        }
        if (!isset($_SESSION['shms_csrf_contact']) || $_SESSION['shms_csrf_contact'] === '') {
            $_SESSION['shms_csrf_contact'] = md5(uniqid((string) mt_rand(), true));
        }
    }
    return $_SESSION['shms_csrf_contact'];
}

/**
 * Create a new arithmetic CAPTCHA (session-stored expected answer).
 *
 * @return array question (string), field name for POST
 */
function shms_contact_captcha_issue()
{
    shms_contact_session_start();
    $a = mt_rand(2, 11);
    $b = mt_rand(2, 11);
    $_SESSION['shms_contact_captcha_expected'] = (int) ($a + $b);
    return array(
        'question' => 'What is ' . $a . ' + ' . $b . '?',
        'field_name' => 'captcha_human',
    );
}

/**
 * @param array $post
 * @return bool
 */
function shms_contact_captcha_validate($post)
{
    shms_contact_session_start();
    if (!isset($_SESSION['shms_contact_captcha_expected'])) {
        return false;
    }
    $expected = (int) $_SESSION['shms_contact_captcha_expected'];
    unset($_SESSION['shms_contact_captcha_expected']);

    $raw = isset($post['captcha_human']) ? trim((string) $post['captcha_human']) : '';
    if ($raw === '' || !preg_match('/^\d{1,3}$/', $raw)) {
        return false;
    }

    return (int) $raw === $expected;
}

/**
 * @param string $s
 * @return string
 */
function shms_contact_strip_header_injection($s)
{
    return str_replace(array("\r", "\n", '%0a', '%0d', '%0A', '%0D'), '', (string) $s);
}

/**
 * @return array
 */
function shms_contact_categories()
{
    return array(
        '' => 'Select a topic',
        'registration' => 'Registration & fees',
        'abstract' => 'Abstract / paper submission',
        'venue' => 'Venue, travel & accommodation',
        'programme' => 'Programme & schedule',
        'committees' => 'Committees & speakers',
        'sponsorship' => 'Sponsorship & exhibition',
        'general' => 'General enquiry',
        'other' => 'Other',
    );
}

/**
 * @return array
 */
function shms_contact_roles()
{
    return array(
        '' => 'Select your role (optional)',
        'delegate' => 'Delegate / attendee',
        'author' => 'Presenting author',
        'student' => 'Student',
        'invited' => 'Invited speaker / guest',
        'sponsor' => 'Sponsor / exhibitor',
        'media' => 'Media',
        'other' => 'Other',
    );
}

/**
 * @return array
 */
function shms_contact_urgency()
{
    return array(
        '' => 'Choose reply timing',
        'normal' => 'Standard (we reply within a few working days)',
        'soon' => 'Time-sensitive (before the conference)',
        'urgent' => 'Urgent (conference week / on-site)',
    );
}

/**
 * @param string $key
 * @param array $map
 * @return bool
 */
function shms_contact_valid_map_key($key, $map)
{
    return is_string($key) && $key !== '' && isset($map[$key]);
}

/**
 * @param array $post
 * @return array{0: array, 1: array} errors, values
 */
function shms_contact_validate_and_collect($post)
{
    $errors = array();
    $v = array();

    $fn = isset($post['first_name']) ? trim((string) $post['first_name']) : '';
    $ln = isset($post['last_name']) ? trim((string) $post['last_name']) : '';
    if ($fn === '') {
        $errors[] = 'Please enter your first name.';
    }
    if ($ln === '') {
        $errors[] = 'Please enter your last name.';
    }
    $v['first_name'] = $fn;
    $v['last_name'] = $ln;

    $email = isset($post['email']) ? trim((string) $post['email']) : '';
    if ($email === '' || !function_exists('filter_var') || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    $v['email'] = $email;

    $cats = shms_contact_categories();
    $cat = isset($post['contact_category']) ? (string) $post['contact_category'] : '';
    if (!shms_contact_valid_map_key($cat, $cats)) {
        $errors[] = 'Please choose a topic for your message.';
    }
    $v['contact_category'] = $cat;

    $roles = shms_contact_roles();
    $role = isset($post['contact_role']) ? (string) $post['contact_role'] : '';
    if ($role !== '' && !isset($roles[$role])) {
        $errors[] = 'Invalid role selection.';
        $role = '';
    }
    $v['contact_role'] = $role;

    $urg = shms_contact_urgency();
    $ug = isset($post['contact_urgency']) ? (string) $post['contact_urgency'] : '';
    if (!shms_contact_valid_map_key($ug, $urg)) {
        $errors[] = 'Please choose how soon you need a reply.';
    }
    $v['contact_urgency'] = $ug;

    $subj = isset($post['subject_line']) ? trim((string) $post['subject_line']) : '';
    if ($subj === '') {
        $errors[] = 'Please enter a short subject.';
    } elseif (strlen($subj) > 200) {
        $errors[] = 'Subject is too long (200 characters max).';
    }
    $v['subject_line'] = $subj;

    $msg = isset($post['message']) ? trim((string) $post['message']) : '';
    if ($msg === '') {
        $errors[] = 'Please enter your message.';
    } elseif (strlen($msg) > 12000) {
        $errors[] = 'Message is too long. Please shorten it or email us directly.';
    }
    $v['message'] = $msg;

    return array($errors, $v);
}

/**
 * @param array $v
 * @return bool
 */
function shms_contact_send_mail($v)
{
    $to = shms_contact_mail_to();
    $cats = shms_contact_categories();
    $roles = shms_contact_roles();
    $urg = shms_contact_urgency();

    $catLabel = isset($cats[$v['contact_category']]) ? $cats[$v['contact_category']] : $v['contact_category'];
    $roleLabel = ($v['contact_role'] !== '' && isset($roles[$v['contact_role']])) ? $roles[$v['contact_role']] : '—';
    $urgLabel = isset($urg[$v['contact_urgency']]) ? $urg[$v['contact_urgency']] : $v['contact_urgency'];

    $subLine = shms_contact_strip_header_injection($v['subject_line']);
    $subject = '[SHMS-2026 Contact] ' . $catLabel . ' — ' . $subLine;
    $subjectPlain = $subject;
    if (function_exists('mb_encode_mimeheader')) {
        $subject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n");
    }

    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    $body = "New message from the SHMS-2026 website contact form.\r\n\r\n";
    $body .= "Name: " . $v['first_name'] . ' ' . $v['last_name'] . "\r\n";
    $body .= "Email (form): " . $v['email'] . "\r\n";
    $body .= "Reply-To header: " . $v['email'] . " — use “Reply” in your mail app to reach the visitor.\r\n";
    $body .= "Topic: " . $catLabel . "\r\n";
    $body .= "Role: " . $roleLabel . "\r\n";
    $body .= "When you need a reply: " . $urgLabel . "\r\n";
    $body .= "Subject: " . $subLine . "\r\n\r\n";
    $body .= "Message:\r\n" . str_replace("\r\n", "\n", $v['message']) . "\r\n\r\n";
    $body .= "---\r\nIP: " . $ip . "\r\nUA: " . $ua . "\r\n";

    return shms_contact_dispatch_mail($to, $subjectPlain, $subject, $body, $v['email']);
}

/**
 * Plain UTF-8 mail (trial / diagnostics). Same From / envelope / log as the contact form.
 *
 * @param string $to
 * @param string $subject
 * @param string $body
 * @param string $replyTo optional valid email for Reply-To header
 * @return bool
 */
function shms_contact_send_simple($to, $subject, $body, $replyTo = '')
{
    $to = trim((string) $to);
    if ($to === '' || !function_exists('filter_var') || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $sub = shms_contact_strip_header_injection($subject);
    if ($sub === '') {
        $sub = '(no subject)';
    }
    $subjectPlain = $sub;
    if (function_exists('mb_encode_mimeheader')) {
        $sub = mb_encode_mimeheader($sub, 'UTF-8', 'B', "\r\n");
    }

    return shms_contact_dispatch_mail($to, $subjectPlain, $sub, $body, $replyTo);
}

/**
 * Process POST; on success redirects and exits.
 *
 * @return array|null null if not a form POST; else result with keys success, errors, values
 */
function shms_contact_handle_request()
{
    if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null;
    }
    if (!isset($_POST['shms_contact_submit'])) {
        return null;
    }

    shms_contact_session_start();

    // Honeypot: must stay empty. Do not use names like "company_website" — password managers autofill them
    // and the user gets a fake "Message sent" with no e-mail or Sheet row.
    $hp = isset($_POST['shms_trhp']) ? trim((string) $_POST['shms_trhp']) : '';
    if ($hp !== '') {
        header('Location: contact.php?sent=1#message-us', true, 303);
        exit;
    }

    $token = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
    if (!isset($_SESSION['shms_csrf_contact']) || $token === '' || $token !== $_SESSION['shms_csrf_contact']) {
        return array(
            'success' => false,
            'errors' => array('Your session expired. Please reload the page and try again.'),
            'values' => array(),
        );
    }

    if (!shms_contact_captcha_validate($_POST)) {
        list($errors, $values) = shms_contact_validate_and_collect($_POST);
        array_unshift($errors, 'Please solve the security check (arithmetic question) correctly.');
        return array('success' => false, 'errors' => $errors, 'values' => $values);
    }

    list($errors, $values) = shms_contact_validate_and_collect($_POST);
    if (count($errors) > 0) {
        return array('success' => false, 'errors' => $errors, 'values' => $values);
    }

    $last = isset($_SESSION['shms_contact_last']) ? (int) $_SESSION['shms_contact_last'] : 0;
    if ($last > 0 && (time() - $last) < 50) {
        return array(
            'success' => false,
            'errors' => array('Please wait a minute before sending another message.'),
            'values' => $values,
        );
    }

    $backend = shms_contact_form_backend();
    if ($backend === 'sheet_browser' && !shms_contact_sheet_browser_ready()) {
        return array(
            'success' => false,
            'errors' => array(
                'Contact delivery is not configured: set sheet_webhook_url and a token of 12+ characters. Use sheet_browser_token (same as Apps Script BROWSER_TOKEN if set), or sheet_webhook_secret (same as WEBHOOK_SECRET) with sheet_browser_token left empty. Redeploy the Web app after updating includes/google-sheet-webhook-apps-script.txt.',
            ),
            'values' => $values,
        );
    }

    if ($backend === 'sheet' && !shms_contact_sheet_curl_available() && !shms_contact_sheet_delivery_via_browser()) {
        return array(
            'success' => false,
            'errors' => array(
                'This server has no PHP cURL. For Sheet delivery, set sheet_webhook_url and sheet_webhook_secret (12+ characters, matching Apps Script WEBHOOK_SECRET). The site will send via the visitor browser automatically; redeploy the Apps Script so browser posts accept that secret when BROWSER_TOKEN is not set. See DEPLOY-MNNIT.md.',
            ),
            'values' => $values,
        );
    }

    if (shms_contact_sheet_delivery_via_browser()) {
        $_SESSION['shms_contact_last'] = time();
        unset($_SESSION['shms_csrf_contact']);
        $payload = shms_contact_sheet_webhook_payload_array($values);
        $payload['sheet_browser'] = true;
        $payload['browser_token'] = shms_contact_sheet_browser_token_for_payload();
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return array(
                'success' => false,
                'errors' => array('Could not prepare your message. Please try again or email the secretariat directly.'),
                'values' => $values,
            );
        }
        return array(
            'sheet_browser_pending' => true,
            'sheet_browser_url' => shms_contact_sheet_webhook_url(),
            'sheet_browser_payload_b64' => base64_encode($json),
            'values' => array(),
        );
    }

    if (shms_contact_dispatch_submission($values)) {
        $_SESSION['shms_contact_last'] = time();
        unset($_SESSION['shms_csrf_contact']);
        header('Location: contact.php?sent=1#message-us', true, 303);
        exit;
    }

    $hint = 'We could not send your message. Please email ' . shms_contact_mail_to() . ' directly.';
    if (shms_contact_form_backend() === 'sheet') {
        $diag = isset($GLOBALS['shms_sheet_webhook_diag']) ? (string) $GLOBALS['shms_sheet_webhook_diag'] : '';
        $curlErr = isset($GLOBALS['shms_sheet_webhook_curl_errno']) ? (int) $GLOBALS['shms_sheet_webhook_curl_errno'] : 0;
        if ($diag === 'no_curl' || !shms_contact_sheet_curl_available()) {
            $hint = 'PHP cURL is not enabled (curl_init is missing). Either enable cURL in php.ini and restart the web server, or rely on automatic browser delivery: keep form_backend sheet with sheet_webhook_url and sheet_webhook_secret (12+ characters) and redeploy Apps Script from includes/google-sheet-webhook-apps-script.txt (browser_token may match WEBHOOK_SECRET). Visitors need JavaScript on contact.php.';
        } elseif ($diag === 'curl_exec' && $curlErr === 60) {
            $hint = 'SSL certificate verification failed (cURL error 60). On Windows, ensure tools/cacert.pem exists in this project, or set curl.cainfo in php.ini to a CA bundle from https://curl.se/ca/cacert.pem — or set sheet_webhook_cainfo in includes/config.php to the full path of that file. Then restart PHP.';
        } elseif ($diag === 'curl_exec' && $curlErr === 28) {
            $hint = 'Connection to Google timed out (cURL error 28). The web server may block or delay outbound HTTPS to script.google.com. If this began after PHP cURL was enabled, set sheet_webhook_use_php_curl => false in includes/config.php so the visitor’s browser sends the webhook (same as before). Or use form_backend sheet_browser. Otherwise ask IT to allow outbound TLS to script.google.com and script.googleusercontent.com.';
        } elseif ($diag === 'curl_exec' && $curlErr !== 0) {
            $hint = 'Could not reach Google Apps Script (cURL error ' . $curlErr . '). Check firewall and proxy: outbound TLS to https://script.google.com must be allowed. If cURL was recently enabled, try sheet_webhook_use_php_curl => false in includes/config.php to use the visitor’s browser instead.';
        } elseif ($diag === 'empty_body' || $diag === 'bad_json') {
            $hint = 'The webhook URL returned an unexpected response. Confirm sheet_webhook_url is the current Web app /exec URL, redeploy the script after edits, and set Web app access to Anyone.';
        } elseif ($diag === 'rejected') {
            $hint = 'The webhook refused the request. Ensure sheet_webhook_secret in includes/config.php matches the WEBHOOK_SECRET Apps Script property exactly.';
        } elseif ($diag === 'bad_config') {
            $hint = 'Sheet webhook is not configured: set sheet_webhook_url and sheet_webhook_secret (12+ characters) in includes/config.php.';
        } else {
            $hint = 'We could not record your message. Check includes/config.php (sheet_webhook_url, sheet_webhook_secret), PHP cURL, outbound HTTPS to script.google.com, and the Apps Script web app (includes/google-sheet-webhook-apps-script.txt).';
        }
    } elseif (shms_contact_smtp_settings() !== null) {
        $hint = 'SMTP send failed. Check smtp_host, smtp_user, and smtp_pass in includes/config.php (Google App Password if 2FA is on).';
    }

    return array(
        'success' => false,
        'errors' => array($hint),
        'values' => $values,
    );
}

/**
 * @param string $s
 * @return string
 */
function shms_contact_h($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/**
 * @param string $name
 * @param array $options value => label
 * @param string $selected
 * @param bool $required
 * @return string
 */
function shms_contact_select_html($name, $options, $selected, $required = true)
{
    $reqAttr = $required ? ' required' : '';
    $out = '<select class="shms-contact-field shms-contact-select" id="' . shms_contact_h($name) . '" name="' . shms_contact_h($name) . '"' . $reqAttr . '>';
    foreach ($options as $val => $label) {
        $valStr = (string) $val;
        $sel = ($valStr === $selected) ? ' selected' : '';
        $out .= '<option value="' . shms_contact_h($valStr) . '"' . $sel . '>' . shms_contact_h($label) . '</option>';
    }
    $out .= '</select>';
    return $out;
}
