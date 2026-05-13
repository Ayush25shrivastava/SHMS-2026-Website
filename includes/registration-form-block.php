<?php
/**
 * Registration form: POSTs to registration-submit.php, or (when use_browser_post) fetch() from the browser to Google.
 *
 * @var string $shmsRegFlash optional: 'ok' | 'err'
 * @var string $shmsRegErrCode optional: Apps Script / PHP error code
 */
if (!defined('SHMS_INIT_LOADED')) {
    require_once dirname(__FILE__) . '/init.php';
}
$shmsRegFlash = isset($shmsRegFlash) ? (string) $shmsRegFlash : '';
$shmsRegErrCode = isset($shmsRegErrCode) ? (string) $shmsRegErrCode : '';
$shmsRegFormBlockId = (isset($shmsRegFormBlockId) && (string) $shmsRegFormBlockId !== '') ? (string) $shmsRegFormBlockId : 'registration-submit';
$shmsRegDisplayMode = isset($shmsRegDisplayMode) ? (string) $shmsRegDisplayMode : 'full';
$shmsRegInPopup = !empty($shmsRegInPopup);
$shmsRegBrowserSubmit = shms_registration_use_browser_post() && shms_registration_post_url() !== '';
$shmsRegFormElId = $shmsRegFormBlockId . '-form';
$shmsRegBrowserErrId = $shmsRegFormBlockId . '-browser-err';

