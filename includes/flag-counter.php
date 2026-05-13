<?php
/**
 * Optional [Flag Counter](https://s01.flagcounter.com/) embed — badge image is always the configured
 * HTTPS URL on s01.flagcounter.com (count / count2 / …), plus optional stats link (no JavaScript).
 *
 * Configure via includes/config.php `flagcounter` (see config.sample.php) or:
 *   SHMS_FLAGCOUNTER_IMG_SRC   — required HTTPS image URL from Flag Counter’s generator
 *   SHMS_FLAGCOUNTER_STATS_HREF — optional link to your counter’s stats page
 */

/**
 * Extract the Flag Counter account segment from an image URL (e.g. MjIp from …/count2/MjIp/…).
 *
 * @param string $img_src normalized HTTPS image URL
 * @return string empty if not recognised
 */
function shms_flagcounter_counter_id_from_img_src($img_src)
{
    if (!is_string($img_src) || $img_src === '') {
        return '';
    }
    if (preg_match('#s01\.flagcounter\.com/(?:count|count2|countxl)/([^/]+)#i', $img_src, $m)) {
        return $m[1];
    }
    if (preg_match('#s01\.flagcounter\.com/more/([^/?#]+)#i', $img_src, $m)) {
        return $m[1];
    }
    if (preg_match('#info\.flagcounter\.com/([^/?#]+)#i', $img_src, $m)) {
        return $m[1];
    }
    return '';
}

/**
 * Default Flag Counter “stamp” image URL (count2) for a counter id — same style as Flag Counter’s HTML generator.
 *
 * @param string $counter_id
 * @return string
 */
function shms_flagcounter_default_count2_url($counter_id)
{
    if (!is_string($counter_id)) {
        return '';
    }
    $id = preg_replace('/[^A-Za-z0-9_-]+/', '', $counter_id);
    if ($id === '') {
        return '';
    }
    return 'https://s01.flagcounter.com/count2/' . $id . '/bg_FFFFFF/txt_000000/border_CCCCCC/columns_2/maxflags_10/viewers_0/labels_0/pageviews_0/flags_0/percent_0/';
}

/**
 * Turn a “more” or info URL (or bare counter id) into the HTTPS image URL the badge must use.
 *
 * @param string $img_src raw or empty
 * @param string $config_counter_id optional counter id from config when img_src is empty
 * @return string normalized HTTPS count2 URL or empty
 */
function shms_flagcounter_resolve_img_src($img_src, $config_counter_id)
{
    $img_src = is_string($img_src) ? trim($img_src) : '';
    $config_counter_id = is_string($config_counter_id) ? trim($config_counter_id) : '';

    if ($img_src !== '' && preg_match('#^https?://s01\.flagcounter\.com/(count|count2|countxl)/#i', $img_src)) {
        return shms_flagcounter_normalize_img_src($img_src);
    }
    if ($img_src !== '' && preg_match('#https?://s01\.flagcounter\.com/more/([^/?#]+)#i', $img_src, $m)) {
        return shms_flagcounter_default_count2_url($m[1]);
    }
    if ($img_src !== '' && preg_match('#https?://info\.flagcounter\.com/([^/?#]+)#i', $img_src, $m)) {
        return shms_flagcounter_default_count2_url($m[1]);
    }
    if ($img_src === '' && $config_counter_id !== '') {
        return shms_flagcounter_default_count2_url($config_counter_id);
    }
    return shms_flagcounter_normalize_img_src($img_src);
}

/**
 * Accept only the Flag Counter image URL (https://s01.flagcounter.com/...).
 * If HTML was pasted by mistake, pull out the first s01.flagcounter.com URL.
 *
 * @param string $raw
 * @return string
 */
function shms_flagcounter_normalize_img_src($raw)
{
    if (!is_string($raw)) {
        return '';
    }
    $raw = trim($raw);
    if ($raw === '') {
        return '';
    }
    if (preg_match('#https?://s01\.flagcounter\.com/[^\s"\'<>]+#i', $raw, $m)) {
        return $m[0];
    }
    return $raw;
}

/**
 * @return array
 */
