<?php
/**
 * Site footer + fixed scroll up / down controls (plain links only, no client-side script).
 * js-required-banner.php appends a noscript notice and a small script for ?shms_js_recheck=1 only.
 * Uses href="#top" (body id on each page) and href="#page-end" (sentinel after <footer> in this file).
 * Smooth scroll: html { scroll-behavior: smooth } only (not body — avoids broken in-page scrolling in some browsers).
 */
$__shmsFooterThemeDark = function_exists('shms_theme') && shms_theme() === 'dark';
$__shmsFooterThemeHref = function_exists('shms_theme_toggle_href') ? shms_theme_toggle_href() : '?shms_theme=dark';
$__shmsFooterThemeLabel = $__shmsFooterThemeDark ? 'Switch to light mode' : 'Switch to dark mode';
$__shmsFooterThemeShort = $__shmsFooterThemeDark ? 'Light mode' : 'Dark mode';
?>
  <div class="page-scroll-fabs" role="navigation" aria-label="Scroll page">
    <a href="#top" class="page-scroll-fab page-scroll-fab--up" aria-label="Scroll to top of page" title="Scroll up">
      <svg class="page-scroll-fab__icon page-scroll-fab__icon--hint" viewBox="0 0 32 54" width="32" height="54" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <g class="page-scroll-fab__chevrons page-scroll-fab__chevrons--up" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
          <path class="page-scroll-fab__chev page-scroll-fab__chev--1" d="M9 12l7 -5.5 7 5.5"/>
          <path class="page-scroll-fab__chev page-scroll-fab__chev--2" d="M9 20l7 -5.5 7 5.5"/>
          <path class="page-scroll-fab__chev page-scroll-fab__chev--3" d="M9 28l7 -5.5 7 5.5"/>
        </g>
        <g class="page-scroll-fab__mouse" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="11" y="35" width="10" height="16" rx="5" fill="none"/>
          <circle class="page-scroll-fab__mouse-wheel" cx="16" cy="40.5" r="1.35" fill="currentColor" stroke="none"/>
        </g>
      </svg>
    </a>
    <a href="#page-end" class="page-scroll-fab page-scroll-fab--down" aria-label="Scroll to end of page" title="End of page">
      <svg class="page-scroll-fab__icon page-scroll-fab__icon--hint" viewBox="0 0 32 54" width="32" height="54" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <g class="page-scroll-fab__mouse" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="11" y="3" width="10" height="16" rx="5" fill="none"/>
          <circle class="page-scroll-fab__mouse-wheel" cx="16" cy="8.5" r="1.35" fill="currentColor" stroke="none"/>
        </g>
        <g class="page-scroll-fab__chevrons page-scroll-fab__chevrons--down" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
          <path class="page-scroll-fab__chev page-scroll-fab__chev--1" d="M9 26l7 5.5 7 -5.5"/>
          <path class="page-scroll-fab__chev page-scroll-fab__chev--2" d="M9 34l7 5.5 7 -5.5"/>
          <path class="page-scroll-fab__chev page-scroll-fab__chev--3" d="M9 42l7 5.5 7 -5.5"/>
        </g>
      </svg>
    </a>
  </div>
  <footer class="site-footer">
    <div class="site-footer-main">
      <div class="site-footer-inner">
        <div class="site-footer-col site-footer-col--brand">
          <p class="site-footer-brand">SHMS‑2026</p>
          <p class="site-footer-tagline">International Conference on Next Gen Structural Health Monitoring — smart materials, AI‑ML, and resilient infrastructure.</p>
          <p class="site-footer-dates">15–17 October 2026 · Prayagraj, India</p>
        </div>
        <nav class="site-footer-col site-footer-col--links" aria-labelledby="site-footer-quick-heading">
          <h2 id="site-footer-quick-heading" class="site-footer-heading">Quick links</h2>
          <ul class="site-footer-linklist">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php#page-content">About</a></li>
            <li><a href="committees.php#page-content">Committees</a></li>
            <li><a href="call.php#page-content">Call for papers</a></li>
            <li><a href="schedule.php#page-content">Program</a></li>
            <li><a href="registration.php#page-content">Registration</a></li>
            <li><a href="venue.php#page-content">Venue &amp; travel</a></li>
            <li><a href="contact.php#page-content">Contact</a></li>
          </ul>
        </nav>
        <div class="site-footer-col site-footer-col--contact">
          <h2 id="site-footer-contact-heading" class="site-footer-heading">Contact us</h2>
          <ul class="site-footer-contact">
            <li>
              <span class="site-footer-contact-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 11.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" stroke="currentColor" stroke-width="1.75"/><path d="M12 22s7-4.35 7-10a7 7 0 10-14 0c0 5.65 7 10 7 10z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>
              </span>
              <span>Department of Civil Engineering, MNNIT Allahabad, Prayagraj, Uttar Pradesh, India — 211004</span>
            </li>
            <li>
              <span class="site-footer-contact-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 4h4l2 4-2 1.5a11 11 0 006 6L16 13l4 2v4a2 2 0 01-2 2A16 16 0 015 6a2 2 0 012-2z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>
              </span>
              <span><a href="tel:+919783750870">+91-9783750870</a></span>
            </li>
            <li>
              <span class="site-footer-contact-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16v12H4V6z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/><path d="M4 7l8 5 8-5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
              <span><a href="mailto:shms2026@mnnit.ac.in">shms2026@mnnit.ac.in</a></span>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="site-footer-bottom">
      <p class="site-footer-bottom__row">© <?php echo date('Y'); ?> SHMS‑2026 · Department of Civil Engineering, MNNIT Allahabad</p>
      <p class="site-footer-bottom__appearance">
        <span class="site-footer-appearance-label">Appearance</span>
        <a class="site-footer-appearance-toggle<?php echo $__shmsFooterThemeDark ? ' is-dark' : ' is-light'; ?>" href="<?php echo htmlspecialchars($__shmsFooterThemeHref, ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?php echo htmlspecialchars($__shmsFooterThemeLabel, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($__shmsFooterThemeLabel, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($__shmsFooterThemeShort, ENT_QUOTES, 'UTF-8'); ?></a>
      </p>
    </div>
  </footer>
  <span id="page-end" class="shms-page-end-anchor" aria-hidden="true">&nbsp;</span>
<?php require_once dirname(__FILE__) . '/js-required-banner.php'; ?>
