<?php
/**
 * Server-side registration receipt: HTML fragment + PDF text (shared by registration-receipt.php and registration-receipt-pdf.php).
 */
if (!defined('SHMS_INIT_LOADED')) {
    require_once dirname(__FILE__) . '/init.php';
}

/**
 * @return array event meta (paths relative to site root for img src)
 */
function shms_registration_receipt_meta_for_display()
{
    $root = dirname(dirname(__FILE__));
    $sealWeb = 'assets/images/seal.png';
    $mnnitWeb = 'assets/images/mnnit-logo.png';
    $ishmsWeb = 'assets/images/ishms-logo.png';

    return array(
        'eventTitle' => 'International Conference on Structural Health Monitoring Systems (SHMS-2026)',
        'eventDates' => 'Oct. 15-17, 2026',
        'venue' => 'Prayagraj (Allahabad), India',
        'footerLine1' => 'This is a computer-generated receipt',
        'footerLine2' => 'SHMS-2026 — SNFCE MNNIT Allahabad · Email: shms2026@mnnit.ac.in · Web: see conference website',
        'sealSrc' => (is_readable($root . '/assets/images/seal.png') ? $sealWeb : ''),
        'logoMnnitSrc' => (is_readable($root . '/assets/images/mnnit-logo.png') ? $mnnitWeb : ''),
        'logoIshmsSrc' => (is_readable($root . '/assets/images/ishms-logo.png') ? $ishmsWeb : ''),
        'regPage' => shms_registration_page_url_path(),
    );
}

/**
 * Indian-style grouping for INR (matches en-IN locale: last 3 digits, then pairs).
 *
 * @param float|null $n
 * @return string
 */
function shms_registration_format_inr_en_in($n)
{
    if ($n === null || !is_numeric($n)) {
        return '—';
    }
    $v = round((float) $n, 2);
    $neg = $v < 0;
    $v = abs($v);
    $paise = (int) round(($v - floor($v)) * 100);
    $intPart = (string) (int) floor($v);
    $len = strlen($intPart);
    if ($len <= 3) {
        $grouped = $intPart;
    } else {
        $last3 = substr($intPart, -3);
        $rest = substr($intPart, 0, -3);
        $chunks = array();
        while ($rest !== '') {
            if (strlen($rest) <= 2) {
                array_unshift($chunks, $rest);
                $rest = '';
            } else {
                array_unshift($chunks, substr($rest, -2));
                $rest = substr($rest, 0, -2);
            }
        }
        $grouped = implode(',', $chunks) . ',' . $last3;
    }
    $num = ($neg ? '-' : '') . $grouped . '.' . str_pad((string) $paise, 2, '0', STR_PAD_LEFT);
    return 'INR ' . $num;
}

/**
 * @param string $s
 * @return string
 */
