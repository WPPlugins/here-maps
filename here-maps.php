<?php
/*
  Plugin Name: HERE Maps
  Plugin URI: http://wordpress.org/extend/plugins/here-maps/
  Description: With this plugin you are able to add a places and addresses into a post or a page.
  Version: 1.2.6
  Author: HERE
  Author URI: http://here.com
  License: BSD License
 */

include_once(dirname(__FILE__) . '/pages/map_button.php');

/**
 * Extend query params from request.
 *
 * @param $query_vars
 * @return array
 */
function here_maps_query_vars($query_vars)
{
  if (false === in_array('plugin', $query_vars)) {
    $query_vars[] = 'plugin';
  }

  if (false === in_array('action', $query_vars)) {
    $query_vars[] = 'action';
  }

  return $query_vars;
}

/**
 * Parse query params and display page.
 *
 * @param $wp
 */
function here_maps_parse_request(&$wp)
{
  $vars = $wp->query_vars;

  if (false === array_key_exists('plugin', $vars)) {
    return;
  }

  if (false === array_key_exists('action', $vars)) {
    return;
  }

  if (HereMapsCore::$pluginName !== strtolower($vars['plugin'])) {
    return;
  }

  $action = strtolower($vars['action']);

  if ('gui' === $action) {
    include(__DIR__ . '/pages/add_map_gui.php');
    exit();
  }

  if ('showmap' === $action) {
    include(__DIR__ . '/pages/show_map.php');
    exit();
  }

  return;
}

/**
 * Allows iframe in editor
 *
 * @param $initArray
 * @return array
 */
function here_maps_add_iframe($initArray)
{
  $initArray['extended_valid_elements'] = "iframe[id|frameborder|height|scrolling|src|width]";
  return $initArray;
}

/**
 * Register Here Maps shortcode and the way extracting it.
 * Supported tags:
 *   - [here-maps ...]
 *   - [nokia-maps ...]
 *
 * @package HERE Maps
 * @param array $atts
 * @param string $c
 * @return string
 */
function here_maps_shortcode($atts, $c)
{
  $params = array(
    'center' => null,
    'zoom' => null,
    'height' => null,
    'width' => null,
    'map_mode' => null,
    'hidden' => null,
    'template' => null,
    'placeid' => null,
    'sizes' => null,
    'theme' => null,
    'contour_color' => null,
    'contour_opacity' => null,
    'contour' => null,
    'places' => array(),
    'title' => null,
  );

  // Places
  foreach ($atts as $name => $value) {
    if (false == preg_match('/^(place|place_?\d+)$/i', $name)) {
      continue;
    }

    $params['places'][] = $value;
    unset($atts[$name]);
  }

  // Customize map
  $params['center'] = (isset($atts['center'])) ? $atts['center'] : null;
  $params['zoom'] = (isset($atts['zoom'])) ? $atts['zoom'] : null;
  $params['hidden'] =(isset($atts['hidden'])) ? $atts['hidden'] : null;
  $params['template'] = (isset($atts['template'])) ? $atts['template'] : null;
  $params['theme'] = isset($atts['theme']) ? $atts['theme'] : null;
  $params['title'] = isset($atts['title']) ? $atts['title'] : null;

  // BC
  if (true === isset($atts['placeid'])) {
    $params['placeid'] = $atts['placeid'];

    if (true === isset($atts['title'])) {
      $params['title'] = $atts['title'];
    }

    if (true === isset($atts['zoomlevel'])) {
      $params['zoom'] = $atts['zoomlevel'];
    }

    if (true === isset($atts['sizes'])) {
      $sizes = json_decode(str_replace("'", '"', $atts['sizes']), true);

      if (null !== $sizes) {
        $atts['height'] = ('auto' === $sizes['height']) ? '100%' : $sizes['height'];
        $atts['width'] = ('auto' === $sizes['width']) ? '100%' : $sizes['width'];
      }

      if ('%' !== substr($atts['height'], -1)) {
        $atts['height'] .= 'px';
      }

      if ('%' !== substr($atts['width'], -1)) {
        $atts['width'] .= 'px';
      }
    }

    if (true === isset($atts['tiletype'])) {
      switch ($atts['tiletype']) {
        case 'terrain':
          $atts['map_mode'] = 'terrain.normal';
          break;
        case 'satellite':
          $atts['map_mode'] = 'satellite.normal';
          break;
        case 'map':
        default:
          $atts['map_mode'] = 'map.normal';
      }
    }
  }

  // Size of map + type
  $params['height'] = isset($atts['height']) ? $atts['height'] : null;
  $params['width'] = isset($atts['width']) ? $atts['width'] : null;
  $params['map_mode'] = isset($atts['map_mode']) ? $atts['map_mode'] : null;

  if (true === isset($atts['contour'])) {
    $params['contour_color'] = (isset($atts['contour_color'])) ? $atts['contour_color'] : null;
    $params['contour_opacity'] = (isset($atts['contour_opacity'])) ? $atts['contour_opacity'] : null;
    $params['contour'] = $atts['contour'];
  }

  return here_maps_create_post($params);
}

