<?php
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';
$SHMS = shms_page_data();
$shmsNavPage = 'call';
?>
<!DOCTYPE html>
<html lang="en" <?php echo shms_html_theme_class(); ?>>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="description"
    content="Submit your paper to SHMS‑2026 — important dates, submission requirements, and publication opportunities. Abstract deadline 30 Jun 2026.">
  <title>Call for Papers | SHMS‑2026</title>
  <link rel="stylesheet" href="style.css">
  <?php echo shms_head_resource_hints(); ?>
</head>

<body id="top">
  <?php require dirname(__FILE__) . '/includes/nav.php'; ?>

  <main>
    <?php require dirname(__FILE__) . '/includes/hero-banner.php'; ?>

    <section class="page-heading" id="page-content">
      <h1>Call for Papers</h1>
      <p>Submit Your Original Research and Join The Conversation at SHMS‑2026.</p>
    </section>

    <section>
      <h2 id="dates-cfp">Important Dates</h2>
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

      <h2 id="submission">Submission Requirements</h2>
      <div class="submission-grid">
        <div class="notice-box notice-box--academic">
          <p class="notice-box-title">Academic Submission</p>
          <p class="notice-box-text">
            <strong>Step 1: </strong>Submit 300-500 words academic abstract including title, authors,
            affiliations and keywords.
            <br />
            <strong>Step 2: </strong>After abstract review and acceptance, submit full paper.
            <br>
          </p>
          <div style="display: flex; justify-content: center; gap: 2rem; margin-top: 2rem;">
            <button
              onclick="window.open('https://docs.google.com/document/d/14r82RQS1PiAMdbCEMuyr3mWpDo-WVvGQ/edit?usp=sharing&ouid=109414172089836850540&rtpof=true&sd=true', '_blank')"
              class="template-btn">Academic
              Abstract Template</button>
            <button
              onclick="window.open('https://docs.google.com/document/d/1s0Ee0Kyo9atNyN3h-8Hb0mawUN8rIGNW/edit?usp=sharing&ouid=109414172089836850540&rtpof=true&sd=true', '_blank')"
              class="template-btn">Full
              Length Paper Template</button>
          </div>
        </div>
        <div class="notice-box notice-box--industry">
          <p class="notice-box-title">Industry Submission</p>
          <p class="notice-box-text">
            <strong>Step 1: </strong>Submit the extended industrial abstract using the Industrial Track Submission
            Template.
            <br />
            <strong>Step 2: </strong>The acceptance will be notified to the authors via email.
            <br>
          </p>
          <div style="display: flex; justify-content: center; gap: 2rem; margin-top: 2rem;">
            <button
              onclick="window.open('https://docs.google.com/document/d/1h93zYfoAe0RhsXXgU4tmKcYAGYUPvZ3h/edit?usp=sharing&ouid=109414172089836850540&rtpof=true&sd=true', '_blank')"
              class="template-btn">Industrial
              Track Extended Abstract Template</button>
          </div>
        </div>
        <div class="notice-box notice-box--full notice-box--general">
          <p class="notice-box-title">General Guidelines</p>
          <ul class="notice-box-text">
            <li>All submissions must be original and not previously published.</li>
            <li>Papers will be peer‑reviewed by at least two reviewers.</li>
            <li>Submit via <strong>Microsoft CMT</strong> — the conference submission link will be published here when
              the
              portal opens.</li>
          </ul>
        </div>
      </div>

      <div class="notice-box easychair-box notice-box--peer-review" id="peer-review">
        <div class="responsive-flex-container">
          <div>
            <p class="notice-box-title">Submissions &amp; peer review</p>
            <p class="notice-box-text">
              SHMS‑2026 will use <a href="https://cmt3.research.microsoft.com/About" target="_blank"
                rel="noopener noreferrer">Microsoft CMT</a> for managing submissions and the peer-review workflow.
              Authors should register and submit through the official
              Microsoft CMT conference page once it is announced.
              <br><br>
              *The Microsoft CMT service was used for managing the peer-reviewing process for this conference. This
              service was provided for free by Microsoft and they bore all expenses, including costs for Azure cloud
              services as well as for software development and support.
            </p>
          </div>
          <div>
            <button onclick="window.open('https://cmt3.research.microsoft.com/SHMS2026/Submission/Index', '_blank')"
              class="template-btn">Submit
              Your Abstract/Full Paper</button>
          </div>
        </div>
      </div>

      <div style="text-align: center;">
        <h2 id="publication">Publication Opportunities</h2>

        <div style="margin-bottom: 3.5rem;">
          <h3 class="themed-sub-heading">Journal Publication</h3>
          <p style="margin: 0 auto 2rem; text-align: center;">Selected papers will be invited for the publication in the
            following journals:</p>
          <div id="journals" class="responsive-flex-container"
            style="justify-content: center; gap: 2.5rem; align-items: stretch;">
            <div class="journal-card">
              <a href="https://journals.sagepub.com/home/JIM" target="_blank" rel="noopener noreferrer">
                <img src="assets/images/journal1.png" alt="journal_img1" width="280" height="auto" loading="lazy">
              </a>
              <a href="https://journals.sagepub.com/home/JIM" target="_blank" rel="noopener noreferrer">Journal of
                Intelligent Material Systems and Structures</a>
            </div>
            <div class="journal-card">
              <a href="https://link.springer.com/journal/44285" target="_blank" rel="noopener noreferrer">
                <img src="assets/images/journal2.png" alt="journal_img2" width="280" height="auto" loading="lazy">
              </a>
              <a href="https://link.springer.com/journal/44285" target="_blank" rel="noopener noreferrer">Urban Lifeline
                (Springer)</a>
            </div>
          </div>
        </div>

        <div>
          <h3 class="themed-sub-heading">Conference Proceedings</h3>
          <p style="text-align: center; margin: 0 auto; max-width: 85ch;">
            Accepted papers will be published in the conference proceedings indexed in Scopus.
            Authors of high‑quality contributions may be recommended for special issues in partner journals following
            the peer‑review process.
          </p>
        </div>
      </div>
    </section>

  </main>

  <?php require dirname(__FILE__) . '/includes/cta-bar.php'; ?>

  <?php require dirname(__FILE__) . '/includes/footer.php'; ?>
</body>

</html>