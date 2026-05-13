<?php
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';
$SHMS = shms_page_data();
$shmsNavPage = 'index';
?>
<!DOCTYPE html>
<html lang="en" <?php echo shms_html_theme_class(); ?>>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="description"
    content="SHMS‑2026 — International Conference on Next Gen Structural Health Monitoring at MNNIT Allahabad, 15–17 October 2026, Prayagraj. Hosted by Civil Engineering, MNNIT with ISHMS.">
  <meta name="theme-color"
    content="<?php echo htmlspecialchars(shms_theme_color_meta_content(), ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:type" content="website">
  <meta property="og:title" content="SHMS‑2026 | International Conference on Next Gen Structural Health Monitoring">
  <meta property="og:description"
    content="15–17 October 2026, Prayagraj · MNNIT Allahabad · AI‑ML &amp; Smart Materials for Structural Health Monitoring.">
  <meta property="og:url" content="https://www.mnnit.ac.in/shms2026/">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="SHMS‑2026 | MNNIT Allahabad, 15–17 Oct 2026">
  <meta name="twitter:description"
    content="International Conference on Next Gen Structural Health Monitoring powered by AI‑ML and Smart Materials.">
  <title>SHMS‑2026 | International Conference on Next Gen Structural Health Monitoring for Engineering Structures
    Powered by AI‑ML and Smart Materials</title>
  <link rel="stylesheet" href="style.css">
  <?php echo shms_head_resource_hints(); ?>
</head>

