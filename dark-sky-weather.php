<?php
/**
 * Plugin Name
 *
 * @package     Dark Sky Weather
 * @author      Stephen Yeargin
 * @copyright   2016 Stephen Yeargin
 * @license     MIT
 *
 * @wordpress-plugin
 * Plugin Name: Dark Sky Weather
 * Plugin URI:  https://github.com/stephenyeargin/dark-sky-weather
 * Description: A versatile weather plugin powered by Dark Sky.
 * Version:     1.0.0
 * Author:      Stephen Yeargin
 * Author URI:  https://stephenyeargin.com/
 * Text Domain: en_US
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

require_once dirname(__FILE__) . '/lib/darkskyapi.php';

use DarkSkyApi\DarkSkyApi;
use DarkSkyApi\DarkSkyApiException;

/**
 * Dark Sky Weather
 */
class DarkSkyWeather
{
  // When empty, use Nashville, Tennessee
  const DEFAULT_LATITUDE = '36.166667';
  const DEFAULT_LONGITUDE = '-86.783333';

  /**
   * @var object API
   */
  private $api;

  /**
   * Constructor
   */
  public function __construct() {
    try {
      $api_key = get_option('dark_sky_api_key', false);
      $this->api = new DarkSkyApi($api_key);
    } catch (DarkSkyApiException $e) {
      $this->addAdminError($e->getMessage());
    }
  }

  public function getCurrentConditions($latitude, $longitude) {
    if (!$this->api) {
      throw new DarkSkyApiException(__('Unable to connect to API.', 'en_US'));
    }
    return $this->api->getCurrentConditions($latitude, $longitude);
  }

  /* Private Methods */

  /**
   * Add Admin Error
   *
   * @param string Error message
   */
  private function addAdminError($error) {
    return add_action('admin_notices', function () use ($error) {
      $class = 'notice notice-error';
      $message = __($error, 'en_US');
      printf('<div class="%1$s"><p>Dark Sky Weather: %2$s <a href="%3$s">Settings</a></p></div>',
        $class,
        $error,
        esc_url(get_admin_url(null, 'options-general.php?page=dark_sky'))
      );
    });
  }
}

/**
 * WIDGET
 */

/**
 * Widget
 */
class DarkSkyWeather_Widget extends WP_Widget {

  function __construct() {
    // Instantiate the parent object
    parent::__construct(
      'dark_sky_weather', // Base ID
      esc_html__( 'Weather', 'en_US' ), // Name
      array( 'description' => esc_html__( 'Dark Sky Weather', 'en_US' ), ) // Args
    );
  }

  function widget($args, $instance) {
    echo $args['before_widget'];
    if (!empty( $instance['title'])) {
    	echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
    }
    try {
      $dark_sky = new DarkSkyWeather();
      $current_conditions = $dark_sky->getCurrentConditions(
        $instance['latitude'],
        $instance['longitude']
      );
      include dirname(__FILE__) . '/views/widget.php';
    } catch (Exception $e) {
      echo $e->getMessage();
    }
    echo $args['after_widget'];
  }

  function update($new_instance, $old_instance) {
    $instance = array();
    $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
    $instance['latitude'] = (!empty($new_instance['latitude'])) ? (float) $new_instance['latitude'] : '';
    $instance['longitude'] = (!empty($new_instance['longitude'])) ? (float) $new_instance['longitude'] : '';
    return $instance;
  }

  function form($instance) {
    $title = !empty($instance['title']) ? $instance['title'] : esc_html__('New title', 'en_US');
    $latitude = !empty($instance['latitude']) ? $instance['latitude'] : get_option('dark_sky_api_latitude', DarkSkyWeather::DEFAULT_LATITUDE);
    $longitude = !empty($instance['longitude']) ? $instance['longitude'] : get_option('dark_sky_api_longitude', DarkSkyWeather::DEFAULT_LONGITUDE);
    include dirname(__FILE__) . '/views/widget-form.php';
  }
}

add_action('widgets_init', function() {
  register_widget('DarkSkyWeather_Widget');
});

/**
 * SHORTCODE
 */

/**
 * Handle Shortcode
 *
 * @param array Attributes
 * @param string Enclosed content
 */
function dark_sky_handle_shortcode($atts, $content = null) {
  $atts = shortcode_atts(
    [
      'latitude' => DarkSkyWeather::DEFAULT_LATITUDE,
      'longitude' => DarkSkyWeather::DEFAULT_LONGITUDE
    ],
    $atts,
    'darksky'
  );
  try {
    $dark_sky = new DarkSkyWeather();
    $current_conditions = $dark_sky->getCurrentConditions(
      $atts['latitude'],
      $atts['longitude']
    );
    // Load Template
    ob_start();
    include dirname(__FILE__) . '/views/shortcode.php';
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  } catch (Exception $e) {
    return '<p class="dark-sky-error">' . $e->getMessage() . '</p>';
  }
}

add_shortcode('darksky', 'dark_sky_handle_shortcode');

/**
 * ADMIN
 */

 // Admin Loading
 if (is_admin()) {
   $dark_sky = new DarkSkyWeather();
   add_action('admin_init', 'dark_sky_add_options');
   add_action('admin_menu', 'dark_sky_admin_add_options_page');
 }

/**
 * Add Options Page
 */
function dark_sky_admin_add_options_page() {
  add_options_page('Dark Sky', 'Dark Sky Weather', 'manage_options', 'dark_sky', 'dark_sky_options_page');
}

/**
 * Update Options
 */
function dark_sky_update_options($post) {
  if ($post['dark_sky_api_key']) {
    update_option('dark_sky_api_key', trim($post['dark_sky_api_key']));
  }
  if ((float) $post['dark_sky_api_latitude']) {
    update_option('dark_sky_api_latitude', (float) $post['dark_sky_api_latitude']);
  }
  if ((float) $post['dark_sky_api_longitude']) {
    update_option('dark_sky_api_longitude', (float) $post['dark_sky_api_longitude']);
  }
}

/**
 * Options Page
 */
function dark_sky_options_page() {
  // Handle update
  if ($_POST) {
    dark_sky_update_options($_POST);
  }
  // Current settings
  $current = [
    'dark_sky_api_key' => get_option('dark_sky_api_key'),
    'dark_sky_api_latitude' => get_option('dark_sky_api_latitude'),
    'dark_sky_api_longitude' => get_option('dark_sky_api_longitude')
  ];
  // Load template
  require_once dirname(__FILE__) . '/views/settings.php';
}

/**
 * Add Options
 */
function dark_sky_add_options() {
  add_option('dark_sky_api_key', '');
  add_option('dark_sky_api_latitude', DarkSkyWeather::DEFAULT_LATITUDE);
  add_option('dark_sky_api_longitude', DarkSkyWeather::DEFAULT_LONGITUDE);
}

/**
 * SHARED
 */

/**
 * Register style sheet.
 */
function dark_sky_register_plugin_styles() {
  wp_register_style('dark-sky-weather', plugins_url('assets/css/dark-sky.css', __FILE__));
  wp_enqueue_style('dark-sky-weather');
}

if (!is_admin()) {
  add_action('wp_enqueue_scripts', 'dark_sky_register_plugin_styles');
}
