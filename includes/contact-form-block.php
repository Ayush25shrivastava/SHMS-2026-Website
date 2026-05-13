<?php
/**
 * Renders the contact hub + form (expects $shmsContactFeedback from contact.php).
 *
 * @var array|null $shmsContactFeedback
 */
if (!function_exists('shms_contact_h')) {
    require_once dirname(__FILE__) . '/contact-form.php';
}

$useGoogle = shms_contact_uses_google_form();
$wantGoogle = shms_contact_form_backend() === 'google';
$wantSheet = shms_contact_form_backend() === 'sheet';
$wantSheetBrowser = shms_contact_form_backend() === 'sheet_browser';
$usesBrowserClient = shms_contact_sheet_delivery_via_browser();
$sheetBrowserReady = shms_contact_sheet_browser_ready();
$googleEmbed = shms_contact_google_form_embed_url();

$cfVals = (is_array($shmsContactFeedback) && isset($shmsContactFeedback['values']) && is_array($shmsContactFeedback['values']))
    ? $shmsContactFeedback['values']
    : array();

$vFirst = isset($cfVals['first_name']) ? (string) $cfVals['first_name'] : '';
$vLast = isset($cfVals['last_name']) ? (string) $cfVals['last_name'] : '';
$vEmail = isset($cfVals['email']) ? (string) $cfVals['email'] : '';
$vCat = isset($cfVals['contact_category']) ? (string) $cfVals['contact_category'] : '';
$vRole = isset($cfVals['contact_role']) ? (string) $cfVals['contact_role'] : '';
$vUrg = isset($cfVals['contact_urgency']) ? (string) $cfVals['contact_urgency'] : '';
$vSubj = isset($cfVals['subject_line']) ? (string) $cfVals['subject_line'] : '';
$vMsg = isset($cfVals['message']) ? (string) $cfVals['message'] : '';

if (!$useGoogle) {
    $csrf = shms_contact_csrf_token();
    $shmsContactCaptcha = shms_contact_captcha_issue();
}

$sentFlash = !$useGoogle && isset($_GET['sent']) && $_GET['sent'] === '1';
$errList = (!$useGoogle && is_array($shmsContactFeedback) && isset($shmsContactFeedback['errors']) && is_array($shmsContactFeedback['errors']))
    ? $shmsContactFeedback['errors']
    : array();
?>
  <section class="shms-contact-hub<?php
    if ($useGoogle) {
        echo ' shms-contact-hub--google';
    } else {
        echo ' shms-contact-hub--native';
        if ($wantSheet) {
            echo ' shms-contact-hub--sheet';
        }
        if ($usesBrowserClient) {
            echo ' shms-contact-hub--sheet-browser';
        }
    }