<body id="top">
  <?php require dirname(__FILE__) . '/includes/nav.php'; ?>

  <main>
    <?php require dirname(__FILE__) . '/includes/hero-banner.php'; ?>

    <section class="logo-strip" id="page-content" aria-label="Conference partner logos">
      <div class="logo-strip-item">
        <img src="assets/images/mnnit-logo.png" alt="MNNIT Allahabad logo" width="180" height="180" loading="lazy">
        <p>Host Institute</p>
      </div>
      <div class="logo-strip-sep" aria-hidden="true"></div>
      <div class="logo-strip-item">
        <img src="assets/images/ishms-logo.png" alt="ISHMS logo" width="180" height="180" loading="lazy">
        <p>Technical Society Partner</p>
      </div>
    </section>

    <!-- Quick links hub -->
    <section class="quick-links" id="quick-links" aria-labelledby="quick-links-heading">
      <h2 id="quick-links-heading" class="quick-links-title">Conference Hub</h2>
      <div class="quick-links-grid">
        <a class="quick-link-card" href="call.php#page-content">
          <span class="quick-link-label">Call For Papers</span>
          <span class="quick-link-hint">Submission &amp; deadlines</span>
        </a>
        <a class="quick-link-card" href="registration.php#page-content">
          <span class="quick-link-label">Registration</span>
          <span class="quick-link-hint">Fees &amp; inclusions</span>
        </a>
        <a class="quick-link-card" href="schedule.php#page-content">
          <span class="quick-link-label">Program</span>
          <span class="quick-link-hint">Schedule overview</span>
        </a>
        <a class="quick-link-card" href="venue.php#page-content">
          <span class="quick-link-label">Venue &amp; Travel</span>
          <span class="quick-link-hint">Maps &amp; accommodation</span>
        </a>
        <a class="quick-link-card" href="registration.php#sponsorship">
          <span class="quick-link-label">Sponsorship</span>
          <span class="quick-link-hint">Partner packages &amp; souvenir options</span>
        </a>
      </div>
    </section>

    <!-- About snippet -->
    <section id="about">
      <h2>About SHMS‑2026</h2>
      <div class="responsive-flex-container">
        <div>
          <p>
            SHMS‑2026 is an international conference that brings together scientists, engineers and practitioners to
            explore
            the next generation of structural health monitoring systems. The event focuses on the convergence of smart
            materials, advanced sensors, artificial intelligence and machine learning to create resilient and
            sustainable
            infrastructure. Hosted by the Department of Civil Engineering at Motilal Nehru National Institute of
            Technology
            (MNNIT) Allahabad in collaboration with the Indian Structural Health Monitoring Society (ISHMS), the
            conference
            will feature keynote lectures, technical sessions, exhibitions and networking opportunities.
          </p>
          <p>
            As global infrastructure ages and urbanisation accelerates, intelligent monitoring becomes essential for
            safety
            and longevity. SHMS‑2026 aims to bridge the gap between cutting‑edge research and real‑world implementation
            by
            fostering collaboration among academia, industry and government agencies. Join us in October 2026 to shape
            the future of structural health monitoring.
          </p>
          <p><a href="about.php#page-content" style="color:#004a8f; text-decoration: underline;">Learn more about the
              conference →</a></p>
        </div>
        <div>
          <img src="assets/images/Logo.png" alt="SHMS-2026" width="250" height="250" loading="lazy">
        </div>
      </div>
    </section>

    <section id="why-attend" class="premium-highlights" aria-labelledby="why-attend-title">
      <h2 id="why-attend-title">Why Attend SHMS‑2026</h2>
      <div class="premium-highlights-grid">
        <article class="premium-highlight-card">
          <h3>Global Exchange</h3>
          <p>Meet international experts, researchers, and industry leaders shaping next-generation SHM.</p>
        </article>
        <article class="premium-highlight-card">
          <h3>10 Technical Tracks</h3>
          <p>From AI/ML and digital twins to seismic resilience, NDE, and smart sensing materials.</p>
        </article>
        <article class="premium-highlight-card">
          <h3>Research to Practice</h3>
          <p>Focused discussions on implementation, field deployment, and infrastructure lifecycle value.</p>
        </article>
        <article class="premium-highlight-card">
          <h3>High-Value Networking</h3>
          <p>Build partnerships across academia, government, and industry for future collaborations.</p>
        </article>
      </div>
    </section>

    <!-- Hosts -->
    <section id="hosts">
      <div class="responsive-flex-container">
        <div>
          <h2>Conference Hosts</h2>
          <p>
            The conference is organised by the
            <strong><a href="https://www.mnnit.ac.in/index.php/department/engineering/ce" target="_blank"
                rel="noopener noreferrer" style="color:#004a8f;">
                Department of Civil Engineering, Motilal Nehru National Institute of Technology (MNNIT) Allahabad
              </a></strong>,
            an Institute of National Importance with a strong legacy in structural engineering and infrastructure
            research.
            Located in Prayagraj, MNNIT’s Civil Engineering Department has been at the forefront of work in structural
            health
            monitoring, making it a natural host for SHMS‑2026.
          </p>
          <p>
            SHMS‑2026 is organised in collaboration with the
            <strong><a href="https://www.ishms.org.in/" target="_blank" rel="noopener noreferrer"
                style="color:#004a8f;">
                Indian Structural Health Monitoring Society (ISHMS)
              </a></strong>, a professional, non‑profit
            society established to advance research and practice in structural health monitoring across India. ISHMS
            brings
            together industry, academia, government bodies and students to promote modern SHM technologies and
            sustainable
            infrastructure solutions.
          </p>
        </div>
        <div style="display: flex; flex-direction: column; justify-content: space-between; align-items: center;">
          <div>
            <img src="assets/images/mnnit-logo.png" alt="MNNIT_LOGO" width="200" height="200"
            loading="lazy">
          </div>
          <!-- <div>
            <img src="assets/images/conference_venue_text.png" alt="MNNIT_CIVIL_DEPT" width="220" height="220"
            loading="lazy">
          </div> -->
        </div>
      </div>
      <div class="ishms-home-block" aria-label="About ISHMS">
        <div class="ishms-home-logo">
          <img src="assets/images/ishms-logo.png" alt="ISHMS logo" width="260" height="260" loading="lazy">
        </div>
        <div class="ishms-home-content">
          <h3>Indian Structural Health Monitoring Society (ISHMS)</h3>
          <p>
            ISHMS was established as a professional, non-profit platform to accelerate research, standards, and
            real-world adoption of structural health monitoring in India. The society connects academia, industry,
            government agencies, and young professionals to promote safer, smarter, and more sustainable infrastructure.
          </p>
          <p>
            Through conferences, training programs, technical collaborations, and outreach, ISHMS supports knowledge
            exchange across sensing systems, diagnostics, AI-driven analytics, and lifecycle infrastructure management.
          </p>
          <p><a href="https://www.ishms.org.in/" target="_blank" rel="noopener noreferrer">Visit ISHMS website →</a></p>
        </div>
      </div>
    </section>

    <!-- Themes highlight -->
    <section id="themes">
      <div class="responsive-flex-container">
        <div>
          <h2>Conference Themes</h2>
          <p>The conference encompasses a broad spectrum of topics centred on smart materials and structural health
            monitoring.
            These themes include smart sensing materials, wireless sensor networks, machine learning techniques, digital
            twin
            frameworks, disruptive non‑destructive evaluation methods, vibration‑based monitoring, real‑world case
            studies,
            seismic monitoring, pavement evaluation, and vision‑based SHM. Explore all 10 thematic tracks in detail on
            the
            dedicated page.</p>
          <p><a href="tracks.php" style="color:#004a8f; text-decoration: underline;">Discover all technical tracks →</a>
          </p>
        </div>
        <div>
          <img src="assets/images/tracks.jpeg" alt="tracks" width="240" height="240" loading="lazy">
        </div>
      </div>
    </section>

    <!-- Important dates snapshot -->
    <section id="dates">
      <h2>Important Dates</h2>
      <table>
        <thead>
          <tr>
            <th>Milestone</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Abstract Submission Opens</td>
            <td>May 12,&nbsp;2026</td>
          </tr>
          <tr>
            <td>Abstract Submission Deadline</td>
            <td>June 30,&nbsp;2026</td>
          </tr>
          <tr>
            <td>Notification of Abstract Acceptance</td>
            <td>July 10,&nbsp;2026</td>
          </tr>
          <tr>
            <td>Full Paper Submission Deadline</td>
            <td>August 16,&nbsp;2026</td>
          </tr>
          <tr>
            <td>Notification of Paper Acceptance</td>
            <td>August 31,&nbsp;2026</td>
          </tr>
          <tr>
            <td>Early Bird Registration Deadline</td>
            <td>September 15,&nbsp;2026</td>
          </tr>
          <tr>
            <td>Conference Dates</td>
            <td>October 15–17,&nbsp;2026</td>
          </tr>
        </tbody>
      </table>
      <p><a href="call.php#page-content" style="color:#004a8f; text-decoration: underline;">See full submission
          guidelines →</a></p>
    </section>

    <!-- Contact summary -->
    <section id="contact-summary">
      <div class="responsive-flex-container">
        <div>
          <h2>Get in Touch</h2>
          <p>For general enquiries about SHMS‑2026, please contact the organising committee at
            <a href="mailto:shms2026@mnnit.ac.in" style="color:#004a8f;">shms2026@mnnit.ac.in</a>. We look forward to
            welcoming you to Prayagraj!
          </p>
          <p><a href="contact.php#page-content" style="color:#004a8f; text-decoration: underline;">More contact
              information
              →</a></p>
        </div>
        <div>
          <img src="assets/images/Logo.png" alt="SHMS-2026" width="220" height="220" loading="lazy">
        </div>
      </div>
    </section>

    <section class="home-final-cta" aria-label="Conference call to action">
      <p class="home-final-cta-badge">Limited Early Bird Window</p>
      <h2>Ready to Join SHMS‑2026?</h2>
      <p>
        Register now for the International Conference on Next Gen Structural Health Monitoring.
        Early-bird registration closes on September 15, 2026.
      </p>
      <div class="home-final-cta-actions">
        <a class="home-final-btn home-final-btn--primary" href="registration.php#page-content">Register Now</a>
        <a class="home-final-btn home-final-btn--secondary" href="about.php#page-content">Learn More</a>
      </div>
      <div class="home-final-stats" aria-label="Conference quick stats">
        <div class="home-final-stat"><strong>3</strong><span>Days</span></div>
        <div class="home-final-stat"><strong>10</strong><span>Tracks</span></div>
        <div class="home-final-stat"><strong>200+</strong><span>Expected Delegates</span></div>
      </div>
    </section>

    <!-- Key facts (moved below main content — hero bar already shows dates & venue) -->
    <section class="at-a-glance" id="key-facts" aria-label="Key facts at a glance">
      <div class="at-a-glance-grid">
        <div class="at-a-glance-item">
          <span class="at-a-glance-label">When</span>
          <span class="at-a-glance-value">15–17 Oct 2026</span>
        </div>
        <div class="at-a-glance-item">
          <span class="at-a-glance-label">Where</span>
          <span class="at-a-glance-value">Prayagraj, India</span>
        </div>
        <div class="at-a-glance-item">
          <span class="at-a-glance-label">Format</span>
          <span class="at-a-glance-value">In-person</span>
        </div>
        <div class="at-a-glance-item">
          <span class="at-a-glance-label">Abstract deadline</span>
          <span class="at-a-glance-value">30 Jun 2026</span>
        </div>
        <div class="at-a-glance-item">
          <span class="at-a-glance-label">Expected delegates</span>
          <span class="at-a-glance-value">200+</span>
        </div>
      </div>
    </section>

  </main>

  <?php require dirname(__FILE__) . '/includes/cta-bar.php'; ?>

  <?php require dirname(__FILE__) . '/includes/footer.php'; ?>

</body>

</html>