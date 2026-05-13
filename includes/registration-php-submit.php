<?php
/**
 * Server-side registration + receipt → Google Apps Script (JSON POST).
 * Prefers file_get_contents + stream_context when OpenSSL and allow_url_fopen are available.
 * Otherwise uses PHP cURL if curl_init exists (HTTPS without loading ext-openssl is possible on some Windows builds).
 * Stream path: follow_location off; redirects re-POST the same JSON to Location.
 */
if (!defined('SHMS_INIT_LOADED')) {
    require_once dirname(__FILE__) . '/init.php';
}

/**
 * CA bundle for HTTPS to script.google.com (same sources as contact sheet webhook).
 *
 * @return string readable path or empty
 */
function shms_registration_ssl_ca_bundle_path()
{
    $cfg = shms_app_config();
    if (is_array($cfg) && isset($cfg['registration']) && is_array($cfg['registration']) && isset($cfg['registration']['ca_bundle'])) {
        $p = trim((string) $cfg['registration']['ca_bundle']);
        if ($p !== '' && @is_readable($p)) {
            return $p;
        }
    }
    if (is_array($cfg) && isset($cfg['contact']) && is_array($cfg['contact']) && isset($cfg['contact']['sheet_webhook_cainfo'])) {
        $p = trim((string) $cfg['contact']['sheet_webhook_cainfo']);
        if ($p !== '' && @is_readable($p)) {
            return $p;
        }
    }
    $cacert = dirname(dirname(__FILE__)) . '/tools/cacert.pem';
    if (@is_readable($cacert)) {
        return $cacert;
    }
    $iniCa = ini_get('openssl.cafile');
    if ($iniCa !== false && $iniCa !== '' && @is_readable($iniCa)) {
        return $iniCa;
    }
    return '';
}

/**
 * Local testing only: skip TLS peer verify (never use on production).
 *
 * @return bool
 */
function shms_registration_insecure_ssl_allowed()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['registration']) || !is_array($cfg['registration'])) {
        return false;
    }
    return !empty($cfg['registration']['insecure_ssl']);
}

/**
 * Optional HTTP(S) proxy for outbound registration POST (corporate networks). e.g. http://proxy:8080
 *
 * @return string
 */
function shms_registration_outbound_proxy_url()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['registration']) || !is_array($cfg['registration'])) {
        return '';
    }
    $p = isset($cfg['registration']['outbound_proxy']) ? trim((string) $cfg['registration']['outbound_proxy']) : '';
    if ($p === '' || stripos($p, 'http') !== 0) {
        return '';
    }
    return $p;
}

/**
 * Append transport failure line to registration.transport_diag_log (writable path) if configured.
 *
 * @param string $line
 * @return void
 */
function shms_registration_transport_diag_append($line)
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['registration']) || !is_array($cfg['registration'])) {
        return;
    }
    $path = isset($cfg['registration']['transport_diag_log']) ? trim((string) $cfg['registration']['transport_diag_log']) : '';
    if ($path === '') {
        return;
    }
    $dir = dirname($path);
    if ($dir !== '' && $dir !== '.' && !is_dir($dir)) {
        return;
    }
    @file_put_contents($path, gmdate('c') . ' ' . $line . "\n", FILE_APPEND | LOCK_EX);
}

/**
 * Map low-level transport diagnostics to a user-facing error key (see registration-form-block.php).
 *
 * @param string $diag
 * @return string
 */
