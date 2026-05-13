# SHMS‑2026 — Hosting Guide for Motilal Nehru National Institute of Technology (MNNIT), Allahabad

**Audience:** Institute web administrators and the SHMS‑2026 organising team, coordinated through the [MNNIT Computer Centre](https://mnnit.ac.in/computercentre/) as required.

**Publication context:** Deployment on the institute’s public web presence, typically under **https://www.mnnit.ac.in/** (dedicated document root, virtual host, or an approved subdirectory for the conference site).

**Purpose:** This document sets out the technical requirements, **Apache HTTP Server configuration on Linux**, application configuration, and verification procedures for hosting the SHMS‑2026 PHP website on institute servers. A narrative HTML companion with overlapping steps is **`doc/google-form-contact-guide.html`**.

**Note on institute documentation:** The Computer Centre’s public pages describe **Email, Web, DNS, FTP** and general software environments (e.g. references to common Linux distributions on the [software resources](https://mnnit.ac.in/computercentre/index.php/software-resources) section). They do **not** publish internal Apache virtual-host templates or PHP-FPM pool files. **Section 3** below uses standard **Apache 2.4** patterns; the exact `DocumentRoot`, `ServerName`, TLS policy, and upload method must be taken from the allocation issued by the Computer Centre.

---

## 1. Scope and assumptions

- The deliverable is a **PHP application** suitable for **shared institute hosting**. **No server-side JavaScript runtime** (e.g. Node.js) is required on the institute host. Most pages are server-rendered; the **Contact** page may emit a **small inline script** executed in the **visitor’s browser** when Sheet delivery uses the **browser** path (no PHP cURL on the host, or `form_backend` `sheet_browser`).
- The site **does not require MySQL** for normal operation. A legacy MySQL visitor tally has been removed to reduce operational risk on the institute host.
- URLs may be served from the **site root** or a **subdirectory**; path-aware logic in `includes/init.php` derives the web base from `SCRIPT_NAME` so internal links remain correct.
- Local development may use Windows or macOS; **production** for `www.mnnit.ac.in` is assumed to be **Linux** with **Apache** (or a reverse proxy in front of Apache), per institute practice.

---

## 2. Server requirements

| Item | Detail |
|------|--------|
| **PHP** | **5.2.7 or newer.** `includes/init.php` enforces this minimum. The codebase avoids PHP 7+–only syntax so it can run on **legacy stacks (e.g. PHP 5.5)** where the Computer Centre has not yet upgraded. Polyfills are included for `http_response_code` (native in 5.4+) and `json_last_error_msg` (native in 5.5+). **Recommendation:** migrate to a supported PHP release (7.4+ or 8.x) when institute policy permits; PHP 5.x is end-of-life. |
| **Extensions** | `json`, `session`. **cURL** (`curl_init`) is **recommended** for `form_backend` **`sheet`** when the server should POST to Google; on hosts **without** cURL, the same **`sheet`** configuration still works: after validation, the **visitor’s browser** POSTs JSON (requires **JavaScript** on `contact.php` and an up-to-date Apps Script from **`includes/google-sheet-webhook-apps-script.txt`**). Outbound **443** from the **server** to `script.google.com` is required only when PHP uses cURL for Sheet mode. |
| **Web server** | **Apache 2.4** (typical on institute Linux) with **`AllowOverride`** so `.htaccess` can set `DirectoryIndex` and optional `mod_rewrite` rules. |
| **Confirmation** | The main portal **https://www.mnnit.ac.in/** does not advertise PHP version in HTTP headers. **Confirm `php -v` and loaded extensions on the actual virtual host** that will execute this application. |

---

## 3. Apache HTTP Server and Linux deployment (stepwise)

The hosting administrator should execute the following in order, reconciling each step with Computer Centre policy.

### Step 1 — Obtain hosting parameters

Request and record:

- Public URL (hostname and path): e.g. `https://www.mnnit.ac.in/shms2026/` or a dedicated host.
- Filesystem path serving that URL (`DocumentRoot` or alias target).
- PHP integration: **mod_php**, **php-fpm** (with Apache proxy), or another SAPI.
- PHP version and package manager conventions (Debian/Ubuntu `apt`, RHEL/CentOS `yum`/`dnf`, etc.).
- Method of file upload: SFTP, SCP, rsync, or an internal deployment pipeline.
- TLS: who provisions certificates and where vhost SSL directives are managed.

### Step 2 — Upload the application tree

- Copy the deployable site: root `*.php`, `includes/`, `assets/`, `style.css`, `api/`, `.htaccess`, and other referenced directories.
- **Exclude** development-only trees such as `node_modules/` from production.
- On the server, create **`includes/config.php`** from **`includes/config.sample.php`**; do not commit secrets to a public repository.

### Step 3 — Ownership and permissions

- Assign the tree to the user and group under which Apache reads files (commonly `www-data:www-data` on Debian/Ubuntu; institute images may use another account).
- Typical permissions: directories **755**, files **644**.
- Restrict **`includes/config.php`**: e.g. **640** and owned by root or the deploy user with group `www-data`, so credentials are not world-readable.

### Step 4 — Apache virtual host or directory block

- Ensure the target directory is served by Apache and that **`.htaccess` is honoured**: inside the relevant `<Directory>` or `<VirtualHost>`, set **`AllowOverride All`** (or at minimum `AllowOverride FileInfo` if the centre restricts options—confirm that `DirectoryIndex` from `.htaccess` still applies).
- **Illustrative** dedicated vhost (adjust `ServerName`, paths, and SSL directives per allocation):

```apache
<VirtualHost *:443>
    ServerName www.mnnit.ac.in
    DocumentRoot /var/www/shms2026

    <Directory /var/www/shms2026>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # SSLEngine, certificate, and chain: per institute standard
</VirtualHost>
```

- **Subdirectory under an existing site:** use `Alias` and a matching `<Directory>`:

```apache
Alias /shms2026 /var/www/shms2026

<Directory /var/www/shms2026>
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

- Validate configuration before reload: `apache2ctl configtest` or `httpd -t`.
- Reload: `sudo systemctl reload apache2` or `sudo systemctl reload httpd` (command varies by distribution).

### Step 5 — Enable Apache modules

- Enable **`mod_rewrite`** if not already loaded (required for the `<IfModule mod_rewrite.c>` block in `.htaccess`).
- On Debian/Ubuntu: `sudo a2enmod rewrite` then reload Apache.

### Step 6 — PHP extensions and session storage

- Install packages equivalent to **php-json** and ensure **session** support is present (usually built-in). Install **php-curl** if the institute enables it and you want **`sheet`** delivery from the **server**; if cURL is unavailable, keep **`form_backend` `sheet`** with **`sheet_webhook_url`** and **`sheet_webhook_secret`** and rely on **browser** delivery (redeploy Apps Script after updating from the repo). Example: `sudo apt install php-curl` (package name may include the PHP version, e.g. `php8.2-curl`).
- Ensure **`session.save_path`** in `php.ini` or the **php-fpm pool** file exists and is writable by the PHP process.
- If SELinux is enforcing, follow institute guidance for `httpd` read access to the document root (e.g. correct file contexts).

### Step 7 — Outbound network (Sheet webhook)

- If PHP **cURL** is used for **`sheet`**, TCP **443** from the **web server** to **`script.google.com`** must be permitted. If cURL is **not** present, the webhook is called from **visitors’ browsers** instead; ensure browsers on your expected networks can reach Google.
- Where the server has cURL, test as root or the web user: `curl -I https://script.google.com`

### Step 8 — Caching

- Do **not** apply full-page caching to **`contact.php`** (reverse proxy, CDN, or `mod_cache`). Cached responses break CSRF/session behaviour.

### Step 9 — Smoke test

- Open `/index.php`, an inner page, and `/contact.php` over HTTPS.
- Submit a test contact if `config.php` is configured (Sheet or PHP mail).

---

## 4. Application configuration (mandatory before go-live)

1. On the server, copy **`includes/config.sample.php`** to **`includes/config.php`**.
2. Edit **`includes/config.php`** according to sections 4.1–4.3 below.
3. The file **must be valid PHP:** it shall begin with `<?php` and return an array via `return array( … );`. Pasting fragments without the opening tag will cause PHP to emit text at the top of every page.
4. **`includes/config.php` must not be committed to version control** (it is listed in `.gitignore`). Restrict filesystem permissions per institute security practice (e.g. not world-readable).
5. **PHP sessions:** The contact form relies on **server-side sessions** for CSRF protection and the arithmetic check. Ensure **`session.save_path`** is writable by the web server user and that **cookies** are not blocked by misaligned `session.cookie_*` settings between HTTP/HTTPS. The application starts the session **before any HTML output** on `contact.php` so the session cookie can be set reliably.

### 4.1 Contact form (`contact` in `config.php`)

The public **Contact** page supports four delivery modes (see comments in `includes/config.sample.php`):

- **`php` (default):** Outbound email via **`mail()`** or optional **SMTP** (`smtp_*` keys). On institute Linux hosting, `mail()` is typical; SMTP may be used if the Computer Centre authorises it.
- **`google`:** Embedded Google Form (iframe); minimal server mail on submit.
- **`sheet`:** After validation, **Google Apps Script** appends a Sheet row and sends mail via **`MailApp`**. If **`curl_init`** exists, PHP POSTs JSON with **`secret`** = **`sheet_webhook_secret`**. If cURL is **missing** (not uncommon on locked-down institute PHP), the **browser** POSTs JSON with **`browser_token`** (same secret when **`BROWSER_TOKEN`** is not set in Script). Requires **`sheet_webhook_url`**, **`sheet_webhook_secret`** (12+ characters, matches Script **`WEBHOOK_SECRET`**). Redeploy the Web app from **`includes/google-sheet-webhook-apps-script.txt`** after pulling updates. Optional **`sheet_browser_token`** + Script **`BROWSER_TOKEN`** if you want a separate browser-only token.
- **`sheet_browser`:** Same outcome as **`sheet`**, but delivery is **always** via the browser’s **`fetch()`** (never PHP cURL), even when cURL is available. Use **`sheet_browser_token`** (matches Script **`BROWSER_TOKEN`**) or leave it empty and use **`sheet_webhook_secret`** only. **JavaScript** is required on **`contact.php`**.

**Operational note:** The honeypot and CSRF logic are designed to reject automated submissions; if a reverse proxy or full-page cache sits in front of **`contact.php`**, caching of that URL should be **disabled** so each visitor receives a fresh token.

**E-mail responsibility:** When `form_backend` is **`sheet`** or **`sheet_browser`**, notification mail is sent by **Google `MailApp`** in Apps Script, not by the institute MTA; SMTP keys in `config.php` do not apply. For details, see **`doc/google-form-contact-guide.html`**. For production handoff after Sheet setup, see **`doc/contact-form-production-handoff.html`**.

### 4.2 Optional: Flag Counter

The sticky call-to-action may display a **Flag Counter** badge ([s01.flagcounter.com](https://s01.flagcounter.com/)) using only an `<img>` (and optional link) — **no JavaScript**, consistent with restrictive institute policies.

1. Generate the badge on the Flag Counter site and copy the **image URL** and **statistics page URL**.
2. In `config.php`, set `'flagcounter' => array('enabled' => true, 'img_src' => '…', 'stats_href' => '…')` as in the sample file.
3. **Third-party content:** Assets load from the vendor’s servers; **confirm with the Computer Centre** that such embedded images are acceptable under institute policy.

### 4.3 SSL / cURL (Sheet mode or Windows staging)

If PHP reports **cURL error 60** (certificate verification), configure a CA bundle (`curl.cainfo` in `php.ini`) or set **`sheet_webhook_cainfo`** in `config.php` to the full path of a current **`cacert.pem`** (e.g. from [curl.se/ca/cacert.pem](https://curl.se/ca/cacert.pem)). The project includes **`tools/cacert.pem`** for local Windows testing; on Linux, the system CA store often suffices once `php-curl` is correctly installed.

---

## 5. Files and directories to deploy

Upload the **deployable web tree** to the approved document root or subdirectory (e.g. `public_html/shms2026/` or the path issued by the Computer Centre):

| Area | Contents |
|------|----------|
| **Pages** | `*.php` at site root (`index.php`, `about.php`, `venue.php`, `contact.php`, …) |
| **Includes** | `includes/init.php`, `includes/shms-page.php`, `includes/nav.php`, `includes/cta-bar.php`, `includes/contact-form.php`, `includes/contact-form-block.php`, `includes/flag-counter.php`, other includes referenced by pages, plus **`includes/config.php`** (created on server from the sample) |
| **API** | `api/visitors.php` (JSON: **Flag Counter** embed metadata — `source`, `enabled`, `img_src`, `stats_href`, optional `counter_id`; HTTP **200**) |
| **Assets** | `assets/`, `expert_photos/` if used, and any other media referenced by pages |
| **Styles** | `style.css` |
| **Apache** | `.htaccess` (directory index, etc.) |

- **Directory index:** `index.php` is set via `.htaccess` (`DirectoryIndex`).
- **JavaScript:** No site-wide `script.js`. Navigation is HTML and CSS; the countdown is server-rendered and **updates on reload**. **`contact.php`** may include a **single inline `<script>`** when Sheet delivery uses the **browser** path (see §1 and §4.1). Otherwise public pages have no client-side scripts.

---

## 6. Application structure (reference)

Typical pattern on each public page:

1. `require_once …/includes/init.php` — shared bootstrap.  
2. `require_once …/includes/shms-page.php` and `$SHMS = shms_page_data();` — countdown and related data.  
3. Set `$shmsNavPage` for the active navigation item.  
4. Include `nav.php`, `cta-bar.php`, and page body partials as required.  
5. **`contact.php`** additionally loads the contact handler and, for the built-in form, starts the session before HTML output (see §4).

---

## 7. Countdown and time zone

- **All conference timing** in the countdown uses **Asia/Kolkata (IST, UTC+05:30)**. There is no per-visitor timezone logic.
- Opening and closing instants are defined in **`includes/shms-page.php`** (e.g. inaugural **15 October 2026, 09:00** IST; conference end **17 October 2026, 18:00** IST). Any change to those instants should be reflected consistently in marketing copy and schedule tables elsewhere on the site.
- The value of `date.timezone` in `php.ini` **need not** be IST for the countdown to be correct; the code interprets the configured moments in **Asia/Kolkata** explicitly.

---

## 8. Visitor tally and `api/visitors.php`

- **Visitor indicator:** Optional **Flag Counter** badge from `config.php` (`flagcounter`); no local file-based tally or MySQL on the live site.
- **`api/visitors.php`:** Returns **200** JSON for **Flag Counter** only (`source` = `flagcounter`, `enabled`, `img_src`, `stats_href`, optional `counter_id`). No numeric total in JSON (the badge is the third-party image at `img_src`).

---

## 9. Local preview (development only)

Developers may run:

```bash
npm run dev
```

and open `http://localhost:8080/index.php` (or the configured port). If PHP is not in `PATH` on Windows:

```powershell
$env:PHP_PATH = "C:\xampp\php\php.exe"
npm run dev
```

*Production deployment on **www.mnnit.ac.in** should follow institute procedures (SFTP, deployment pipeline, or as directed by the web administrator), not the Node dev server.*

---

## 10. Post-deployment verification checklist

Please confirm the following after publication:

1. **Home page:** Countdown displays correctly; Flag Counter appears in the CTA **if enabled**.  
2. **Inner pages:** e.g. `about.php` — countdown and navigation state are correct.  
3. **Countdown (pre-conference):** CTA subtitle references **IST**; remaining time aligns with **`includes/shms-page.php`**.  
4. **Contact page:** Submit a test message; confirm receipt by email and/or Sheet row per `form_backend`. If users see “session expired,” ensure **`contact.php`** is deployed with current sources (session started before output) and that **`contact.php` is not cached** by a CDN or proxy.  
5. **Optional:** `GET …/api/visitors.php` returns **200** JSON with Flag Counter fields (`img_src`, `stats_href`, …).

---

## 11. Troubleshooting (summary)

| Symptom | Likely cause |
|---------|----------------|
| Flag Counter missing | Incorrect `img_src` / `stats_href`, or network policy blocking third-party images |
| Contact: “session expired” | Stale deployment, full-page cache on `contact.php`, or session storage not writable |
| Sheet webhook failures | Wrong `/exec` URL or `WEBHOOK_SECRET`; outdated Apps Script (redeploy from repo); with PHP cURL: blocked outbound HTTPS or SSL CA (error 60); with browser delivery: JavaScript off, or CORS/network blocking `script.google.com` |
| Broken styles | `style.css` not uploaded or incorrect base URL under a subdirectory |
| `api/visitors.php` 404 | File not deployed or wrong vhost path (endpoint is non-essential) |
| `.htaccess` ignored | `AllowOverride None` or missing `mod_rewrite` |

---

## 12. Supplementary note

A legacy **Node + SQLite** development server (`tools/legacy-node-server.js`) exists for historical reference only; it is **not** used in production and is not wired in `package.json`.

---

*Document version aligned with the SHMS‑2026 website repository. For coordination with institute systems under **www.mnnit.ac.in**, direct technical queries to the SHMS‑2026 web maintainer and the MNNIT Computer Centre as appropriate.*
