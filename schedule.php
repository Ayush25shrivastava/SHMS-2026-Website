<?php
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';
$SHMS = shms_page_data();
$shmsNavPage = 'schedule';
?>
<!DOCTYPE html>
<html lang="en" <?php echo shms_html_theme_class(); ?>>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Program Schedule | SHMS‑2026</title>
  <link rel="stylesheet" href="style.css">
  <?php echo shms_head_resource_hints(); ?>
</head>

<body id="top">
  <?php require dirname(__FILE__) . '/includes/nav.php'; ?>

  <main>
    <?php require dirname(__FILE__) . '/includes/hero-banner.php'; ?>

    <section class="page-heading" id="page-content">
      <h1>Program Schedule</h1>
      <p>Three Days of Keynote Lectures, Technical Sessions, Panels and Networking.</p>
    </section>

    <section>
      <h2 id="day1">Day 1 – October 15, 2026</h2>
      <table>
        <thead>
          <tr>
            <th>Time</th>
            <th>Session / Activity</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>08:00 – 09:00</td>
            <td>Registration and Welcome</td>
          </tr>
          <tr>
            <td>09:00 – 10:00</td>
            <td>Inaugural Ceremony: Welcome address, lighting of lamp & ISHMS President’s address</td>
          </tr>
          <tr>
            <td>10:00 – 11:00</td>
            <td>Keynote 1 – <em>“Digital Twins for Smart Infrastructure”</em> (Prof. Harpal Singh, Arctic University of
              Norway)</td>
          </tr>
          <tr>
            <td>11:00 – 11:30</td>
            <td>Tea/Coffee Break & Networking</td>
          </tr>
          <tr>
            <td>11:30 – 13:00</td>
            <td>Parallel Technical Sessions I: Track 1 (Smart Materials) | Track 2 (Sensor Technologies) | Track 3 (WSN
              & IoT)</td>
          </tr>
          <tr>
            <td>13:00 – 14:00</td>
            <td>Lunch & Exhibition Visit</td>
          </tr>
          <tr>
            <td>14:00 – 15:00</td>
            <td>Keynote 2 – <em>“AI‑Driven Structural Health Monitoring”</em> (Prof. Suresh Bhalla, IIT Delhi)</td>
          </tr>
          <tr>
            <td>15:00 – 16:30</td>
            <td>Parallel Technical Sessions II: Track 4 (AI/ML in SHM) | Track 5 (Digital Twins) | Track 6 (NDE)</td>
          </tr>
          <tr>
            <td>16:30 – 17:00</td>
            <td>Tea/Coffee Break</td>
          </tr>
          <tr>
            <td>17:00 – 18:30</td>
            <td>Poster Session I & Industry Exhibition</td>
          </tr>
        </tbody>
      </table>

      <h2 id="day2">Day 2 – October 16, 2026</h2>
      <table>
        <thead>
          <tr>
            <th>Time</th>
            <th>Session / Activity</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>09:00 – 10:00</td>
            <td>Keynote 3 – <em>“Pavement Evaluation and Assessment”</em> (Prof. Praveen Kumar, IIT Roorkee)</td>
          </tr>
          <tr>
            <td>10:00 – 10:45</td>
            <td>Expert Lecture 1 – <em>“Bridge SHM Using Vehicle‑Bound Sensors”</em> (Prof. K.V.L. Subramaniam, IIT
              Hyderabad)</td>
          </tr>
          <tr>
            <td>10:45 – 11:30</td>
            <td>Parallel Technical Sessions III: Track 7 (Vibration‑Based SHM) | Track 8 (Field Applications)</td>
          </tr>
          <tr>
            <td>11:30 – 12:00</td>
            <td>Tea/Coffee Break</td>
          </tr>
          <tr>
            <td>12:00 – 13:00</td>
            <td>Panel Discussion – <em>“SHM Implementation Challenges in Indian Infrastructure”</em></td>
          </tr>
          <tr>
            <td>13:00 – 14:00</td>
            <td>Lunch</td>
          </tr>
          <tr>
            <td>14:00 – 15:00</td>
            <td>Keynote 4 – <em>“AI‑Driven Structural Health Monitoring”</em> (Prof. Gyuhae Park, Chonnam National
              University, South Korea)</td>
          </tr>
          <tr>
            <td>15:00 – 16:30</td>
            <td>Parallel Technical Sessions IV: Track 9 (Seismic Monitoring) | Track 10 (Pavement Evaluation)</td>
          </tr>
          <tr>
            <td>16:30 – 17:00</td>
            <td>Tea/Coffee Break</td>
          </tr>
          <tr>
            <td>17:00 – 18:30</td>
            <td>Poster Session II & ISHMS General Body Meeting</td>
          </tr>
          <tr>
            <td>18:30 – 19:15</td>
            <td>Expert Lecture 3 – <em>“Structural Health Monitoring for Concrete Structures”</em>
              (Prof. K.V.L. Subramaniam, IIT Hyderabad)</td>
          </tr>
          <tr>
            <td>19:30 – 22:00</td>
            <td>Conference Banquet</td>
          </tr>
        </tbody>
      </table>

      <h2 id="day3">Day 3 – October 17, 2026</h2>
      <table>
        <thead>
          <tr>
            <th>Time</th>
            <th>Session / Activity</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>09:00 – 10:00</td>
            <td>Keynote 5 – <em>“Energy Harvesting for Self‑Powered SHM Systems”</em> (Prof. Daniel J. Inman, University
              of Michigan)</td>
          </tr>
          <tr>
            <td>10:00 – 10:45</td>
            <td>Expert Lecture 2 – <em>“Taking Piezo Sensing to the Skies”</em> (Dr. Naveet Kaur, CSIR‑CRRI, New Delhi)
            </td>
          </tr>
          <tr>
            <td>10:45 – 11:30</td>
            <td>Special Sessions: Young Researchers Forum</td>
          </tr>
          <tr>
            <td>11:30 – 12:00</td>
            <td>Tea/Coffee Break</td>
          </tr>
          <tr>
            <td>12:00 – 13:00</td>
            <td>Panel – <em>“Future Directions in Smart Materials & SHM Research”</em></td>
          </tr>
          <tr>
            <td>13:00 – 14:00</td>
            <td>Lunch</td>
          </tr>
          <tr>
            <td>14:00 – 15:00</td>
            <td>Valedictory & Closing Ceremony: Best Paper Awards, Conference Summary, Vote of Thanks</td>
          </tr>
          <tr>
            <td>15:00 onwards</td>
            <td>Technical Tour: Sangam (Triveni) & Heritage Sites of Prayagraj (optional)</td>
          </tr>
        </tbody>
      </table>
    </section>

  </main>

  <?php require dirname(__FILE__) . '/includes/cta-bar.php'; ?>

  <!-- Footer -->
  <?php require dirname(__FILE__) . '/includes/footer.php'; ?>
</body>

</html>