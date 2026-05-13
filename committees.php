<?php
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';
$SHMS = shms_page_data();
$shmsNavPage = 'committees';
?>
<!DOCTYPE html>
<html lang="en"<?php echo shms_html_theme_class(); ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Committees | SHMS‑2026</title>
  <link rel="stylesheet" href="style.css">
<?php echo shms_head_resource_hints(); ?>
</head>
<body id="top">
<?php require dirname(__FILE__) . '/includes/nav.php'; ?>

<main>
  <?php require dirname(__FILE__) . '/includes/hero-banner.php'; ?>

  <section class="page-heading" id="page-content">
    <h1>Conference Committees</h1>
    <p>Meet The Dedicated Individuals Who Make SHMS‑2026 Possible.</p>
  </section>

  <!-- Organizing Committee -->
  <section id="organizing">
    <h2>Organizing Committee</h2>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Role</th>
          <th>Affiliation</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>Prof. Rama Shanker Verma</td><td>Patron</td><td>Director, MNNIT Allahabad, Prayagraj, India</td></tr>
        <tr><td>Prof. Rama Shanker</td><td>Organising Secretary</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Dr. Varun Singh</td><td>Organising Secretary</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Dr. Snehal K</td><td>Joint Organising Secretary</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Dr. Bharat Rajan</td><td>Joint Organising Secretary</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. L.K. Mishra</td><td>Conference Chair</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. Suresh Bhalla</td><td>President, ISHMS</td><td>Department of Civil Engineering, IIT Delhi, India</td></tr>
        <tr><td>Dr. Navneet Kaur</td><td>Vice President, ISHMS</td><td>CSIR–Central Road Research Institute, Delhi, India</td></tr>
        <tr><td>Col. Rohit Gogna</td><td>General Secretary, ISHMS</td><td>Founder & Chairman, Avinya Green Constructions</td></tr>
        <tr><td>Er. Sayed Sameer H</td><td>Financial Secretary, ISHMS</td><td>Project Scientist/Engineer, Department of Civil Engineering, IIT Delhi, India</td></tr>
        <tr><td>Prof. A.K. Singh</td><td>Member</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. P.K. Mehta</td><td>Member</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. R.M. Singh</td><td>Member</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. R.D. Gupta</td><td>Member</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. R.C. Vaishya</td><td>Member</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. R.P. Singh</td><td>Member</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. Kumar Venkatesh</td><td>Member</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. Nekram Rawal</td><td>Member</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
      </tbody>
    </table>
  </section>

  <!-- Technical Committee -->
  <section id="technical">
    <h2>Technical Committee</h2>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Affiliation</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>Prof. Suresh Bhalla</td><td>Department of Civil Engineering, IIT Delhi, India</td></tr>
        <tr><td>Prof. K.V.L. Subramaniam</td><td>Department of Civil Engineering, IIT Hyderabad, India</td></tr>
        <tr><td>Prof. Praveen Kumar</td><td>Department of Civil Engineering, IIT Roorkee, India</td></tr>
        <tr><td>Prof. Pramod Kumar Gupta</td><td>Department of Civil Engineering, IIT Roorkee, India</td></tr>
        <tr><td>Prof. Sandeep Chaudhary</td><td>Department of Civil Engineering, IIT Indore, India</td></tr>
        <tr><td>Prof. Akhilesh Kumar Maurya</td><td>Department of Civil Engineering, IIT Guwahati, India</td></tr>
        <tr><td>Dr. Naveet Kaur</td><td>CSIR–Central Road Research Institute, New Delhi, India</td></tr>
        <tr><td>Dr. K. Lakshmi</td><td>CSIR–Structural Engineering Research Centre, Chennai, India</td></tr>
        <tr><td>Dr. Satish Dhandole</td><td>School of Mechanical Sciences, IIT Bhubaneswar, India</td></tr>
        <tr><td>Dr. Kumar Pallav</td><td>Department of Civil Engineering & Surveying, Cape Peninsula University of Technology, South Africa</td></tr>
        <tr><td>Prof. Rama Shanker</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Dr. Varun Singh</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
      </tbody>
    </table>
  </section>

  <!-- Advisory Committee -->
  <section id="advisory">
    <h2>Advisory Committee</h2>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Affiliation</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>Prof. Shivesh Sharma</td><td>Department of Biotechnology, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. Neeraj Tyagi</td><td>Department of Computer Science & Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. Asheesh Kumar Singh</td><td>Department of Electrical Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. Purnendu K. Das</td><td>Ex‑Professor of Marine Structures, Universities of Glasgow and Strathclyde, UK</td></tr>
        <tr><td>Prof. Ashok Gupta (Retired)</td><td>Department of Civil Engineering, IIT Delhi, India</td></tr>
        <tr><td>Prof. Pradeep Bhargava</td><td>Department of Civil Engineering, IIT Roorkee, India</td></tr>
        <tr><td>Prof. Bhrigu Nath Singh</td><td>Vice Chancellor, Rajiv Gandhi National Aviation University (RGNAU), Amethi, India</td></tr>
        <tr><td>Prof. Shamsher Bahadur Singh</td><td>Department of Civil Engineering, Birla Institute of Technology & Science, Pilani, India</td></tr>
        <tr><td>Prof. A.K. Sachan (Retired)</td><td>Department of Civil Engineering, MNNIT Allahabad, India</td></tr>
        <tr><td>Prof. Alok Madan</td><td>Department of Civil Engineering, IIT Delhi, India</td></tr>
        <tr><td>Er. Dinesh Kumar</td><td>Department of Water Resource & Irrigation, Uttar Pradesh, India</td></tr>
      </tbody>
    </table>
  </section>

</main>

<?php require dirname(__FILE__) . '/includes/cta-bar.php'; ?>

  <!-- Footer -->
<?php require dirname(__FILE__) . '/includes/footer.php'; ?>
</body>
</html>