function shms_registration_transport_user_error($diag)
{
    $diag = (string) $diag;
    if ($diag === '') {
        return 'http_failed';
    }
    if (preg_match('/curl_errno_(\d+)/', $diag, $em)) {
        $ce = (int) $em[1];
        if ($ce === 6 || $ce === 7 || $ce === 28 || $ce === 9 || $ce === 52 || $ce === 56) {
            return 'google_unreachable';
        }
        if ($ce === 35 || $ce === 60) {
            return shms_registration_insecure_ssl_allowed() ? 'curl_tls_handshake' : 'ssl_failed';
        }
    }
    $lower = strtolower($diag);
    if (strpos($lower, 'failed to connect') !== false) {
        return 'google_unreachable';
    }
    if (strpos($lower, 'connection timed out') !== false || strpos($lower, 'operation timed out') !== false) {
        return 'google_unreachable';
    }
    if (strpos($lower, 'could not resolve host') !== false || strpos($lower, 'could not resolve') !== false) {
        return 'google_unreachable';
    }
    if (strpos($lower, 'unable to connect') !== false || strpos($lower, 'connection refused') !== false) {
        return 'google_unreachable';
    }
    if (strpos($lower, 'network is unreachable') !== false) {
        return 'google_unreachable';
    }
    if (strpos($lower, 'empty reply') !== false || strpos($lower, 'empty_body') !== false) {
        return 'google_empty_response';
    }
    if (strpos($diag, 'curl_bad_response') !== false || $diag === 'bad_redirect' || $diag === 'too_many_redirects') {
        return 'transport_malformed';
    }
    if (strpos($diag, 'http_0') !== false) {
        return 'transport_http_parse';
    }
    if (preg_match('/http_(\d{3})/', $diag, $hm)) {
        $c = (int) $hm[1];
        if ($c === 401 || $c === 403) {
            return 'google_access_denied';
        }
        if ($c === 404) {
            return 'google_not_found';
        }
        if ($c === 429) {
            return 'google_rate_limited';
        }
        if ($c >= 500 && $c < 600) {
            return 'google_server_error';
        }
        if ($c >= 400) {
            return 'google_client_error';
        }
    }
    return 'http_failed';
}

/**
 * Split raw cURL response when CURLOPT_HEADER is true (fallback if CURLINFO_HEADER_SIZE is wrong).
 *
 * @param string $raw
 * @return array{header:string,body:string}|null
 */
function shms_registration_curl_split_headers_body($raw)
{
    if (!is_string($raw) || $raw === '') {
        return null;
    }
    $pos = strpos($raw, "\r\n\r\n");
    $sepLen = 4;
    if ($pos === false) {
        $pos = strpos($raw, "\n\n");
        $sepLen = 2;
    }
    if ($pos === false) {
        return null;
    }
    return array(
        'header' => substr($raw, 0, $pos),
        'body' => substr($raw, $pos + $sepLen),
    );
}

/**
 * Resolve a redirect Location header to an absolute URL.
 *
 * @param string $location
 * @param string $baseUrl
 * @return string
 */
function shms_registration_resolve_redirect_url($location, $baseUrl)
{
    $location = trim($location);
    if ($location === '') {
        return '';
    }
    if (stripos($location, 'http://') === 0 || stripos($location, 'https://') === 0) {
        return $location;
    }
    if (strlen($location) >= 2 && $location[0] === '/' && $location[1] === '/') {
        return 'https:' . $location;
    }
    $base = parse_url($baseUrl);
    if ($base === false || !isset($base['scheme']) || !isset($base['host'])) {
        return '';
    }
    $scheme = $base['scheme'];
    $host = $base['host'];
    $port = isset($base['port']) ? ':' . $base['port'] : '';
    if (isset($location[0]) && $location[0] === '/') {
        return $scheme . '://' . $host . $port . $location;
    }
    $path = isset($base['path']) ? $base['path'] : '/';
    $dir = dirname($path);
    if ($dir === '.' || $dir === '\\') {
        $dir = '/';
    }
    return $scheme . '://' . $host . $port . rtrim(str_replace('\\', '/', $dir), '/') . '/' . $location;
}

/**
 * Parse status code and Location from $http_response_header (after file_get_contents http wrapper).
 *
 * @param array $http_response_header
 * @return array{code:int,location:string}
 */
