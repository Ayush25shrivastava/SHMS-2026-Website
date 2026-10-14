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
  <style>
    .schedule-container {
      max-width: 1060px;
      margin: 0 auto;
      padding: 0 0.5rem;
    }

    .schedule-info-banner {
      background: var(--color-bg-elevated);
      border: 1px solid var(--surface-border);
      border-left: 5px solid var(--color-accent-strong);
      border-radius: var(--radius-md);
      padding: 1.25rem 1.5rem;
      margin-bottom: 2rem;
      box-shadow: var(--surface-shadow);
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .schedule-info-title {
      font-family: var(--font-display);
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--color-primary-900);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .schedule-info-desc {
      margin: 0;
      font-size: 0.95rem;
      color: var(--color-text-secondary);
      line-height: 1.6;
    }

    .schedule-info-badges {
      display: flex;
      flex-wrap: wrap;
      gap: 0.6rem;
      margin-top: 0.35rem;
    }

    .schedule-meta-chip {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.85rem;
      font-weight: 600;
      padding: 0.25rem 0.75rem;
      border-radius: var(--radius-pill);
      background: rgba(14, 165, 233, 0.1);
      color: var(--color-primary-900);
      border: 1px solid rgba(14, 165, 233, 0.25);
    }

    .schedule-day-nav {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      justify-content: center;
      margin-bottom: 2.25rem;
      padding: 0.6rem;
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid var(--surface-border);
      border-radius: var(--radius-pill);
      box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
      width: fit-content;
      margin-left: auto;
      margin-right: auto;
      position: sticky;
      top: calc(var(--nav-height) + 10px);
      z-index: 20;
    }

    .schedule-day-nav a {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.55rem 1.25rem;
      border-radius: var(--radius-pill);
      background: var(--color-bg-elevated);
      color: var(--color-primary-900);
      font-weight: 700;
      font-size: 0.9rem;
      border: 1px solid var(--surface-border);
      transition: all 0.2s ease;
      text-decoration: none !important;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .schedule-day-nav a:hover {
      background: var(--color-primary-900);
      color: #ffffff !important;
      border-color: var(--color-primary-900);
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
    }

    .schedule-day-block {
      scroll-margin-top: calc(var(--nav-height) + 70px);
      margin-bottom: 3.25rem;
    }

    .schedule-table-wrap {
      border-radius: var(--radius-md);
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      box-shadow: var(--surface-shadow), 0 0 0 1px var(--surface-border);
      background: var(--color-bg-elevated);
    }

    .schedule-table {
      width: 100%;
      border-collapse: collapse;
      margin: 0 !important;
      text-align: left;
      font-size: 0.94rem;
    }

    .schedule-table th,
    .schedule-table td {
      border: 1px solid var(--surface-border);
      padding: 0.75rem 1rem;
      vertical-align: middle;
    }

    /* Day Banner Header */
    .schedule-table .day-banner-th {
      background: linear-gradient(135deg, #0b1220 0%, #1e3a5f 60%, #152942 100%);
      color: #ffffff;
      font-family: var(--font-display);
      font-size: 1.2rem;
      font-weight: 700;
      letter-spacing: 0.02em;
      text-align: center;
      padding: 1.05rem 1rem;
      text-transform: none;
      border-bottom: 2px solid var(--color-accent-strong);
    }

    /* Column Headers */
    .schedule-table .col-header-th {
      background: linear-gradient(180deg, #1e3a5f 0%, var(--color-primary-900) 100%);
      color: #f8fafc;
      font-family: var(--font-display);
      font-weight: 600;
      font-size: 0.9rem;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      padding: 0.65rem 1rem;
    }

    /* Parallel Session Banner Row */
    .schedule-table .session-time-banner {
      background-color: #e0f2fe;
      color: #0369a1;
      font-weight: 700;
      font-size: 0.95rem;
      white-space: nowrap;
      text-align: center;
      border-color: #bae6fd;
    }

    .schedule-table .session-title-banner {
      background-color: #e0f2fe;
      color: #0369a1;
      font-family: var(--font-display);
      font-weight: 700;
      font-size: 1.02rem;
      letter-spacing: 0.02em;
      text-align: center;
      padding: 0.7rem 1rem;
      border-color: #bae6fd;
    }

    /* Sub-Headers for Parallel Tracks */
    .schedule-table .sub-header-tr th {
      background: rgba(30, 58, 95, 0.09);
      color: var(--color-primary-900);
      font-family: var(--font-display);
      font-weight: 700;
      font-size: 0.88rem;
      letter-spacing: 0.02em;
      text-align: center;
      border-bottom: 2px solid var(--surface-border);
      padding: 0.55rem 0.75rem;
    }

    .schedule-table .sub-header-tr th.sub-th-time {
      text-align: left;
      padding-left: 1rem;
    }

    /* Time Column */
    .schedule-table .time-col {
      font-weight: 600;
      color: var(--color-primary-900);
      white-space: nowrap;
      font-size: 0.92rem;
      width: 25%;
    }

    /* Keynote Rows */
    .schedule-table .keynote-row {
      background-color: rgba(254, 243, 199, 0.38) !important;
    }

    .schedule-table .keynote-cell {
      border-left: 4px solid var(--color-accent-strong) !important;
      padding: 0.95rem 1.15rem;
    }

    .keynote-badge {
      display: inline-block;
      padding: 0.2rem 0.6rem;
      background: var(--color-primary-900);
      color: #ffffff;
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      border-radius: var(--radius-sm);
      text-transform: uppercase;
      margin-bottom: 0.35rem;
    }

    .keynote-speaker {
      font-size: 1.02rem;
      font-weight: 700;
      color: var(--color-primary-900);
      line-height: 1.4;
    }

    .keynote-org {
      font-weight: 500;
      color: var(--color-text-secondary);
    }

    .keynote-topic {
      margin-top: 0.4rem;
      font-size: 0.92rem;
      color: var(--color-text);
      line-height: 1.5;
    }

    .keynote-topic strong {
      color: var(--color-primary-800);
    }

    /* Paper Cells & Badges */
    .schedule-table .paper-cell {
      text-align: center;
      width: 37.5%;
      padding: 0.55rem 0.75rem;
    }

    .paper-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 44px;
      padding: 0.25rem 0.7rem;
      background: rgba(14, 165, 233, 0.12);
      color: var(--color-primary-900);
      font-weight: 700;
      font-size: 0.95rem;
      border-radius: 6px;
      border: 1px solid rgba(14, 165, 233, 0.35);
    }

    /* Break Rows */
    .schedule-table .break-row {
      background-color: rgba(241, 245, 249, 0.7) !important;
    }

    .schedule-table .break-cell {
      font-weight: 600;
      color: var(--color-primary-800);
    }

    .break-tag {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      font-weight: 600;
      font-size: 0.93rem;
      color: var(--color-primary-800);
    }

    /* Gala Dinner Row */
    .schedule-table .gala-row {
      background-color: rgba(254, 240, 138, 0.55) !important;
    }

    .schedule-table .gala-time {
      color: #854d0e;
      font-weight: 700;
    }

    .schedule-table .gala-cell {
      border-left: 4px solid #eab308 !important;
      padding: 1rem 1.15rem;
    }

    .gala-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 700;
      font-size: 1.05rem;
      color: #854d0e;
    }

    /* Dark Mode Adjustments */
    html.theme-dark .schedule-info-banner {
      background: rgba(30, 41, 59, 0.85);
      border-color: rgba(148, 163, 184, 0.2);
    }

    html.theme-dark .schedule-info-title {
      color: #f1f5f9;
    }

    html.theme-dark .schedule-info-desc {
      color: #cbd5e1;
    }

    html.theme-dark .schedule-meta-chip {
      background: rgba(56, 189, 248, 0.15);
      color: #e0f2fe;
      border-color: rgba(56, 189, 248, 0.35);
    }

    html.theme-dark .schedule-day-nav {
      background: rgba(15, 23, 42, 0.85);
      border-color: rgba(148, 163, 184, 0.2);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    }

    html.theme-dark .schedule-day-nav a {
      background: #1e293b;
      border-color: rgba(148, 163, 184, 0.2);
      color: #f1f5f9;
    }

    html.theme-dark .schedule-day-nav a:hover {
      background: var(--color-accent-strong);
      color: #0b1220 !important;
      border-color: var(--color-accent-strong);
    }

    html.theme-dark .schedule-table-wrap {
      background: rgba(15, 23, 42, 0.95);
    }

    html.theme-dark .schedule-table .day-banner-th {
      background-color: #1e3a5f;
      color: #f1f5f9;
      border-bottom-color: #38bdf8;
    }

    html.theme-dark .schedule-table .session-time-banner,
    html.theme-dark .schedule-table .session-title-banner {
      background-color: rgba(14, 165, 233, 0.16);
      color: #7dd3fc;
      border-color: rgba(56, 189, 248, 0.28);
    }

    html.theme-dark .schedule-table .sub-header-tr th {
      background: rgba(30, 41, 59, 0.9);
      color: #e2e8f0;
      border-color: rgba(51, 65, 85, 0.8);
    }

    html.theme-dark .schedule-table .time-col {
      color: #7dd3fc;
    }

    html.theme-dark .schedule-table .keynote-row {
      background-color: rgba(30, 58, 95, 0.35) !important;
    }

    html.theme-dark .keynote-badge {
      background: var(--color-accent-strong);
      color: #0b1220;
    }

    html.theme-dark .keynote-speaker {
      color: #f8fafc;
    }

    html.theme-dark .keynote-org {
      color: #94a3b8;
    }

    html.theme-dark .keynote-topic {
      color: #e2e8f0;
    }

    html.theme-dark .keynote-topic strong {
      color: #7dd3fc;
    }

    html.theme-dark .paper-badge {
      background: rgba(56, 189, 248, 0.18);
      color: #f8fafc;
      border-color: rgba(56, 189, 248, 0.4);
    }

    html.theme-dark .schedule-table .break-row {
      background-color: rgba(15, 23, 42, 0.6) !important;
    }

    html.theme-dark .break-tag {
      color: #cbd5e1;
    }

    html.theme-dark .schedule-table .gala-row {
      background-color: rgba(120, 53, 15, 0.32) !important;
    }

    html.theme-dark .schedule-table .gala-time {
      color: #fde047;
    }

    html.theme-dark .schedule-table .gala-cell {
      border-left-color: #f59e0b !important;
    }

    html.theme-dark .gala-badge {
      color: #fde047;
    }

    @media (max-width: 640px) {
      .schedule-day-nav {
        width: 100%;
        border-radius: var(--radius-md);
      }
      .schedule-day-nav a {
        flex: 1 1 auto;
        text-align: center;
        justify-content: center;
        padding: 0.5rem 0.75rem;
        font-size: 0.82rem;
      }
      .schedule-table th,
      .schedule-table td {
        padding: 0.55rem 0.65rem;
      }
      .schedule-table .time-col {
        font-size: 0.82rem;
      }
      .paper-badge {
        min-width: 36px;
        padding: 0.2rem 0.45rem;
        font-size: 0.85rem;
      }
    }
  </style>
</head>

<body id="top">
  <?php require dirname(__FILE__) . '/includes/nav.php'; ?>

  <main>
    <?php require dirname(__FILE__) . '/includes/hero-banner.php'; ?>

    <section class="page-heading" id="page-content">
      <h1>Program Schedule</h1>
      <!-- <p>Three Days of Keynote Lectures, Technical Sessions, Panels and Networking.</p> -->
    </section>

    <section>
      <div class="schedule-container">
        <!-- Overview / Notice Card -->
        <div class="schedule-info-banner">
          <h2 class="schedule-info-title">
            <span>📋</span> SHMS 2026 Conference Schedule
          </h2>
          <p class="schedule-info-desc">
            <strong>15 to 17 October 2026</strong> at <strong>MNNIT Allahabad</strong>, Prayagraj, India.
            In the parallel sessions, the numbers in each column represent <strong>Paper IDs</strong>.
          </p>
          <div class="schedule-info-badges">
            <span class="schedule-meta-chip">⏱️ 12 mins Presentation + 3 mins Q&amp;A per paper</span>
            <span class="schedule-meta-chip">🎤 6 Keynote Addresses</span>
            <span class="schedule-meta-chip">📑 12 Parallel Technical Sessions</span>
          </div>
        </div>

        <!-- Quick-jump Navigation for Days -->
        <div class="schedule-day-nav" role="navigation" aria-label="Schedule Days">
          <a href="#day1">📅 Day 1 (Thu, 15 Oct)</a>
          <a href="#day2">📅 Day 2 (Fri, 16 Oct)</a>
          <a href="#day3">📅 Day 3 (Sat, 17 Oct)</a>
        </div>

        <!-- ==================== DAY 1 ==================== -->
        <div id="day1" class="schedule-day-block">
          <div class="schedule-table-wrap">
            <table class="schedule-table">
              <thead>
                <tr>
                  <th colspan="3" class="day-banner-th">Day 1: Thursday, 15 October 2026</th>
                </tr>
                <tr>
                  <th class="col-header-th" style="width: 25%;">Time</th>
                  <th class="col-header-th" colspan="2">Programme</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="time-col">08:00 – 09:30</td>
                  <td colspan="2"><strong>Registration</strong></td>
                </tr>
                <tr>
                  <td class="time-col">09:30 – 10:30</td>
                  <td colspan="2"><strong>Inaugural Ceremony</strong></td>
                </tr>
                <tr class="break-row">
                  <td class="time-col">10:30 – 11:00</td>
                  <td colspan="2" class="break-cell"><span class="break-tag">☕ High Tea</span></td>
                </tr>
                <tr class="keynote-row">
                  <td class="time-col">11:00 – 11:55</td>
                  <td colspan="2" class="keynote-cell">
                    <span class="keynote-badge">Keynote 1</span>
                    <div class="keynote-speaker">Prof. Gyuhae Park, <span class="keynote-org">Chonnam National University, Republic of Korea</span></div>
                    <div class="keynote-topic"><strong>Topic:</strong> Non-Contact Sensing for Structural Dynamics and Monitoring Applications</div>
                  </td>
                </tr>
                <tr class="session-banner-tr">
                  <td class="session-time-banner">12:00 – 13:30</td>
                  <td colspan="2" class="session-title-banner">Sessions 1 &amp; 2</td>
                </tr>
                <tr class="sub-header-tr">
                  <th class="sub-th-time">Time</th>
                  <th>Session 1 (Paper ID)</th>
                  <th>Session 2 (Paper ID)</th>
                </tr>
                <tr>
                  <td class="time-col">12:00 – 12:15</td>
                  <td class="paper-cell"><span class="paper-badge">47</span></td>
                  <td class="paper-cell"><span class="paper-badge">45</span></td>
                </tr>
                <tr>
                  <td class="time-col">12:15 – 12:30</td>
                  <td class="paper-cell"><span class="paper-badge">65</span></td>
                  <td class="paper-cell"><span class="paper-badge">52</span></td>
                </tr>
                <tr>
                  <td class="time-col">12:30 – 12:45</td>
                  <td class="paper-cell"><span class="paper-badge">92</span></td>
                  <td class="paper-cell"><span class="paper-badge">136</span></td>
                </tr>
                <tr>
                  <td class="time-col">12:45 – 13:00</td>
                  <td class="paper-cell"><span class="paper-badge">97</span></td>
                  <td class="paper-cell"><span class="paper-badge">82</span></td>
                </tr>
                <tr>
                  <td class="time-col">13:00 – 13:15</td>
                  <td class="paper-cell"><span class="paper-badge">137</span></td>
                  <td class="paper-cell"><span class="paper-badge">125</span></td>
                </tr>
                <tr>
                  <td class="time-col">13:15 – 13:30</td>
                  <td class="paper-cell"><span class="paper-badge">111</span></td>
                  <td class="paper-cell"><span class="paper-badge">113</span></td>
                </tr>
                <tr class="break-row">
                  <td class="time-col">14:00 – 15:00</td>
                  <td colspan="2" class="break-cell"><span class="break-tag">🍽️ Lunch</span></td>
                </tr>
                <tr class="session-banner-tr">
                  <td class="session-time-banner">15:10 – 15:55</td>
                  <td colspan="2" class="session-title-banner">Sessions 3 &amp; 4</td>
                </tr>
                <tr class="sub-header-tr">
                  <th class="sub-th-time">Time</th>
                  <th>Session 3 (Paper ID)</th>
                  <th>Session 4 (Paper ID)</th>
                </tr>
                <tr>
                  <td class="time-col">15:10 – 15:25</td>
                  <td class="paper-cell"><span class="paper-badge">116</span></td>
                  <td class="paper-cell"><span class="paper-badge">109</span></td>
                </tr>
                <tr>
                  <td class="time-col">15:25 – 15:40</td>
                  <td class="paper-cell"><span class="paper-badge">121</span></td>
                  <td class="paper-cell"><span class="paper-badge">122</span></td>
                </tr>
                <tr>
                  <td class="time-col">15:40 – 15:55</td>
                  <td class="paper-cell"><span class="paper-badge">20</span></td>
                  <td class="paper-cell"><span class="paper-badge">74</span></td>
                </tr>
                <tr class="keynote-row">
                  <td class="time-col">16:00 – 17:00</td>
                  <td colspan="2" class="keynote-cell">
                    <span class="keynote-badge">Keynote 2</span>
                    <div class="keynote-speaker">Prof. Praveen Kumar, <span class="keynote-org">Indian Institute of Technology Roorkee, India</span></div>
                    <div class="keynote-topic"><strong>Topic:</strong> Utilization of Waste Plastic for the Construction of Flexible Roads: Studies at IIT Roorkee</div>
                  </td>
                </tr>
                <tr class="break-row">
                  <td class="time-col">17:00 – 17:20</td>
                  <td colspan="2" class="break-cell"><span class="break-tag">☕ Tea Break</span></td>
                </tr>
                <tr class="gala-row">
                  <td class="time-col gala-time">18:30 onwards</td>
                  <td colspan="2" class="gala-cell">
                    <div class="gala-badge">🌟 Gala Dinner</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ==================== DAY 2 ==================== -->
        <div id="day2" class="schedule-day-block">
          <div class="schedule-table-wrap">
            <table class="schedule-table">
              <thead>
                <tr>
                  <th colspan="3" class="day-banner-th">Day 2: Friday, 16 October 2026</th>
                </tr>
                <tr>
                  <th class="col-header-th" style="width: 25%;">Time</th>
                  <th class="col-header-th" colspan="2">Programme</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="time-col">08:30 – 09:30</td>
                  <td colspan="2">
                    <div><strong>Registration</strong></div>
                    <div style="margin-top: 0.2rem;"><strong>Breakfast</strong></div>
                  </td>
                </tr>
                <tr>
                  <td class="time-col">09:30 – 09:45</td>
                  <td colspan="2"><strong>Welcome Session</strong></td>
                </tr>
                <tr class="keynote-row">
                  <td class="time-col">09:45 – 10:45</td>
                  <td colspan="2" class="keynote-cell">
                    <span class="keynote-badge">Keynote 3</span>
                    <div class="keynote-speaker">Prof. Suresh Bhalla, <span class="keynote-org">Indian Institute of Technology Delhi, India</span></div>
                    <div class="keynote-topic"><strong>Topic:</strong> Fatigue and Corrosion: The Twin Silent Threats to Indian Infrastructure: Can SHM Make the Difference?</div>
                  </td>
                </tr>
                <tr class="break-row">
                  <td class="time-col">10:45 – 11:05</td>
                  <td colspan="2" class="break-cell"><span class="break-tag">☕ Tea Break</span></td>
                </tr>
                <tr class="session-banner-tr">
                  <td class="session-time-banner">11:15 – 13:00</td>
                  <td colspan="2" class="session-title-banner">Sessions 5 &amp; 6</td>
                </tr>
                <tr class="sub-header-tr">
                  <th class="sub-th-time">Time</th>
                  <th>Session 5 (Paper ID)</th>
                  <th>Session 6 (Paper ID)</th>
                </tr>
                <tr>
                  <td class="time-col">11:15 – 11:30</td>
                  <td class="paper-cell"><span class="paper-badge">17</span></td>
                  <td class="paper-cell"><span class="paper-badge">83</span></td>
                </tr>
                <tr>
                  <td class="time-col">11:30 – 11:45</td>
                  <td class="paper-cell"><span class="paper-badge">12</span></td>
                  <td class="paper-cell"><span class="paper-badge">91</span></td>
                </tr>
                <tr>
                  <td class="time-col">11:45 – 12:00</td>
                  <td class="paper-cell"><span class="paper-badge">13</span></td>
                  <td class="paper-cell"><span class="paper-badge">93</span></td>
                </tr>
                <tr>
                  <td class="time-col">12:00 – 12:15</td>
                  <td class="paper-cell"><span class="paper-badge">54</span></td>
                  <td class="paper-cell"><span class="paper-badge">96</span></td>
                </tr>
                <tr>
                  <td class="time-col">12:15 – 12:30</td>
                  <td class="paper-cell"><span class="paper-badge">62</span></td>
                  <td class="paper-cell"><span class="paper-badge">42</span></td>
                </tr>
                <tr>
                  <td class="time-col">12:30 – 12:45</td>
                  <td class="paper-cell"><span class="paper-badge">67</span></td>
                  <td class="paper-cell"><span class="paper-badge">18</span></td>
                </tr>
                <tr>
                  <td class="time-col">12:45 – 13:00</td>
                  <td class="paper-cell"><span class="paper-badge">11</span></td>
                  <td class="paper-cell"><span class="paper-badge">132</span></td>
                </tr>
                <tr class="break-row">
                  <td class="time-col">13:00 – 14:00</td>
                  <td colspan="2" class="break-cell"><span class="break-tag">🍽️ Lunch</span></td>
                </tr>
                <tr class="session-banner-tr">
                  <td class="session-time-banner">14:10 – 15:55</td>
                  <td colspan="2" class="session-title-banner">Sessions 7 &amp; 8</td>
                </tr>
                <tr class="sub-header-tr">
                  <th class="sub-th-time">Time</th>
                  <th>Session 7 (Paper ID)</th>
                  <th>Session 8 (Paper ID)</th>
                </tr>
                <tr>
                  <td class="time-col">14:10 – 14:25</td>
                  <td class="paper-cell"><span class="paper-badge">15</span></td>
                  <td class="paper-cell"><span class="paper-badge">16</span></td>
                </tr>
                <tr>
                  <td class="time-col">14:25 – 14:40</td>
                  <td class="paper-cell"><span class="paper-badge">60</span></td>
                  <td class="paper-cell"><span class="paper-badge">68</span></td>
                </tr>
                <tr>
                  <td class="time-col">14:40 – 14:55</td>
                  <td class="paper-cell"><span class="paper-badge">108</span></td>
                  <td class="paper-cell"><span class="paper-badge">103</span></td>
                </tr>
                <tr>
                  <td class="time-col">14:55 – 15:10</td>
                  <td class="paper-cell"><span class="paper-badge">107</span></td>
                  <td class="paper-cell"><span class="paper-badge">123</span></td>
                </tr>
                <tr>
                  <td class="time-col">15:10 – 15:25</td>
                  <td class="paper-cell"><span class="paper-badge">110</span></td>
                  <td class="paper-cell"><span class="paper-badge">127</span></td>
                </tr>
                <tr>
                  <td class="time-col">15:25 – 15:40</td>
                  <td class="paper-cell"><span class="paper-badge">115</span></td>
                  <td class="paper-cell"><span class="paper-badge">126</span></td>
                </tr>
                <tr>
                  <td class="time-col">15:40 – 15:55</td>
                  <td class="paper-cell"><span class="paper-badge">95</span></td>
                  <td class="paper-cell"><span class="paper-badge">98</span></td>
                </tr>
                <tr class="keynote-row">
                  <td class="time-col">16:00 – 17:00</td>
                  <td colspan="2" class="keynote-cell">
                    <span class="keynote-badge">Keynote 4</span>
                    <div class="keynote-speaker">Dr. K. Lakshmi, <span class="keynote-org">CSIR-Structural Engineering Research Centre, Chennai, India</span></div>
                    <div class="keynote-topic"><strong>Topic:</strong> Solving Inverse Problems in SHM using Optimization Techniques</div>
                  </td>
                </tr>
                <tr class="break-row">
                  <td class="time-col">17:00 – 17:20</td>
                  <td colspan="2" class="break-cell"><span class="break-tag">☕ Tea Break</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ==================== DAY 3 ==================== -->
        <div id="day3" class="schedule-day-block">
          <div class="schedule-table-wrap">
            <table class="schedule-table">
              <thead>
                <tr>
                  <th colspan="3" class="day-banner-th">Day 3: Saturday, 17 October 2026</th>
                </tr>
                <tr>
                  <th class="col-header-th" style="width: 25%;">Time</th>
                  <th class="col-header-th" colspan="2">Programme</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="time-col">08:15 – 09:00</td>
                  <td colspan="2">
                    <div><strong>Registration</strong></div>
                    <div style="margin-top: 0.2rem;"><strong>Breakfast</strong></div>
                  </td>
                </tr>
                <tr class="keynote-row">
                  <td class="time-col">09:00 – 10:00</td>
                  <td colspan="2" class="keynote-cell">
                    <span class="keynote-badge">Keynote 5</span>
                    <div class="keynote-speaker">Prof. K.V.L. Subramaniam, <span class="keynote-org">Indian Institute of Technology Hyderabad, India</span></div>
                    <div class="keynote-topic"><strong>Topic:</strong> Comprehensive Monitoring of Concrete Structures from Casting through Service Life with Embedded PZT Sensors</div>
                  </td>
                </tr>
                <tr class="break-row">
                  <td class="time-col">10:00 – 10:20</td>
                  <td colspan="2" class="break-cell"><span class="break-tag">☕ Tea Break</span></td>
                </tr>
                <tr class="session-banner-tr">
                  <td class="session-time-banner">10:30 – 12:00</td>
                  <td colspan="2" class="session-title-banner">Sessions 9 &amp; 10</td>
                </tr>
                <tr class="sub-header-tr">
                  <th class="sub-th-time">Time</th>
                  <th>Session 9 (Paper ID)</th>
                  <th>Session 10 (Paper ID)</th>
                </tr>
                <tr>
                  <td class="time-col">10:30 – 10:45</td>
                  <td class="paper-cell"><span class="paper-badge">58</span></td>
                  <td class="paper-cell"><span class="paper-badge">19</span></td>
                </tr>
                <tr>
                  <td class="time-col">10:45 – 11:00</td>
                  <td class="paper-cell"><span class="paper-badge">77</span></td>
                  <td class="paper-cell"><span class="paper-badge">21</span></td>
                </tr>
                <tr>
                  <td class="time-col">11:00 – 11:15</td>
                  <td class="paper-cell"><span class="paper-badge">99</span></td>
                  <td class="paper-cell"><span class="paper-badge">22</span></td>
                </tr>
                <tr>
                  <td class="time-col">11:15 – 11:30</td>
                  <td class="paper-cell"><span class="paper-badge">104</span></td>
                  <td class="paper-cell"><span class="paper-badge">63</span></td>
                </tr>
                <tr>
                  <td class="time-col">11:30 – 11:45</td>
                  <td class="paper-cell"><span class="paper-badge">117</span></td>
                  <td class="paper-cell"><span class="paper-badge">120</span></td>
                </tr>
                <tr>
                  <td class="time-col">11:45 – 12:00</td>
                  <td class="paper-cell"><span class="paper-badge">105</span></td>
                  <td class="paper-cell"><span class="paper-badge">141</span></td>
                </tr>
                <tr class="break-row">
                  <td class="time-col">12:30 – 13:30</td>
                  <td colspan="2" class="break-cell"><span class="break-tag">🍽️ Lunch</span></td>
                </tr>
                <tr class="session-banner-tr">
                  <td class="session-time-banner">13:30 – 14:00</td>
                  <td colspan="2" class="session-title-banner">Sessions 11 &amp; 12</td>
                </tr>
                <tr class="sub-header-tr">
                  <th class="sub-th-time">Time</th>
                  <th>Session 11 (Paper ID)</th>
                  <th>Session 12 (Paper ID)</th>
                </tr>
                <tr>
                  <td class="time-col">13:30 – 13:45</td>
                  <td class="paper-cell"><span class="paper-badge">59</span></td>
                  <td class="paper-cell"><span class="paper-badge">114</span></td>
                </tr>
                <tr>
                  <td class="time-col">13:45 – 14:00</td>
                  <td class="paper-cell"><span class="paper-badge">88</span></td>
                  <td class="paper-cell"><span class="paper-badge">7</span></td>
                </tr>
                <tr class="keynote-row">
                  <td class="time-col">14:30 – 15:30</td>
                  <td colspan="2" class="keynote-cell">
                    <span class="keynote-badge">Keynote 6</span>
                    <div class="keynote-speaker">Prof. Harpal Singh, <span class="keynote-org">Arctic University of Norway (UiT), Tromsø, Norway</span></div>
                    <div class="keynote-topic"><strong>Topic:</strong> SHM of Real-Life Infrastructure Using Operational Modal Analysis</div>
                  </td>
                </tr>
                <tr>
                  <td class="time-col">15:30 – 17:00</td>
                  <td colspan="2">
                    <strong>Valedictory Function</strong>
                    <span style="color: var(--color-muted); margin-left: 0.35rem;">(best paper awards, thanks note)</span>
                  </td>
                </tr>
                <tr class="break-row">
                  <td class="time-col">17:00 – 17:20</td>
                  <td colspan="2" class="break-cell"><span class="break-tag">☕ Tea and Departure</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php require dirname(__FILE__) . '/includes/cta-bar.php'; ?>

  <!-- Footer -->
  <?php require dirname(__FILE__) . '/includes/footer.php'; ?>
</body>

</html>