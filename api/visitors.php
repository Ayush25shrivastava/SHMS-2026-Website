<?php
/**
 * Visitor counter API — Flag Counter configuration (image URL from s01.flagcounter.com + stats link + counter id).
 */
require_once dirname(dirname(__FILE__)) . '/includes/init.php';
require_once dirname(dirname(__FILE__)) . '/includes/flag-counter.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
// Short cache: config rarely changes; repeat API clients get a snappy response.
header('Cache-Control: private, max-age=120');

$s = shms_flagcounter_settings();
$meta = shms_flagcounter_instant_meta($s);

$payload = array(
    'source' => 'flagcounter',
    'enabled' => (bool) $s['show'],
    'img_src' => $s['img_src'],
    'stats_href' => $s['stats_href'],
    'instant' => array(
        'counter_id' => $meta['counter_id'],
        'map_href' => $meta['map_href'],
    ),
);

if ($meta['counter_id'] !== '') {
    $payload['counter_id'] = $meta['counter_id'];
}

http_response_code(200);
echo shms_json_encode($payload);