function shms_registration_parse_http_response_header($http_response_header)
{
    $code = 0;
    $location = '';
    if (!is_array($http_response_header) || empty($http_response_header)) {
        return array('code' => 0, 'location' => '');
    }
    if (preg_match('/HTTP\/[^\s]+\s+(\d+)/', $http_response_header[0], $m)) {
        $code = (int) $m[1];
    }
    foreach ($http_response_header as $line) {
        if (stripos($line, 'Location:') === 0) {
            $location = trim(substr($line, 9));
            break;
        }
    }
    return array('code' => $code, 'location' => $location);
}

/**
 * Single POST; follow_location => 0. Parses $http_response_header in this same scope (PHP requirement).
 *
 * @param string $url
 * @param string $body
 * @param string $diagOut
 * @return array|false array('body'=>string,'code'=>int,'location'=>string) or false on transport failure
 */
function shms_registration_file_get_post_once($url, $body, &$diagOut)
{
    $diagOut = '';
    $len = strlen($body);
    $ca = shms_registration_ssl_ca_bundle_path();
    $insecure = shms_registration_insecure_ssl_allowed();
    $ssl = array();
    if ($insecure) {
        $ssl['verify_peer'] = false;
        if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 50600) {
            $ssl['verify_peer_name'] = false;
        }
    } else {
        $ssl['verify_peer'] = true;
        if ($ca !== '') {
            $ssl['cafile'] = $ca;
        }
        if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 50600) {
            $ssl['verify_peer_name'] = true;
        }
    }
    $http = array(
        'method' => 'POST',
        'header' =>
            "Content-Type: application/json; charset=UTF-8\r\n" .
            'Content-Length: ' . $len . "\r\n" .
            "User-Agent: SHMS-Registration-PHP/1.3\r\n" .
            "Connection: close\r\n",
        'content' => $body,
        'ignore_errors' => true,
        'follow_location' => 0,
    );
    $opts = array(
        'http' => $http,
        'ssl' => $ssl,
    );
    $oldTimeout = ini_get('default_socket_timeout');
    @ini_set('default_socket_timeout', '120');
    $ctx = stream_context_create($opts);
    $raw = @file_get_contents($url, false, $ctx);
    @ini_set('default_socket_timeout', $oldTimeout);
    $hdr = isset($http_response_header) && is_array($http_response_header) ? $http_response_header : array();
    $meta = shms_registration_parse_http_response_header($hdr);
    if ($raw === false) {
        $last = function_exists('error_get_last') ? error_get_last() : null;
        $diagOut = is_array($last) && isset($last['message']) ? (string) $last['message'] : 'file_get_failed';
        return false;
    }
    return array(
        'body' => $raw,
        'code' => $meta['code'],
        'location' => $meta['location'],
    );
}

/**
 * POST JSON; on 301–308 re-POST same body to Location (avoids wrapper turning redirect into GET).
 *
 * @param string $url
 * @param string $jsonBody
 * @param string $diagOut
 * @return string|false
 */
function shms_registration_https_post_json_file_get($url, $jsonBody, &$diagOut)
{
    $diagOut = '';
    $current = $url;
    $body = $jsonBody;
    $max = 8;
    for ($i = 0; $i < $max; $i++) {
        $res = shms_registration_file_get_post_once($current, $body, $diagOut);
        if ($res === false) {
            return false;
        }
        $code = $res['code'];
        $loc = $res['location'];
        $raw = $res['body'];
        if ($code >= 301 && $code <= 308 && $loc !== '') {
            $next = shms_registration_resolve_redirect_url($loc, $current);
            if ($next === '') {
                $diagOut = 'bad_redirect';
                return false;
            }
            $current = $next;
            continue;
        }
        if ($code >= 200 && $code < 300) {
            return $raw;
        }
        $diagOut = 'http_' . $code;
        if ($raw !== '') {
            $diagOut .= ':' . substr($raw, 0, 200);
        }
        return false;
    }
    $diagOut = 'too_many_redirects';
    return false;
}

/**
 * When true, POST to Apps Script with cURL first, then file_get_contents if cURL fails.
 *
 * @return bool
 */
