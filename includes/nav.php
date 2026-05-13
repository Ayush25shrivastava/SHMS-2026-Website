<?php
$__shmsThemeIsDark = function_exists('shms_theme') && shms_theme() === 'dark';
$__shmsThemeToggleHref = function_exists('shms_theme_toggle_href') ? shms_theme_toggle_href() : '?shms_theme=dark';
$__shmsThemeToggleLabel = $__shmsThemeIsDark ? 'Switch to light theme' : 'Switch to dark theme';

if (!function_exists('shms_nav_link_attrs')) {
    /**
     * @param string $navPage
     * @param string $key
     * @return string
     */
    function shms_nav_link_attrs($navPage, $key)
    {
        return $navPage === $key ? ' class="active" aria-current="page"' : '';
    }
}

$__shmsNav = isset($shmsNavPage) ? $shmsNavPage : '';
?>
  <!-- Navigation bar -->
  <nav>
    <div class="container">
      <input type="checkbox" id="shms-nav-toggle" class="shms-nav-toggle" tabindex="-1">
      <div class="logo logo-left">
        <img src="assets/images/mnnit-logo.png" alt="MNNIT Logo">
      </div>
      <label for="shms-nav-toggle" class="menu-toggle" aria-label="Open or close menu" role="button">
        <span class="visually-hidden">Menu</span>
        <span class="menu-toggle-bars" aria-hidden="true"><span></span><span></span><span></span></span>
      </label>
      <div class="nav-nav-cluster">
      <ul>
        <li><a href="index.php"<?php echo shms_nav_link_attrs($__shmsNav, 'index'); ?>>Home</a>
          <ul>
            <li><a href="index.php#quick-links">Conference hub</a></li>
            <li><a href="index.php#about">About</a></li>
            <li><a href="index.php#hosts">Hosts</a></li>
            <li><a href="index.php#dates">Important Dates</a></li>
          </ul>
        </li>
        <li><a href="about.php#page-content"<?php echo shms_nav_link_attrs($__shmsNav, 'about'); ?>>About</a>
          <ul>
            <li><a href="about.php#conference">Conference</a></li>
            <li><a href="about.php#mnnit">MNNIT</a></li>
            <li><a href="about.php#ishms">ISHMS</a></li>
            <li><a href="about.php#objectives">Objectives</a></li>
            <li><a href="about.php#brochure">Brochure</a></li>
          </ul>
        </li>
        <li class="nav-dropdown-tracks"><a href="tracks.php#track-1"<?php echo shms_nav_link_attrs($__shmsNav, 'tracks'); ?>>Tracks <span class="nav-dropdown-caret" aria-hidden="true">▾</span></a>
          <input type="checkbox" id="nav-tracks-toggle" class="nav-tracks-toggle" tabindex="-1">
          <ul>
            <li class="nav-track-page1"><a href="tracks.php#track-1">Track 1</a></li>
            <li class="nav-track-page1"><a href="tracks.php#track-2">Track 2</a></li>
            <li class="nav-track-page1"><a href="tracks.php#track-3">Track 3</a></li>
            <li class="nav-track-page1"><a href="tracks.php#track-4">Track 4</a></li>
            <li class="nav-track-page1"><a href="tracks.php#track-5">Track 5</a></li>
            <li class="nav-track-page2"><a href="tracks.php#track-6">Track 6</a></li>
            <li class="nav-track-page2"><a href="tracks.php#track-7">Track 7</a></li>
            <li class="nav-track-page2"><a href="tracks.php#track-8">Track 8</a></li>
            <li class="nav-track-page2"><a href="tracks.php#track-9">Track 9</a></li>
            <li class="nav-track-page2"><a href="tracks.php#track-10">Track 10</a></li>
            <li class="nav-dropdown-scroll-control nav-dropdown-scroll-control--down"><label for="nav-tracks-toggle" aria-label="Show more tracks">↓ More tracks</label></li>
            <li class="nav-dropdown-scroll-control nav-dropdown-scroll-control--up"><label for="nav-tracks-toggle" aria-label="Show previous tracks">↑ Back to Track 1</label></li>
          </ul>
        </li>
        <li><a href="committees.php#page-content"<?php echo shms_nav_link_attrs($__shmsNav, 'committees'); ?>>Committees</a>
          <ul>
            <li><a href="committees.php#organizing">Organizing</a></li>
            <li><a href="committees.php#technical">Technical</a></li>
            <li><a href="committees.php#advisory">Advisory</a></li>
          </ul>
        </li>
        <li><a href="schedule.php#page-content"<?php echo shms_nav_link_attrs($__shmsNav, 'schedule'); ?>>Program</a>
          <ul>
            <li><a href="schedule.php#day1">Day 1</a></li>
            <li><a href="schedule.php#day2">Day 2</a></li>
            <li><a href="schedule.php#day3">Day 3</a></li>
          </ul>
        </li>
        <li><a href="call.php#page-content"<?php echo shms_nav_link_attrs($__shmsNav, 'call'); ?>>Call for Papers</a>
          <ul>
            <li><a href="call.php#dates-cfp">Important Dates</a></li>
            <li><a href="call.php#submission">Abstract Format</a></li>
            <li><a href="call.php#peer-review">Submission & Peer Review</a></li>
            <li><a href="call.php#publication">Publication</a></li>
          </ul>
        </li>
        <li><a href="speakers.php#page-content"<?php echo shms_nav_link_attrs($__shmsNav, 'speakers'); ?>>Speakers</a>
          <ul>
            <li><a href="speakers.php#keynote-speakers">Keynote Speakers</a></li>
          </ul>
        </li>
        <li><a href="registration.php#page-content"<?php echo shms_nav_link_attrs($__shmsNav, 'registration'); ?>>Registration</a>
          <ul>
            <li><a href="registration.php#fees">Fees</a></li>
            <?php if ((function_exists('shms_registration_webapp_popup_enabled') && shms_registration_webapp_popup_enabled()) || (function_exists('shms_registration_php_form_enabled') && shms_registration_php_form_enabled())) : ?>
            <li><a href="registration.php#registration-submit">Submit registration &amp; receipt</a></li>
            <?php endif; ?>
            <li><a href="registration.php#includes">What is included?</a></li>
            <li><a href="registration.php#sponsorship">Sponsorship</a></li>
          </ul>
        </li>
        <li><a href="venue.php#page-content"<?php echo shms_nav_link_attrs($__shmsNav, 'venue'); ?>>Venue</a>
          <ul>
            <li><a href="venue.php#venue">Conference Venue</a></li>
            <li><a href="venue.php#accommodation">Accommodation</a></li>
            <li><a href="venue.php#travel">Travel</a></li>
          </ul>
        </li>
        <li class="nav-dropdown-right"><a href="contact.php#page-content"<?php echo shms_nav_link_attrs($__shmsNav, 'contact'); ?>>Contact</a>
          <ul>
            <li><a href="contact.php#organising-contact">Organising Committee</a></li>
            <li><a href="contact.php#ishms-contact">ISHMS</a></li>
            <li><a href="contact.php#secretariat">Secretariat</a></li>
          </ul>
        </li>
      </ul>
      <a class="nav-theme-toggle nav-theme-toggle--pan<?php echo $__shmsThemeIsDark ? ' nav-theme-toggle--state-dark' : ' nav-theme-toggle--state-light'; ?>" href="<?php echo htmlspecialchars($__shmsThemeToggleHref, ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?php echo htmlspecialchars($__shmsThemeToggleLabel, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($__shmsThemeToggleLabel, ENT_QUOTES, 'UTF-8'); ?>">
        <span class="nav-theme-toggle__illu" aria-hidden="true">
          <span class="nav-theme-toggle__track">
            <span class="nav-theme-toggle__thumb">
              <?php if ($__shmsThemeIsDark) : ?>
              <svg class="nav-theme-toggle__icon" width="13" height="13" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill="currentColor" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
              <?php else : ?>
              <svg class="nav-theme-toggle__icon" width="13" height="13" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="3.35" fill="currentColor"/><g stroke="currentColor" stroke-width="1.85" stroke-linecap="round"><path d="M12 3.25v1.85M12 18.9v1.85M3.25 12h1.85M18.9 12h1.85"/><path d="M5.8 5.8l1.3 1.3M16.9 16.9l1.3 1.3M5.8 18.2l1.3-1.3M16.9 7.1l1.3-1.3"/></g></svg>
              <?php endif; ?>
            </span>
          </span>
        </span>
      </a>
      </div>
      <div class="logo logo-right">
        <img src="assets/images/ishms-logo.png" alt="ISHMS Logo">
      </div>
    </div>
  </nav>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const navToggle = document.getElementById('shms-nav-toggle');
      const menuLinks = document.querySelectorAll('nav .nav-nav-cluster ul a');
      
      menuLinks.forEach(link => {
        link.addEventListener('click', function() {
          if (window.innerWidth <= 1024) {
            navToggle.checked = false;
          }
        });
      });
    });
  </script>