/**
 * Insert Here Map to post
 *
 * @package HERE Maps
 * @param array $params
 * @return string|null
 */
function here_maps_create_post($params)
{
  extract($params);
  $frame_id = md5(join('', $places));
  $count_places = 0;

  // URL attributes - iframe src attribute
  $url_attributes = array();

  foreach ($places as $item) {
    ++$count_places;
    $url_attributes[] = sprintf("place[]=%s", urlencode($item));
  }

  $templates = array('fixed', 'tooltip', 'empty');
  $themes = array('dark', 'light');

  if (false === in_array($template, $templates)) {
    $template = 'fixed';
  }

  if (false === in_array($theme, $themes)) {
    $theme = null;
  }

  if (null !== $center) $url_attributes[] = sprintf('center=%s', $center);
  if (null !== $zoom) $url_attributes[] = sprintf('zoom=%s', $zoom);

  if (null !== $map_mode) $url_attributes[] = sprintf('map_mode=%s', $map_mode);
  if (null !== $hidden) $url_attributes[] = sprintf('hidden=%s', $hidden);

  if (null !== $template) $url_attributes[] = sprintf('template=%s', $template);
  if (null !== $theme) $url_attributes[] = sprintf('theme=%s', $theme);

  // BC
  if (null !== $placeid) $url_attributes[] = sprintf('placeid=%s', $placeid);
  if (null !== $title) $url_attributes[] = sprintf('title=%s', $title);

  // Contour
  if (null !== $contour_color) $url_attributes[] = sprintf('contour_color=%s', str_replace('#', '', $contour_color));
  if (null !== $contour_opacity) $url_attributes[] = sprintf('contour_opacity=%s', $contour_opacity);

  if (null !== $contour) {
    $contour = explode('|', $contour);

    foreach ($contour as $row) {
      $url_attributes[] = sprintf('contour[]=%s', $row);
    }
  }

  $height = $height ? $height : 400;
  $width = $width ? $width : '100%';

  // Iframe attributes
  $attributes = array_filter(array(
    'id' => sprintf('here_map_id_%s', $frame_id),
    'frameborder' => 'no',
    'scrolling' => 'no',
    'height' => $height,
    'width' => $width,
    'src' => sprintf('%s/?plugin=here-maps&amp;action=showmap&amp;%s', get_option('siteurl'), join('&amp;', $url_attributes)),
  ));

  if (0 === $count_places && null === $center && null === $placeid) {
    return null;
  }

  $result = '<div class="here-maps-map-container" style="';

  if (true === isset($attributes['height']) && false === is_null($attributes['height'])) {
    $result .= sprintf('height:%s;', $attributes['height']);
  }

  if (true === isset($attributes['width']) && false === is_null($attributes['width'])) {
    $result .= sprintf('width:%s;', $attributes['width']);
  }

  $result .= '">';

  if (false == preg_match('/full_screen/i', $hidden)) {
    $result .= '<div class="here-maps-full-screen-mode" title="' . __('here-maps-full-screen-mode', 'here-maps') . '"><span class="glyphicon glyphicon-fullscreen"></span></div>';
  }

  $result .= '<iframe ';

  $attributes['width'] = '100%';
  $attributes['height'] = '100%';

  foreach (array_filter($attributes) as $name => $value) {
    $result .= sprintf('%s="%s" ', $name, $value);
  }

  $result .= '>Iframes not supported</iframe>';
  $result .= '</div>';
  return $result;
}

