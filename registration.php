<?php
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';
$SHMS = shms_page_data();
$shmsNavPage = 'registration';
$shmsRegPaymentFormUrl = shms_registration_payment_form_url();
$shmsRegWebappUrl = shms_registration_webapp_url();
$shmsRegPhpForm = shms_registration_php_form_enabled();
$shmsRegPhpAlongsideWebapp = shms_registration_php_form_alongside_webapp();
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
$shmsRegQrPath = dirname(__FILE__) . '/assets/images/scanner.png';
$shmsRegQrWeb = 'assets/images/scanner.png';
$shmsRegQrExists = is_readable($shmsRegQrPath);
?>
<!DOCTYPE html>
<html lang="en"<?php echo shms_html_theme_class(); ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Registration | SHMS‑2026</title>
  <link rel="stylesheet" href="style.css">
<?php echo shms_head_resource_hints(); ?>
</head>
<body id="top">
<?php require dirname(__FILE__) . '/includes/nav.php'; ?>

<main>
  <?php require dirname(__FILE__) . '/includes/hero-banner.php'; ?>

  <section class="page-heading" id="page-content">
    <h1>Registration</h1>
    <p>Join SHMS‑2026 and Be Part of The Next Generation of Structural Health Monitoring.</p>
  </section>

  <section>
    <h2 id="fees">Registration Fees</h2>
    <table>
      <thead>
        <tr>
          <th>Category</th>
          <th>Early Bird<br><small>(by Sept&nbsp;15)</small></th>
          <th>Regular<br><small>(after Sept&nbsp;15)</small></th>
        </tr>
      </thead>
      <tbody>
        <tr><td>Students / Research Scholars</td><td>₹ 7,080</td><td>₹ 8,850</td></tr>
        <tr><td>Faculty Members</td><td>₹ 8,260</td><td>₹ 10,030</td></tr>
        <tr><td>ISHMS Members</td><td>₹ 5,900</td><td>₹ 7,670</td></tr>
        <tr><td>Industry Participants</td><td>₹ 14,160</td><td>₹ 15,930</td></tr>
        <tr><td>Foreign Delegates</td><td>USD 150</td><td>USD 200</td></tr>
      </tbody>
    </table>
    <p>The above fees are inclusive of all applicable taxes.</p>

    <h2 id="payment">Payment &amp; confirmation</h2>
    <p>Pay the fee applicable to your category and date of payment using the conference account below. You may use
      <strong>UPI</strong>, <strong>IMPS</strong>, <strong>NEFT</strong>, <strong>RTGS</strong>, net banking, or other
      bank-offered channels in India that credit the same account. Foreign delegates may use international wire transfer
      (SWIFT) where applicable. Always keep the transaction reference (UTR / RRN / transaction ID) and a screenshot or PDF
      receipt for your records.</p>

    <div class="registration-payment-grid" role="group" aria-label="Payment details">
      <div class="registration-payment-card">
        <h3 class="registration-payment-card__title">Bank transfer</h3>
        <dl class="registration-payment-dl">
          <dt>Account name</dt><dd>SNFCE MNNIT Allahabad</dd>
          <dt>Account number</dt><dd><code class="registration-payment-mono">10424975574</code></dd>
          <dt>IFSC</dt><dd><code class="registration-payment-mono">SBIN0002580</code></dd>
          <dt>MICR</dt><dd>211002016</dd>
          <dt>Branch</dt><dd>State Bank of India, MNNIT Allahabad</dd>
          <dt>SWIFT (international)</dt><dd><code class="registration-payment-mono">SBININBB828</code></dd>
        </dl>
        <p class="registration-payment-note">For NEFT/RTGS/IMPS, use these details exactly as shown. In the remarks /
          narration, include <strong>SHMS2026</strong>, your name, and category (e.g. student / faculty) if the bank allows.</p>
      </div>
      <div class="registration-payment-card">
        <h3 class="registration-payment-card__title">UPI &amp; scan to pay</h3>
        <p class="registration-payment-upi"><strong>UPI ID:</strong> <code class="registration-payment-mono">10424975574@sbi</code></p>
        <p class="registration-payment-note">You can pay via BHIM, PhonePe, Google Pay, Paytm, or any UPI app that supports this VPA.</p>
        <?php if ($shmsRegQrExists) : ?>
        <figure class="registration-payment-qr">
          <img src="<?php echo htmlspecialchars($shmsRegQrWeb, ENT_QUOTES, 'UTF-8'); ?>" width="220" height="220" alt="UPI scan to pay — SHMS-2026" loading="lazy" decoding="async">
          <figcaption>Scan with your UPI app</figcaption>
        </figure>
        <?php else : ?>
        <p class="registration-payment-note">Scan-to-pay image: add <code>assets/images/scanner.png</code> to the site folder.</p>
        <?php endif; ?>
      </div>
    </div>

    <h3 id="after-payment">After payment</h3>
    <?php if ($shmsRegWebappUrl !== '') : ?>
    <p>After you pay, use the link below to open the official registration form in a new window. Enter your details and upload your payment receipt (PDF or image). The
      secretariat uses this to match your payment with your registration. You will see a confirmation on that page when the submission succeeds.</p>
    <?php elseif ($shmsRegPhpForm) : ?>
    <p>After you pay, open the registration form in the window that appears from the button below. Enter your details and upload your payment receipt (PDF or image). The
      secretariat uses this to match your payment with your registration. When the submission succeeds, a printable receipt opens (you can print it from that page).</p>
    <?php else : ?>
    <p>After you pay, follow the instructions below to complete your registration.</p>
    <?php endif; ?>

    <?php if ($shmsRegWebappUrl !== '') : ?>
    <div class="registration-submit-block">
      <p class="registration-payment-cta registration-payment-cta--row">
        <a class="registration-payment-cta__btn registration-payment-cta__btn--primary"
           id="registration-submit"
           href="<?php echo htmlspecialchars($shmsRegWebappUrl, ENT_QUOTES, 'UTF-8'); ?>"
           target="_blank"
           rel="noopener noreferrer"
           onclick="try{var w=window.open(this.href,'shms2026registration','width=1000,height=900,scrollbars=yes,resizable=yes');if(w){w.focus();return false;}}catch(e){}return true;">Open registration form — submit details &amp; receipt</a>
      </p>
      <p class="registration-payment-note">This tries to open a dedicated window; if your browser blocks it, the link still opens in a new tab.</p>
    </div>
    <?php if ($shmsRegPhpAlongsideWebapp) : ?>
    <div class="registration-submit-block registration-submit-block--php-fallback" id="registration-php-fallback">
      <h3 class="registration-payment-card__title" style="margin-top:1.25rem;">Submit on this website instead</h3>
      <p class="registration-payment-note">If the Google form shows an error after you choose your receipt (for example “did not return JSON”), use the same on-site form in a pop-up window (sends to the same Google endpoint).</p>
      <?php
      if ($shmsRegFlash === 'ok' || $shmsRegFlash === 'err') {
          $shmsRegDisplayMode = 'flash_only';
          $shmsRegFormBlockId = 'registration-php-fallback-status';
          require dirname(__FILE__) . '/includes/registration-form-block.php';
          unset($shmsRegDisplayMode, $shmsRegFormBlockId);
      }
      ?>
      <?php if ($shmsRegFlash !== 'ok') :
          $shmsRegPopHref = htmlspecialchars(shms_registration_popup_url_path(), ENT_QUOTES, 'UTF-8');
          ?>
      <p class="registration-payment-cta registration-payment-cta--row">
        <a class="registration-payment-cta__btn registration-payment-cta__btn--primary"
           href="<?php echo $shmsRegPopHref; ?>"
           target="_blank"
           rel="noopener noreferrer"
           onclick="try{var w=window.open('<?php echo $shmsRegPopHref; ?>','shms2026regpopup','width=1000,height=920,scrollbars=yes,resizable=yes');if(w){w.focus();return false;}}catch(e){}return true;">Open registration form — this website</a>
      </p>
      <p class="registration-payment-note">Opens in a separate window. If pop-ups are blocked, the link opens in a new tab.</p>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php elseif ($shmsRegPhpForm) : ?>
    <div class="registration-submit-block"<?php echo ($shmsRegFlash === 'ok' || $shmsRegFlash === 'err') ? ' id="registration-submit"' : ''; ?>>
      <?php
      if ($shmsRegFlash === 'ok' || $shmsRegFlash === 'err') {
          $shmsRegDisplayMode = 'flash_only';
          $shmsRegFormBlockId = 'registration-main-status';
          require dirname(__FILE__) . '/includes/registration-form-block.php';
          unset($shmsRegDisplayMode, $shmsRegFormBlockId);
      }
      ?>
      <?php if ($shmsRegFlash !== 'ok') :
          $shmsRegPopHref = htmlspecialchars(shms_registration_popup_url_path(), ENT_QUOTES, 'UTF-8');
          ?>
      <p class="registration-payment-cta registration-payment-cta--row">
        <a class="registration-payment-cta__btn registration-payment-cta__btn--primary"
           <?php if ($shmsRegFlash === '') : ?>id="registration-submit"<?php endif; ?>
           href="<?php echo $shmsRegPopHref; ?>"
           target="_blank"
           rel="noopener noreferrer"
           onclick="try{var w=window.open('<?php echo $shmsRegPopHref; ?>','shms2026regpopup','width=1000,height=920,scrollbars=yes,resizable=yes');if(w){w.focus();return false;}}catch(e){}return true;">Open registration form — submit details &amp; receipt</a>
      </p>
      <p class="registration-payment-note">Opens in a separate window. If your browser blocks pop-ups, the link still opens in a new tab.</p>
      <?php endif; ?>
    </div>
    <?php else : ?>
    <div class="registration-submit-block" id="registration-submit">
      <p class="registration-payment-note">Online registration submission is not enabled on this site yet. If you have already paid, please email
        <a href="mailto:shms2026@mnnit.ac.in">shms2026@mnnit.ac.in</a> with your payment details and receipt.</p>
    </div>
    <?php endif; ?>

    <?php if ($shmsRegPaymentFormUrl !== '') : ?>
    <p class="registration-payment-cta"><a class="registration-payment-cta__btn" href="<?php echo htmlspecialchars($shmsRegPaymentFormUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">Alternative: payment confirmation form</a></p>
    <?php endif; ?>

    <h2 id="includes">What is included?</h2>
    <ul>
      <li>Access to all technical sessions, keynote lectures and panel discussions</li>
      <li>Conference kit, proceedings (USB) and certificate of participation</li>
      <li>Lunch and tea/coffee breaks for all three days</li>
      <li>Welcome reception and conference banquet</li>
      <li>Exhibition access and networking opportunities</li>
    </ul>
    <p>For any queries regarding registration or payment matching, contact the conference secretariat at
      <a href="mailto:shms2026@mnnit.ac.in">shms2026@mnnit.ac.in</a>.</p>
  </section>

  <section id="sponsorship" class="sponsorship-block" aria-labelledby="sponsorship-heading">
    <p class="sponsorship-kicker">Industry &amp; institutional partners</p>
    <h2 id="sponsorship-heading">Sponsorship opportunities</h2>
    <p class="sponsorship-lead">
      SHMS‑2026 offers a focused platform for organisations that wish to support structural health monitoring research,
      smart materials, and AI/ML for resilient infrastructure. Packages combine visibility with direct access to delegates
      from academia, industry, and government. Commercial terms are agreed with the organising committee and may be
      tailored to your objectives.
    </p>

    <h3 class="sponsorship-subtitle">Partnership categories</h3>
    <!-- <p class="sponsorship-note">Indicative benefits below; final tariffs, inclusions, and branding slots are confirmed in the official sponsorship prospectus.</p> -->
    <div class="sponsorship-tiers" role="list">
      <article class="sponsorship-tier sponsorship-tier--platinum" role="listitem">
        <div class="sponsorship-tier__badge sponsorship-tier__badge--platinum" aria-hidden="true">P</div>
        <h4 class="sponsorship-tier__name">Platinum</h4>
        <p class="sponsorship-tier__tagline">Maximum visibility</p>
        <ul class="sponsorship-tier__list">
          <li>₹1,50,000 + GST</li>
          <li>Platinum Sponsorship offers the highest level of visibility and recognition throughout the conference</li>
          <li>4 Complimentary Conference Registrations</li>
          <li>Prime logo placement on website, banners, and all conference materials</li>
          <!-- <li>Special acknowledgement during the conference opening and closing ceremonies</li> -->
        </ul>
        <p class="sponsorship-tier__price">Commercial terms on request</p>
      </article>
      <article class="sponsorship-tier sponsorship-tier--featured sponsorship-tier--gold" role="listitem">
        <div class="sponsorship-tier__badge sponsorship-tier__badge--gold" aria-hidden="true">G</div>
        <h4 class="sponsorship-tier__name">Gold</h4>
        <p class="sponsorship-tier__tagline">Strong conference presence</p>
        <ul class="sponsorship-tier__list">
          <li>₹1,00,000 + GST</li>
          <li>Gold Sponsorship offers substantial visibility and engagement opportunities</li>
          <li>3 Complimentary Conference Registrations</li>
          <li>Display of company logo on conference banners, and the official conference website</li>
        </ul>
        <p class="sponsorship-tier__price">Commercial terms on request</p>
      </article>
      <article class="sponsorship-tier sponsorship-tier--silver" role="listitem">
        <div class="sponsorship-tier__badge sponsorship-tier__badge--silver" aria-hidden="true">S</div>
        <h4 class="sponsorship-tier__name">Silver</h4>
        <p class="sponsorship-tier__tagline">Targeted exposure</p>
        <ul class="sponsorship-tier__list">
          <li>₹50,000 + GST</li>
          <li>Silver Sponsorship provides an excellent platform for organizations to showcase their brand and connect with the academic and professional community.</li>
          <li>2 Complimentary Conference Registrations</li>
          <li>Display of company logo on the official conference website</li>
        </ul>
        <p class="sponsorship-tier__price">Commercial terms on request</p>
      </article>
    </div>

    <!-- <h3 class="sponsorship-subtitle">Proceedings &amp; souvenir options</h3>
    <p class="sponsorship-note">Reach every delegate through the printed / digital conference kit. Layouts and deadlines are coordinated with the publication chair.</p>
    <div class="sponsorship-addons" role="list">
      <article class="sponsorship-addon" role="listitem">
        <h4 class="sponsorship-addon__title">Inside full page</h4>
        <p class="sponsorship-addon__text">Single full‑colour page in the conference souvenir / abstract volume.</p>
        <p class="sponsorship-addon__price">Rate on application</p>
      </article>
      <article class="sponsorship-addon" role="listitem">
        <h4 class="sponsorship-addon__title">Inside half page</h4>
        <p class="sponsorship-addon__text">Compact advertisement suitable for logos, QR codes, and short messaging.</p>
        <p class="sponsorship-addon__price">Rate on application</p>
      </article>
    </div> -->

    <p class="sponsorship-outro">
      The account details for sponsorship are same as registration account details.
      For more information write to the conference secretariat at
      <a href="mailto:shms2026@mnnit.ac.in?subject=SHMS-2026%20sponsorship%20enquiry">shms2026@mnnit.ac.in</a>
      with subject line <strong>SHMS‑2026 sponsorship enquiry</strong>.
    </p>
  </section>

</main>

  <!-- Sticky CTA bar: countdown & visitor tally (no Register/Submit on this page) -->
<?php $shmsCtaVariant = 'registration'; require dirname(__FILE__) . '/includes/cta-bar.php'; ?>

<?php require dirname(__FILE__) . '/includes/footer.php'; ?>
</body>
</html>
