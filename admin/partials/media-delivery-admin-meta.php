<?php
/**
 * Provide the meta sidebar area for the plugin admin
 *
 * @link       https://joshcorne.co.uk
 * @since      1.0.0
 *
 * @package    Media_Delivery
 * @subpackage Media_Delivery/admin/partials
 */
?>
<p class="notice notice-error notice-alt inline <?php if (! $error_msg) echo 'hidden'; ?>" id="meta-fields-error-msg">
    <?php echo esc_html( $error_msg ); ?>
</p>
<p>
    <label for="meta-fields-booking-ref"><?php _e('Booking Reference', 'media-delivery') ?>
        <input type="text" class="meta-field" id="meta-fields-booking-ref" name="meta_fields_booking_ref" value="<?php echo esc_attr( $booking ); ?>" required />
    </label>
</p>

<p>
    <label for="meta-fields-customer-first-name"><?php _e('Customer First Name', 'media-delivery') ?>
        <input type="text" class="meta-field" id="meta-fields-customer-first-name" name="meta_fields_customer_first_name" value="<?php echo esc_attr( $first_name ); ?>" required />
    </label>
</p>

<p>
    <label for="meta-fields-customer-surname"><?php _e('Customer Surname', 'media-delivery') ?>
        <input type="text" class="meta-field" id="meta-fields-customer-surname" name="meta_fields_customer_surname" value="<?php echo esc_attr( $surname ); ?>" required />
    </label>
</p>

<p>
    <label for="meta-fields-customer-email"><?php _e('Customer Email', 'media-delivery') ?>
        <input type="email" class="meta-field" id="meta-fields-customer-email" name="meta_fields_customer_email" value="<?php echo esc_attr( $email ); ?>" required />
    </label>
</p>