$shmsRegErrHuman = array(
    'not_configured' => 'Registration is not configured on this server.',
    'missing_fields' => 'Please fill in all required fields.',
    'missing_file' => 'Please attach your payment receipt.',
    'upload_error' => 'The file upload failed. Try again or use a smaller file.',
    'file_too_large' => 'Receipt must be 5 MB or smaller.',
    'invalid_file_type' => 'Receipt must be a PDF, PNG, JPG, or JPEG file.',
    'empty_file' => 'The uploaded file appears empty.',
    'google_unreachable' => 'This server or network cannot reach Google (script.google.com). Common causes: firewall, VPN, or ISP blocking; institute Wi‑Fi blocking outbound HTTPS; or no route to the internet. Try another network (e.g. mobile hotspot), ask IT to allow HTTPS to script.google.com, or set registration.outbound_proxy in includes/config.php if your site must use an HTTP proxy. To see the exact failure, run: php tools/registration-transport-test.php',
    'google_access_denied' => 'Google returned HTTP 401/403. Redeploy the registration web app as Execute as: Me and Who has access: Anyone (with the link). Confirm registration.webhook_url is the /exec URL from that deployment, not a library URL or /dev.',
    'google_not_found' => 'Google returned HTTP 404. Check registration.webhook_url — it must be the live Web App /exec link from Apps Script → Deploy → Manage deployments.',
    'google_rate_limited' => 'Google returned HTTP 429 (too many requests). Wait a few minutes and try again.',
    'google_server_error' => 'Google returned a 5xx error. Retry later; if it continues, check Apps Script execution logs and quotas.',
    'google_client_error' => 'Google returned a 4xx error other than 403/404. Check the webhook URL and deployment; run php tools/registration-transport-test.php to see the status line in the raw diagnostic.',
    'google_empty_response' => 'Google returned an empty HTTP body. Usually a proxy or network issue; run php tools/registration-transport-test.php.',
    'transport_malformed' => 'Could not parse the HTTP response from Google (unexpected headers or redirect). Try registration.use_curl => false or true to switch transport; run php tools/registration-transport-test.php.',
    'transport_http_parse' => 'Could not read the HTTP status from Google (often fixed by updating PHP/cURL). Run php tools/registration-transport-test.php; set registration.transport_diag_log in includes/config.php to log each failure to a file.',
    'curl_tls_handshake' => 'TLS handshake to script.google.com failed (cURL error 35/60 or similar) even though a connection was attempted. Try another PHP build, ensure Windows root certificates are current, or set registration.insecure_ssl => true only on localhost. See doc/registration-php-google-handoff.html.',
    'http_failed' => 'Could not complete HTTPS to Google Apps Script. Enable PHP openssl and/or cURL; on Windows localhost try registration.insecure_ssl => true; on production use tools/cacert.pem or registration.ca_bundle. Try registration.use_curl => false. Run php tools/registration-transport-test.php and optionally set registration.transport_diag_log to a writable file path. See doc/registration-php-google-handoff.html.',
    'ssl_failed' => 'HTTPS to Google failed (certificate verify). Use tools/cacert.pem, set registration.ca_bundle or contact.sheet_webhook_cainfo in includes/config.php, or openssl.cafile in php.ini. For localhost only you may set registration.insecure_ssl => true (not for production).',
    'bad_response' => 'Unexpected response from the registration service.',
    'url_fopen_disabled' => 'This server cannot send HTTPS requests (allow_url_fopen is off). Ask hosting to enable it or use a different setup.',
    'https_unavailable' => 'This server cannot send HTTPS to Google: enable extension=openssl and allow_url_fopen for the default path, or enable PHP cURL (extension=php_curl / php_curl.dll) so registration can POST without the OpenSSL stream wrapper. Set registration.ca_bundle or use tools/cacert.pem if you see certificate errors. Restart PHP after changing php.ini.',
    'openssl_required' => 'This server cannot send HTTPS to Google: enable extension=openssl and allow_url_fopen for the default path, or enable PHP cURL (extension=php_curl / php_curl.dll) so registration can POST without the OpenSSL stream wrapper. Set registration.ca_bundle or use tools/cacert.pem if you see certificate errors. Restart PHP after changing php.ini.',
    'bad_payload' => 'Could not encode your submission. Please try again or email the secretariat.',
    'unauthorized' => 'Server configuration error (secret mismatch). Contact the secretariat.',
    'rejected' => 'Submission was rejected. Check your details and try again.',
    'reg_browser_required' => 'Browser registration needs JavaScript. Enable it and try again, or set registration.use_browser_post => false in includes/config.php so the server forwards the form (requires the server to reach Google).',
);
if ($shmsRegDisplayMode === 'flash_only') {
    $shmsRegPagePath = shms_registration_page_url_path();
    ?>
<div class="registration-php-form registration-php-form--flash-only" id="<?php echo htmlspecialchars($shmsRegFormBlockId, ENT_QUOTES, 'UTF-8'); ?>">
  <?php if ($shmsRegFlash === 'ok') : ?>
  <p class="registration-php-form__flash registration-php-form__flash--ok" role="status">Registration complete. Thank you — the secretariat will match your payment to your registration.</p>
  <p class="registration-payment-note"><a href="<?php echo htmlspecialchars($shmsRegPagePath, ENT_QUOTES, 'UTF-8'); ?>">Back to registration information</a></p>
  <?php elseif ($shmsRegFlash === 'err') : ?>
  <p class="registration-php-form__flash registration-php-form__flash--err" role="alert"><?php
      $msg = isset($shmsRegErrHuman[$shmsRegErrCode]) ? $shmsRegErrHuman[$shmsRegErrCode] : $shmsRegErrHuman['rejected'];
      echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
  ?></p>
  <p class="registration-payment-note">Use the button on the registration page to open the form again, or email <a href="mailto:shms2026@mnnit.ac.in">shms2026@mnnit.ac.in</a>.</p>
  <?php endif; ?>
</div>
    <?php
    return;
}
?>
<div class="registration-php-form" id="<?php echo htmlspecialchars($shmsRegFormBlockId, ENT_QUOTES, 'UTF-8'); ?>">
  <?php if ($shmsRegFlash !== 'ok') : ?>
  <?php if (!$shmsRegInPopup) : ?>
  <h3 class="registration-php-form__title">Submit registration &amp; upload receipt</h3>
  <p class="registration-payment-note">After payment, complete this form once.</p>
  <?php else : ?>
  <p class="registration-payment-note">Submit once after payment. After you press Submit, keep this window open — you will see “Please wait…” until the window closes automatically and your receipt opens in the main site.</p>
  <?php endif; ?>
  <?php endif; ?>

  <?php if ($shmsRegFlash === 'ok') : ?>
  <p class="registration-php-form__flash registration-php-form__flash--ok" role="status">Registration complete. Thank you — the secretariat will match your payment to your registration.</p>
  <?php if ($shmsRegInPopup) : ?>
  <p class="registration-payment-note">You may close this window, or <a href="<?php echo htmlspecialchars(shms_registration_page_url_path(), ENT_QUOTES, 'UTF-8'); ?>">return to the registration page</a>.</p>
  <?php endif; ?>
  <?php elseif ($shmsRegFlash === 'err') : ?>
  <p class="registration-php-form__flash registration-php-form__flash--err" role="alert"><?php
      $msg = isset($shmsRegErrHuman[$shmsRegErrCode]) ? $shmsRegErrHuman[$shmsRegErrCode] : $shmsRegErrHuman['rejected'];
      echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
  ?></p>
  <?php endif; ?>

  <?php if ($shmsRegFlash === 'ok') : ?>
  <p class="registration-payment-note"><?php if ($shmsRegInPopup) : ?><a href="<?php echo htmlspecialchars(shms_registration_popup_url_path(), ENT_QUOTES, 'UTF-8'); ?>">Submit another registration</a><?php else : ?><a href="<?php echo htmlspecialchars(shms_registration_page_url_path(), ENT_QUOTES, 'UTF-8'); ?>">Clear and submit another registration</a><?php endif; ?></p>
  <?php else : ?>
  <?php if ($shmsRegBrowserSubmit) : ?>
  <p class="registration-payment-note"><?php echo $shmsRegInPopup ? 'Your submission is sent <strong>directly from this window</strong> to Google.' : 'Your submission is sent <strong>directly from your browser</strong> to Google (same as opening the live web app). This works when the conference website server cannot reach Google. If JavaScript is disabled, the form is sent via the server instead.'; ?></p>
  <p class="registration-php-form__flash registration-php-form__flash--err" id="<?php echo htmlspecialchars($shmsRegBrowserErrId, ENT_QUOTES, 'UTF-8'); ?>" style="display:none" role="alert"></p>
  <?php endif; ?>
  <?php if ($shmsRegInPopup && $shmsRegBrowserSubmit) : ?>
  <style>
  .registration-popup-wait {
    display: none;
    position: fixed;
    z-index: 2147483000;
    inset: 0;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    box-sizing: border-box;
    margin: 0;
    background: rgba(248, 250, 252, 0.96);
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    padding: 1.5rem;
  }
  html.theme-dark .registration-popup-wait { background: rgba(15, 23, 42, 0.96); }
  .registration-popup-wait.is-visible { display: flex; }
  .registration-popup-wait h2 { font-size: 1.1rem; margin: 0 0 0.5rem; color: #0f2847; font-weight: 700; }
  html.theme-dark .registration-popup-wait h2 { color: #f1f5f9; }
  .registration-popup-wait p { margin: 0; color: #475569; font-size: 0.95rem; max-width: 24rem; line-height: 1.55; }
  html.theme-dark .registration-popup-wait p { color: #94a3b8; }
  </style>
  <div id="registration-popup-wait" class="registration-popup-wait" role="status" aria-live="polite" aria-hidden="true">
    <h2>Please wait…</h2>
    <p>Uploading your receipt and confirming with the registration service. This window will close automatically when finished.</p>
  </div>
  <?php endif; ?>
  <form id="<?php echo htmlspecialchars($shmsRegFormElId, ENT_QUOTES, 'UTF-8'); ?>" class="shms-contact-form registration-php-form__form" method="post" action="registration-submit.php" enctype="multipart/form-data" accept-charset="UTF-8" data-shms-reg-browser="<?php echo $shmsRegBrowserSubmit ? '1' : '0'; ?>">
    <input type="hidden" name="shms_receipt_bridge_token" id="shms-receipt-bridge-token" value="<?php echo htmlspecialchars(shms_registration_receipt_bridge_token(), ENT_QUOTES, 'UTF-8'); ?>">
    <div class="shms-contact-grid2">
      <div class="shms-contact-fieldwrap">
        <label class="shms-contact-label" for="reg-first-name">First name <span class="registration-php-req">*</span></label>
        <input class="shms-contact-field" type="text" name="first_name" id="reg-first-name" required maxlength="120" autocomplete="given-name">
      </div>
      <div class="shms-contact-fieldwrap">
        <label class="shms-contact-label" for="reg-last-name">Last name <span class="registration-php-req">*</span></label>
        <input class="shms-contact-field" type="text" name="last_name" id="reg-last-name" required maxlength="120" autocomplete="family-name">
      </div>
    </div>
    <div class="shms-contact-fieldwrap">
      <label class="shms-contact-label" for="reg-email">Email <span class="registration-php-req">*</span></label>
      <input class="shms-contact-field" type="email" name="email" id="reg-email" required maxlength="254" autocomplete="email">
    </div>
    <div class="shms-contact-fieldwrap">
      <label class="shms-contact-label" for="reg-phone">Phone <span class="registration-php-req">*</span></label>
      <input class="shms-contact-field" type="text" name="phone" id="reg-phone" required maxlength="40" autocomplete="tel">
    </div>
    <div class="shms-contact-fieldwrap">
      <label class="shms-contact-label" for="reg-affiliation">Affiliation / institution</label>
      <input class="shms-contact-field" type="text" name="affiliation" id="reg-affiliation" maxlength="300">
    </div>
    <div class="shms-contact-fieldwrap">
      <label class="shms-contact-label" for="reg-category">Registration category <span class="registration-php-req">*</span></label>
      <select class="shms-contact-select" name="category" id="reg-category" required>
        <option value="">Select…</option>
        <option>Students / Research Scholars</option>
        <option>Faculty Members</option>
        <option>ISHMS Members</option>
        <option>Industry Participants</option>
        <option>Foreign Delegates</option>
      </select>
    </div>
    <div class="shms-contact-fieldwrap">
      <label class="shms-contact-label" for="reg-tx">Transaction ID / UTR / reference <span class="registration-php-req">*</span></label>
      <input class="shms-contact-field" type="text" name="transaction_id" id="reg-tx" required maxlength="120" autocomplete="off">
    </div>
    <div class="shms-contact-fieldwrap">
      <label class="shms-contact-label" for="reg-amount">Amount paid (incl. 18% GST)</label>
      <input class="shms-contact-field" type="text" name="amount" id="reg-amount" maxlength="20" placeholder="e.g. 8850">
    </div>
    <div class="shms-contact-fieldwrap">
      <label class="shms-contact-label" for="reg-channel">Payment channel <span class="registration-php-req">*</span></label>
      <select class="shms-contact-select" name="payment_channel" id="reg-channel" required>
        <option value="">Select…</option>
        <option>UPI</option>
        <option>IMPS</option>
        <option>NEFT</option>
        <option>RTGS</option>
        <option>Net banking / bank transfer</option>
        <option>International wire / SWIFT</option>
        <option>Other</option>
      </select>
    </div>
    <div class="shms-contact-fieldwrap">
      <label class="shms-contact-label" for="reg-notes">Notes for secretariat (optional)</label>
      <textarea class="shms-contact-textarea" name="notes" id="reg-notes" rows="3" maxlength="2000"></textarea>
    </div>
    <div class="shms-contact-fieldwrap">
      <label class="shms-contact-label" for="reg-receipt">Transaction receipt (PDF, PNG, JPG — max 5&nbsp;MB) <span class="registration-php-req">*</span></label>
      <input class="shms-contact-field" type="file" name="receipt" id="reg-receipt" required accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg">
    </div>
    <p class="registration-php-form__fineprint"><span class="registration-php-req">*</span> Required fields.</p>
    <div class="registration-payment-cta registration-payment-cta--row">
      <button type="submit" class="registration-payment-cta__btn registration-payment-cta__btn--primary registration-php-form__submit" id="<?php echo htmlspecialchars($shmsRegFormBlockId, ENT_QUOTES, 'UTF-8'); ?>-submit-btn">Submit</button>
    </div>
  </form>
  <?php if ($shmsRegBrowserSubmit) : ?>
  <noscript>
    <p class="registration-payment-note" role="note">JavaScript is off — your data will be sent through this server to Google. If submission fails, enable JavaScript or email <a href="mailto:shms2026@mnnit.ac.in">shms2026@mnnit.ac.in</a>.</p>
  </noscript>
  <script>
  (function () {
    var formId = <?php echo json_encode($shmsRegFormElId); ?>;
    var errId = <?php echo json_encode($shmsRegBrowserErrId); ?>;
    var btnId = <?php echo json_encode($shmsRegFormBlockId . '-submit-btn'); ?>;
    var webhookUrl = <?php echo json_encode(shms_registration_post_url()); ?>;
    var submitSecret = <?php echo json_encode(shms_registration_submit_secret()); ?>;
    var bridgeUrl = <?php echo json_encode(shms_registration_receipt_bridge_path()); ?>;
    var inPopup = <?php echo !empty($shmsRegInPopup) ? 'true' : 'false'; ?>;
    var maxBytes = 5242880;
    var mimeByExt = { pdf: 'application/pdf', png: 'image/png', jpg: 'image/jpeg', jpeg: 'image/jpeg' };
    var form = document.getElementById(formId);
    var errEl = document.getElementById(errId);
    var btn = document.getElementById(btnId);
    var waitEl = document.getElementById('registration-popup-wait');
    if (!form || form.getAttribute('data-shms-reg-browser') !== '1' || !webhookUrl) {
      return;
    }
    function showErr(msg) {
      if (errEl) {
        errEl.style.display = 'block';
        errEl.textContent = msg;
      }
    }
    function hideErr() {
      if (errEl) {
        errEl.style.display = 'none';
        errEl.textContent = '';
      }
    }
    function showWait() {
      if (waitEl) {
        waitEl.classList.add('is-visible');
        waitEl.setAttribute('aria-hidden', 'false');
      }
    }
    function hideWait() {
      if (waitEl) {
        waitEl.classList.remove('is-visible');
        waitEl.setAttribute('aria-hidden', 'true');
      }
    }
    function extOf(name) {
      var i = name.lastIndexOf('.');
      return i >= 0 ? name.substring(i + 1).toLowerCase() : '';
    }
    function parseInrDecl(s) {
      s = String(s || '')
        .trim()
        .replace(/,/g, '');
      if (!s) {
        return null;
      }
      var t = s.replace(/[^\d.]/g, '');
      if (!t || isNaN(parseFloat(t))) {
        return null;
      }
      return parseFloat(t);
    }
    function localSubmittedAt() {
      var d = new Date();
      function p(n) {
        return n < 10 ? '0' + n : String(n);
      }
      return d.getFullYear() + '-' + p(d.getMonth() + 1) + '-' + p(d.getDate()) + ' ' + p(d.getHours()) + ':' + p(d.getMinutes());
    }
    function postReceiptToBridge(rjson) {
      var tokenEl = document.getElementById('shms-receipt-bridge-token');
      var token = tokenEl ? String(tokenEl.value || '') : '';
      if (!token) {
        hideWait();
        showErr('Session expired. Reload this page and try again.');
        if (btn) {
          btn.disabled = false;
        }
        return;
      }
      var targetDoc = document;
      var closeAfter = false;
      if (inPopup && window.opener && !window.opener.closed) {
        try {
          var od = window.opener.document;
          if (od && od.body) {
            targetDoc = od;
            closeAfter = true;
          }
        } catch (eOpener) {
          targetDoc = document;
          closeAfter = false;
        }
      }
      var f = targetDoc.createElement('form');
      f.method = 'POST';
      f.action = bridgeUrl;
      f.setAttribute('accept-charset', 'UTF-8');
      var h1 = targetDoc.createElement('input');
      h1.type = 'hidden';
      h1.name = 'shms_receipt_bridge_token';
      h1.value = token;
      var h2 = targetDoc.createElement('input');
      h2.type = 'hidden';
      h2.name = 'receipt_json';
      h2.value = rjson;
      f.appendChild(h1);
      f.appendChild(h2);
      targetDoc.body.appendChild(f);
      f.submit();
      if (closeAfter) {
        window.setTimeout(function () {
          try {
            window.close();
          } catch (eClose) {}
        }, 150);
      }
    }
    function localUniqueReceiptStem() {
      var d = new Date();
      function pad(n) {
        return n < 10 ? '0' + n : String(n);
      }
      var ts =
        d.getFullYear() +
        pad(d.getMonth() + 1) +
        pad(d.getDate()) +
        pad(d.getHours()) +
        pad(d.getMinutes()) +
        pad(d.getSeconds());
      var hex = '';
      if (window.crypto && typeof window.crypto.getRandomValues === 'function') {
        var arr = new Uint8Array(5);
        window.crypto.getRandomValues(arr);
        for (var ri = 0; ri < arr.length; ri++) {
          hex += ('0' + arr[ri].toString(16)).slice(-2);
        }
      } else {
        hex = String(Math.floor(Math.random() * 1e12));
      }
      return ts + '-' + hex.toUpperCase();
    }
    form.addEventListener('submit', function (ev) {
      if (typeof fetch === 'undefined' || typeof JSON === 'undefined' || !JSON.stringify) {
        return;
      }
      ev.preventDefault();
      hideErr();
      var first = (form.querySelector('[name="first_name"]') || {}).value;
      var last = (form.querySelector('[name="last_name"]') || {}).value;
      var email = (form.querySelector('[name="email"]') || {}).value;
      var phone = (form.querySelector('[name="phone"]') || {}).value;
      var affiliation = (form.querySelector('[name="affiliation"]') || {}).value;
      var category = (form.querySelector('[name="category"]') || {}).value;
      var tx = (form.querySelector('[name="transaction_id"]') || {}).value;
      var amount = (form.querySelector('[name="amount"]') || {}).value;
      var channel = (form.querySelector('[name="payment_channel"]') || {}).value;
      var notes = (form.querySelector('[name="notes"]') || {}).value;
      first = first ? String(first).trim() : '';
      last = last ? String(last).trim() : '';
      email = email ? String(email).trim() : '';
      phone = phone ? String(phone).trim() : '';
      category = category ? String(category).trim() : '';
      tx = tx ? String(tx).trim() : '';
      channel = channel ? String(channel).trim() : '';
      if (!first || !last || !email || !phone || !category || !tx || !channel) {
        showErr('Please fill in all required fields.');
        return;
      }
      var fin = form.querySelector('[name="receipt"]');
      if (!fin || !fin.files || !fin.files[0]) {
        showErr('Please attach your payment receipt.');
        return;
      }
      var file = fin.files[0];
      if (file.size > maxBytes) {
        showErr('Receipt must be 5 MB or smaller.');
        return;
      }
      var ext = extOf(file.name);
      if (!mimeByExt[ext]) {
        showErr('Receipt must be a PDF, PNG, JPG, or JPEG file.');
        return;
      }
      var mimeType = mimeByExt[ext];
      showWait();
      if (btn) {
        btn.disabled = true;
      }
      var reader = new FileReader();
      reader.onload = function () {
        var dataUrl = reader.result;
        var comma = typeof dataUrl === 'string' ? dataUrl.indexOf(',') : -1;
        var b64 = comma >= 0 ? dataUrl.substring(comma + 1) : '';
        if (!b64) {
          hideWait();
          if (btn) {
            btn.disabled = false;
          }
          showErr('Could not read the receipt file. Try another file or browser.');
          return;
        }
        var payload = {
          firstName: first,
          lastName: last,
          email: email,
          phone: phone,
          affiliation: affiliation ? String(affiliation).trim() : '',
          category: category,
          transactionId: tx,
          amount: amount ? String(amount).trim() : '',
          paymentChannel: channel,
          notes: notes ? String(notes).trim() : '',
          fileName: file.name,
          mimeType: mimeType,
          fileBase64: b64
        };
        if (submitSecret) {
          payload.submitSecret = submitSecret;
        }
        fetch(webhookUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'text/plain' },
          body: JSON.stringify(payload),
          mode: 'cors',
          credentials: 'omit',
          redirect: 'follow'
        })
          .then(function (r) {
            return r.text().then(function (text) {
              return { okHttp: r.ok, status: r.status, text: text };
            });
          })
          .then(function (res) {
            var text = res.text;
            var raw = typeof text === 'string' ? text.replace(/^\uFEFF/, '').trim() : '';
            var j = null;
            try {
              j = raw ? JSON.parse(raw) : null;
            } catch (e1) {
              j = null;
            }
            if (j && j.ok === true) {
              var num = parseInrDecl(amount);
              var sub = null;
              var gst = null;
              var grand = null;
              var rate = 18;
              if (num !== null) {
                grand = Math.round(num * 100) / 100;
                var divisor = 1 + rate / 100;
                sub = Math.round((grand / divisor) * 100) / 100;
                gst = Math.round((grand - sub) * 100) / 100;
              }
              var rid = j.receiptNo ? String(j.receiptNo) : '';
              var regno = j.registrationNo ? String(j.registrationNo) : '';
              if (!rid) {
                rid = 'SHMS2026/R' + localUniqueReceiptStem();
              }
              if (!regno) {
                regno = rid;
              }
              var when = j.submittedAt ? String(j.submittedAt) : localSubmittedAt();
              var receiptObj = {
                receiptNo: rid,
                registrationNo: regno,
                fullName: (first + ' ' + last).trim(),
                email: email,
                phone: phone,
                affiliation: affiliation ? String(affiliation).trim() : '',
                category: category,
                transactionId: tx,
                amountDeclared: amount ? String(amount).trim() : '',
                paymentChannel: channel,
                notes: notes ? String(notes).trim() : '',
                subTotal: sub,
                gstRate: rate,
                gst: gst,
                grandTotal: grand,
                fileUrl: j.fileUrl ? String(j.fileUrl) : '',
                receiptPdfUrl: j.receiptPdfUrl ? String(j.receiptPdfUrl) : '',
                receiptPdfError: j.receiptPdfError ? String(j.receiptPdfError) : '',
                submittedAt: when
              };
              var rjson = JSON.stringify(receiptObj);
              postReceiptToBridge(rjson);
              return;
            }
            var msg;
            if (!j && raw && (/^\s*<!DOCTYPE/i.test(raw) || /^\s*<html/i.test(raw))) {
              msg =
                'Google returned a sign-in page or HTML instead of JSON. Redeploy the web app with “Who has access: Anyone” and use the /exec URL in registration.webhook_url. Email shms2026@mnnit.ac.in if this continues.';
            } else if (j && j.error) {
              var code = String(j.error);
              var map = {
                unauthorized: 'Server secret mismatch (submit_secret). Contact the secretariat.',
                missing_fields: 'Please fill in all required fields.',
                missing_file: 'Receipt was not accepted. Try re-attaching the file.',
                invalid_file_type: 'Receipt must be PDF, PNG, or JPEG.',
                file_too_large: 'Receipt must be 5 MB or smaller.',
                empty_file: 'The receipt file appears empty.',
                invalid_base64: 'Could not send the file. Try a smaller file or different format.',
                config_missing: 'Registration service is not fully configured on Google’s side.',
                save_failed: 'Google could not save your registration. Try again later or email shms2026@mnnit.ac.in.'
              };
              msg = map[code] || ('Registration service reported: “' + code.substring(0, 200) + '”. Email shms2026@mnnit.ac.in if you need help.');
            } else if (!j && raw) {
              msg =
                'Unexpected response (HTTP ' +
                res.status +
                '). Check the webhook URL. Start of reply: “' +
                raw.substring(0, 100).replace(/\s+/g, ' ') +
                '…”.';
            } else {
              msg = 'Empty or unreadable response (HTTP ' + res.status + '). Try again or email shms2026@mnnit.ac.in.';
            }
            hideWait();
            showErr(msg);
            if (btn) {
              btn.disabled = false;
            }
          })
          .catch(function () {
            hideWait();
            showErr(
              'Could not reach Google from this browser (network, VPN, or blocker). Try another network or disable extensions. You can set registration.use_browser_post => false only if the web server can reach script.google.com.'
            );
            if (btn) {
              btn.disabled = false;
            }
          });
      };
      reader.onerror = function () {
        hideWait();
        if (btn) {
          btn.disabled = false;
        }
        showErr('Could not read the receipt file.');
      };
      reader.readAsDataURL(file);
    });
  })();
  </script>
  <?php endif; ?>
  <?php endif; ?>
</div>
