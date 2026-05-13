<?php
/**
 * Noscript-only notice: cookie-banner style when JavaScript is disabled.
 * Sites cannot enable JavaScript remotely; this block offers steps and reload / verify links.
 *
 * Included from footer.php after init.php (theme class on html is available for CSS).
 */

/**
 * @return string edge|chrome|firefox|safari|unknown
 */
function shms_js_banner_browser_family()
{
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
    if ($ua === '') {
        return 'unknown';
    }
    if (preg_match('/Edg|EdgiOS/i', $ua)) {
        return 'edge';
    }
    if (preg_match('/Firefox|FxiOS/i', $ua)) {
        return 'firefox';
    }
    if (preg_match('/Safari/i', $ua) && !preg_match('/Chrome|CriOS|Edg|Android/i', $ua)) {
        return 'safari';
    }
    if (preg_match('/Chrome|CriOS/i', $ua)) {
        return 'chrome';
    }
    return 'unknown';
}

/**
 * @return bool
 */
function shms_js_banner_is_apple_mobile()
{
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
    return (bool) preg_match('/iPhone|iPad|iPod/i', $ua);
}

/**
 * @param string $path
 * @param array $queryArr
 * @return string
 */
function shms_js_banner_build_path_query($path, array $queryArr)
{
    $qs = http_build_query($queryArr, '', '&');
    return $path . ($qs !== '' ? '?' . $qs : '');
}

/**
 * @return array clean and verify href paths (path + query only)
 */
function shms_js_banner_reload_hrefs()
{
    $raw = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
    if ($raw === '' || $raw[0] !== '/') {
        $raw = '/';
    }
    $parts = @parse_url($raw);
    if (!is_array($parts)) {
        return array('clean' => '/', 'verify' => '/?shms_js_recheck=1');
    }
    $path = isset($parts['path']) ? $parts['path'] : '/';
    $q = array();
    if (isset($parts['query']) && $parts['query'] !== '') {
        @parse_str($parts['query'], $q);
        if (!is_array($q)) {
            $q = array();
        }
    }
    $qClean = $q;
    unset($qClean['shms_js_recheck']);
    $qVerify = $q;
    $qVerify['shms_js_recheck'] = '1';
    return array(
        'clean' => shms_js_banner_build_path_query($path, $qClean),
        'verify' => shms_js_banner_build_path_query($path, $qVerify),
    );
}

