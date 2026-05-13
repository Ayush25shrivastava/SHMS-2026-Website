<?php
/**
 * Quick check: is PHP cURL available? (Same runtime as the website when served via the web server.)
 * Open in a browser or run: php tools/php-curl-check.php
 * Remove or protect this file on production if you do not want paths visible.
 */
if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/plain; charset=UTF-8');
}
echo "SHMS PHP / cURL check\n";
echo 'PHP version: ' . PHP_VERSION . "\n";
echo 'curl_init available: ' . (function_exists('curl_init') ? 'YES' : 'NO — enable extension in php.ini and restart the server') . "\n";
if (function_exists('php_ini_loaded_file')) {
    $ini = php_ini_loaded_file();
    echo 'Loaded php.ini: ' . ($ini ? $ini : '(none)') . "\n";
}
$caIni = ini_get('curl.cainfo');
echo 'curl.cainfo (php.ini): ' . ($caIni !== '' && $caIni !== false ? $caIni : '(not set — Sheet webhook uses tools/cacert.pem if present)') . "\n";
$bundled = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cacert.pem';
echo 'tools/cacert.pem: ' . (is_readable($bundled) ? 'present (' . filesize($bundled) . ' bytes)' : 'missing') . "\n";
