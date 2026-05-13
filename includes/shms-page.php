<?php
/**
 * Per-request data: countdown (server-rendered; no client-side JavaScript).
 *
 * Conference window: start 2026-10-15 09:00 +05:30, end 2026-10-17 18:00 +05:30.
 * Countdown = time remaining until opening (inaugural) in Asia/Kolkata (IST, no DST).
 */

/**
 * Build countdown fields from non-negative whole seconds until start.
 *
 * @param int $diffSeconds
 * @return array
 */
function shms_countdown_from_seconds($diffSeconds)
{
    $diff = max(0, (int) $diffSeconds);
    $days = (int) floor($diff / 86400);
    $r = $diff % 86400;
    $H = (int) floor($r / 3600);
    $r = $r % 3600;
    $M = (int) floor($r / 60);
    $S = $r % 60;

    return array(
        'countdown_days' => $days,
        'countdown_hours' => str_pad((string) $H, 2, '0', STR_PAD_LEFT),
        'countdown_minutes' => str_pad((string) $M, 2, '0', STR_PAD_LEFT),
        'countdown_seconds' => str_pad((string) $S, 2, '0', STR_PAD_LEFT),
    );
}

/**
 * @return array
 */
function shms_page_data()
{
    // Visitor tally in the CTA is Flag Counter only (see includes/flag-counter.php); no local file counter.
    $visitorDisplay = '';

    // Wall-clock times below are interpreted in Asia/Kolkata (IST).
    // DateTime two-arg constructor is PHP 5.3+; use default TZ swap for PHP 5.2.
    $tzName = 'Asia/Kolkata';
    $openStr = '2026-10-15 09:00:00';
    $closeStr = '2026-10-17 18:00:00';

    $oldTz = @date_default_timezone_get();
    if ($oldTz === '' || $oldTz === false) {
        $oldTz = 'UTC';
    }
    date_default_timezone_set($tzName);
    $now = new DateTime('now');
    $open = new DateTime($openStr);
    $close = new DateTime($closeStr);
    date_default_timezone_set($oldTz);

    $tsNow = $now->getTimestamp();
    $tsOpen = $open->getTimestamp();
    $tsClose = $close->getTimestamp();

    $base = array(
        'visitor_display' => $visitorDisplay,
        'countdown_subtitle' => '',
        'countdown_message' => '',
    );

    if ($tsNow >= $tsClose) {
        $base['countdown_subtitle'] = 'Conference ended 17 Oct 2026, 6:00 PM IST.';
        return array_merge($base, array(
            'countdown_days' => 0,
            'countdown_hours' => '00',
            'countdown_minutes' => '00',
            'countdown_seconds' => '00',
            'countdown_message' => 'SHMS‑2026 has concluded. Thank you for visiting.',
        ));
    }

    if ($tsNow >= $tsOpen) {
        $base['countdown_subtitle'] = 'Conference in progress · closes 17 Oct 2026, 6:00 PM IST.';
        return array_merge($base, array(
            'countdown_days' => 0,
            'countdown_hours' => '00',
            'countdown_minutes' => '00',
            'countdown_seconds' => '00',
            'countdown_message' => 'The conference is now in progress. Welcome to SHMS‑2026!',
        ));
    }

    $base['countdown_subtitle'] = 'Time until inaugural · 15 Oct 2026, 9:00 AM IST (Asia/Kolkata).';
    $cd = shms_countdown_from_seconds($tsOpen - $tsNow);

    return array_merge($base, $cd, array('countdown_message' => ''));
}
