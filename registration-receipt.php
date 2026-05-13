<?php
/**
 * Registration receipt after successful submission (PHP session). Optional official PDF link from Google (receiptPdfUrl). Print uses the browser dialog; @media print forces light colours even in dark theme.
 */
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';
require_once dirname(__FILE__) . '/includes/registration-receipt-render.php';

shms_registration_session_start();

$shmsReceiptData = null;
if (isset($_SESSION['shms2026_registration_receipt']) && is_array($_SESSION['shms2026_registration_receipt'])) {
    $shmsReceiptData = shms_registration_receipt_sanitize_array($_SESSION['shms2026_registration_receipt']);
}

$shmsReceiptMeta = shms_registration_receipt_meta_for_display();
?>
<!DOCTYPE html>
<html lang="en"<?php echo shms_html_theme_class(); ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration receipt | SHMS‑2026</title>
  <link rel="stylesheet" href="style.css">
<?php echo shms_head_resource_hints(); ?>
  <style>
    /* Clear fixed nav; sticky .page-heading from global CSS would otherwise overlap the toolbar when scrolling */
    .shms-page-registration-receipt .shms-receipt-page {
      padding: calc(var(--nav-height) + 1rem) 1rem 2rem;
      max-width: 52rem;
      margin: 0 auto;
    }
    .shms-page-registration-receipt .page-heading {
      position: relative;
      top: auto;
      z-index: auto;
      padding-left: 0;
      padding-right: 0;
      max-width: none;
      margin: 0 0 1rem;
      background: transparent;
      backdrop-filter: none;
      -webkit-backdrop-filter: none;
      border: none;
      box-shadow: none;
      animation: none;
    }
    .shms-page-registration-receipt .page-scroll-fabs {
      display: none !important;
    }
    .shms-receipt-toolbar {
      display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between;
      margin-bottom: 1.25rem; padding: 0.75rem 1rem; background: #f1f5f9; border-radius: 10px; border: 1px solid #e2e8f0;
      position: relative;
      z-index: 2;
    }
    html.theme-dark .shms-receipt-toolbar { background: rgba(30, 41, 59, 0.85); border-color: rgba(148, 163, 184, 0.25); }
    .shms-receipt-toolbar__actions { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
    .shms-receipt-toolbar__print {
      cursor: pointer; font-weight: 600; padding: 0.5rem 1.1rem; border-radius: 8px; border: none;
      background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%); color: #fff; font-size: 0.95rem;
      font-family: inherit;
    }
    .shms-receipt-toolbar__print:hover { filter: brightness(1.06); }
    .shms-receipt-toolbar__link { font-size: 0.9rem; color: #0369a1; }
    html.theme-dark .shms-receipt-toolbar__link { color: #38bdf8; }
    .shms-receipt-toolbar__hint { margin: 0; font-size: 0.9rem; max-width: 28rem; }

    .shms-receipt-sheet {
      background: #fff; color: #0f172a; padding: 1.75rem 2rem 2rem; border: 1px solid #cbd5e1;
      box-shadow: 0 4px 24px rgba(15, 23, 42, 0.08); font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.45;
    }
    html.theme-dark .shms-receipt-sheet { background: #fff; color: #0f172a; }

    .shms-receipt-head { display: flex; justify-content: space-between; align-items: center; gap: 0.75rem 1rem; margin-bottom: 0.5rem; }
    .shms-receipt-head__logo { flex: 0 0 auto; width: 7.5rem; min-height: 4rem; display: flex; align-items: center; justify-content: center; }
    .shms-receipt-head__logo--left { justify-content: flex-start; }
    .shms-receipt-head__logo--right { justify-content: flex-end; }
    .shms-receipt-head__logo img { max-height: 4.25rem; max-width: 7.25rem; width: auto; height: auto; object-fit: contain; display: block; }
    .shms-receipt-title-block { text-align: center; flex: 1; min-width: 0; }
    .shms-receipt-title-block h1 { margin: 0 0 0.35rem; font-size: 1.05rem; font-weight: 700; line-height: 1.3; }
    .shms-receipt-title-block .shms-receipt-dates { font-size: 0.95rem; margin: 0.15rem 0; }
    .shms-receipt-title-block .shms-receipt-venue { font-size: 0.95rem; margin: 0; }
    .shms-receipt-h-receipt { text-align: center; margin: 1rem 0 1.25rem; font-size: 1.15rem; font-weight: 700; text-decoration: underline; letter-spacing: 0.04em; }

    .shms-receipt-dl { margin: 0 0 1.25rem; }
    .shms-receipt-dl > div { display: grid; grid-template-columns: 11rem 1fr; gap: 0.35rem 1rem; margin-bottom: 0.45rem; }
    .shms-receipt-dl dt { font-weight: 600; margin: 0; }
    .shms-receipt-dl dd { margin: 0; }

    .shms-receipt-box { border: 1px solid #0f172a; padding: 0.85rem 1rem 1rem; margin-bottom: 1.5rem; }
    .shms-receipt-box h2 { margin: 0 0 0.65rem; font-size: 0.95rem; text-decoration: underline; font-weight: 700; }
    .shms-receipt-rows { width: 100%; border-collapse: collapse; }
    .shms-receipt-rows td { padding: 0.35rem 0; vertical-align: top; }
    .shms-receipt-rows td:last-child { text-align: right; font-weight: 600; white-space: nowrap; }
    .shms-receipt-rows tr.shms-receipt-grand td { font-weight: 700; padding-top: 0.5rem; border-top: 1px solid #94a3b8; }

    .shms-receipt-foot { display: flex; justify-content: space-between; align-items: flex-end; gap: 1.5rem; flex-wrap: wrap; margin-top: 0.5rem; }
    .shms-receipt-qr { flex-shrink: 0; }
    .shms-receipt-qr img { display: block; width: 100px; height: 100px; }
    .shms-receipt-seal { flex-shrink: 0; text-align: center; max-width: 160px; }
    .shms-receipt-seal img { max-width: 140px; height: auto; display: block; margin: 0 auto 0.25rem; }
    .shms-receipt-seal .shms-receipt-sig { font-family: Georgia, serif; font-style: italic; color: #1e40af; font-size: 1rem; margin-bottom: 0.25rem; }

    .shms-receipt-footer-text { text-align: center; margin-top: 1.25rem; font-size: 0.8rem; color: #475569; line-height: 1.5; }
    .shms-receipt-footer-text p { margin: 0.2rem 0; }

    .shms-receipt-missing { padding: 2rem; text-align: center; color: #64748b; }
    .shms-receipt-missing a { color: #0369a1; font-weight: 600; }

    @media print {
      html { color-scheme: light; }
      html, body {
        background: #fff !important;
        color: #0f172a !important;
      }
      html.theme-dark,
      html.theme-dark body {
        background: #fff !important;
        color: #0f172a !important;
      }
      body * { visibility: hidden; }
      .shms-receipt-sheet,
      .shms-receipt-sheet * {
        visibility: visible;
        color: #0f172a !important;
      }
      .shms-receipt-page,
      main.shms-receipt-page {
        background: #fff !important;
      }
      .shms-receipt-sheet {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: none;
        box-shadow: none !important;
        border: 1px solid #000;
        padding: 12mm 14mm;
        background: #fff !important;
        color: #0f172a !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .shms-receipt-sheet .shms-receipt-box,
      .shms-receipt-sheet .shms-receipt-dl > div,
      .shms-receipt-sheet table,
      .shms-receipt-sheet td,
      .shms-receipt-sheet th,
      .shms-receipt-sheet dt,
      .shms-receipt-sheet dd,
      .shms-receipt-sheet p,
      .shms-receipt-sheet h1 {
        background: #fff !important;
        color: #0f172a !important;
        border-color: #0f172a #94a3b8 #cbd5e1 !important;
      }
      .shms-receipt-sheet .shms-receipt-footer-text,
      .shms-receipt-sheet .shms-receipt-footer-text p {
        color: #475569 !important;
        background: transparent !important;
      }
      .shms-receipt-sheet .shms-receipt-sig {
        color: #1e40af !important;
      }
      .shms-receipt-sheet img {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .shms-receipt-toolbar,
      .page-scroll-fabs,
      body > nav,
      .shms-receipt-page .page-heading,
      footer { display: none !important; }
      @page { margin: 12mm; size: A4 portrait; }
    }
  </style>
</head>
<body id="top" class="shms-page-registration-receipt">
<?php require dirname(__FILE__) . '/includes/nav.php'; ?>

<main class="shms-receipt-page">
  <div class="page-heading">
    <h1 style="font-size:1.25rem;">Registration receipt</h1>
  </div>

  <?php if ($shmsReceiptData !== null) : ?>
  <div class="shms-receipt-toolbar">
    <p class="shms-receipt-toolbar__hint">
      <?php if (!empty($shmsReceiptData['receiptPdfUrl'])) : ?>
      An official A4 receipt PDF is sent to your registered email address.
      You can still print this page from the browser (light layout for printing even in dark theme).
      <?php elseif (!empty($shmsReceiptData['receiptPdfError'])) : ?>
      The server could not create the Drive PDF automatically (<?php echo htmlspecialchars($shmsReceiptData['receiptPdfError'], ENT_QUOTES, 'UTF-8'); ?>). Use Print below to save a copy; check your confirmation e-mail if a PDF was attached.
      <?php else : ?>
      Review your details below, then use Print — your system dialog lets you choose a printer or a PDF printer (e.g. “Save as PDF” / “Microsoft Print to PDF”). Printing uses a light layout even when the site is in dark theme.
      <?php endif; ?>
    </p>
    <div class="shms-receipt-toolbar__actions">
      <button type="button" class="shms-receipt-toolbar__print" id="shms-receipt-print-btn">Print receipt</button>
      <a class="shms-receipt-toolbar__link" href="<?php echo htmlspecialchars($shmsReceiptMeta['regPage'], ENT_QUOTES, 'UTF-8'); ?>">← Back to registration</a>
    </div>
  </div>
  <?php echo shms_registration_receipt_sheet_html($shmsReceiptMeta, $shmsReceiptData); ?>
  <?php else : ?>
  <div class="shms-receipt-missing">
    <p>No receipt data was found. If you just submitted the form, try again from the registration page.</p>
    <p><a href="<?php echo htmlspecialchars($shmsReceiptMeta['regPage'], ENT_QUOTES, 'UTF-8'); ?>">Go to registration</a></p>
  </div>
  <?php endif; ?>
</main>

<?php require dirname(__FILE__) . '/includes/footer.php'; ?>
<?php if ($shmsReceiptData !== null) : ?>
<script>
(function () {
  var btn = document.getElementById('shms-receipt-print-btn');
  if (btn) {
    btn.addEventListener('click', function () {
      window.print();
    });
  }
})();
</script>
<?php endif; ?>
</body>
</html>