function shms_registration_use_curl_first()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['registration']) || !is_array($cfg['registration'])) {
        return false;
    }
    return !empty($cfg['registration']['use_curl']);
}

/**
 * Apply TLS options for registration cURL (CA bundle or insecure_ssl).
 *
 * @param resource $ch
 * @return void
 */
function shms_registration_curl_apply_ssl($ch)
{
    $insecure = shms_registration_insecure_ssl_allowed();
    if ($insecure) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (defined('CURLOPT_SSL_VERIFYHOST')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        return;
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $ca = shms_registration_ssl_ca_bundle_path();
    if ($ca !== '') {
        curl_setopt($ch, CURLOPT_CAINFO, $ca);
    }
}

/**
 * POST JSON via cURL (primary when registration.use_curl is true, otherwise fallback).
 * Does not use CURLOPT_FOLLOWLOCATION for POST: libcurl may repeat the request as GET after302,
 * which breaks Apps Script /exec (same as stream wrapper: re-POST JSON to each Location).
 *
 * @param string $url
 * @param string $jsonBody
 * @param string $diagOut
 * @return string|false
 */
function shms_registration_https_post_json_curl($url, $jsonBody, &$diagOut)
{
    $diagOut = '';
    if (!function_exists('curl_init')) {
        $diagOut = 'no_curl';
        return false;
    }
    $current = $url;
    $body = $jsonBody;
    $max = 8;
    for ($i = 0; $i < $max; $i++) {
        $ch = curl_init($current);
        if ($ch === false) {
            $diagOut = 'curl_init_failed';
            return false;
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=UTF-8',
            'User-Agent: SHMS-Registration-PHP/1.3',
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 25);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        if (defined('CURL_HTTP_VERSION_1_1')) {
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        }
        if (defined('CURL_IPRESOLVE_V4')) {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        $proxyUrl = shms_registration_outbound_proxy_url();
        if ($proxyUrl !== '') {
            curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
        }
        shms_registration_curl_apply_ssl($ch);
        $raw = curl_exec($ch);
        $errno = (int) curl_errno($ch);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        if ($errno !== 0 || $raw === false) {
            $diagOut = 'curl_errno_' . $errno;
            return false;
        }
        if ($headerSize >= 1 && strlen($raw) >= $headerSize) {
            $header = substr($raw, 0, $headerSize);
            $responseBody = substr($raw, $headerSize);
        } else {
            $split = shms_registration_curl_split_headers_body($raw);
            if ($split === null) {
                $diagOut = 'curl_bad_response';
                return false;
            }
            $header = $split['header'];
            $responseBody = $split['body'];
        }
        $code = 0;
        if (preg_match('/HTTP\/[^\s]+\s+(\d+)/', $header, $m)) {
            $code = (int) $m[1];
        }
        $location = '';
        $headerNorm = str_replace("\r\n", "\n", $header);
        foreach (explode("\n", $headerNorm) as $line) {
            if (stripos($line, 'Location:') === 0) {
                $location = trim(substr($line, 9));
                break;
            }
        }
        if ($code >= 301 && $code <= 308 && $location !== '') {
            $next = shms_registration_resolve_redirect_url($location, $current);
            if ($next === '') {
                $diagOut = 'bad_redirect';
                return false;
            }
            $current = $next;
            continue;
        }
        if ($code >= 200 && $code < 300) {
            if ($responseBody === '') {
                $diagOut = 'empty_body:http_' . $code;
                return false;
            }
            return $responseBody;
        }
        if ($code === 0 && $responseBody !== '') {
            $trim = ltrim($responseBody);
            if ($trim !== '' && ($trim[0] === '{' || $trim[0] === '[')) {
                return $responseBody;
            }
        }
        $diagOut = 'http_' . $code;
        if ($responseBody !== '') {
            $diagOut .= ':' . substr($responseBody, 0, 200);
        }
        return false;
    }
    $diagOut = 'too_many_redirects';
    return false;
}

/**
 * POST JSON to Apps Script Web App /exec.
 *
 * @param array $payload
 * @return array{ok:bool,error:string,raw:string}
 */
function shms_registration_post_json_to_apps_script($payload)
{
    $url = shms_registration_post_url();
    if ($url === '') {
        return array('ok' => false, 'error' => 'not_configured', 'raw' => '');
    }
    $json = shms_json_encode($payload);
    if ($json === false) {
        return array('ok' => false, 'error' => 'bad_payload', 'raw' => '');
    }

    $canStream = extension_loaded('openssl') && ini_get('allow_url_fopen');
    $canCurl = function_exists('curl_init');
    $curlFirst = shms_registration_use_curl_first();

    $raw = false;
    $diag = '';
    $diagParts = array();
    $order = $curlFirst ? array('curl', 'stream') : array('stream', 'curl');
    foreach ($order as $method) {
        $partDiag = '';
        if ($method === 'curl' && $canCurl) {
            $raw = shms_registration_https_post_json_curl($url, $json, $partDiag);
            if ($partDiag !== '') {
                $diagParts[] = 'curl:' . $partDiag;
            }
            if ($raw !== false) {
                break;
            }
        } elseif ($method === 'stream' && $canStream) {
            $raw = shms_registration_https_post_json_file_get($url, $json, $partDiag);
            if ($partDiag !== '') {
                $diagParts[] = 'stream:' . $partDiag;
            }
            if ($raw !== false) {
                break;
            }
        }
    }
    if (!empty($diagParts)) {
        $diag = implode(' | ', $diagParts);
    }

    if ($raw === false) {
        if (!$canStream && !$canCurl) {
            return array('ok' => false, 'error' => 'https_unavailable', 'raw' => 'no_stream_no_curl');
        }
        shms_registration_transport_diag_append($diag !== '' ? $diag : '(empty diag)');
        $ca = shms_registration_ssl_ca_bundle_path();
        $insecure = shms_registration_insecure_ssl_allowed();
        $isSslDiag = (stripos($diag, 'SSL') !== false || stripos($diag, 'certificate') !== false || stripos($diag, 'Peer') !== false || strpos($diag, 'curl_errno_60') !== false);
        if (!$insecure && $ca === '' && $isSslDiag) {
            return array('ok' => false, 'error' => 'ssl_failed', 'raw' => $diag);
        }
        return array('ok' => false, 'error' => shms_registration_transport_user_error($diag), 'raw' => $diag);
    }

    $j = json_decode($raw, true);
    if (!is_array($j)) {
        return array('ok' => false, 'error' => 'bad_response', 'raw' => $raw);
    }
    if (!empty($j['ok'])) {
        return array('ok' => true, 'error' => '', 'raw' => $raw, 'api' => $j);
    }
    $err = isset($j['error']) ? trim((string) $j['error']) : 'rejected';
    return array('ok' => false, 'error' => $err !== '' ? $err : 'rejected', 'raw' => $raw);
}

/**
 * Validate POST + file upload and forward to Apps Script.
 *
 * @return array{ok:bool,error:string,raw:string}
 */
function shms_registration_process_submission()
{
    if (shms_registration_post_url() === '') {
        return array('ok' => false, 'error' => 'not_configured', 'raw' => '');
    }

    $first = isset($_POST['first_name']) ? trim((string) $_POST['first_name']) : '';
    $last = isset($_POST['last_name']) ? trim((string) $_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim((string) $_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim((string) $_POST['phone']) : '';
    $affiliation = isset($_POST['affiliation']) ? trim((string) $_POST['affiliation']) : '';
    $category = isset($_POST['category']) ? trim((string) $_POST['category']) : '';
    $transactionId = isset($_POST['transaction_id']) ? trim((string) $_POST['transaction_id']) : '';
    $amount = isset($_POST['amount']) ? trim((string) $_POST['amount']) : '';
    $paymentChannel = isset($_POST['payment_channel']) ? trim((string) $_POST['payment_channel']) : '';
    $notes = isset($_POST['notes']) ? trim((string) $_POST['notes']) : '';

    if ($first === '' || $last === '' || $email === '' || $phone === '' || $category === '' || $transactionId === '' || $paymentChannel === '') {
        return array('ok' => false, 'error' => 'missing_fields', 'raw' => '');
    }

    if (!isset($_FILES['receipt']) || !is_array($_FILES['receipt'])) {
        return array('ok' => false, 'error' => 'missing_file', 'raw' => '');
    }
    $f = $_FILES['receipt'];
    if (!isset($f['error']) || $f['error'] !== UPLOAD_ERR_OK) {
        return array('ok' => false, 'error' => 'upload_error', 'raw' => '');
    }
    $maxBytes = 5 * 1024 * 1024;
    if (isset($f['size']) && (int) $f['size'] > $maxBytes) {
        return array('ok' => false, 'error' => 'file_too_large', 'raw' => '');
    }
    $origName = isset($f['name']) ? (string) $f['name'] : 'receipt';
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $allowedExt = array('pdf' => 1, 'png' => 1, 'jpg' => 1, 'jpeg' => 1);
    if (!isset($allowedExt[$ext])) {
        return array('ok' => false, 'error' => 'invalid_file_type', 'raw' => '');
    }
    $mimeMap = array(
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
    );
    $mime = $mimeMap[$ext];

    if (!isset($f['tmp_name']) || !is_uploaded_file($f['tmp_name'])) {
        return array('ok' => false, 'error' => 'upload_error', 'raw' => '');
    }
    $bin = @file_get_contents($f['tmp_name']);
    if ($bin === false || $bin === '') {
        return array('ok' => false, 'error' => 'empty_file', 'raw' => '');
    }
    if (strlen($bin) > $maxBytes) {
        return array('ok' => false, 'error' => 'file_too_large', 'raw' => '');
    }

    $payload = array(
        'firstName' => $first,
        'lastName' => $last,
        'email' => $email,
        'phone' => $phone,
        'affiliation' => $affiliation,
        'category' => $category,
        'transactionId' => $transactionId,
        'amount' => $amount,
        'paymentChannel' => $paymentChannel,
        'notes' => $notes,
        'fileName' => $origName,
        'mimeType' => $mime,
        'fileBase64' => base64_encode($bin),
    );
    $sec = shms_registration_submit_secret();
    if ($sec !== '') {
        $payload['submitSecret'] = $sec;
    }

    return shms_registration_post_json_to_apps_script($payload);
}

/**
 * Parse amount field as INR numeric (digits and decimal); empty or invalid returns null.
 *
 * @param string $s
 * @return float|null
 */
function shms_registration_parse_inr_numeric($s)
{
    $s = trim((string) $s);
    if ($s === '') {
        return null;
    }
    $s = preg_replace('/[^\d.]/', '', str_replace(',', '', $s));
    if ($s === '' || !is_numeric($s)) {
        return null;
    }
    return (float) $s;
}

/**
 * Unique receipt / registration numbers when Apps Script does not return them (never reuse short hashes alone).
 *
 * @return array{receiptNo:string,registrationNo:string}
 */
function shms_registration_generate_fallback_receipt_numbers()
{
    $rand = '';
    if (function_exists('openssl_random_pseudo_bytes')) {
        $b = @openssl_random_pseudo_bytes(5);
        if ($b !== false && strlen($b) > 0) {
            $rand = bin2hex($b);
        }
    }
    if ($rand === '' && function_exists('random_bytes')) {
        try {
            $rand = bin2hex(random_bytes(5));
        } catch (Exception $e) {
            $rand = '';
        }
    }
    if ($rand === '') {
        $rand = strtoupper(substr(sha1(uniqid((string) mt_rand(), true)), 0, 10));
    }
    $stem = gmdate('YmdHis') . '-' . strtoupper($rand);

    return array(
        'receiptNo' => 'SHMS2026/R' . $stem,
        'registrationNo' => 'SHMS2026/REG-' . $stem,
    );
}

/**
 * Build receipt array for registration-receipt.php (session or browser JSON).
 *
 * @param array $fields POST or JS field names (snake or camelCase)
 * @param array $api Apps Script success JSON
 * @return array
 */
function shms_registration_build_receipt_data(array $fields, array $api = array())
{
    $first = '';
    if (isset($fields['first_name'])) {
        $first = trim((string) $fields['first_name']);
    } elseif (isset($fields['firstName'])) {
        $first = trim((string) $fields['firstName']);
    }
    $last = '';
    if (isset($fields['last_name'])) {
        $last = trim((string) $fields['last_name']);
    } elseif (isset($fields['lastName'])) {
        $last = trim((string) $fields['lastName']);
    }
    $email = isset($fields['email']) ? trim((string) $fields['email']) : '';
    $phone = isset($fields['phone']) ? trim((string) $fields['phone']) : '';
    $affiliation = isset($fields['affiliation']) ? trim((string) $fields['affiliation']) : '';
    $category = isset($fields['category']) ? trim((string) $fields['category']) : '';
    $tx = '';
    if (isset($fields['transaction_id'])) {
        $tx = trim((string) $fields['transaction_id']);
    } elseif (isset($fields['transactionId'])) {
        $tx = trim((string) $fields['transactionId']);
    }
    $amountRaw = isset($fields['amount']) ? trim((string) $fields['amount']) : '';
    $channel = '';
    if (isset($fields['payment_channel'])) {
        $channel = trim((string) $fields['payment_channel']);
    } elseif (isset($fields['paymentChannel'])) {
        $channel = trim((string) $fields['paymentChannel']);
    }
    $notes = isset($fields['notes']) ? trim((string) $fields['notes']) : '';

    $num = shms_registration_parse_inr_numeric($amountRaw);
    $sub = null;
    $gst = null;
    $grand = null;
    $gstRate = 18;
    if ($num !== null) {
        // Amount entered is the total paid, inclusive of 18% GST.
        $grand = round($num, 2);
        $divisor = 1 + ($gstRate / 100);
        $sub = round($grand / $divisor, 2);
        $gst = round($grand - $sub, 2);
    }

    $receiptNo = isset($api['receiptNo']) ? trim((string) $api['receiptNo']) : '';
    $regNo = isset($api['registrationNo']) ? trim((string) $api['registrationNo']) : '';
    if ($receiptNo === '') {
        $fb = shms_registration_generate_fallback_receipt_numbers();
        $receiptNo = $fb['receiptNo'];
        if ($regNo === '') {
            $regNo = $fb['registrationNo'];
        }
    }
    if ($regNo === '') {
        $regNo = $receiptNo;
    }

    $submittedAt = isset($api['submittedAt']) ? trim((string) $api['submittedAt']) : gmdate('Y-m-d H:i') . ' UTC';
    $fileUrl = isset($api['fileUrl']) ? trim((string) $api['fileUrl']) : '';
    $receiptPdfUrl = isset($api['receiptPdfUrl']) ? trim((string) $api['receiptPdfUrl']) : '';
    $receiptPdfError = isset($api['receiptPdfError']) ? trim((string) $api['receiptPdfError']) : '';

    return array(
        'receiptNo' => $receiptNo,
        'registrationNo' => $regNo,
        'fullName' => trim($first . ' ' . $last),
        'email' => $email,
        'phone' => $phone,
        'affiliation' => $affiliation,
        'category' => $category,
        'transactionId' => $tx,
        'amountDeclared' => $amountRaw,
        'paymentChannel' => $channel,
        'notes' => $notes,
        'subTotal' => $sub,
        'gstRate' => $gstRate,
        'gst' => $gst,
        'grandTotal' => $grand,
        'fileUrl' => $fileUrl,
        'receiptPdfUrl' => $receiptPdfUrl,
        'receiptPdfError' => $receiptPdfError,
        'submittedAt' => $submittedAt,
    );
}
