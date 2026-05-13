<?php
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';
$SHMS = shms_page_data();
$shmsNavPage = 'tracks';
?>
<!DOCTYPE html>
<html lang="en" <?php echo shms_html_theme_class(); ?>>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Technical Tracks | SHMS‑2026</title>
  <link rel="stylesheet" href="style.css">
  <?php echo shms_head_resource_hints(); ?>
</head>

<body id="top">
  <?php require dirname(__FILE__) . '/includes/nav.php'; ?>

  <main>
    <?php require dirname(__FILE__) . '/includes/hero-banner.php'; ?>

    <section class="page-heading" id="page-content">
      <h1>Technical Tracks</h1>
      <p>Explore The Ten Thematic Tracks Covering The Breadth of Smart Materials and SHM.</p>
    </section>

    <section>
      <h2 id="track-1">Track 1: Smart Sensing Materials for SHM</h2>
      <ul>
        <li>Piezoelectric materials for sensing, actuation and energy harvesting</li>
        <li>Carbon nanotube and graphene‑reinforced smart composites</li>
        <li>Fiber optic sensors: Fiber Bragg gratings (FBG), distributed sensing (DFOS), OTDR, OFDR</li>
        <li>Piezoelectric transducers: PZT patches, smart aggregates, PVDF sensors</li>
        <li>MEMS accelerometers and inclinometers for vibration monitoring</li>
        <li>Acoustic emission sensors for crack detection and propagation monitoring</li>
      </ul>

      <h2 id="track-2">Track 2: Wireless Sensor Networks and IoT</h2>
      <ul>
        <li>Low‑power wireless sensor nodes for long‑term monitoring</li>
        <li>Energy harvesting technologies: piezoelectric, thermoelectric, solar, RF transmission</li>
        <li>IoT architectures for smart infrastructure monitoring</li>
        <li>Edge computing and real‑time data processing</li>
        <li>5G‑enabled monitoring systems and cloud analytics</li>
        <li>Self‑powered and autonomous sensing systems</li>
      </ul>

      <h2 id="track-3">Track 3: Artificial Intelligence and Machine Learning in SHM</h2>
      <ul>
        <li>Deep learning for damage detection and classification</li>
        <li>Convolutional neural networks (CNN) for image‑based inspection</li>
        <li>Transfer learning and domain adaptation for limited data scenarios</li>
        <li>Anomaly detection and unsupervised learning approaches</li>
        <li>Physics‑informed neural networks (PINNs) for structural analysis</li>
        <li>Bayesian methods for uncertainty quantification</li>
      </ul>

      <h2 id="track-4">Track 4: Digital Twins and Predictive Maintenance</h2>
      <ul>
        <li>Digital twin frameworks for bridges, buildings and infrastructure</li>
        <li>Real‑time simulation and state estimation</li>
        <li>BIM integration with SHM systems</li>
        <li>Remaining useful life prediction and condition‑based maintenance</li>
        <li>Multi‑fidelity surrogate models and model updating</li>
        <li>Blockchain integration for secure SHM data management</li>
      </ul>

      <h2 id="track-5">Track 5: Novel Disruptions in Non‑Destructive Evaluation (NDE) and Testing</h2>
      <ul>
        <li>Ultrasonic testing and guided wave propagation</li>
        <li>Non‑destructive testing of concrete using rebound hammer</li>
        <li>Electromechanical impedance (EMI) techniques</li>
        <li>Ground penetrating radar (GPR) for subsurface inspection</li>
        <li>Infrared thermography and thermal imaging</li>
        <li>Laser vibrometry and optical measurement techniques</li>
        <li>UAV‑based and robotic inspection systems</li>
      </ul>

      <h2 id="track-6">Track 6: Vibration‑Based SHM and Modal Analysis</h2>
      <ul>
        <li>Operational modal analysis and output‑only system identification</li>
        <li>Damage‑sensitive features and statistical pattern recognition</li>
        <li>Environmental and operational variability compensation</li>
        <li>Finite element model updating and validation</li>
        <li>Time‑frequency analysis and wavelet‑based methods</li>
        <li>Fatigue damage accumulation and crack growth monitoring</li>
      </ul>

      <h2 id="track-7">Track 7: Field Applications and Case Studies</h2>
      <ul>
        <li>Long‑span bridge monitoring systems</li>
        <li>High‑rise building and tower monitoring</li>
        <li>Pipeline and offshore structure monitoring</li>
        <li>Railway infrastructure and track monitoring</li>
        <li>Dam and reservoir monitoring systems</li>
        <li>Heritage structure preservation and monitoring</li>
      </ul>

      <h2 id="track-8">Track 8: Seismic Monitoring and Disaster Resilience</h2>
      <ul>
        <li>Earthquake early warning systems</li>
        <li>Post‑earthquake rapid damage assessment</li>
        <li>SMA‑based seismic retrofitting and isolation</li>
        <li>Self‑centring structural systems</li>
        <li>Resilient infrastructure design principles</li>
        <li>Multi‑hazard monitoring and assessment</li>
      </ul>

      <h2 id="track-9">Track 9: Pavement Evaluation and Monitoring Techniques</h2>
      <ul>
        <li>Falling weight deflectometer (FWD) and heavy weight deflectometer (HWD) testing</li>
        <li>Pavement condition index (PCI) assessment and distress quantification</li>
        <li>Ground penetrating radar (GPR) for pavement layer thickness and void detection</li>
        <li>International roughness index (IRI) and ride quality evaluation</li>
        <li>Network‑level pavement management systems (PMS)</li>
        <li>Pavement performance modelling and remaining service life prediction</li>
      </ul>

      <h2 id="track-10">Track 10: Vision‑Based SHM</h2>
      <ul>
        <li>Vision‑based modal analysis</li>
        <li>Vision‑based crack detection</li>
        <li>Integration with UAV technology</li>
        <li>Vision‑based sensing and digital image correlation (DIC) techniques</li>
        <li>Edge AI + IoT integration for real‑time SHM</li>
      </ul>
    </section>

  </main>

  <?php require dirname(__FILE__) . '/includes/cta-bar.php'; ?>

  <!-- Footer -->
  <?php require dirname(__FILE__) . '/includes/footer.php'; ?>
</body>

</html>