?>" id="message-us" aria-labelledby="shms-contact-hub-title">
    <div class="shms-contact-hub__intro">
      <h2 id="shms-contact-hub-title">Send us a message</h2>
      <?php if ($useGoogle) : ?>
      <p class="shms-contact-hub__lede">Complete the form below — your enquiry goes to the SHMS‑2026 secretariat at <strong><?php echo shms_contact_h(shms_contact_mail_to()); ?></strong>. Replies are addressed to the email you enter.</p>
      <p class="shms-contact-hub__lede shms-contact-hub__lede--secondary"><a class="shms-contact-google-open" href="<?php echo shms_contact_h($googleEmbed); ?>" target="_blank" rel="noopener noreferrer">Open in full window</a> if the form is hard to use here.</p>
      <?php elseif ($usesBrowserClient) : ?>
      <p class="shms-contact-hub__lede">Fill in the form below. After the security check, your browser sends the message to our logging service (PHP cURL is not used on this server for this step). We receive it at <strong><?php echo shms_contact_h(shms_contact_mail_to()); ?></strong> and reply to the address you provide. <strong>JavaScript must be enabled</strong> to complete sending.</p>
      <?php elseif ($wantSheet) : ?>
      <p class="shms-contact-hub__lede">Fill in the form below. We will receive your message at <strong><?php echo shms_contact_h(shms_contact_mail_to()); ?></strong> and reply to the address you provide.</p>
      <?php else : ?>
      <p class="shms-contact-hub__lede">Submissions go to <strong><?php echo shms_contact_h(shms_contact_mail_to()); ?></strong>. The address you type in the <strong>Email</strong> field is set as <strong>Reply-To</strong>, so the secretariat can reply to you directly.</p>
      <?php endif; ?>
    </div>

    <?php if ($wantGoogle && !$useGoogle) : ?>
    <div class="shms-contact-localhost-note shms-contact-localhost-note--warn" role="alert">
      <strong>Google Form mode</strong> is selected (<code>form_backend</code> = <code>google</code>) but <code>google_form_embed_url</code> is missing or invalid. Add the embed URL from Google Forms → <strong>Send</strong> → <strong>&lt;&gt; Embed HTML</strong> (<code>src="…"</code>) to <code>includes/config.php</code>, or switch <code>form_backend</code> to <code>php</code>. Showing the built-in PHP form below until then.
    </div>
    <?php endif; ?>

    <?php if ($wantSheetBrowser && !$sheetBrowserReady) : ?>
    <div class="shms-contact-localhost-note shms-contact-localhost-note--warn" role="alert">
      <strong>Sheet (browser) mode</strong> needs <code>sheet_webhook_url</code> and a 12+ character token: <code>sheet_browser_token</code> (matches Apps Script <code>BROWSER_TOKEN</code> if set) or <code>sheet_webhook_secret</code> with <code>sheet_browser_token</code> left empty (matches <code>WEBHOOK_SECRET</code>). See <code>includes/google-sheet-webhook-apps-script.txt</code>. Redeploy the Web app after script changes.
    </div>
    <?php endif; ?>

    <?php if ($wantSheet && !shms_contact_sheet_curl_available() && !$usesBrowserClient) : ?>
    <div class="shms-contact-localhost-note shms-contact-localhost-note--warn" role="alert">
      <strong>Sheet mode without PHP cURL</strong> needs <code>sheet_webhook_url</code> and <code>sheet_webhook_secret</code> (12+ characters, same as Apps Script <code>WEBHOOK_SECRET</code>). The contact form will then deliver via the visitor’s browser. Redeploy the Web app from <code>includes/google-sheet-webhook-apps-script.txt</code> if it is an older copy.
    </div>
    <?php endif; ?>

    <div class="shms-contact-shell">
      <div class="shms-contact-panel<?php echo $useGoogle ? ' shms-contact-panel--google' : ''; ?>" role="region" aria-label="<?php echo $useGoogle ? 'Contact form' : 'Contact form'; ?>">
        <?php if ($useGoogle) : ?>
        <div class="shms-contact-google-wrap">
          <iframe class="shms-contact-google-frame" title="SHMS-2026 contact form" src="<?php echo shms_contact_h($googleEmbed); ?>" width="100%" height="920" loading="lazy" frameborder="0" marginheight="0" marginwidth="0">Loading…</iframe>
        </div>
        <?php else : ?>

        <?php if ($sentFlash) : ?>
        <div class="shms-contact-flash shms-contact-flash--success" role="status">
          <span class="shms-contact-flash__icon" aria-hidden="true">✓</span>
          <div>
            <strong>Message sent</strong>
            <p>Thank you. We have received your message and will reply to the address you provided.</p>
          </div>
        </div>
        <?php endif; ?>

        <?php if (count($errList) > 0) : ?>
        <div class="shms-contact-flash shms-contact-flash--error" role="alert">
          <span class="shms-contact-flash__icon" aria-hidden="true">!</span>
          <div>
            <strong>Please fix the following</strong>
            <ul>
              <?php foreach ($errList as $er) : ?>
              <li><?php echo shms_contact_h($er); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <?php endif; ?>

        <form class="shms-contact-form" method="post" action="contact.php#message-us" accept-charset="UTF-8" novalidate>
          <input type="hidden" name="csrf" value="<?php echo shms_contact_h($csrf); ?>">
          <div class="shms-contact-hp" aria-hidden="true">
            <input type="text" name="shms_trhp" id="shms_trhp" value="" tabindex="-1" autocomplete="new-password" inputmode="none" aria-hidden="true">
          </div>

          <div class="shms-contact-grid2">
            <div class="shms-contact-fieldwrap">
              <label class="shms-contact-label" for="first_name">First name</label>
              <input class="shms-contact-field" type="text" name="first_name" id="first_name" maxlength="120" required value="<?php echo shms_contact_h($vFirst); ?>" autocomplete="given-name">
            </div>
            <div class="shms-contact-fieldwrap">
              <label class="shms-contact-label" for="last_name">Last name</label>
              <input class="shms-contact-field" type="text" name="last_name" id="last_name" maxlength="120" required value="<?php echo shms_contact_h($vLast); ?>" autocomplete="family-name">
            </div>
          </div>

          <div class="shms-contact-fieldwrap">
            <label class="shms-contact-label" for="email">Email</label>
            <input class="shms-contact-field" type="email" name="email" id="email" maxlength="254" required value="<?php echo shms_contact_h($vEmail); ?>" autocomplete="email" inputmode="email">
          </div>

          <div class="shms-contact-grid2 shms-contact-grid2--narrow">
            <div class="shms-contact-fieldwrap">
              <label class="shms-contact-label" for="contact_category">Topic</label>
              <?php echo shms_contact_select_html('contact_category', shms_contact_categories(), $vCat, true); ?>
            </div>
            <div class="shms-contact-fieldwrap">
              <label class="shms-contact-label" for="contact_role">Your role</label>
              <?php echo shms_contact_select_html('contact_role', shms_contact_roles(), $vRole, false); ?>
            </div>
          </div>

          <div class="shms-contact-fieldwrap">
            <label class="shms-contact-label" for="contact_urgency">When do you need a reply?</label>
            <?php echo shms_contact_select_html('contact_urgency', shms_contact_urgency(), $vUrg, true); ?>
          </div>

          <div class="shms-contact-fieldwrap">
            <label class="shms-contact-label" for="subject_line">Subject</label>
            <input class="shms-contact-field" type="text" name="subject_line" id="subject_line" maxlength="200" required placeholder="Brief summary of your enquiry" value="<?php echo shms_contact_h($vSubj); ?>">
          </div>

          <div class="shms-contact-fieldwrap">
            <label class="shms-contact-label" for="message">Message</label>
            <textarea class="shms-contact-field shms-contact-textarea" name="message" id="message" rows="6" maxlength="12000" required placeholder="Your message…"><?php echo shms_contact_h($vMsg); ?></textarea>
          </div>

          <div class="shms-contact-fieldwrap shms-contact-captcha">
            <label class="shms-contact-label" for="captcha_human">Security check</label>
            <p class="shms-contact-captcha__prompt" id="captcha_human-desc"><?php echo shms_contact_h($shmsContactCaptcha['question']); ?></p>
            <input class="shms-contact-field shms-contact-captcha__input" type="text" name="captcha_human" id="captcha_human" maxlength="3" required autocomplete="off" inputmode="numeric" pattern="[0-9]{1,3}" aria-describedby="captcha_human-desc" placeholder="Answer">
          </div>

          <button type="submit" name="shms_contact_submit" value="1" class="shms-contact-submit">
            <svg class="shms-contact-submit__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Send message</span>
          </button>
        </form>

        <?php if (is_array($shmsContactFeedback) && !empty($shmsContactFeedback['sheet_browser_pending'])) : ?>
        <?php
            $sbUrl = isset($shmsContactFeedback['sheet_browser_url']) ? (string) $shmsContactFeedback['sheet_browser_url'] : '';
            $sbB64 = isset($shmsContactFeedback['sheet_browser_payload_b64']) ? (string) $shmsContactFeedback['sheet_browser_payload_b64'] : '';
        ?>
        <div class="shms-contact-flash shms-contact-flash--pending" id="shms-sheet-browser-wrap" role="status" aria-live="polite">
          <span class="shms-contact-flash__icon" aria-hidden="true">…</span>
          <div>
            <strong>Sending your message</strong>
            <p id="shms-sheet-browser-status">Please wait — delivering to the conference log…</p>
          </div>
        </div>
        <noscript>
          <div class="shms-contact-flash shms-contact-flash--error" role="alert">
            <strong>JavaScript required</strong>
            <p>This delivery mode finishes in your browser. Enable JavaScript and try again, or email <?php echo shms_contact_h(shms_contact_mail_to()); ?> directly.</p>
          </div>
        </noscript>
        <script>
        (function () {
          var url = <?php echo json_encode($sbUrl); ?>;
          var b64 = <?php echo json_encode($sbB64); ?>;
          var statusEl = document.getElementById('shms-sheet-browser-status');
          var wrap = document.getElementById('shms-sheet-browser-wrap');
          if (!url || !b64 || typeof atob === 'undefined' || typeof JSON === 'undefined' || !JSON.parse) {
            if (statusEl) {
              statusEl.textContent = 'Could not start delivery. Please email the secretariat directly.';
            }
            return;
          }
          var payload;
          try {
            payload = JSON.parse(atob(b64));
          } catch (e1) {
            if (statusEl) {
              statusEl.textContent = 'Could not prepare delivery. Please try again or email the secretariat.';
            }
            return;
          }
          /* JSON as text/plain avoids a CORS preflight (application/json triggers OPTIONS; Apps Script often does not answer it). doPost still receives the same body. */
          fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'text/plain' },
            body: JSON.stringify(payload),
            mode: 'cors',
            credentials: 'omit',
            redirect: 'follow'
          }).then(function (r) {
            return r.text().then(function (text) {
              return { okHttp: r.ok, status: r.status, text: text };
            });
          }).then(function (res) {
            var text = res.text;
            var raw = typeof text === 'string' ? text.replace(/^\uFEFF/, '').trim() : '';
            var j = null;
            try {
              j = raw ? JSON.parse(raw) : null;
            } catch (e2) {
              j = null;
            }
            if (j && j.ok === true) {
              window.location.replace('contact.php?sent=1#message-us');
              return;
            }
            if (statusEl) {
              var msg;
              if (!j && raw && (/^\s*<!DOCTYPE/i.test(raw) || /^\s*<html/i.test(raw))) {
                msg =
                  'Google returned HTML instead of JSON (wrong Web App /exec URL, or access not set to “Anyone”). Check sheet_webhook_url in includes/config.php. Email <?php echo shms_contact_h(shms_contact_mail_to()); ?>.';
              } else if (j && j.error) {
                var code = String(j.error);
                var map = {
                  unauthorized:
                    'The webhook rejected this request (unauthorized). In Apps Script → Project Settings → Script properties: WEBHOOK_SECRET must exactly match sheet_webhook_secret in includes/config.php. If BROWSER_TOKEN is set there, set sheet_browser_token in config to the same value (or remove BROWSER_TOKEN to allow browser_token to match WEBHOOK_SECRET). Redeploy the Web app after changes.',
                  'missing SPREADSHEET_ID script property':
                    'Apps Script is missing the SPREADSHEET_ID script property. Add it in Script properties and redeploy.'
                };
                msg =
                  map[code] ||
                  ('Google reported: “' + code.substring(0, 220) + '”. Check Apps Script → Executions for details. Email <?php echo shms_contact_h(shms_contact_mail_to()); ?>.');
              } else if (!j && raw) {
                msg =
                  'Unexpected response (HTTP ' +
                  res.status +
                  '). Start of reply: “' +
                  raw.substring(0, 120).replace(/\s+/g, ' ') +
                  '…”. Confirm sheet_webhook_url is the current /exec URL. Email <?php echo shms_contact_h(shms_contact_mail_to()); ?>.';
              } else {
                msg =
                  'Delivery failed (HTTP ' + res.status + ', empty or unreadable reply). Email <?php echo shms_contact_h(shms_contact_mail_to()); ?> or try again later.';
              }
              statusEl.textContent = msg;
            }
            if (wrap) {
              wrap.className = 'shms-contact-flash shms-contact-flash--error';
            }
          }).catch(function () {
            if (statusEl) {
              statusEl.textContent = 'Could not complete the request (network, extension, or firewall). Try another browser or network. If the web server can reach Google, set sheet_webhook_use_php_curl => true in includes/config.php to send via the server instead. Email <?php echo shms_contact_h(shms_contact_mail_to()); ?>.';
            }
            if (wrap) {
              wrap.className = 'shms-contact-flash shms-contact-flash--error';
            }
          });
        })();
        </script>
        <?php endif; ?>

        <?php endif; ?>
      </div>

      <aside class="shms-contact-sidebar" aria-label="How we respond">
        <div class="shms-contact-card">
          <h3 class="shms-contact-card__title">Direct email</h3>
          <p>For attachments or long technical threads, you can still write to us directly.</p>
          <a class="shms-contact-card__link" href="mailto:<?php echo shms_contact_h(shms_contact_mail_to()); ?>"><?php echo shms_contact_h(shms_contact_mail_to()); ?></a>
        </div>
        <div class="shms-contact-card">
          <h3 class="shms-contact-card__title">Response time</h3>
          <p>We aim to answer general enquiries within a few working days. Mark your message as time-sensitive if the conference dates are close.</p>
        </div>
        <div class="shms-contact-card">
          <h3 class="shms-contact-card__title">Phone</h3>
          <p>Conference secretariat</p>
          <a class="shms-contact-card__link" href="tel:+915322271301">+91‑532‑227‑1301</a>
        </div>
      </aside>
    </div>
  </section>
