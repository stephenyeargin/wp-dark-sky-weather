<div class="dark-sky-weather-shortcode">
  <div class="dark-sky-weather-container">
    <div class="dark-sky-weather-icon">
      <img src="<?php echo plugins_url('assets/icons/' . $current_conditions->currently->icon . '.png', dirname(__FILE__) . '/../../'); ?>" alt="Icon" title="<?php echo $current_conditions->currently->summary; ?>" />
    </div>
    <div class="dark-sky-summary">
      <div class="dark-sky-weather-temperature">
        <?php echo (int) $current_conditions->currently->temperature; ?><sup>&deg;</sup>
      </div>
      <div class="dark-sky-weather-conditions">
        <?php echo $current_conditions->currently->summary; ?>
      </div>
    </div>
  </div>
  <div class="dark-sky-weather-attribution">
    Powered by <a href="https://darksky.net/">Dark Sky</a>
  </div>
</div>
