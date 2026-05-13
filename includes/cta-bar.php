<?php
/** @var array $SHMS from shms_page_data() */
/** @var string $shmsCtaVariant optional: 'full' | 'registration' */
if (!isset($SHMS) || !is_array($SHMS)) {
    $SHMS = shms_page_data();
}
$variant = isset($shmsCtaVariant) ? $shmsCtaVariant : 'full';

$d = (int) $SHMS['countdown_days'];
$h = htmlspecialchars($SHMS['countdown_hours'], ENT_QUOTES, 'UTF-8');
$m = htmlspecialchars($SHMS['countdown_minutes'], ENT_QUOTES, 'UTF-8');
$s = htmlspecialchars($SHMS['countdown_seconds'], ENT_QUOTES, 'UTF-8');
$msg = htmlspecialchars($SHMS['countdown_message'], ENT_QUOTES, 'UTF-8');
$sub = '';
if (isset($SHMS['countdown_subtitle']) && $SHMS['countdown_subtitle'] !== '') {
    $sub = htmlspecialchars($SHMS['countdown_subtitle'], ENT_QUOTES, 'UTF-8');
}

require_once dirname(__FILE__) . '/flag-counter.php';
$shmsFlagCounterHtml = shms_flagcounter_embed_html();
?>
  <aside class="cta-bar<?php echo $variant === 'registration' ? ' cta-bar--registration' : ''; ?>" aria-label="<?php echo $variant === 'registration' ? 'Conference countdown and visitors map' : 'Conference countdown, visitors map, and quick actions'; ?>">
    <div class="cta-bar-inner cta-bar-layout<?php echo $variant === 'registration' ? ' cta-bar-layout--registration' : ''; ?>">
      <div class="cta-bar-col cta-bar-col--countdown" id="countdown">
        <span class="cta-bar-col-label">Countdown</span>
        <?php if ($sub !== '') : ?>
        <p class="countdown-target-note"><?php echo $sub; ?></p>
        <?php endif; ?>
        <div class="countdown-timer countdown-timer--cta" aria-label="Countdown to inaugural 15 October 2026 9:00 AM India time">
          <div class="countdown-unit">
            <span><?php echo $d; ?></span>
            <span class="label">Days</span>
          </div>
          <div class="countdown-unit">
            <span><?php echo $h; ?></span>
            <span class="label">Hrs</span>
          </div>
          <div class="countdown-unit">
            <span><?php echo $m; ?></span>
            <span class="label">Min</span>
          </div>
          <div class="countdown-unit">
            <span><?php echo $s; ?></span>
            <span class="label">Sec</span>
          </div>
        </div>
        <p id="countdown-message" class="countdown-message countdown-message--cta" aria-live="polite"><?php echo $msg !== '' ? $msg : ''; ?></p>
      </div>
<?php if ($variant === 'full') : ?>
      <div class="cta-bar-col cta-bar-col--actions">
        <a class="cta-btn cta-btn--primary" href="registration.php#page-content">Register Now</a>
        <a class="cta-btn cta-btn--secondary" href="call.php#page-content">Submit Paper</a>
      </div>
<?php endif; ?>
<?php if ($shmsFlagCounterHtml !== '') : ?>
      <div class="cta-bar-col cta-bar-col--visitors" id="visitors">
        <span class="cta-bar-col-label">Visitors</span>
        <div class="cta-visitor-row">
          <div class="flag-counter-wrap" aria-label="Visitor map">
            <?php echo $shmsFlagCounterHtml; ?>
          </div>
        </div>
      </div>
<?php endif; ?>
    </div>
  </aside>
