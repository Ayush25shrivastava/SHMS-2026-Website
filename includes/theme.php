<?php
/**
 * Light / dark theme via cookie (no JavaScript).
 * Toggle: GET ?shms_theme=dark|light sets cookie and redirects back (query stripped).
 */

if (!function_exists('shms_theme')) {
    /**
     * @return string 'light'|'dark'
     */
    function shms_theme()
    {
        if (isset($_COOKIE['shms_theme']) && $_COOKIE['shms_theme'] === 'dark') {
            return 'dark';
        }
        return 'light';
    }

    /**
     * @return void
     */
    function shms_theme_handle_request()
    {
        if (!isset($_GET['shms_theme'])) {
            return;
        }
        $sn = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        if (strpos($sn, '/api/') !== false) {
            return;
        }
        $v = $_GET['shms_theme'];
        if ($v !== 'dark' && $v !== 'light') {
            return;
        }
        if (headers_sent()) {
            return;
        }
        $exp = time() + 365 * 24 * 3600;
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $base = function_exists('shms_web_base') ? shms_web_base() : '';
        $cpath = ($base === '') ? '/' : ($base . '/');
        setcookie('shms_theme', $v, $exp, $cpath, '', $secure, true);

        $qs = array();
        if (isset($_GET) && is_array($_GET)) {
            foreach ($_GET as $k => $val) {
                if ($k === 'shms_theme') {
                    continue;
                }
                $qs[$k] = $val;
            }
        }
        $tail = '';
        if (count($qs) > 0) {
            $tail = '?' . http_build_query($qs, '', '&');
        }
        $self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '/index.php';
        header('Location: ' . $self . $tail, true, 302);
        exit;
    }

    /**
     * Relative URL: toggles theme, preserves other query params (hash not preserved — server limitation).
     *
     * @return string
     */
    function shms_theme_toggle_href()
    {
        $next = shms_theme() === 'dark' ? 'light' : 'dark';
        $qs = array();
        if (isset($_GET) && is_array($_GET)) {
            foreach ($_GET as $k => $v) {
                if ($k === 'shms_theme') {
                    continue;
                }
                $qs[$k] = $v;
            }
        }
        $qs['shms_theme'] = $next;
        return '?' . http_build_query($qs, '', '&');
    }

    /**
     * For <html class="..."> — theme class only. Put id="top" on <body> so #top scrolls the document reliably in all engines.
     *
     * @return string
     */
    function shms_html_theme_class()
    {
        $c = shms_theme() === 'dark' ? 'theme-dark' : 'theme-light';
        return ' class="' . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . '"';
    }

    /**
     * Optional: meta theme-color for browser UI.
     *
     * @return string
     */
    function shms_theme_color_meta_content()
    {
        return shms_theme() === 'dark' ? '#020617' : '#0f172a';
    }
}

shms_theme_handle_request();
