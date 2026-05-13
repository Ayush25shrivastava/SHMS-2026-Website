<?php
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';
$SHMS = shms_page_data();
$shmsNavPage = 'speakers';
?>
<!DOCTYPE html>
<html lang="en" <?php echo shms_html_theme_class(); ?>>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Keynote Speakers | SHMS‑2026</title>
  <link rel="stylesheet" href="style.css">
  <?php echo shms_head_resource_hints(); ?>
</head>

<body id="top">
  <?php require dirname(__FILE__) . '/includes/nav.php'; ?>

  <main>
    <?php require dirname(__FILE__) . '/includes/hero-banner.php'; ?>

    <section class="page-heading" id="page-content">
      <h1>Keynote &amp; Invited Speakers</h1>
      <p>Distinguished Experts Bringing Global Perspectives to SHMS‑2026.</p>
    </section>

    <section id="keynote-speakers">
      <h2>Keynote Speakers</h2>
      <div class="speakers">
        <div class="speaker">
          <img src="expert_photos/gyuhae-park.jpg" alt="Prof. Gyuhae Park"
            class="avatar-top speaker-avatar--gyuhae-park">
          <h3>Prof. Gyuhae Park</h3>
          <p>Chonnam National University, South Korea</p>
          <p>A leading researcher in piezoelectric and impedance‑based structural health monitoring, smart materials
            and
            energy harvesting for self‑powered sensing. His widely cited work spans electromechanical impedance
            methods
            for
            civil and aerospace structures, wireless SHM sensor concepts, macro‑fibre composite transducers, and
            data‑driven
            damage detection under operational and environmental variability — including contributions to foundational
            SHM
            reviews and benchmark studies.</p>
        </div>
        <div class="speaker">
          <img src="expert_photos/harpal-singh.jpg" alt="Prof. Harpal Singh">
          <h3>Prof. Harpal Singh</h3>
          <p>Arctic University of Norway (UiT), Tromsø, Norway</p>
          <p>An expert in digital twin technology and smart infrastructure systems. He develops scalable digital
            frameworks for infrastructure monitoring under extreme environmental conditions, tackling unique Arctic
            engineering challenges.</p>
        </div>
        <div class="speaker">
          <img src="expert_photos/suresh-bhalla.jpg" alt="Prof. Suresh Bhalla" class="avatar-top">
          <h3>Prof. Suresh Bhalla</h3>
          <p>Indian Institute of Technology Delhi, India</p>
          <p>President of ISHMS and a leading researcher in piezoelectric‑based structural health monitoring,
            including
            electromechanical impedance methods and smart materials for civil infrastructure. He champions low‑cost
            SHM
            solutions for developing countries.</p>
        </div>
        <div class="speaker">
          <img src="expert_photos/praveen-kumar.jpg" alt="Prof. Praveen Kumar">
          <h3>Prof. Praveen Kumar</h3>
          <p>Indian Institute of Technology Roorkee, India</p>
          <p>A renowned expert in pavement engineering, highway materials and pavement evaluation techniques. His work
            spans flexible and rigid pavements, bituminous materials characterisation and sustainable pavement
            construction
            practices.</p>
        </div>
        <div class="speaker">
          <img src="expert_photos/kvl-subramaniam.jpg" alt="Prof. K.V.L. Subramaniam">
          <h3>Prof. K.V.L. Subramaniam</h3>
          <p>Indian Institute of Technology Hyderabad, India</p>
          <p>Professor, Civil Engineering, IIT Hyderabad. His research focuses on concrete materials and structures,
            structural health monitoring (SHM), sensor development, and material characterization for safer and more
            resilient infrastructure.</p>
        </div>
        <!-- <div class="speaker">
        <img src="expert_photos/k-lakshmi.jpg" alt="Dr. K. Lakshmi">
        <h3>Dr. K. Lakshmi</h3>
        <p>CSIR‑Structural Engineering Research Centre (CSIR‑SERC), Chennai, India</p>
        <p>Scientist at the Structural Health Monitoring Laboratory specialising in vibration‑based damage detection,
          vehicle‑bound sensor techniques for bridge monitoring, optimal sensor placement and metaheuristic optimisation.
          Recipient of the Ramaiah Best Paper Prize (2019).</p>
      </div>
      <div class="speaker">
        <img src="expert_photos/naveet-kaur.jpg" alt="Dr. Naveet Kaur">
        <h3>Dr. Naveet Kaur</h3>
        <p>CSIR‑Central Road Research Institute (CSIR‑CRRI), New Delhi, India</p>
        <p>Senior scientist and Vice President of ISHMS. She is an expert in piezoelectric energy harvesting and structural
          health monitoring using PZT transducers. Inventor of the Vibro‑Integrity Sensing Device (VInSD) for
          multipurpose non‑destructive evaluation of bridge structures.</p>
      </div> -->
        <!-- <div class="speaker">
        <img src="expert_photos/pilate-moyo.jpg" alt="Prof. Pilate Moyo">
        <h3>Prof. Pilate Moyo</h3>
        <p>University of Cape Town, South Africa</p>
        <p>Director of the Concrete Materials and Structural Integrity Research Unit (CoMSIRU). He is renowned for his
          contributions to structural health monitoring, condition assessment, structural dynamics and vibration testing
          of civil infrastructure.</p>
      </div> -->
        <!-- <div class="speaker">
        <img src="expert_photos/chee-kiong-soh.jpg" alt="Prof. Chee‑Kiong Soh">
        <h3>Prof. Chee‑Kiong Soh</h3>
        <p>National University of Singapore, Singapore</p>
        <p>A distinguished researcher in smart materials, electromechanical impedance techniques and energy harvesting for
          structural health monitoring. His pioneering work on metamaterial‑based sensing technologies has advanced
          sustainable urban systems.</p>
      </div> -->
      </div>
      <h4 style="text-align: center; margin-top: 12px;">Many More National and International Keynotes Will Be Added Soon...</h4>
    </section>
  </main>

  <?php require dirname(__FILE__) . '/includes/cta-bar.php'; ?>

  <?php require dirname(__FILE__) . '/includes/footer.php'; ?>

</body>

</html>