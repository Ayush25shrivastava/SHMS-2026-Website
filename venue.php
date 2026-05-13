<?php
require_once dirname(__FILE__) . '/includes/init.php';
require_once dirname(__FILE__) . '/includes/shms-page.php';
$SHMS = shms_page_data();
$shmsNavPage = 'venue';

// OpenStreetMap embed — MNNIT Allahabad (coordinates match directions link below)
$shmsVenueMapLat = 25.49161;
$shmsVenueMapLon = 81.86327;
$shmsVenueMapBbox = sprintf(
    '%.5f,%.5f,%.5f,%.5f',
    $shmsVenueMapLon - 0.011,
    $shmsVenueMapLat - 0.007,
    $shmsVenueMapLon + 0.011,
    $shmsVenueMapLat + 0.007
);
$shmsVenueOsmEmbedUrl = 'https://www.openstreetmap.org/export/embed.html?bbox='
    . rawurlencode($shmsVenueMapBbox)
    . '&layer=mapnik&marker='
    . rawurlencode($shmsVenueMapLat . ',' . $shmsVenueMapLon);
$shmsVenueOsmLargerUrl = sprintf(
    'https://www.openstreetmap.org/?mlat=%.5f&mlon=%.5f&zoom=16',
    $shmsVenueMapLat,
    $shmsVenueMapLon
);
?>
<!DOCTYPE html>
<html lang="en"<?php echo shms_html_theme_class(); ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Venue & Accommodation | SHMS‑2026</title>
  <link rel="stylesheet" href="style.css">
