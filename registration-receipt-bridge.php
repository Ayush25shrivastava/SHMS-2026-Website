<?php
/**
 * POST: receipt_json + shms_receipt_bridge_token → session → redirect to registration-receipt.php (no JavaScript on receipt).
 */
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/registration-receipt-render.php';

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . shms_registration_page_url_path(), true, 302);
    exit;
}

shms_registration_session_start();

$tokenIn = isset($_POST['shms_receipt_bridge_token']) ? (string) $_POST['shms_receipt_bridge_token'] : '';
$expected = isset($_SESSION['shms_receipt_bridge_token']) ? (string) $_SESSION['shms_receipt_bridge_token'] : '';
if ($tokenIn === '' || $expected === '' || $tokenIn !== $expected) {
    header('Location: ' . shms_registration_page_url_path() . '?reg=err&e=rejected', true, 302);
    exit;
}

$rawJson = isset($_POST['receipt_json']) ? (string) $_POST['receipt_json'] : '';
if (strlen($rawJson) > 524288) {
    unset($_SESSION['shms_receipt_bridge_token']);
    header('Location: ' . shms_registration_page_url_path() . '?reg=err&e=rejected', true, 302);
    exit;
}

unset($_SESSION['shms_receipt_bridge_token']);

$decoded = json_decode($rawJson, true);
$data = shms_registration_receipt_sanitize_array($decoded);
if ($data === null) {
    header('Location: ' . shms_registration_page_url_path() . '?reg=err&e=bad_response', true, 302);
    exit;
}

$_SESSION['shms2026_registration_receipt'] = $data;
header('Location: ' . shms_registration_receipt_page_path(), true, 302);
exit;
