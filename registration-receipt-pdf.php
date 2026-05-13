<?php
/**
 * Download registration receipt as a single-page A4 portrait PDF (session). Pure PHP; triggers browser save dialog.
 */
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/registration-receipt-render.php';

shms_registration_session_start();

$data = null;
if (isset($_SESSION['shms2026_registration_receipt']) && is_array($_SESSION['shms2026_registration_receipt'])) {
    $data = shms_registration_receipt_sanitize_array($_SESSION['shms2026_registration_receipt']);
}

if ($data === null) {
    header('Location: ' . shms_registration_receipt_page_path(), true, 302);
    exit;
}

$meta = shms_registration_receipt_meta_for_display();
$pdf = shms_registration_receipt_pdf_bytes($meta, $data);
$fname = shms_registration_receipt_pdf_filename(isset($data['receiptNo']) ? $data['receiptNo'] : 'receipt');

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . str_replace(array('"', "\r", "\n"), '', $fname) . '"');
header('Content-Length: ' . (string) strlen($pdf));
header('Cache-Control: private, no-store, no-cache, must-revalidate');

echo $pdf;
exit;