<?php echo shms_head_resource_hints(); ?>
</head>
<body id="top">
<?php require dirname(__FILE__) . '/includes/nav.php'; ?>

  <main>
  <?php require dirname(__FILE__) . '/includes/hero-banner.php'; ?>

  <section class="page-heading" id="page-content">
    <h1>Venue &amp; Accommodation</h1>
    <p>Everything You Need to Know About Getting to and Staying in Prayagraj.</p>
  </section>

  <section id="venue">
    <h2>Conference Venue</h2>
    <p>
      The conference sessions will be held at the Seminar Hall / Lecture Hall Complex of
      Motilal Nehru National Institute of Technology (MNNIT) Allahabad, located at Teliyarganj,
      Prayagraj – 211004, Uttar Pradesh, India. The modern campus provides comfortable
      lecture halls, meeting rooms and exhibition spaces.
    </p>
    <figure class="venue-map" aria-label="Interactive map: MNNIT Allahabad on OpenStreetMap">
      <div class="venue-map-iframe-wrap">
        <iframe
          class="venue-map-frame"
          title="OpenStreetMap — MNNIT Allahabad, Teliyarganj, Prayagraj"
          src="<?php echo htmlspecialchars($shmsVenueOsmEmbedUrl, ENT_QUOTES, 'UTF-8'); ?>"
          width="800"
          height="480"
          loading="lazy"
          referrerpolicy="strict-origin-when-cross-origin"
          allowfullscreen></iframe>
      </div>
      <noscript>
        <picture>
          <source srcset="assets/images/venue-map-mnnit.png" type="image/png">
          <img
            src="assets/images/venue-map-mnnit.svg"
            alt="Map showing MNNIT Allahabad (static image)."
            loading="lazy"
            decoding="async"
            width="1600"
            height="900">
        </picture>
      </noscript>
      <figcaption class="venue-map-caption">
        <a href="<?php echo htmlspecialchars($shmsVenueOsmLargerUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">Larger map on OpenStreetMap</a>
        <span aria-hidden="true"> · </span>
        © <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap contributors</a>.
        Use the directions links below for routing.
      </figcaption>
    </figure>
    <p>
      For step‑by‑step directions, open the venue in
      <a href="https://www.openstreetmap.org/directions?to=25.49161%2C81.86327" target="_blank" rel="noopener noreferrer" style="color:#004a8f;">
        OpenStreetMap
      </a>
      <span aria-hidden="true">·</span>
      <a href="https://www.google.com/maps/dir/?api=1&destination=Motilal+Nehru+National+Institute+of+Technology+Allahabad" target="_blank" rel="noopener noreferrer" style="color:#004a8f;">
        Google Maps
      </a>.
    </p>
  </section>

  <section id="city">
    <h2>About Prayagraj</h2>
    <p class="city-lead">
      <strong>Intelligent monitoring meets living heritage.</strong> Prayagraj offers the right setting for SHMS‑2026:
      a strong academic ecosystem, excellent connectivity, and a culturally rich urban landscape.
    </p>
    <p>
      Prayagraj (formerly Allahabad) lies at the sacred confluence of the Ganges, Yamuna and mythical Saraswati rivers,
      known as the Triveni Sangam. One of India’s most historically and spiritually significant cities, it is home
      to several premier educational institutions including MNNIT, the University of Allahabad and the High Court of
      Judicature. The city offers a unique blend of academic excellence and cultural heritage. October in Prayagraj
      is pleasant, with temperatures ranging between 22 °C and 33 °C.
    </p>
    <p>
      Ancient texts refer to the region as <em>Prayag</em>—a place of pilgrimage and learning. Today, visitors can
      explore the Sangam ghats, the historic <strong>Allahabad Fort</strong>, <strong>Anand Bhavan</strong> (associated
      with India’s freedom movement), and vibrant riverfront life. The city is well connected by rail and air, and
      offers local cuisine, markets and evening strolls along the Yamuna—ideal for extending your stay before or after
      the conference.
    </p>

    <div class="city-highlights" aria-label="Prayagraj at a glance">
      <span class="city-chip">Triveni Sangam</span>
      <span class="city-chip">Allahabad Fort</span>
      <span class="city-chip">Anand Bhavan</span>
      <span class="city-chip">Academic Hub</span>
      <span class="city-chip">Pleasant October Weather</span>
    </div>

    <div class="city-gallery" aria-label="Prayagraj highlights">
      <div class="city-gallery-header">
        <h3>Prayagraj glimpse</h3>
        <p>Scroll through iconic views from the host city.</p>
      </div>
      <p class="carousel-scroll-hint">The slideshow rotates automatically. You can also use the arrows to see more.</p>
      <div class="city-gallery-carousel" id="venue-slideshow" role="region" aria-roledescription="carousel" aria-label="Prayagraj images">
        <!-- Radio states for 9 slides -->
        <input class="carousel-state" type="radio" name="pr-city-slide" id="pr-city-s1" checked>
        <input class="carousel-state" type="radio" name="pr-city-slide" id="pr-city-s2">
        <input class="carousel-state" type="radio" name="pr-city-slide" id="pr-city-s3">
        <input class="carousel-state" type="radio" name="pr-city-slide" id="pr-city-s4">
        <input class="carousel-state" type="radio" name="pr-city-slide" id="pr-city-s5">
        <input class="carousel-state" type="radio" name="pr-city-slide" id="pr-city-s6">
        <input class="carousel-state" type="radio" name="pr-city-slide" id="pr-city-s7">
        <input class="carousel-state" type="radio" name="pr-city-slide" id="pr-city-s8">
        <input class="carousel-state" type="radio" name="pr-city-slide" id="pr-city-s9">

        <!-- Previous Buttons -->
        <div class="carousel-btn-slot carousel-btn-slot--prev">
          <label class="carousel-btn" for="pr-city-s9"><span class="carousel-chevron carousel-chevron--prev"></span></label>
          <label class="carousel-btn" for="pr-city-s1"><span class="carousel-chevron carousel-chevron--prev"></span></label>
          <label class="carousel-btn" for="pr-city-s2"><span class="carousel-chevron carousel-chevron--prev"></span></label>
          <label class="carousel-btn" for="pr-city-s3"><span class="carousel-chevron carousel-chevron--prev"></span></label>
          <label class="carousel-btn" for="pr-city-s4"><span class="carousel-chevron carousel-chevron--prev"></span></label>
          <label class="carousel-btn" for="pr-city-s5"><span class="carousel-chevron carousel-chevron--prev"></span></label>
          <label class="carousel-btn" for="pr-city-s6"><span class="carousel-chevron carousel-chevron--prev"></span></label>
          <label class="carousel-btn" for="pr-city-s7"><span class="carousel-chevron carousel-chevron--prev"></span></label>
          <label class="carousel-btn" for="pr-city-s8"><span class="carousel-chevron carousel-chevron--prev"></span></label>
        </div>

        <div class="carousel-viewport" tabindex="0">
          <div class="carousel-track">
            <figure class="carousel-slide"><img src="assets/scroll/image1.jpg" alt="Prayagraj 1" loading="lazy"></figure>
            <figure class="carousel-slide"><img src="assets/scroll/image2.jpg" alt="Prayagraj 2" loading="lazy"></figure>
            <figure class="carousel-slide"><img src="assets/scroll/image3.jpg" alt="Prayagraj 3" loading="lazy"></figure>
            <figure class="carousel-slide"><img src="assets/scroll/image4.jpeg" alt="Prayagraj 4" loading="lazy"></figure>
            <figure class="carousel-slide"><img src="assets/scroll/image6.jpg" alt="Prayagraj 6" loading="lazy"></figure>
            <figure class="carousel-slide"><img src="assets/scroll/image7.jpg" alt="Prayagraj 7" loading="lazy"></figure>
            <figure class="carousel-slide"><img src="assets/scroll/image8.png" alt="Prayagraj 8" loading="lazy"></figure>
            <figure class="carousel-slide"><img src="assets/scroll/image9.jpeg" alt="Prayagraj 9" loading="lazy"></figure>
            <figure class="carousel-slide"><img src="assets/scroll/image10.jpg" alt="Prayagraj 10" loading="lazy"></figure>
          </div>
        </div>

        <!-- Next Buttons -->
        <div class="carousel-btn-slot carousel-btn-slot--next">
          <label class="carousel-btn" for="pr-city-s2"><span class="carousel-chevron carousel-chevron--next"></span></label>
          <label class="carousel-btn" for="pr-city-s3"><span class="carousel-chevron carousel-chevron--next"></span></label>
          <label class="carousel-btn" for="pr-city-s4"><span class="carousel-chevron carousel-chevron--next"></span></label>
          <label class="carousel-btn" for="pr-city-s5"><span class="carousel-chevron carousel-chevron--next"></span></label>
          <label class="carousel-btn" for="pr-city-s6"><span class="carousel-chevron carousel-chevron--next"></span></label>
          <label class="carousel-btn" for="pr-city-s7"><span class="carousel-chevron carousel-chevron--next"></span></label>
          <label class="carousel-btn" for="pr-city-s8"><span class="carousel-chevron carousel-chevron--next"></span></label>
          <label class="carousel-btn" for="pr-city-s9"><span class="carousel-chevron carousel-chevron--next"></span></label>
          <label class="carousel-btn" for="pr-city-s1"><span class="carousel-chevron carousel-chevron--next"></span></label>
        </div>
      </div>
    </div>

    <script>
      (function() {
        const carousel = document.getElementById('venue-slideshow');
        if (!carousel) return;
        const radios = Array.from(carousel.querySelectorAll('input[type="radio"]'));
        let currentIndex = 0;
        let intervalId = null;

        function startAutoSlide() {
          intervalId = setInterval(() => {
            currentIndex = (currentIndex + 1) % radios.length;
            radios[currentIndex].checked = true;
          }, 2000);
        }

        function stopAutoSlide() {
          if (intervalId) clearInterval(intervalId);
        }

        // Initialize
        startAutoSlide();

        // Pause on interaction
        carousel.addEventListener('mouseenter', stopAutoSlide);
        carousel.addEventListener('mouseleave', startAutoSlide);
        
        // Update index if user clicks manually
        radios.forEach((radio, idx) => {
          radio.addEventListener('change', () => {
            if (radio.checked) currentIndex = idx;
          });
        });
      })();
    </script>
  </section>

  <section id="accommodation">
    <h2>Accommodation Options</h2>
    <p>Conference delegates may choose from a range of options to suit different budgets:</p>
    <ul>
      <li><strong>Executive Development Centre (EDC), MNNIT Allahabad:</strong> Limited rooms available on campus; advance booking required.</li>
      <li><strong>Recommended Hotels:</strong> Hotel Kanha Shyam, The Legend Hotel, Hotel Yatrik.</li>
      <li><strong>Budget Options:</strong> OYO Hotels, Hotel Milan Palace, Hotel Presidency.</li>
    </ul>
    <p>Special conference rates will be available. Details will be announced on the registration portal.</p>
  </section>

  <section id="travel">
    <h2>Travel Information</h2>
    <p>
      <strong>By Air:</strong> Prayagraj Airport (IXD) is 12 km from the venue. Varanasi Airport (VNS) is approximately 120 km away.
    </p>
    <p>
      <strong>By Rail:</strong> Prayagraj Junction (PRYJ) is 8 km and Prayagraj Rambag (PRRB) is 5 km from campus.
    </p>
    <p>
      <strong>Weather:</strong> The weather in October is mild and pleasant, ideal for sightseeing and outdoor activities.
    </p>
  </section>
  </main>

<?php require dirname(__FILE__) . '/includes/cta-bar.php'; ?>

<?php require dirname(__FILE__) . '/includes/footer.php'; ?>

</body>
</html>
