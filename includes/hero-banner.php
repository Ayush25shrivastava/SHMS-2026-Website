<?php
/**
 * Full-width hero: CSS-only horizontal slide carousel (no JavaScript).
 * Fifth image duplicates the first so the infinite loop resets without a visible jump.
 */
?>
  <div class="hero hero--banner" role="img" aria-label="MNNIT Allahabad campus views with SHMS‑2026 conference branding overlay">
    <div class="hero-banner-slides" aria-hidden="true">
      <div class="hero-banner-track">
        <img src="assets/images/hero_banner/banner_img1.jpg" alt="" class="hero-banner-slide" width="1920" height="1080" fetchpriority="high" decoding="async">
        <img src="assets/images/hero_banner/banner_img2.jpeg" alt="" class="hero-banner-slide" width="1920" height="1080" loading="eager" decoding="async">
        <img src="assets/images/hero_banner/banner_img3.png" alt="" class="hero-banner-slide" width="1920" height="1080" loading="eager" decoding="async">
        <img src="assets/images/hero_banner/banner_img4.jpg" alt="" class="hero-banner-slide" width="1920" height="1080" loading="eager" decoding="async">
        <img src="assets/images/hero_banner/banner_img1.jpg" alt="" class="hero-banner-slide hero-banner-slide--loop" width="1920" height="1080" loading="lazy" decoding="async">
      </div>
    </div>
    <div class="hero-banner-overlay" aria-hidden="true">
      <img src="assets/images/hero_banner/overlay.apng" alt="" class="hero-banner-overlay-img">
    </div>
    <div class="hero-banner-dots" role="presentation" aria-hidden="true">
      <span class="hero-banner-dot"></span>
      <span class="hero-banner-dot"></span>
      <span class="hero-banner-dot"></span>
      <span class="hero-banner-dot"></span>
    </div>
  </div>
