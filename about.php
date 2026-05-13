<?php
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';
$SHMS = shms_page_data();
$shmsNavPage = 'about';
?>
<!DOCTYPE html>
<html lang="en" <?php echo shms_html_theme_class(); ?>>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="description"
    content="About SHMS‑2026 — conference overview, MNNIT Allahabad, ISHMS, and objectives for the International Conference on Structural Health Monitoring.">
  <title>About SHMS‑2026 | International Conference on Next Gen Structural Health Monitoring for Engineering Structures
    Powered by AI‑ML and Smart Materials</title>
  <link rel="stylesheet" href="style.css">
  <?php echo shms_head_resource_hints(); ?>
</head>

<body id="top">
  <?php require dirname(__FILE__) . '/includes/nav.php'; ?>

  <main>
    <?php require dirname(__FILE__) . '/includes/hero-banner.php'; ?>

    <section class="page-heading" id="page-content">
      <h1>About SHMS‑2026</h1>
      <p>International Conference on Next Gen Structural Health Monitoring for Engineering Structures Powered by AI‑ML
        and Smart Materials.</p>
    </section>

    <section class="logo-strip" aria-label="Conference partner logos">
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

    <!-- About conference -->
    <section id="conference">
      <div class="responsive-flex-container">
        <div>
          <h2>About SHMS‑2026</h2>
          <p>
            The INTERNATIONAL CONFERENCE on Next Gen Structural Health Monitoring for Engineering Structures Powered by
            AI‑ML and Smart Materials
            (SHMS‑2026) provides a premier platform for scientists, engineers, researchers, practitioners and
            infrastructure
            managers to exchange knowledge on next generation technologies. The focus is on integrating structural
            health
            monitoring with artificial intelligence, machine learning and smart materials to ensure optimal performance,
            safety and enhanced life‑span of critical infrastructure, thus contributing to overall sustainability.
          </p>
          <p>
            Rapid urbanisation and ageing infrastructure worldwide have heightened the need for intelligent monitoring
            systems. SHMS‑2026 addresses the convergence of advanced materials science, sensor technologies, artificial
            intelligence and structural engineering to create resilient, self‑monitoring and self‑healing infrastructure
            systems. The conference will feature keynote lectures by world‑renowned experts, technical sessions covering
            both fundamental research and practical applications, industrial exhibitions and ample networking
            opportunities.
            Special emphasis will be placed on bridging the gap between laboratory research and field implementation,
            with a particular focus on infrastructure challenges around the world.
          </p>
        </div>
        <div>
          <img src="assets/images/Logo.png" alt="SHMS-2026" width="250" height="250" loading="lazy">
        </div>
      </div>
    </section>

    <!-- About MNNIT -->
    <section id="mnnit">
      <h2>About MNNIT Allahabad</h2>
      <div class="about-org-block" aria-label="MNNIT Allahabad">
        <div>
          <img src="assets/images/mnnit-logo.png" alt="MNNIT Allahabad logo" width="200" height="200" loading="lazy"
            decoding="async">
        </div>
        <div class="about-org-content">
          <h3>Motilal Nehru National Institute of Technology (MNNIT) Allahabad</h3>
          <p>
            Motilal Nehru National Institute of Technology (MNNIT) Allahabad, established in 1961, is one of India’s
            premier technical institutions and an Institute of National Importance. Located in the historic city of
            Prayagraj (formerly Allahabad), MNNIT has a rich legacy of excellence in engineering education and research.
            The Department of Civil Engineering at MNNIT has been at the forefront of research in structural engineering
            and structural health monitoring, making it a natural host for SHMS‑2026.
          </p>
          <p>
            Over the decades, MNNIT has developed into a multidisciplinary academic ecosystem with strong undergraduate,
            postgraduate and doctoral programmes across engineering, sciences and management. The institute is known for
            its rigorous curriculum, active research culture and close engagement with industry and government agencies.
            Its faculty members and research scholars contribute to high‑impact work in infrastructure systems, advanced
            construction materials, sustainability and digital technologies for civil engineering applications.
          </p>
          <p>
            MNNIT’s campus offers modern laboratories, testing facilities and collaborative spaces that support both
            fundamental and application‑oriented research. With a long tradition of organising national and
            international
            technical events, the institute provides an ideal platform for meaningful dialogue among researchers,
            professionals and policy stakeholders. Hosting SHMS‑2026 aligns with MNNIT’s mission of advancing technology
            for societal benefit and resilient infrastructure development.
          </p>
        </div>
      </div>
    </section>

    <!-- About ISHMS -->
    <section id="ishms">
      <h2>About ISHMS</h2>
      <div class="about-org-block" aria-label="Indian Structural Health Monitoring Society">
        <div class="about-org-logo">
          <img src="assets/images/ishms-logo.png" alt="Indian Structural Health Monitoring Society (ISHMS) logo"
            width="200" height="200" loading="lazy" decoding="async">
        </div>
        <div class="about-org-content">
          <h3>Indian Structural Health Monitoring Society (ISHMS)</h3>
          <p>
            The Indian Structural Health Monitoring Society (ISHMS) was established in 2023 as a professional,
            non‑profit
            organisation dedicated to advancing structural health monitoring research and practice in India. ISHMS
            serves
            as a catalyst for the development and implementation of SHM and related multidisciplinary technologies. The
            society fosters an ecosystem where industry, academia, government bodies, students and other stakeholders
            converge and collaborate to create sustainable infrastructure solutions.
          </p>
          <p>
            ISHMS promotes scientific exchange and capacity building through conferences, workshops, training
            programmes,
            technical lectures and collaborative initiatives. The society encourages dissemination of best practices in
            sensing, diagnostics, data analytics, asset management and life‑cycle performance monitoring for critical
            infrastructure. By connecting experts from civil engineering, materials science, electronics, data science
            and
            allied domains, ISHMS supports integrated solutions for real‑world infrastructure challenges.
          </p>
          <p>
            A key objective of ISHMS is to bridge the gap between research outcomes and field deployment by facilitating
            dialogue between researchers, infrastructure owners, consultants and technology developers. Through
            standards
            discussions, outreach activities and professional networking, the society is helping build a strong SHM
            community in India and contributing to safer, smarter and more sustainable infrastructure systems.
          </p>
        </div>
      </div>
    </section>

    <!-- Objectives -->
    <section id="objectives">
      <h2>Conference Objectives</h2>
      <p>SHMS‑2026 aims to:</p>
      <ul>
        <li>Present state‑of‑the‑art research in structural health monitoring and smart materials.</li>
        <li>Highlight futuristic technologies in structural monitoring.</li>
        <li>Explore science, systems and sustainability aspects of SHM.</li>
        <li>Identify challenges and solutions for implementing SHM in developing countries.</li>
        <li>Bridge the academia–industry gap through collaborative sessions and exhibitions.</li>
        <li>Promote sustainable infrastructure development using smart health monitoring systems.</li>
        <li>Explore integration of AI, Internet of Things (IoT) and digital twins in infrastructure management.</li>
        <li>Foster international collaboration and knowledge exchange in smart materials and SHM.</li>
      </ul>
    </section>

    <section id="brochure" style="text-align: center; margin: 2rem auto;">
      <h2 style="border-bottom: none; margin-bottom: 0.5rem; display: block;">Conference Brochure</h2>
      <p style="text-align: center; margin-bottom: 1.5rem; opacity: 0.8; max-width: 100%;">Download the official
        SHMS-2026 brochure for detailed event information, schedules, and themes.</p>
      <a href="assets/files/SHMS 2026_MNNIT Allahabad, India.pdf" download
        class="home-final-btn home-final-btn--primary"
        style="display: inline-flex; align-items: center; gap: 0.65rem; text-decoration: none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
          <path
            d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
          <path
            d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
        </svg>
        <span>Download Brochure (PDF)</span>
      </a>
    </section>

  </main>

  <?php require dirname(__FILE__) . '/includes/cta-bar.php'; ?>

  <!-- Footer -->
  <?php require dirname(__FILE__) . '/includes/footer.php'; ?>
</body>

</html>