<?php
/**
 * Optional SMTP (e.g. smtp.gmail.com:587 + STARTTLS) for contact mail when PHP mail() is unavailable.
 * Configure in includes/config.php under contact.* — see config.sample.php.
 */
if (!function_exists('shms_app_config')) {
    require_once dirname(__FILE__) . '/init.php';
}

/**
 * @return array|null host, port, user, pass, encryption (tls|ssl|''), timeout
 */
function shms_contact_smtp_settings()
{
    $cfg = shms_app_config();
    if (!is_array($cfg) || !isset($cfg['contact']) || !is_array($cfg['contact'])) {
        return null;
    }
    $c = $cfg['contact'];
    $host = isset($c['smtp_host']) ? trim((string) $c['smtp_host']) : '';
    if ($host === '') {
        return null;
    }
    $port = isset($c['smtp_port']) ? (int) $c['smtp_port'] : 587;
    if ($port <= 0 || $port > 65535) {
        $port = 587;
    }
    $user = isset($c['smtp_user']) ? trim((string) $c['smtp_user']) : '';
    $pass = isset($c['smtp_pass']) ? (string) $c['smtp_pass'] : '';
    if ($user === '' && function_exists('getenv')) {
        $ev = getenv('SHMS_SMTP_USER');
        if ($ev !== false && trim((string) $ev) !== '') {
            $user = trim((string) $ev);
        }
    }
    if ($pass === '' && function_exists('getenv')) {
        $ev = getenv('SHMS_SMTP_PASS');
        if ($ev !== false) {
            $pass = (string) $ev;
        }
    }
    if ($user === '' || $pass === '') {
        return null;
    }
    $enc = isset($c['smtp_encryption']) ? strtolower(trim((string) $c['smtp_encryption'])) : 'tls';
    if ($enc === 'none' || $enc === 'off') {
        $enc = '';
    } elseif ($enc !== 'ssl' && $enc !== 'tls') {
        $enc = 'tls';
    }
    $timeout = isset($c['smtp_timeout']) ? (int) $c['smtp_timeout'] : 25;
    if ($timeout < 5) {
        $timeout = 25;
    }

    return array(
        'host' => $host,
        'port' => $port,
        'user' => $user,
        'pass' => $pass,
        'encryption' => $enc,
        'timeout' => $timeout,
    );
}

/**
 * @param resource $fp
 * @return string
 */
function shms_contact_smtp_read_response($fp)
{
    $buf = '';
    while (!feof($fp)) {
        $line = @fgets($fp, 8192);
        if ($line === false) {
            break;
        }
        $buf .= $line;
        if (strlen($line) >= 4 && $line[3] === ' ') {
            break;
        }
    }
    return $buf;
}

/**
 * @param string $resp
 * @return int
 */
function shms_contact_smtp_code($resp)
{
    if (strlen($resp) < 3) {
        return 0;
    }
    return (int) substr($resp, 0, 3);
}

/**
 * @param string $email
 * @return string
 */
function shms_contact_smtp_angle_addr($email)
{
    $email = trim((string) $email);
    if ($email === '') {
        return '';
    }
    return '<' . str_replace(array('>', '<'), '', $email) . '>';
}

/**
 * Dot-stuff per RFC 5321.
 *
 * @param string $body
 * @return string
 */
function shms_contact_smtp_dot_stuff($body)
{
    $lines = preg_split("/\r\n|\n|\r/", (string) $body);
    if (!is_array($lines)) {
        return str_replace("\n", "\r\n", (string) $body);
    }
    $out = array();
    foreach ($lines as $ln) {
        if (isset($ln[0]) && $ln[0] === '.') {
            $out[] = '.' . $ln;
        } else {
            $out[] = $ln;
        }
    }
    return implode("\r\n", $out);
}

/**
 * @param string $to
 * @param string $subjectHeader MIME-safe subject line value (no "Subject:" prefix)
 * @param string $body
 * @param string $fromEmail
 * @param string $replyTo
 * @param string $errOut filled on failure
 * @return bool
 */