/**
 * When Admin turn on plugin
 *
 * @return void
 */
function here_maps_activation_hook()
{
  $config = include(dirname(__FILE__) . '/config.php');

  add_option('here_maps_app_id', $config['app_id']);
  add_option('here_maps_app_code', $config['app_code']);
}

/**
 * When Admin turn off plugin
 *
 * @return void
 */
function here_maps_deactivation_hook()
{
  delete_option('here_maps_app_id');
  delete_option('here_maps_app_code');
}

/**
 * Add custom tab to admin panel
 *
 * @retuvn void
 */
function here_maps_admin_page()
{
  add_menu_page(
    'Here Maps - Authorization Configuration',
    'Here Maps',
    'manage_options',
    'here-maps-config',
    'here_maps_plugin_options'
  );
}

/**
 * Page with settings Here Maps
 *
 * @return void
 */
function here_maps_plugin_options()
{
  include(dirname(__FILE__) . '/pages/admin_configuration.php');
}

/**
 * Function attach to file name last edit time. Its good practice for reload file in cache after changes.
 *
 * @param $fileName
 * @return string|void
 */
function here_maps_join_file($fileName)
{
  $file_path = sprintf('%sdist/%s', plugin_dir_url(__FILE__), $fileName);
  $date = date("YmdHis", filemtime(sprintf('%s/dist/%s', __DIR__, $fileName)));

  return sprintf('%s?%d', $file_path, $date);
}

/**
 * Detect user browser language
 */
function here_maps_detect_language()
{
  // Exists languages files
  $languages = array(
    'pl' => 'pl_PL',
    'en' => 'en_US',
  );

  $browser_locale = (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : '';

  if (true === array_key_exists($browser_locale, $languages)) {
    return $languages[$browser_locale];
  }

  return $languages['en'];
}

/**
 * Return dynamic path to language file.
 *
 * @param string $mofile
 * @param string $domain
 * @return string
 */
function here_maps_detect_browser_language_file($mofile, $domain)
{
  if (HereMapsCore::$pluginName !== $domain) {
    return $mofile;
  }

  return sprintf('%s/languages/here-maps-%s.mo', dirname(__FILE__), here_maps_detect_language());
}

function here_maps_enqueue_styles()
{
  wp_enqueue_style($file = 'dist/stylesheets/wordpress-page.min.css', plugins_url($file, __FILE__), array(), false);
}

function here_maps_enqueue_scripts()
{
  wp_enqueue_script('jquery');
  wp_enqueue_script($file = 'dist/javascripts/wordpress-page.min.js', plugins_url($file, __FILE__));
}

// Attach and parse variables from request
add_filter('query_vars', 'here_maps_query_vars');
add_action('parse_request', 'here_maps_parse_request');

// Add activation hook
register_activation_hook(__FILE__, 'here_maps_activation_hook');
register_deactivation_hook(__FILE__, 'here_maps_deactivation_hook');

// Add to WP-Admin link
add_action('admin_menu', 'here_maps_admin_page');

// Add custom filter to TinyMce editor
add_filter('tiny_mce_before_init', 'here_maps_add_iframe');

// Add new short codes
add_shortcode('nokia-maps', 'here_maps_shortcode');
add_shortcode('here-maps', 'here_maps_shortcode');

// Add custom sources to map
add_action('wp_enqueue_styles', 'here_maps_enqueue_styles');

// Add jQuery and custom scripts to map
add_action('wp_enqueue_scripts', 'here_maps_enqueue_scripts');

// Detect browser language
add_filter('load_textdomain_mofile', 'here_maps_detect_browser_language_file', 10, 2);

// Load language
// For creating *.mo file use: msgfmt file.po -o file.mo
load_plugin_textdomain('here-maps', false, 'here-maps/languages/');