function shms_registration_receipt_html_esc($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/**
 * Allowed keys for receipt data (session / bridge).
 *
 * @return array
 */
function shms_registration_receipt_allowed_keys()
{
    return array(
        'receiptNo',
        'registrationNo',
        'fullName',
        'email',
        'phone',
        'affiliation',
        'category',
        'transactionId',
        'amountDeclared',
        'paymentChannel',
        'notes',
        'subTotal',
        'gstRate',
        'gst',
        'grandTotal',
        'fileUrl',
        'receiptPdfUrl',
        'receiptPdfError',
        'submittedAt',
    );
}

/**
 * @param array $raw
 * @return array|null
 */
function shms_registration_receipt_sanitize_array($raw)
{
    if (!is_array($raw)) {
        return null;
    }
    $out = array();
    foreach (shms_registration_receipt_allowed_keys() as $k) {
        if (!array_key_exists($k, $raw)) {
            continue;
        }
        $v = $raw[$k];
        if ($k === 'subTotal' || $k === 'gst' || $k === 'grandTotal') {
            if ($v === null || $v === '') {
                $out[$k] = null;
            } elseif (is_numeric($v)) {
                $out[$k] = (float) $v;
            }
            continue;
        }
        if ($k === 'gstRate') {
            if (is_numeric($v)) {
                $out[$k] = (float) $v;
            }
            continue;
        }
        if (!is_string($v) && !is_numeric($v)) {
            continue;
        }
        $s = trim((string) $v);
        if ($k === 'receiptPdfError') {
            if (strlen($s) > 800) {
                $s = substr($s, 0, 800);
            }
            $out[$k] = $s;
            continue;
        }
        if (strlen($s) > 4000) {
            $s = substr($s, 0, 4000);
        }
        if ($k === 'fileUrl' || $k === 'receiptPdfUrl') {
            if ($s !== '') {
                if (!filter_var($s, FILTER_VALIDATE_URL)) {
                    $s = '';
                } else {
                    $scheme = parse_url($s, PHP_URL_SCHEME);
                    if ($scheme !== 'http' && $scheme !== 'https') {
                        $s = '';
                    }
                }
            }
        }
        $out[$k] = $s;
    }
    if (empty($out['receiptNo'])) {
        return null;
    }
    return $out;
}

/**
 * @param array $meta from shms_registration_receipt_meta_for_display()
 * @param array $data sanitized receipt row
 * @return string HTML (outer .shms-receipt-sheet)
 */
function shms_registration_receipt_sheet_html(array $meta, array $data)
{
    $sub = isset($data['subTotal']) ? $data['subTotal'] : null;
    $gst = isset($data['gst']) ? $data['gst'] : null;
    $grand = isset($data['grandTotal']) ? $data['grandTotal'] : null;
    $gstRate = isset($data['gstRate']) ? $data['gstRate'] : 18;

    if ($sub !== null && is_numeric($sub) && $gst !== null && is_numeric($gst) && $grand !== null && is_numeric($grand)) {
        $feeRow = '<tr><td>Base amount (exclusive of GST)</td><td>' . shms_registration_receipt_html_esc(shms_registration_format_inr_en_in((float) $sub)) . '</td></tr>'
            . '<tr><td>GST @ ' . shms_registration_receipt_html_esc((string) $gstRate) . '% (on base amount)</td><td>' . shms_registration_receipt_html_esc(shms_registration_format_inr_en_in((float) $gst)) . '</td></tr>';
        $gstLine = '';
        $grandLine = '<tr class="shms-receipt-grand"><td>Total amount paid (incl. GST, matches fee deposited)</td><td>' . shms_registration_receipt_html_esc(shms_registration_format_inr_en_in((float) $grand)) . '</td></tr>';
    } elseif ($sub !== null && is_numeric($sub)) {
        $feeRow = '<tr><td>Base amount (exclusive of GST)</td><td>' . shms_registration_receipt_html_esc(shms_registration_format_inr_en_in((float) $sub)) . '</td></tr>';
        $gstLine = '';
        $grandLine = '<tr class="shms-receipt-grand"><td>Total (incl. taxes as applicable)</td><td>Per declaration above</td></tr>';
    } else {
        $feeRow = '<tr><td>Amount paid as declared (incl. GST)</td><td>' . shms_registration_receipt_html_esc(isset($data['amountDeclared']) ? $data['amountDeclared'] : '—') . '</td></tr>';
        $gstLine = '';
        $grandLine = '<tr class="shms-receipt-grand"><td>Total (incl. taxes as applicable)</td><td>Per declaration above</td></tr>';
    }

    $qrSrc = '';
    if (!empty($data['fileUrl'])) {
        $qrSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . rawurlencode($data['fileUrl']);
    }

    $logoLeft = !empty($meta['logoMnnitSrc'])
        ? '<div class="shms-receipt-head__logo shms-receipt-head__logo--left"><img src="' . shms_registration_receipt_html_esc($meta['logoMnnitSrc']) . '" alt="MNNIT Allahabad" width="116" height="68" loading="lazy"></div>'
        : '<div class="shms-receipt-head__logo shms-receipt-head__logo--left" aria-hidden="true"></div>';
    $logoRight = !empty($meta['logoIshmsSrc'])
        ? '<div class="shms-receipt-head__logo shms-receipt-head__logo--right"><img src="' . shms_registration_receipt_html_esc($meta['logoIshmsSrc']) . '" alt="ISHMS" width="116" height="68" loading="lazy"></div>'
        : '<div class="shms-receipt-head__logo shms-receipt-head__logo--right" aria-hidden="true"></div>';

    $aff = (!empty($data['affiliation']))
        ? '<div><dt>Affiliation</dt><dd>' . shms_registration_receipt_html_esc($data['affiliation']) . '</dd></div>'
        : '';

    $html = ''
        . '<div class="shms-receipt-sheet" id="shms-receipt-print-area">'
        . '<div class="shms-receipt-head">'
        . $logoLeft
        . '<div class="shms-receipt-title-block">'
        . '<h1>' . shms_registration_receipt_html_esc(isset($meta['eventTitle']) ? $meta['eventTitle'] : 'SHMS-2026') . '</h1>'
        . '<p class="shms-receipt-dates">' . shms_registration_receipt_html_esc(isset($meta['eventDates']) ? $meta['eventDates'] : '') . '</p>'
        . '<p class="shms-receipt-venue">' . shms_registration_receipt_html_esc(isset($meta['venue']) ? $meta['venue'] : '') . '</p>'
        . '</div>'
        . $logoRight
        . '</div>'
        . '<div class="shms-receipt-h-receipt">RECEIPT</div>'
        . '<dl class="shms-receipt-dl">'
        . '<div><dt>Receipt No</dt><dd>' . shms_registration_receipt_html_esc($data['receiptNo']) . '</dd></div>'
        . '<div><dt>Reg. No</dt><dd>' . shms_registration_receipt_html_esc(isset($data['registrationNo']) ? $data['registrationNo'] : '') . '</dd></div>'
        . '<div><dt>Name</dt><dd>' . shms_registration_receipt_html_esc(isset($data['fullName']) ? $data['fullName'] : '') . '</dd></div>'
        . '<div><dt>Category</dt><dd>' . shms_registration_receipt_html_esc(isset($data['category']) ? $data['category'] : '') . '</dd></div>'
        . '<div><dt>Email</dt><dd>' . shms_registration_receipt_html_esc(isset($data['email']) ? $data['email'] : '') . '</dd></div>'
        . '<div><dt>Phone</dt><dd>' . shms_registration_receipt_html_esc(isset($data['phone']) ? $data['phone'] : '') . '</dd></div>'
        . $aff
        . '<div><dt>Transaction ID / UTR</dt><dd>' . shms_registration_receipt_html_esc(isset($data['transactionId']) ? $data['transactionId'] : '') . '</dd></div>'
        . '<div><dt>Payment channel</dt><dd>' . shms_registration_receipt_html_esc(isset($data['paymentChannel']) ? $data['paymentChannel'] : '') . '</dd></div>'
        . '<div><dt>Date &amp; time</dt><dd>' . shms_registration_receipt_html_esc(isset($data['submittedAt']) ? $data['submittedAt'] : '') . '</dd></div>'
        . '</dl>'
        . '<div class="shms-receipt-box">'
        . '<h2>Registration category details</h2>'
        . '<table class="shms-receipt-rows" role="presentation"><tbody>'
        . $feeRow
        . $gstLine
        . $grandLine
        . '</tbody></table></div>'
        . '<div class="shms-receipt-foot">'
        . '<div class="shms-receipt-qr"></div>'
        . '<div class="shms-receipt-seal">'
        . (!empty($meta['sealSrc'])
            ? '<img src="' . shms_registration_receipt_html_esc($meta['sealSrc']) . '" alt="Official seal" width="140" height="140" loading="lazy">'
            : '<p style="font-size:0.8rem;color:#64748b;margin:0;">Seal image optional.</p>')
        . '<div class="shms-receipt-sig" aria-hidden="true">Authorized signatory</div>'
        . '</div></div>'
        . '<div class="shms-receipt-footer-text">'
        . '<p>' . shms_registration_receipt_html_esc(isset($meta['footerLine1']) ? $meta['footerLine1'] : '') . '</p>'
        . '<p>' . shms_registration_receipt_html_esc(isset($meta['footerLine2']) ? $meta['footerLine2'] : '') . '</p>'
        . '</div></div>';

    return $html;
}

/**
 * @param string $utf8
 * @return string PDF string literal body (parentheses contents, escaped, WinAnsi)
 */
function shms_registration_pdf_escape_winansi($utf8)
{
    $s = (string) $utf8;
    if (function_exists('iconv')) {
        $t = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $s);
        if ($t !== false) {
            $s = $t;
        }
    }
    $out = '';
    $len = strlen($s);
    for ($i = 0; $i < $len; $i++) {
        $c = $s[$i];
        $o = ord($c);
        if ($c === '\\') {
            $out .= '\\\\';
        } elseif ($c === '(') {
            $out .= '\\(';
        } elseif ($c === ')') {
            $out .= '\\)';
        } elseif ($o < 32 || $o > 126) {
            $out .= sprintf('\\%03o', $o);
        } else {
            $out .= $c;
        }
    }
    return $out;
}

