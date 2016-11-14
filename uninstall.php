<?php
// Ignore if directly called
if (!defined('WP_UNINSTALL_PLUGIN')) {
  die;
}

// Single Site
delete_option('dark_sky_api_key');
delete_option('dark_sky_api_latitude');
delete_option('dark_sky_api_longitude');

// Multisite
delete_site_option('dark_sky_api_key');
delete_site_option('dark_sky_api_latitude');
delete_site_option('dark_sky_api_longitude');