function shms_contact_smtp_send($to, $subjectHeader, $body, $fromEmail, $replyTo, &$errOut)
{
    $errOut = '';
    $s = shms_contact_smtp_settings();
    if ($s === null) {
        $errOut = 'SMTP not configured';
        return false;
    }

    if (!function_exists('stream_socket_client')) {
        $errOut = 'stream_socket_client unavailable';
        return false;
    }

    if (($s['encryption'] === 'tls' || $s['encryption'] === 'ssl') && !extension_loaded('openssl')) {
        $errOut = 'PHP openssl extension required for SMTP TLS/SSL';
        return false;
    }

    $host = $s['host'];
    $port = $s['port'];

    if ($s['encryption'] === 'ssl') {
        $remote = 'ssl://' . $host . ':' . $port;
        $fp = @stream_socket_client(
            $remote,
            $errno,
            $errstr,
            $s['timeout'],
            STREAM_CLIENT_CONNECT
        );
    } else {
        $remote = $host . ':' . $port;
        $fp = @stream_socket_client(
            'tcp://' . $remote,
            $errno,
            $errstr,
            $s['timeout'],
            STREAM_CLIENT_CONNECT
        );
    }

    if (!is_resource($fp)) {
        $errOut = 'connect failed: ' . $errstr . ' (' . $errno . ')';
        return false;
    }

    stream_set_timeout($fp, $s['timeout']);

    $read = function () use ($fp) {
        return shms_contact_smtp_read_response($fp);
    };
    $write = function ($cmd) use ($fp) {
        return @fwrite($fp, $cmd . "\r\n") !== false;
    };

    $resp = $read();
    if (shms_contact_smtp_code($resp) !== 220) {
        $errOut = 'no greeting: ' . trim($resp);
        fclose($fp);
        return false;
    }

    if (!$write('EHLO shms-local')) {
        $errOut = 'write failed';
        fclose($fp);
        return false;
    }
    $resp = $read();
    if (shms_contact_smtp_code($resp) !== 250) {
        $errOut = 'EHLO: ' . trim($resp);
        fclose($fp);
        return false;
    }

    if ($s['encryption'] === 'tls') {
        if (!$write('STARTTLS')) {
            $errOut = 'STARTTLS write failed';
            fclose($fp);
            return false;
        }
        $resp = $read();
        if (shms_contact_smtp_code($resp) !== 220) {
            $errOut = 'STARTTLS: ' . trim($resp);
            fclose($fp);
            return false;
        }
        $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
        if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
            $cryptoMethod = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
        }
        if (!@stream_socket_enable_crypto($fp, true, $cryptoMethod)) {
            $errOut = 'TLS handshake failed';
            fclose($fp);
            return false;
        }
        if (!$write('EHLO shms-local')) {
            $errOut = 'EHLO after TLS failed';
            fclose($fp);
            return false;
        }
        $resp = $read();
        if (shms_contact_smtp_code($resp) !== 250) {
            $errOut = 'EHLO2: ' . trim($resp);
            fclose($fp);
            return false;
        }
    }

    if (!$write('AUTH LOGIN')) {
        $errOut = 'AUTH write failed';
        fclose($fp);
        return false;
    }
    $resp = $read();
    if (shms_contact_smtp_code($resp) !== 334) {
        $errOut = 'AUTH LOGIN: ' . trim($resp);
        fclose($fp);
        return false;
    }

    if (!$write(base64_encode($s['user']))) {
        fclose($fp);
        $errOut = 'user send failed';
        return false;
    }
    $resp = $read();
    if (shms_contact_smtp_code($resp) !== 334) {
        $errOut = 'auth user rejected: ' . trim($resp);
        fclose($fp);
        return false;
    }

    if (!$write(base64_encode($s['pass']))) {
        fclose($fp);
        $errOut = 'pass send failed';
        return false;
    }
    $resp = $read();
    if (shms_contact_smtp_code($resp) !== 235) {
        $errOut = 'auth failed: ' . trim($resp);
        fclose($fp);
        return false;
    }

    $fromBr = shms_contact_smtp_angle_addr($fromEmail);
    if ($fromBr === '') {
        $fromBr = shms_contact_smtp_angle_addr($s['user']);
    }
    if (!$write('MAIL FROM:' . $fromBr)) {
        fclose($fp);
        $errOut = 'MAIL FROM write failed';
        return false;
    }
    $resp = $read();
    if (shms_contact_smtp_code($resp) !== 250) {
        $errOut = 'MAIL FROM: ' . trim($resp);
        fclose($fp);
        return false;
    }

    $toBr = shms_contact_smtp_angle_addr($to);
    if (!$write('RCPT TO:' . $toBr)) {
        fclose($fp);
        $errOut = 'RCPT write failed';
        return false;
    }
    $resp = $read();
    if (shms_contact_smtp_code($resp) !== 250 && shms_contact_smtp_code($resp) !== 251) {
        $errOut = 'RCPT TO: ' . trim($resp);
        fclose($fp);
        return false;
    }

    if (!$write('DATA')) {
        fclose($fp);
        $errOut = 'DATA write failed';
        return false;
    }
    $resp = $read();
    if (shms_contact_smtp_code($resp) !== 354) {
        $errOut = 'DATA: ' . trim($resp);
        fclose($fp);
        return false;
    }

    $date = gmdate('D, d M Y H:i:s') . ' +0000';
    $hdr = '';
    $hdr .= 'Date: ' . $date . "\r\n";
    $hdr .= 'To: ' . $toBr . "\r\n";
    $hdr .= 'From: SHMS-2026 Web ' . $fromBr . "\r\n";
    $rt = trim((string) $replyTo);
    if ($rt !== '' && function_exists('filter_var') && filter_var($rt, FILTER_VALIDATE_EMAIL)) {
        $hdr .= 'Reply-To: ' . $rt . "\r\n";
    }
    $hdr .= 'Subject: ' . str_replace(array("\r", "\n"), ' ', (string) $subjectHeader) . "\r\n";
    $hdr .= "MIME-Version: 1.0\r\n";
    $hdr .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $hdr .= "Content-Transfer-Encoding: 8bit\r\n";
    $hdr .= 'X-Mailer: PHP/' . phpversion() . " (SHMS-2026 SMTP)\r\n";
    $hdr .= "\r\n";
    $payload = $hdr . shms_contact_smtp_dot_stuff($body) . "\r\n.\r\n";

    if (@fwrite($fp, $payload) === false) {
        fclose($fp);
        $errOut = 'body write failed';
        return false;
    }

    $resp = $read();
    if (shms_contact_smtp_code($resp) !== 250) {
        $errOut = 'after DATA: ' . trim($resp);
        fclose($fp);
        return false;
    }

    $write('QUIT');
    fclose($fp);
    return true;
}
