<?php

namespace DarkSkyApi;

/**
 * Dark Sky API
 */
class DarkSkyApi {
  /**
   * @var int Cache lifetime
   */
  const CACHE_TTL = 3600;

  /**
   * @var string API key
   */
  private $api_key;

  /**
   * @var string API base URL
   */
  private $api_base_url = 'https://api.darksky.net/forecast/';

  /**
   * Constructor
   *
   * @param string API key
   */
  public function __construct($api_key) {
    if (!$api_key) {
      throw new DarkSkyApiException('Missing API key.', 500);
    }
    $this->api_key = $api_key;
  }

  /**
   * Get Current Conditions
   *
   * @param float Latitude
   * @param float Longitude
   */
  public function getCurrentConditions($lat, $lon) {
    $data = $this->_request(sprintf('/%f,%f', $lat, $lon));
    if (!$data) {
      throw new DarkSkyApiException('Unable to retrieve forecast.');
    }
    return $data;
  }

  /* Private Methods */

  /**
   * Request
   *
   * @param string API method
   */
  private function _request($method) {
    $cache_key = 'dark_sky_api_' . $method;

    // Check if key is set
    if ($data = wp_cache_get($cache_key, 'dark_sky')) {
      return $data;
    }

    // No cache match, retrieve from API
    $url = $this->api_base_url . $this->api_key . $method;
    $response = wp_remote_get($url);

    // Something went wrong (bad API key?)
    if (is_wp_error($response)) {
      throw new DarkSkyApiException($response->get_error_message());
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    // Write decoded data to the cache
    wp_cache_set($cache_key, $data, 'dark_sky', DarkSkyApi::CACHE_TTL);
    return $data;
  }
}

/**
 * Dark Sky API Exception
 */
class DarkSkyApiException extends \Exception {}
