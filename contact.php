<?php
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';
require_once dirname(__FILE__) . '/includes/contact-form.php';
$SHMS = shms_page_data();
$shmsNavPage = 'contact';
// Start session before any output so Set-Cookie is sent; otherwise the CSRF token
// lives only in a request-local session and the next POST sees a new empty session.
if (!shms_contact_uses_google_form()) {
  shms_contact_session_start();
}
$shmsContactFeedback = shms_contact_uses_google_form() ? null : shms_contact_handle_request();
?>
<!DOCTYPE html>
<html lang="en" <?php echo shms_html_theme_class(); ?>>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Contact | SHMS‑2026</title>
  <link rel="stylesheet" href="style.css">
  <?php echo shms_head_resource_hints(); ?>
</head>

<body id="top">
  <?php require dirname(__FILE__) . '/includes/nav.php'; ?>

  <main>
    <?php require dirname(__FILE__) . '/includes/hero-banner.php'; ?>

    <section class="page-heading" id="page-content">
      <h1>Contact Us</h1>
      <p>We’re here to help. Reach out with any questions about SHMS‑2026.</p>
    </section>

    <?php require dirname(__FILE__) . '/includes/contact-form-block.php'; ?>

    <section>
      <div>
        <h2 id="organising-contact">Organising Committee (MNNIT Allahabad)</h2>
        <p><strong>Prof. Rama Shanker</strong> – Conference Secretary<br>
          Department of Civil Engineering, MNNIT Allahabad<br>
          <!-- Email: <a href="mailto:shms2026@mnnit.ac.in" style="color:#004a8f;">shms2026@mnnit.ac.in</a></p> -->
        <p><strong>Dr. Varun Singh</strong> – Conference Secretary<br>
          Department of Civil Engineering, MNNIT Allahabad<br>
          <!-- Email: <a href="mailto:shms2026@mnnit.ac.in" style="color:#004a8f;">shms2026@mnnit.ac.in</a></p> -->

        <h2 id="organising-contact">Joint Organising Committee (MNNIT Allahabad)</h2>
        <p><strong>Dr. Snehal K</strong> – Joint Conference Secretary<br>
          Department of Civil Engineering, MNNIT Allahabad<br>
          <!-- Email: <a href="mailto:shms2026@mnnit.ac.in" style="color:#004a8f;">shms2026@mnnit.ac.in</a></p> -->
        <p><strong>Dr. Bharat Rajan</strong> – Joint Conference Secretary<br>
          Department of Civil Engineering, MNNIT Allahabad<br>
          <!-- Email: <a href="mailto:shms2026@mnnit.ac.in" style="color:#004a8f;">shms2026@mnnit.ac.in</a></p> -->

        <h2 id="ishms-contact">ISHMS Coordination</h2>
        <p><strong>Prof. Suresh Bhalla</strong> – ISHMS President<br>
          Email: <a href="mailto:admin@ishms.org.in" style="color:#004a8f;">admin@ishms.org.in</a><br>
          Website: <a href="https://www.ishms.org.in" target="_blank" style="color:#004a8f;">www.ishms.org.in</a></p>

        <h2 id="secretariat">Conference Secretariat</h2>
        <p>Email: <a href="mailto:shms2026@mnnit.ac.in" style="color:#004a8f;">shms2026@mnnit.ac.in</a><br>
          Website: <a href="https://mnnit.ac.in/shms2026" target="_blank"
            style="color:#004a8f;">https://mnnit.ac.in/shms2026</a><br>
          Phone: +91‑532‑227‑1301</p>
      </div>
      <p>We look forward to welcoming you to SHMS‑2026 in Prayagraj! If you have any queries or require assistance
        regarding your participation, travel arrangements or accommodations, please do not hesitate to contact us.</p>
    </section>

  </main>

  <?php require dirname(__FILE__) . '/includes/cta-bar.php'; ?>

  <?php require dirname(__FILE__) . '/includes/footer.php'; ?>
</body>

</html>