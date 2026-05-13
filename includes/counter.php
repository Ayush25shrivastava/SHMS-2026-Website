<?php
/**
 * File-based visitor counter (no JS, no DB).
 * PHP 5.2.7+ compatible.
 *
 * Stores a single integer in /data/visits.txt with flock() locking.
 */

/**
 * @return string absolute path to counter file
 */
function shms_counter_file_path()
{
    return dirname(dirname(__FILE__)) . '/data/visits.txt';
}

/**
 * Ensure the data directory exists.
 *
 * @return void
 */
function shms_counter_ensure_dir()
{
    $dir = dirname(shms_counter_file_path());
    if (is_dir($dir)) {
        return;
    }
    // Best-effort create (ignore failure; handled by read/write later).
    @mkdir($dir, 0755, true);
}

/**
 * Read current count (0 if missing/unreadable).
 *
 * @return int
 */
function shms_counter_get()
{
    $path = shms_counter_file_path();
    if (!is_readable($path)) {
        return 0;
    }
    $raw = @file_get_contents($path);
    if ($raw === false) {
        return 0;
    }
    $raw = trim($raw);
    if ($raw === '' || !preg_match('/^\d+$/', $raw)) {
        return 0;
    }
    // Avoid int overflow display issues; PHP int size depends on build.
    return (int) $raw;
}

/**
 * Increment the counter safely and return the new value.
 *
 * @return int
 */
function shms_counter_increment()
{
    shms_counter_ensure_dir();
    $path = shms_counter_file_path();

    $fh = @fopen($path, 'c+'); // create if missing
    if (!$fh) {
        return shms_counter_get();
    }

    // Exclusive lock while reading/updating.
    if (!@flock($fh, LOCK_EX)) {
        @fclose($fh);
        return shms_counter_get();
    }

    // Read current value
    $raw = '';
    @rewind($fh);
    while (!feof($fh)) {
        $chunk = fread($fh, 8192);
        if ($chunk === false) {
            break;
        }
        $raw .= $chunk;
        if (strlen($raw) > 64) {
            break;
        }
    }
    $raw = trim($raw);
    $cur = (preg_match('/^\d+$/', $raw)) ? (int) $raw : 0;
    $cur++;

    // Write back
    @ftruncate($fh, 0);
    @rewind($fh);
    @fwrite($fh, (string) $cur);
    @fflush($fh);
    @flock($fh, LOCK_UN);
    @fclose($fh);

    return $cur;
}

/**
 * Increment at most once per PHP session (recommended default).
 *
 * @return int current total after optional increment
 */
function shms_counter_increment_once_per_session()
{
    if (session_id() === '') {
        @session_start();
    }
    if (!isset($_SESSION['shms_counter_recorded']) || !$_SESSION['shms_counter_recorded']) {
        $n = shms_counter_increment();
        $_SESSION['shms_counter_recorded'] = true;
        return $n;
    }
    return shms_counter_get();
}

/**
 * @param int $n
 * @return string
 */
function shms_counter_format($n)
{
    return str_pad((string) max(0, (int) $n), 6, '0', STR_PAD_LEFT);
}

