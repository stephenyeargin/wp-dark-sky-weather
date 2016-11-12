<p>
  <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e( 'Title:', 'en_US' ); ?></label>
  <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
</p>
<p>
  <label for="<?php echo esc_attr($this->get_field_id('latitude')); ?>"><?php esc_attr_e( 'Latitude:', 'en_US' ); ?></label>
  <input class="widefat" id="<?php echo esc_attr($this->get_field_id('latitude')); ?>" name="<?php echo esc_attr($this->get_field_name('latitude')); ?>" type="text" value="<?php echo esc_attr($latitude); ?>">
</p>
<p>
  <label for="<?php echo esc_attr($this->get_field_id('longitude')); ?>"><?php esc_attr_e( 'Longitude:', 'en_US' ); ?></label>
  <input class="widefat" id="<?php echo esc_attr($this->get_field_id('longitude')); ?>" name="<?php echo esc_attr($this->get_field_name('longitude')); ?>" type="text" value="<?php echo esc_attr($longitude); ?>">
</p>