$shmsJsBannerFamily = shms_js_banner_browser_family();
$shmsJsBannerAppleMobile = shms_js_banner_is_apple_mobile();
$shmsJsBannerHrefs = shms_js_banner_reload_hrefs();
$shmsJsHrefClean = htmlspecialchars($shmsJsBannerHrefs['clean'], ENT_QUOTES, 'UTF-8');
$shmsJsHrefVerify = htmlspecialchars($shmsJsBannerHrefs['verify'], ENT_QUOTES, 'UTF-8');
?>
<noscript>
<div class="shms-js-noscript-bar" role="alert">
  <details class="shms-js-noscript-details" open>
    <summary class="shms-js-noscript-details__summary">JavaScript is disabled</summary>
    <div class="shms-js-noscript-details__body">
      <p class="shms-js-noscript-details__question">This site requires JavaScript to function properly. Would you like to enable it now?</p>
      <p class="shms-js-noscript-details__note">A website cannot turn JavaScript on in your browser. Use the steps for your browser, then reload or use &ldquo;Reload to verify&rdquo;.</p>
      <div class="shms-js-noscript-details__actions">
        <a class="shms-js-noscript-btn shms-js-noscript-btn--primary" href="#shms-js-enable-steps">Yes</a>
        <a class="shms-js-noscript-btn shms-js-noscript-btn--ghost" href="<?php echo $shmsJsHrefClean; ?>">Reload</a>
        <a class="shms-js-noscript-btn shms-js-noscript-btn--secondary" href="<?php echo $shmsJsHrefVerify; ?>">Reload to verify</a>
      </div>
      <div id="shms-js-enable-steps" class="shms-js-enable-steps">
        <h2 class="shms-js-enable-steps__title">Enable JavaScript</h2>
        <p class="shms-js-enable-steps__lead">Detected: <strong><?php echo htmlspecialchars($shmsJsBannerFamily === 'unknown' ? 'unknown browser — see all sections below' : $shmsJsBannerFamily, ENT_QUOTES, 'UTF-8'); ?></strong></p>

        <section class="shms-js-enable-steps__block<?php echo $shmsJsBannerFamily === 'edge' ? ' shms-js-enable-steps__block--active' : ''; ?>" aria-labelledby="shms-js-h-edge">
          <h3 id="shms-js-h-edge" class="shms-js-enable-steps__h">Microsoft Edge</h3>
          <ol class="shms-js-enable-steps__ol">
            <li>Open <strong>Settings and more</strong> (<kbd>…</kbd>) → <strong>Settings</strong>.</li>
            <li>Go to <strong>Cookies and site permissions</strong> → <strong>JavaScript</strong>.</li>
            <li>Set <strong>Allowed (recommended)</strong> or allow this site.</li>
          </ol>
          <p class="shms-js-enable-steps__linkwrap">Or paste <code>edge://settings/content/javascript</code> into the address bar.</p>
        </section>

        <section class="shms-js-enable-steps__block<?php echo $shmsJsBannerFamily === 'chrome' ? ' shms-js-enable-steps__block--active' : ''; ?>" aria-labelledby="shms-js-h-chrome">
          <h3 id="shms-js-h-chrome" class="shms-js-enable-steps__h">Google Chrome</h3>
          <ol class="shms-js-enable-steps__ol">
            <li>Open <strong>Settings</strong> → <strong>Privacy and security</strong> → <strong>Site settings</strong>.</li>
            <li>Open <strong>JavaScript</strong> and choose <strong>Sites can use JavaScript</strong>.</li>
          </ol>
          <p class="shms-js-enable-steps__linkwrap">Or paste <code>chrome://settings/content/javascript</code> into the address bar.</p>
        </section>

        <section class="shms-js-enable-steps__block<?php echo $shmsJsBannerFamily === 'firefox' ? ' shms-js-enable-steps__block--active' : ''; ?>" aria-labelledby="shms-js-h-firefox">
          <h3 id="shms-js-h-firefox" class="shms-js-enable-steps__h">Mozilla Firefox</h3>
          <ol class="shms-js-enable-steps__ol">
            <li>Open <strong>Settings</strong> → <strong>Privacy &amp; Security</strong>.</li>
            <li>Under <strong>Permissions</strong>, manage <strong>JavaScript</strong> (or use <code>about:config</code>: set <code>javascript.enabled</code> to <code>true</code>).</li>
          </ol>
          <p class="shms-js-enable-steps__linkwrap">Quick open: <code>about:preferences#privacy</code></p>
        </section>

        <section class="shms-js-enable-steps__block<?php echo $shmsJsBannerFamily === 'safari' ? ' shms-js-enable-steps__block--active' : ''; ?>" aria-labelledby="shms-js-h-safari">
          <h3 id="shms-js-h-safari" class="shms-js-enable-steps__h">Apple Safari</h3>
          <?php if ($shmsJsBannerAppleMobile) : ?>
          <ol class="shms-js-enable-steps__ol">
            <li>Open the device <strong>Settings</strong> app → <strong>Safari</strong>.</li>
            <li>Scroll to <strong>Advanced</strong> → turn <strong>JavaScript</strong> on.</li>
          </ol>
          <?php else : ?>
          <ol class="shms-js-enable-steps__ol">
            <li>In Safari: <strong>Safari</strong> menu → <strong>Settings</strong> (or <strong>Preferences</strong>).</li>
            <li><strong>Security</strong> tab → enable <strong>JavaScript</strong>.</li>
          </ol>
          <?php endif; ?>
        </section>

        <?php if ($shmsJsBannerFamily === 'unknown') : ?>
        <section class="shms-js-enable-steps__block shms-js-enable-steps__block--active" aria-labelledby="shms-js-h-generic">
          <h3 id="shms-js-h-generic" class="shms-js-enable-steps__h">Other browsers</h3>
          <p class="shms-js-enable-steps__generic">Use the browser&rsquo;s settings or help documentation to allow JavaScript for this site, then reload the page.</p>
        </section>
        <?php endif; ?>
      </div>
    </div>
  </details>
</div>
</noscript>
<script>
(function () {
  try {
    if (typeof URLSearchParams === 'undefined') {
      return;
    }
    var p = new URLSearchParams(window.location.search || '');
    if (p.get('shms_js_recheck') !== '1') {
      return;
    }
    p.delete('shms_js_recheck');
    var q = p.toString();
    var path = window.location.pathname || '/';
    var hash = window.location.hash || '';
    var newUrl = path + (q ? '?' + q : '') + hash;
    if (window.history && history.replaceState) {
      history.replaceState(null, '', newUrl);
    }
    function showToast() {
      var t = document.createElement('div');
      t.className = 'shms-js-recheck-toast';
      t.setAttribute('role', 'status');
      t.setAttribute('aria-live', 'polite');
      t.textContent = 'JavaScript is enabled.';
      document.body.appendChild(t);
      requestAnimationFrame(function () {
        t.classList.add('shms-js-recheck-toast--visible');
      });
      window.setTimeout(function () {
        t.classList.add('shms-js-recheck-toast--out');
        window.setTimeout(function () {
          if (t.parentNode) {
            t.parentNode.removeChild(t);
          }
        }, 320);
      }, 4800);
    }
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', showToast);
    } else {
      showToast();
    }
  } catch (e) {}
})();
</script>
