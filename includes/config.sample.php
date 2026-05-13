<?php
/**
 * Copy to config.php (config.php must not be committed).
 *
 * This project no longer uses a MySQL-backed visitor counter on MNNIT hosting.
 * Only the optional Flag Counter badge uses this config.
 */
return array(
    /**
     * Contact form delivery:
     *  - If smtp_host + smtp_user + smtp_pass are set, mail is sent via SMTP (e.g. Google:
     *    smtp.gmail.com, port 587, smtp_encryption tls). Use an App Password if the account has 2FA.
     *  - Otherwise PHP mail() is used (typical on MNNIT hosting; not localhost Windows).
     *
     * Localhost trial: use includes/config.php (from this sample) with smtp_* and/or env vars
     * SHMS_SMTP_USER / SHMS_SMTP_PASS (see config.php header comment). Optionally set mail_to to
     * your own address while testing.
     *
     * Production: on institute servers you can remove smtp_* and rely on mail(), or keep SMTP if IT allows.
     *
     * mail_log_path: absolute path to a writable log file (e.g. outside public_html) to record OK/FAIL.
     *
     * Alternative A — Google Form iframe (no PHP mail on submit):
     *   form_backend => 'google' and google_form_embed_url => embed src from Google Forms.
     *   Email on submit: includes/google-form-email-apps-script.txt
     *
     * Alternative B — Native PHP form → Google Sheet + email (no Google Form):
     *   form_backend => 'sheet', sheet_webhook_url, sheet_webhook_secret (same as WEBHOOK_SECRET).
     *   If PHP cURL is available and sheet_webhook_use_php_curl is true (default), the server POSTs JSON;
     *   if cURL is missing or sheet_webhook_use_php_curl is false, the same secret is sent as browser_token
     *   after validation (visitor’s fetch). Redeploy Apps Script
     *   from includes/google-sheet-webhook-apps-script.txt so browser posts are accepted when BROWSER_TOKEN
     *   is not set. On Windows with cURL, tools/cacert.pem helps SSL (error 60).
     *
     * Alternative C — Same as B but always use the browser (never PHP cURL): either form_backend => 'sheet_browser',
     *   or keep 'sheet' and set sheet_webhook_use_php_curl => false. Optional sheet_browser_token
     *   (12+ chars, matches Script BROWSER_TOKEN). If sheet_browser_token is empty, sheet_webhook_secret
     *   is used as browser_token (WEBHOOK_SECRET in Script). JavaScript required on contact.php.
     */
    'contact' => array(
        'mail_to' => 'shms2026@mnnit.ac.in',
        // 'form_backend' => 'php', // 'php' (default) | 'google' | 'sheet' | 'sheet_browser'
        // 'google_form_embed_url' => 'https://docs.google.com/forms/d/e/XXXX/viewform?embedded=true',
        // 'sheet_webhook_url' => 'https://script.google.com/macros/s/XXXX/exec',
        // 'sheet_webhook_secret' => 'use-a-long-random-string-min-12-chars',
        // 'sheet_webhook_use_php_curl' => false, // server cannot reach Google (cURL 28): use visitor browser POST
        // 'sheet_browser_token' => 'different-long-random-string-min-12-chars-for-browser-mode',
        // 'mail_from' => 'webforms@mnnit.ac.in',
        // 'mail_envelope_from' => 'webforms@mnnit.ac.in',
        // 'mail_log_path' => '/path/outside/site/contact-mail.log',

        /* Uncomment in config.php for localhost / Google Workspace SMTP (never commit real passwords). */
        // 'smtp_host' => 'smtp.gmail.com',
        // 'smtp_port' => 587,
        // 'smtp_encryption' => 'tls', // or 'ssl' with port 465
        // 'smtp_user' => 'yourname@mnnit.ac.in',
        // 'smtp_pass' => 'your-google-app-password',
        // 'smtp_from' => 'yourname@mnnit.ac.in', // optional; defaults to smtp_user
        // 'smtp_timeout' => 25,
    ),
    /**
     * Registration + receipt:
     *  - webapp_url: optional Google-hosted form link — https://script.google.com/macros/s/DEPLOYMENT_ID/exec (NOT library URLs like …/macros/library/d/…/6). If empty, that button is hidden.
     *  - webhook_url: same /exec URL for PHP POST (registration-submit.php). Never use a library URL here. Recommended: set webhook_url and leave webapp_url empty so delegates only use the on-site form (avoids browser HTML vs JSON issues with script.google.com).
     *  - If webapp_url is empty and webhook_url is set: embedded PHP form on registration.php → registration-submit.php (no JS on the form).
     *  - php_form_alongside_webapp => true: when webapp_url is set, also show that PHP form on registration.php (delegates can avoid browser POST issues to Google).
     *  - submit_secret: optional; if set, must match CONFIG.SUBMIT_SECRET in includes/registration-webapp/Code.gs.txt.
     *  - use_browser_post: true = visitor’s browser POSTs JSON to webhook_url (text/plain body); use when the server cannot reach script.google.com. false = only registration-submit.php (server-side).
     *  - use_curl: true = try cURL first (needs php_curl), then file_get_contents; false = stream first, then cURL fallback (server path only).
     * HTTPS from PHP: requires OpenSSL+allow_url_fopen and/or cURL as above.
     * For Windows/local dev: place cacert.pem in tools/
     * (https://curl.se/ca/cacert.pem) or set registration.ca_bundle. If TLS still fails on localhost only:
     * 'insecure_ssl' => true (never on a public server).
     */
    'registration' => array(
        // 'webapp_url' => 'https://script.google.com/macros/s/XXXX/exec',
        // 'php_form_alongside_webapp' => true,
        // 'webhook_url' => 'https://script.google.com/macros/s/XXXX/exec',
        // 'use_browser_post' => true,
        // 'use_curl' => true,
        // 'outbound_proxy' => 'http://proxy.example.com:8080',
        // 'transport_diag_log' => '/path/writable/registration-transport.log',
        // 'ca_bundle' => 'D:/path/to/cacert.pem',
        // 'insecure_ssl' => true,
        // 'submit_secret' => 'same-as-SUBMIT_SECRET-in-Apps-Script',
        // 'payment_form_url' => 'https://docs.google.com/forms/d/e/XXXX/viewform',
    ),
    /**
     * Optional: https://s01.flagcounter.com/ — from their HTML, copy ONLY the img src into img_src
     * (https://s01.flagcounter.com/count2/.../). You may paste https://s01.flagcounter.com/more/YourId/ as img_src or
     * stats_href; the site maps it to the count2 image. stats_href is the link around the badge (e.g. /more/… or info…).
     * Set enabled => true for the CTA badge to show. Optional key: counter_id if you omit img_src (same id as in URLs).
     */
    'flagcounter' => array(
        'enabled' => false,
        'img_src' => 'https://s01.flagcounter.com/count2/MjIp/bg_FFFFFF/txt_000000/border_CCCCCC/columns_2/maxflags_10/viewers_0/labels_0/pageviews_0/flags_0/percent_0/',
        'stats_href' => 'https://s01.flagcounter.com/more/MjIp/',
    ),
);