/**
 * @param array $meta
 * @param array $data
 * @return string single-page A4 portrait PDF bytes
 */
function shms_registration_receipt_pdf_bytes(array $meta, array $data)
{
    $lines = array();
    $lines[] = 'SHMS-2026 — Registration receipt';
    $lines[] = '';
    $lines[] = isset($meta['eventTitle']) ? $meta['eventTitle'] : '';
    $lines[] = isset($meta['eventDates']) ? $meta['eventDates'] : '';
    $lines[] = isset($meta['venue']) ? $meta['venue'] : '';
    $lines[] = '';
    $lines[] = 'RECEIPT';
    $lines[] = '';
    $lines[] = 'Receipt No: ' . (isset($data['receiptNo']) ? $data['receiptNo'] : '');
    $lines[] = 'Reg. No: ' . (isset($data['registrationNo']) ? $data['registrationNo'] : '');
    $lines[] = 'Name: ' . (isset($data['fullName']) ? $data['fullName'] : '');
    $lines[] = 'Category: ' . (isset($data['category']) ? $data['category'] : '');
    $lines[] = 'Email: ' . (isset($data['email']) ? $data['email'] : '');
    $lines[] = 'Phone: ' . (isset($data['phone']) ? $data['phone'] : '');
    if (!empty($data['affiliation'])) {
        $lines[] = 'Affiliation: ' . $data['affiliation'];
    }
    $lines[] = 'Transaction ID / UTR: ' . (isset($data['transactionId']) ? $data['transactionId'] : '');
    $lines[] = 'Payment channel: ' . (isset($data['paymentChannel']) ? $data['paymentChannel'] : '');
    $lines[] = 'Date & time: ' . (isset($data['submittedAt']) ? $data['submittedAt'] : '');
    $lines[] = '';
    $gstRate = isset($data['gstRate']) ? $data['gstRate'] : 18;
    $lines[] = 'Registration category details (fee inclusive of ' . $gstRate . '% GST)';
    $sub = isset($data['subTotal']) ? $data['subTotal'] : null;
    $gst = isset($data['gst']) ? $data['gst'] : null;
    $grand = isset($data['grandTotal']) ? $data['grandTotal'] : null;
    if ($sub !== null && is_numeric($sub) && $gst !== null && is_numeric($gst) && $grand !== null && is_numeric($grand)) {
        $lines[] = 'Base amount (exclusive of GST): ' . shms_registration_format_inr_en_in((float) $sub);
        $lines[] = 'GST @ ' . $gstRate . '% (on base amount): ' . shms_registration_format_inr_en_in((float) $gst);
        $lines[] = 'Total amount paid (incl. GST, matches fee deposited): ' . shms_registration_format_inr_en_in((float) $grand);
    } elseif ($sub !== null && is_numeric($sub)) {
        $lines[] = 'Base amount (exclusive of GST): ' . shms_registration_format_inr_en_in((float) $sub);
        $lines[] = 'Total (incl. taxes as applicable): Per declaration above';
    } else {
        $lines[] = 'Amount paid as declared (incl. GST): ' . (isset($data['amountDeclared']) ? $data['amountDeclared'] : '—');
        $lines[] = 'Total (incl. taxes as applicable): Per declaration above';
    }
    if (!empty($data['fileUrl'])) {
        $lines[] = '';
        $lines[] = 'Uploaded receipt link: ' . $data['fileUrl'];
    }
    $lines[] = '';
    $lines[] = isset($meta['footerLine1']) ? $meta['footerLine1'] : '';
    $lines[] = isset($meta['footerLine2']) ? $meta['footerLine2'] : '';

    $stream = "BT\n/F1 9 Tf\n10.5 TL\n50 802 Td\n";
    $first = true;
    foreach ($lines as $ln) {
        $esc = shms_registration_pdf_escape_winansi($ln);
        if ($first) {
            $stream .= '(' . $esc . ") Tj\n";
            $first = false;
        } else {
            $stream .= "T*\n(" . $esc . ") Tj\n";
        }
    }
    $stream .= "ET\n";

    $objects = array();
    $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
    $objects[2] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
    $objects[3] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>';
    $objects[5] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';

    $streamLen = strlen($stream);
    $objects[4] = '<< /Length ' . $streamLen . " >>\nstream\n" . $stream . "\nendstream";

    $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
    $offsets = array(0);
    $maxObj = 0;
    ksort($objects);
    foreach ($objects as $i => $body) {
        $maxObj = max($maxObj, $i);
        $offsets[$i] = strlen($pdf);
        $pdf .= $i . " 0 obj\n" . $body . "\nendobj\n";
    }

    $xrefPos = strlen($pdf);
    $pdf .= "xref\n0 " . ($maxObj + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";
    for ($i = 1; $i <= $maxObj; $i++) {
        if (isset($offsets[$i])) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        } else {
            $pdf .= "0000000000 00000 n \n";
        }
    }
    $pdf .= "trailer\n<< /Size " . ($maxObj + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefPos . "\n%%EOF";

    return $pdf;
}

/**
 * @param string $receiptNo
 * @return string safe ASCII filename fragment
 */
function shms_registration_receipt_pdf_filename($receiptNo)
{
    $s = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) $receiptNo);
    $s = trim($s, '-');
    if ($s === '') {
        $s = 'receipt';
    }
    return 'SHMS2026-registration-receipt-' . $s . '.pdf';
}