function shms_flagcounter_settings()
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $cfg = shms_app_config();

    // Local dev: SHMS_FLAGCOUNTER_IMG_SRC in the shell overrides config "enabled" => false
    // (common when config.php is copied from sample with flagcounter disabled).
    $envImgForce = getenv('SHMS_FLAGCOUNTER_IMG_SRC');
    $envImgForce = ($envImgForce !== false && trim($envImgForce) !== '') ? true : false;

    if (is_array($cfg) && isset($cfg['flagcounter']) && is_array($cfg['flagcounter'])) {
        $fc = $cfg['flagcounter'];
        if (isset($fc['enabled']) && $fc['enabled'] === false && !$envImgForce) {
            $cached = array('show' => false, 'img_src' => '', 'stats_href' => '');
            return $cached;
        }
    }

    $img = '';
    $href = '';
    $counterIdCfg = '';

    if (is_array($cfg) && isset($cfg['flagcounter']) && is_array($cfg['flagcounter'])) {
        $fc = $cfg['flagcounter'];
        if (isset($fc['img_src']) && is_string($fc['img_src'])) {
            $img = trim($fc['img_src']);
        }
        if (isset($fc['stats_href']) && is_string($fc['stats_href'])) {
            $href = trim($fc['stats_href']);
        }
        if (isset($fc['counter_id']) && is_string($fc['counter_id'])) {
            $counterIdCfg = trim($fc['counter_id']);
        }
    }

    $envImg = getenv('SHMS_FLAGCOUNTER_IMG_SRC');
    if ($envImg !== false && $envImg !== '') {
        $img = trim($envImg);
    }
    $envHref = getenv('SHMS_FLAGCOUNTER_STATS_HREF');
    if ($envHref !== false && $envHref !== '') {
        $href = trim($envHref);
    }

    $img = shms_flagcounter_resolve_img_src($img, $counterIdCfg);
    if ($img === '' && $href !== '') {
        $img = shms_flagcounter_resolve_img_src($href, $counterIdCfg);
    }

    $show = ($img !== '' && (stripos($img, 'https://') === 0 || stripos($img, '//') === 0));
    if ($show && stripos($img, '//') === 0) {
        $img = 'https:' . $img;
    }

    $cached = array(
        'show' => $show,
        'img_src' => $show ? $img : '',
        'stats_href' => $href,
    );
    return $cached;
}

/**
 * Counter id and best URL for a human “map / stats” link (same logic for CTA strip and api/visitors.php).
 *
 * @param array $s shms_flagcounter_settings() row
 * @return array counter_id (string), map_href (string)
 */
function shms_flagcounter_instant_meta($s)
{
    if (!is_array($s)) {
        return array('counter_id' => '', 'map_href' => '');
    }
    $imgSrc = isset($s['img_src']) && is_string($s['img_src']) ? $s['img_src'] : '';
    $stats = isset($s['stats_href']) && is_string($s['stats_href']) ? trim($s['stats_href']) : '';
    $id = shms_flagcounter_counter_id_from_img_src($imgSrc);
    if ($id === '' && $stats !== '') {
        $id = shms_flagcounter_counter_id_from_img_src($stats);
    }
    $mapHref = $stats !== '' ? $stats : $imgSrc;
    return array('counter_id' => $id, 'map_href' => $mapHref);
}

/**
 * @return string HTML fragment or empty string.
 */
function shms_flagcounter_embed_html()
{
    $s = shms_flagcounter_settings();
    if (!$s['show']) {
        return '';
    }

    $srcEsc = htmlspecialchars($s['img_src'], ENT_QUOTES, 'UTF-8');
    $alt = htmlspecialchars('Visitor map (Flag Counter)', ENT_QUOTES, 'UTF-8');
    $imgTag = '<img class="flag-counter-img" src="' . $srcEsc . '" alt="' . $alt . '" width="88" height="31" loading="eager" fetchpriority="high" decoding="async" referrerpolicy="no-referrer-when-downgrade">';

    $embed = '<div class="flag-counter-embed">' . $imgTag . '</div>';

    $stats = isset($s['stats_href']) && is_string($s['stats_href']) ? trim($s['stats_href']) : '';
    if ($stats !== '') {
        $h = htmlspecialchars($stats, ENT_QUOTES, 'UTF-8');
        $aTitle = htmlspecialchars('Visitor map (opens in new tab)', ENT_QUOTES, 'UTF-8');
        return '<a class="flag-counter-badge" href="' . $h . '" target="_blank" rel="noopener noreferrer" title="' . $aTitle . '">' . $embed . '</a>';
    }

    return $embed;
}
