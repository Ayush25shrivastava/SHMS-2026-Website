<?php
/**
 * Registration form in a dedicated window (opened from registration.php).
 */
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';

$shmsRegFlash = '';
$shmsRegErrCode = '';
if (isset($_GET['reg'])) {
    if ($_GET['reg'] === 'ok') {
        $shmsRegFlash = 'ok';
    } elseif ($_GET['reg'] === 'err') {
        $shmsRegFlash = 'err';
        if (isset($_GET['e']) && is_string($_GET['e']) && preg_match('/^[a-z0-9_]+$/i', $_GET['e'])) {
            $shmsRegErrCode = $_GET['e'];
        }
    }
}

$shmsRegInPopup = true;
$shmsRegFormBlockId = 'registration-popup';
?>
<!DOCTYPE html>
<html lang="en"<?php echo shms_html_theme_class(); ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Submit registration | SHMS‑2026</title>
  <link rel="stylesheet" href="style.css">
<?php echo shms_head_resource_hints(); ?>
</head>
<body class="registration-popup-body" id="top">
<main class="registration-popup-main">
  <h1 class="registration-popup-heading">SHMS‑2026 — registration &amp; receipt</h1>
  <?php require dirname(__FILE__) . '/includes/registration-form-block.php'; ?>
</main>
</body>
</html>
