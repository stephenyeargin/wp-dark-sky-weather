<div class="wrap">
  <h1>Dark Sky Weather</h1>
  <?php if ($_POST): ?>
  <div class="notice notice-success"><p>Settings updated!</p></div>
  <?php endif; ?>
  <form method="post">
    <table class="form-table">
      <tr valign="top">
        <th scope="row">API Key</th>
        <td><input type="text" name="dark_sky_api_key" value="<?php echo esc_attr(get_option('dark_sky_api_key')); ?>" /></td>
      </tr>
    </table>
    <h2>Default Location</h2>
    <p><a class="dark-sky-load-geo" href="#geo">Update using my browser's location</a></p>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Latitude</th>
        <td><input type="text" name="dark_sky_api_latitude" value="<?php echo esc_attr(get_option('dark_sky_api_latitude')); ?>" /></td>
      </tr>
      <tr valign="top">
        <th scope="row">Longitude</th>
        <td><input type="text" name="dark_sky_api_longitude" value="<?php echo esc_attr(get_option('dark_sky_api_longitude')); ?>" /></td>
      </tr>
    </table>
    <?php submit_button(); ?>
  </form>
</div>

<!-- Allow user to load in a location from their browser -->
<script>
if ("geolocation" in navigator) {
  jQuery('.dark-sky-load-geo').bind('click', function() {
    navigator.geolocation.getCurrentPosition(
      // Success callback
      function(position) {
        console.log(position);
        jQuery('input[name="dark_sky_api_latitude"]').val(position.coords.latitude);
        jQuery('input[name="dark_sky_api_longitude"]').val(position.coords.longitude);
      },
      // Error callback
      function(error) {
        jQuery('.dark-sky-load-geo').innerHTML(error.message);
      }
    );
  })
} else {
  jQuery('.dark-sky-load-geo').remove();
}
</script>

<!-- Remove the error message, if present -->
<script>jQuery('div.notice-error').remove();</script>
